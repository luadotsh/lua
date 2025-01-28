<?php

declare(strict_types=1);

use App\Models\Link;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->withWorkspace()->create();
});

it('returns a successful response', function () {
    $response = $this
        ->actingAs($this->user)
        ->get(route('links.index'));

    $response->assertStatus(200);
});

it('can create a link with an expired redirect URL', function () {
    Link::factory()->create([
        'expires_at' => now()->subDay(),
        'expired_redirect_url' => 'https://example.com',
    ]);

    $this->assertDatabaseCount('links', 1);
});

it('can create a link', function () {
    Link::factory()->create();

    $this->assertDatabaseCount('links', 1);
});

it('cannot create a link without url', function () {

    $response = $this
        ->actingAs($this->user)
        ->from(route('links.index'))
        ->post(route('links.store'), [
            'domain' => config('domains.main'),
            'key' => 'new-link',
            'url' => null,
        ]);

    $response->assertInvalid(['url']);
});
