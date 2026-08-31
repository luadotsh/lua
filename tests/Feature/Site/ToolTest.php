<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use Inertia\Testing\AssertableInertia as Assert;

it('lists the tools', function (): void {
    $this->get(route('site.tools.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Site/Tools/Index')
            ->has('tools', 3)
            ->has('tools.0.url')
        );
});

it('renders each tool', function (string $route, string $component): void {
    $this->get(route($route))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component($component));
})->with([
    ['site.tools.utm-builder', 'Site/Tools/UtmBuilder'],
    ['site.tools.qr-generator', 'Site/Tools/QrGenerator'],
    ['site.tools.link-checker', 'Site/Tools/LinkChecker'],
]);

it('follows a redirect chain and reports each hop', function (): void {
    Http::fake([
        'https://example.com/one' => Http::response('', 302, ['Location' => 'https://example.com/two']),
        'https://example.com/two' => Http::response('', 200),
    ]);

    $this->postJson(route('site.tools.check'), ['url' => 'https://example.com/one'])
        ->assertOk()
        ->assertJsonPath('destination', 'https://example.com/two')
        ->assertJsonPath('hops.0.status', 302)
        ->assertJsonPath('hops.1.status', 200)
        ->assertJsonPath('error', null);
});

it('resolves a relative Location against the hop it came from', function (): void {
    // A relative redirect left unresolved would be checked as a hostless
    // string on the next hop and skip the address guard entirely.
    Http::fake([
        'https://example.com/a' => Http::response('', 301, ['Location' => '/b']),
        'https://example.com/b' => Http::response('', 200),
    ]);

    $this->postJson(route('site.tools.check'), ['url' => 'https://example.com/a'])
        ->assertOk()
        ->assertJsonPath('destination', 'https://example.com/b');
});

it('gives up on a loop instead of hanging', function (): void {
    Http::fake([
        'https://example.com/loop' => Http::response('', 302, ['Location' => 'https://example.com/loop']),
    ]);

    $this->postJson(route('site.tools.check'), ['url' => 'https://example.com/loop'])
        ->assertOk()
        ->assertJsonPath('destination', null)
        ->assertJsonPath('error', 'Stopped after 10 redirects. This link loops.');
});

// The whole point of the guard. This endpoint fetches a URL a stranger chose,
// from our server, so every one of these has to be refused before any request
// is made.
it('refuses to fetch anything that is not a public web address', function (string $url): void {
    Http::fake();

    $response = $this->postJson(route('site.tools.check'), ['url' => $url]);

    // Either validation rejects it or the action does, but nothing is fetched.
    expect($response->status())->toBeIn([200, 422]);

    if ($response->status() === 200) {
        expect($response->json('error'))->not->toBeNull()
            ->and($response->json('destination'))->toBeNull();
    }

    Http::assertNothingSent();
})->with([
    'loopback' => ['http://127.0.0.1/admin'],
    'private range' => ['http://10.0.0.1/'],
    'link-local metadata' => ['http://169.254.169.254/latest/meta-data/'],
    'file scheme' => ['file:///etc/passwd'],
    'no scheme' => ['example.com'],
]);

it('rejects a url that is not a url', function (): void {
    $this->postJson(route('site.tools.check'), ['url' => 'not a url'])
        ->assertJsonValidationErrors('url');
});
