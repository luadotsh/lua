<?php

declare(strict_types=1);

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

/**
 * The proxy exists so the browser never asks Google directly, which would hand
 * it every destination a visitor is looking at. None of these reach the
 * network: the old test did, which made the suite depend on Google being up.
 */
it('passes through the favicon it was given', function () {
    Http::fake(['t1.gstatic.com/*' => Http::response('a-real-png', 200)]);

    $this->get(route('websites.favicon', ['url' => 'https://github.com']))
        ->assertOk()
        ->assertHeader('Content-Type', 'image/png');

    expect($this->get(route('websites.favicon', ['url' => 'https://github.com']))->getContent())
        ->toBe('a-real-png');
});

it('asks google for the site it was handed', function () {
    Http::fake(['t1.gstatic.com/*' => Http::response('png', 200)]);

    $this->get(route('websites.favicon', ['url' => 'https://github.com']));

    Http::assertSent(fn ($request) => str_contains($request->url(), 'github.com'));
});

it('falls back to our own icon when the answer is not one', function () {
    Http::fake(['t1.gstatic.com/*' => Http::response('', 404)]);

    $response = $this->get(route('websites.favicon', ['url' => 'https://nowhere.example']));

    $response->assertOk()->assertHeader('Content-Type', 'image/png');

    expect($response->getContent())
        ->toBe(file_get_contents(public_path('/images/websites/favicon.png')));
});

it('falls back when the lookup fails outright', function () {
    // A timeout or a DNS failure must not become a 500 on our side.
    Http::fake(fn () => throw new ConnectionException('timed out'));

    $this->get(route('websites.favicon', ['url' => 'https://nowhere.example']))
        ->assertOk();
});

it('falls back when asked for nothing at all', function () {
    Http::fake();

    $this->get(route('websites.favicon'))->assertOk();

    Http::assertNothingSent();
});
