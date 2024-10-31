<?php

declare(strict_types=1);

use App\Models\Link;

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('the link will be redirect successfully', function () {

    $link = Link::factory()->create();

    $response = $this
        ->get(route('links.redirect', $link->key));

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

    $response->assertRedirect('https://www.lua.sh');
});


it('fig.now needs to be redirected to https://www.lua.sh [website]', function () {
    $response = $this
        ->get('https://git.now');

    $response->assertRedirect('https://www.lua.sh');
});

it('cal.now needs to be redirected to https://www.lua.sh [website]', function () {
    $response = $this
        ->get('https://git.now');

    $response->assertRedirect('https://www.lua.sh');
});

it('spoti.now needs to be redirected to https://www.lua.sh [website]', function () {
    $response = $this
        ->get('https://git.now');

    $response->assertRedirect('https://www.lua.sh');
});
