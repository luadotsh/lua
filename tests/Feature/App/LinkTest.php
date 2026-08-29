<?php

declare(strict_types=1);

use App\Actions\Link\ListLinks;
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

it('accepts a mixed case custom back-half', function () {
    $this
        ->actingAs($this->user)
        ->post(route('links.store'), [
            'url' => 'https://example.com',
            'domain' => config('domains.main'),
            'key' => 'MyLink-01',
        ])
        ->assertSessionHasNoErrors();

    expect(Link::where('key', 'MyLink-01')->exists())->toBeTrue();
});

it('rejects a back-half with characters that cannot sit in a path', function () {
    $this
        ->actingAs($this->user)
        ->post(route('links.store'), [
            'url' => 'https://example.com',
            'domain' => config('domains.main'),
            'key' => 'my link/01',
        ])
        ->assertSessionHasErrors('key');
});

it('rejects a back-half with unicode letters', function () {
    // alpha_dash without :ascii would let this through, and a path cannot
    // carry it without percent-encoding.
    $this
        ->actingAs($this->user)
        ->post(route('links.store'), [
            'url' => 'https://example.com',
            'domain' => config('domains.main'),
            'key' => 'promoção',
        ])
        ->assertSessionHasErrors('key');
});

it('finds a link regardless of the case typed into search', function () {
    Link::factory()->create([
        'workspace_id' => $this->user->current_workspace_id,
        'url' => 'https://github.com/luadotsh',
    ]);

    $found = ListLinks::execute(
        $this->user->currentWorkspace,
        ['search' => 'GITHUB'],
    );

    expect($found->total())->toBe(1);
});
