<?php

declare(strict_types=1);

use App\Models\Link;

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('the link will be redirect successfully', function () {

    $link = Link::factory()->create();

    $response = $this
        ->get(route('links.redirect', $link->key));

    $response->assertStatus(302);
    $response->assertRedirect($link->url);
});

it('invalid link will return 404', function () {

    $link = Link::factory()->create();

    $response = $this
        ->get(route('links.redirect', 'abc'));

    $response->assertNotFound();
});

it('git.now needs to be redirected to https://www.lua.sh [website]', function () {
    $response = $this
        ->get('https://git.now');

    $response->assertStatus(302);
    $response->assertRedirect('https://www.lua.sh');
});


it('fig.now needs to be redirected to https://www.lua.sh [website]', function () {
    $response = $this
        ->get('https://git.now');

    $response->assertStatus(302);
    $response->assertRedirect('https://www.lua.sh');
});

it('cal.now needs to be redirected to https://www.lua.sh [website]', function () {
    $response = $this
        ->get('https://git.now');

    $response->assertStatus(302);
    $response->assertRedirect('https://www.lua.sh');
});

it('spoti.now needs to be redirected to https://www.lua.sh [website]', function () {
    $response = $this
        ->get('https://git.now');

    $response->assertStatus(302);
    $response->assertRedirect('https://www.lua.sh');
});


it('redirects to the iOS URL if the user is on iOS', function () {
    $link = Link::factory()->create([
        'ios' => 'https://example.com/ios'
    ]);

    $response = $this
        ->withHeaders([
            'User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1',
        ])
        ->get($link->link);

    $response->assertStatus(302);
    $response->assertRedirect($link->ios);
});

it('redirects to the default URL if the user not on iOS', function () {
    $link = Link::factory()->create([
        'ios' => 'https://example.com/ios'
    ]);

    $response = $this->get($link->link);

    $response->assertStatus(302);
    $response->assertRedirect($link->url);
});

it('redirects to the default URL if user its on iOS but no iOS URL is set', function () {
    $link = Link::factory()->create([
        'ios' => null
    ]);

    $response = $this
        ->withHeaders([
            'User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1',
        ])
        ->get($link->link);

    $response->assertStatus(302);
    $response->assertRedirect($link->url);
});

it('redirects to the default URL if the user not on Android', function () {
    $link = Link::factory()->create([
        'android' => 'https://example.com/android'
    ]);

    $response = $this->get($link->link);

    $response->assertStatus(302);
    $response->assertRedirect($link->url);
});

it('redirects to the default URL if user its on Android but no Android URL is set', function () {
    $link = Link::factory()->create([
        'android' => null
    ]);

    $response = $this
        ->withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Linux; Android 14; Pixel 6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Mobile Safari/537.36',
        ])
    ->get($link->link);

    $response->assertStatus(302);
    $response->assertRedirect($link->url);
});

it('redirects to the Android URL if the user is on Android', function () {
    $link = Link::factory()->create([
        'android' => 'https://example.com/android'
    ]);

    $response = $this
        ->withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Linux; Android 14; Pixel 6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Mobile Safari/537.36',
        ])
        ->get($link->link);

    $response->assertStatus(302);
    $response->assertRedirect($link->android);
});

it('expired links without url will return 404', function () {
    $link = Link::factory()->create([
        'expires_at' => now()->subDay(),
        'expired_redirect_url' => null,
    ]);

    $response = $this->get($link->link);

    $response->assertNotFound();
});

it('redirects to the expired redirect URL if the link is expired', function () {
    $link = Link::factory()->create([
        'expires_at' => now()->subDay(),
        'expired_redirect_url' => 'https://example.com',
    ]);

    $response = $this->get($link->link);

    $response->assertStatus(302);
    $response->assertRedirect($link->expired_redirect_url);
});
