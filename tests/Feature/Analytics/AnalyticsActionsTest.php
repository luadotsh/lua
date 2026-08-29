<?php

declare(strict_types=1);

use App\Actions\Analytics\GetBreakdown;
use App\Actions\Analytics\GetOverview;
use App\Actions\Analytics\GetTimeseries;
use App\Enums\LinkStat\Event;
use App\Models\Link;
use App\Models\LinkStat;
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
    $user = App\Models\User::factory()->withWorkspace()->create();
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
