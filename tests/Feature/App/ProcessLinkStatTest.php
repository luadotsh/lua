<?php

declare(strict_types=1);

use App\Enums\LinkStat\Event;
use App\Jobs\ProcessLinkStat;
use App\Models\Link;
use App\Models\LinkStat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Stevebauman\Location\Facades\Location;
use Stevebauman\Location\Position;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->withWorkspace()->create();
    $this->workspace = $this->user->currentWorkspace;
    $this->link = Link::factory()->create([
        'workspace_id' => $this->workspace->id,
        'domain' => 'lua.test',
        'key' => 'tracked',
    ]);
});

/**
 * The package ships its own fake, keyed by the address it answers for, so no
 * test resolves a real one.
 */
function fakeLocation(string $ip = '*', ?string $country = 'BR', ?string $region = 'São Paulo', ?string $city = 'Santos'): void
{
    $position = new Position;
    $position->countryCode = $country;
    $position->regionName = $region;
    $position->cityName = $city;

    Location::fake([$ip => $position]);
}

function trackedHit(Link $link, array $overrides = []): ProcessLinkStat
{
    return new ProcessLinkStat(
        $link,
        $overrides['userAgent'] ?? 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 Chrome/151.0.0.0 Safari/537.36',
        $overrides['languages'] ?? ['pt-BR', 'en'],
        $overrides['ip'] ?? '203.0.113.7',
        $overrides['qr'] ?? false,
        $overrides['utms'] ?? array_fill_keys(
            ['utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term'],
            null,
        ),
        // array_key_exists, not ??: a null referer is the case worth testing,
        // and ?? would fall through to the default.
        array_key_exists('referer', $overrides)
            ? $overrides['referer']
            : 'https://news.ycombinator.com/',
    );
}

it('records everything it can read off one hit', function () {
    fakeLocation();

    trackedHit($this->link)->handle();

    $stat = LinkStat::firstOrFail();

    expect($stat->workspace_id)->toBe($this->workspace->id)
        ->and($stat->link_id)->toBe($this->link->id)
        ->and($stat->event)->toBe(Event::CLICK)
        ->and($stat->country)->toBe('BR')
        ->and($stat->region)->toBe('São Paulo')
        ->and($stat->city)->toBe('Santos')
        ->and($stat->browser)->toBe('Chrome')
        ->and($stat->os)->toBe('macOS')
        ->and($stat->device)->toBe('Desktop')
        ->and($stat->language)->toBe('pt-BR')
        ->and($stat->referer)->toBe('https://news.ycombinator.com/')
        ->and($stat->ip)->toBe('203.0.113.7');
});

it('tells a scan apart from a click', function () {
    fakeLocation();

    trackedHit($this->link, ['qr' => true])->handle();

    // The two are counted separately everywhere: the dashboard, the events
    // table and the link's own totals.
    expect(LinkStat::firstOrFail()->event)->toBe(Event::QR_SCAN);
});

it('records a hit from an address it cannot place', function () {
    // The lookup failing is not the visitor's fault: the click still counts,
    // it just has no country on it.
    Location::fake([]);

    trackedHit($this->link)->handle();

    $stat = LinkStat::firstOrFail();

    expect($stat->country)->toBeNull()
        ->and($stat->region)->toBeNull()
        ->and($stat->city)->toBeNull()
        ->and($stat->browser)->toBe('Chrome');
});

it('keeps the campaign parameters the visit arrived with', function () {
    fakeLocation();

    trackedHit($this->link, ['utms' => [
        'utm_source' => 'newsletter',
        'utm_medium' => 'email',
        'utm_campaign' => 'launch',
        'utm_content' => 'header',
        'utm_term' => 'short links',
    ]])->handle();

    $stat = LinkStat::firstOrFail();

    expect($stat->utm_source)->toBe('newsletter')
        ->and($stat->utm_medium)->toBe('email')
        ->and($stat->utm_campaign)->toBe('launch')
        ->and($stat->utm_content)->toBe('header')
        ->and($stat->utm_term)->toBe('short links');
});

it('calls a visit with no referrer direct', function () {
    fakeLocation();

    trackedHit($this->link, ['referer' => null])->handle();

    // "Direct" is the word the whole dashboard uses for an absent referrer.
    expect(LinkStat::firstOrFail()->referer)->toBe('Direct');
});

it('records a hit that carried no language header', function () {
    fakeLocation();

    trackedHit($this->link, ['languages' => []])->handle();

    expect(LinkStat::firstOrFail()->language)->toBe('Unknown Language');
});

it('counts the hit towards the link and the dashboard alike', function () {
    fakeLocation();

    trackedHit($this->link)->handle();
    trackedHit($this->link, ['qr' => true])->handle();

    $link = Link::withClickTotals()->findOrFail($this->link->id);

    // Two events, one of them a scan, so the link shows one click.
    expect(LinkStat::count())->toBe(2)
        ->and($link->clicks)->toBe(1);
});
