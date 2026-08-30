<?php

declare(strict_types=1);

use App\Actions\Analytics\GetBreakdown;
use App\Actions\Analytics\GetOverview;
use App\Actions\Analytics\GetTimeseries;
use App\Actions\Analytics\ResolveFilters;
use App\Enums\LinkStat\Event;
use App\Models\Link;
use App\Models\LinkStat;
use App\Models\User;
use App\Models\Workspace;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function hit(Workspace $workspace, Link $link, array $attributes = []): LinkStat
{
    $createdAt = $attributes['created_at'] ?? CarbonImmutable::now();
    unset($attributes['created_at']);

    $stat = LinkStat::create(array_merge([
        'workspace_id' => $workspace->id,
        'link_id' => $link->id,
        'event' => Event::CLICK->value,
        'ip' => '1.1.1.1',
    ], $attributes));

    // created_at is not fillable, so mass assignment would silently drop it
    // and every row would land in the current window.
    $stat->forceFill(['created_at' => $createdAt])->saveQuietly();

    return $stat;
}

beforeEach(function () {
    $this->workspace = Workspace::factory()->create();
    $this->link = Link::factory()->create(['workspace_id' => $this->workspace->id]);
    $this->end = CarbonImmutable::now();
    $this->start = $this->end->subDays(7);
});

it('counts events, clicks, scans and distinct visitors', function () {
    hit($this->workspace, $this->link, ['ip' => '1.1.1.1']);
    hit($this->workspace, $this->link, ['ip' => '1.1.1.1']);
    hit($this->workspace, $this->link, ['ip' => '2.2.2.2']);
    hit($this->workspace, $this->link, ['ip' => '3.3.3.3', 'event' => Event::QR_SCAN->value]);

    $overview = GetOverview::execute($this->workspace, $this->start, $this->end);

    expect($overview['events']['value'])->toBe(4)
        ->and($overview['clicks']['value'])->toBe(3)
        ->and($overview['qr_scans']['value'])->toBe(1)
        // Two hits from one address count as one visitor.
        ->and($overview['visitors']['value'])->toBe(3);
});

it('compares against the same span immediately before', function () {
    // Two in the current window, one in the window before it.
    hit($this->workspace, $this->link, ['created_at' => $this->end->subDay()]);
    hit($this->workspace, $this->link, ['created_at' => $this->end->subDays(2)]);
    hit($this->workspace, $this->link, ['created_at' => $this->start->subDays(2)]);

    $overview = GetOverview::execute($this->workspace, $this->start, $this->end);

    expect($overview['events']['value'])->toBe(2)
        ->and($overview['events']['previous'])->toBe(1)
        ->and($overview['events']['change'])->toBe(100.0);
});

it('reports no change rather than infinity when there is no baseline', function () {
    hit($this->workspace, $this->link);

    $overview = GetOverview::execute($this->workspace, $this->start, $this->end);

    expect($overview['events']['previous'])->toBe(0)
        ->and($overview['events']['change'])->toBeNull();
});

it('never counts another workspace', function () {
    $other = Workspace::factory()->create();
    $otherLink = Link::factory()->create(['workspace_id' => $other->id]);

    hit($this->workspace, $this->link);
    hit($other, $otherLink);
    hit($other, $otherLink);

    expect(GetOverview::execute($this->workspace, $this->start, $this->end)['events']['value'])->toBe(1);
});

it('fills empty buckets so the chart has no holes', function () {
    hit($this->workspace, $this->link, ['created_at' => $this->end->subDays(3)]);

    $series = GetTimeseries::execute($this->workspace, $this->start, $this->end, 'day', 'UTC');

    expect($series->count())->toBeGreaterThan(1)
        ->and($series->sum('events'))->toBe(1)
        // Every bucket is present, including the quiet ones.
        ->and($series->every(fn ($row) => array_key_exists('events', $row)))->toBeTrue();
});

it('breaks a dimension down with each row share of the total', function () {
    hit($this->workspace, $this->link, ['country' => 'BR', 'ip' => '1.1.1.1']);
    hit($this->workspace, $this->link, ['country' => 'BR', 'ip' => '2.2.2.2']);
    hit($this->workspace, $this->link, ['country' => 'US', 'ip' => '3.3.3.3']);

    $rows = GetBreakdown::execute($this->workspace, 'country', $this->start, $this->end);

    expect($rows->first()['value'])->toBe('BR')
        ->and($rows->first()['events'])->toBe(2)
        ->and($rows->first()['visitors'])->toBe(2)
        ->and($rows->first()['share'])->toBe(66.7);
});

it('leaves rows with no value out of a breakdown', function () {
    hit($this->workspace, $this->link, ['country' => 'BR']);
    hit($this->workspace, $this->link, ['country' => null]);

    $rows = GetBreakdown::execute($this->workspace, 'country', $this->start, $this->end);

    expect($rows)->toHaveCount(1)
        ->and($rows->first()['share'])->toBe(100.0);
});

it('refuses a dimension it does not know', function () {
    GetBreakdown::execute($this->workspace, 'passport_number', $this->start, $this->end);
})->throws(InvalidArgumentException::class);

it('ranks the busiest links', function () {
    $quiet = Link::factory()->create(['workspace_id' => $this->workspace->id]);

    hit($this->workspace, $this->link);
    hit($this->workspace, $this->link);
    hit($this->workspace, $quiet);

    $rows = GetBreakdown::links($this->workspace, $this->start, $this->end);

    expect($rows->first()['value'])->toBe($this->link->link)
        ->and($rows->first()['events'])->toBe(2);
});

it('serves the whole dashboard in one call', function () {
    $user = User::factory()->withWorkspace()->create();
    $link = Link::factory()->create(['workspace_id' => $user->current_workspace_id]);

    hit($user->currentWorkspace, $link, ['country' => 'BR', 'browser' => 'Chrome']);

    $response = Pest\Laravel\actingAs($user)->getJson(route('analytics.statistics', [
        'start' => CarbonImmutable::now()->subDays(7)->toDateString(),
        'end' => CarbonImmutable::now()->toDateString(),
        'group' => 'day',
        'timezone' => 'UTC',
    ]));

    $response->assertOk()->assertJsonStructure([
        'overview' => ['events' => ['value', 'previous', 'change']],
        'timeseries' => [['bucket', 'events', 'clicks', 'qr_scans', 'visitors']],
        'links' => [['value', 'url', 'events', 'visitors', 'share']],
        'breakdowns' => ['sources', 'locations', 'devices'],
    ]);

    expect($response->json('overview.events.value'))->toBe(1)
        ->and($response->json('breakdowns.locations.0.rows.0.value'))->toBe('BR');
});

it('keeps only known dimensions and drops empty or unsafe values', function () {
    $filters = ResolveFilters::execute([
        'country' => 'BR',
        'browser' => ['Chrome', '', 'Chrome', 'Safari'],
        'start' => '2026-01-01',
        'nonsense' => 'x',
        'city' => "bad\0byte",
        'os' => [],
    ]);

    expect($filters)->toBe([
        'country' => ['BR'],
        'browser' => ['Chrome', 'Safari'],
    ]);
});

it('orders filters by dimension rather than by query string', function () {
    $filters = ResolveFilters::execute(['browser' => 'Chrome', 'referer' => 'https://x.com/']);

    expect(array_keys($filters))->toBe(['referer', 'browser']);
});

it('shapes filters as a list the frontend can render without branching', function () {
    expect(ResolveFilters::toActive(['country' => ['BR', 'PT']]))
        ->toBe([['dimension' => 'country', 'values' => ['BR', 'PT']]]);
});

it('narrows the overview, the chart and every breakdown to a filter', function () {
    hit($this->workspace, $this->link, ['country' => 'BR', 'browser' => 'Chrome']);
    hit($this->workspace, $this->link, ['country' => 'BR', 'browser' => 'Safari']);
    hit($this->workspace, $this->link, ['country' => 'PT', 'browser' => 'Chrome']);

    $filters = ['country' => ['BR']];

    $overview = GetOverview::execute($this->workspace, $this->start, $this->end, $filters);
    expect($overview['events']['value'])->toBe(2);

    $timeseries = GetTimeseries::execute(
        $this->workspace,
        $this->start,
        $this->end,
        'day',
        'UTC',
        $filters,
    );
    expect($timeseries->sum('events'))->toBe(2);

    // A filter narrows its own card too: the country list shows Brazil alone.
    $countries = GetBreakdown::execute($this->workspace, 'country', $this->start, $this->end, $filters);
    expect($countries->pluck('value')->all())->toBe(['BR']);

    $browsers = GetBreakdown::execute($this->workspace, 'browser', $this->start, $this->end, $filters);
    expect($browsers->pluck('value')->sort()->values()->all())->toBe(['Chrome', 'Safari']);

    expect(GetBreakdown::links($this->workspace, $this->start, $this->end, $filters)->first()['events'])
        ->toBe(2);
});

it('combines two filters rather than replacing one with the other', function () {
    hit($this->workspace, $this->link, ['country' => 'BR', 'browser' => 'Chrome']);
    hit($this->workspace, $this->link, ['country' => 'BR', 'browser' => 'Safari']);
    hit($this->workspace, $this->link, ['country' => 'PT', 'browser' => 'Chrome']);

    $overview = GetOverview::execute($this->workspace, $this->start, $this->end, [
        'country' => ['BR'],
        'browser' => ['Chrome'],
    ]);

    expect($overview['events']['value'])->toBe(1);
});

it('widens a dimension when it holds several values', function () {
    hit($this->workspace, $this->link, ['country' => 'BR']);
    hit($this->workspace, $this->link, ['country' => 'PT']);
    hit($this->workspace, $this->link, ['country' => 'ES']);

    $overview = GetOverview::execute($this->workspace, $this->start, $this->end, [
        'country' => ['BR', 'PT'],
    ]);

    expect($overview['events']['value'])->toBe(2);
});

it('buckets timestamps with the expression its database driver understands', function (string $driver, string $expected) {
    // The driver is passed in, so this asserts the MySQL arm without a MySQL
    // server — the arm no local run would otherwise reach.
    [$sql] = GetTimeseries::bucketExpression('day', 'America/Sao_Paulo', $driver);

    expect($sql)->toBe($expected);
})->with([
    ['pgsql', "date_trunc(?, created_at at time zone 'UTC' at time zone ?)"],
    ['mysql', "date_format(convert_tz(created_at, '+00:00', ?), ?)"],
    ['mariadb', "date_format(convert_tz(created_at, '+00:00', ?), ?)"],
]);

it('binds the unit each driver needs for the requested grouping', function () {
    expect(GetTimeseries::bucketExpression('hour', 'UTC', 'mysql')[1])
        ->toBe(['UTC', '%Y-%m-%d %H:00:00'])
        ->and(GetTimeseries::bucketExpression('month', 'UTC', 'pgsql')[1])
        ->toBe(['month', 'UTC']);
});

it('refuses to guess a bucket expression for an unknown driver', function () {
    // Better a clear failure here than SQL that only breaks once it reaches
    // the database.
    expect(fn () => GetTimeseries::bucketExpression('day', 'UTC', 'sqlite'))
        ->toThrow(RuntimeException::class, 'sqlite');
});
