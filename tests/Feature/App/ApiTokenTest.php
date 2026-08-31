<?php

declare(strict_types=1);

use App\Models\AccessToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->withWorkspace()->create();
});

it('returns a successful response', function () {
    $response = $this
        ->actingAs($this->user)
        ->get(route('setting.api-tokens.index'));

    $response->assertStatus(200);
});

it('creates an api token and shows it once', function () {
    $this->actingAs($this->user)
        ->post(route('setting.api-tokens.store'), ['name' => 'Deploy key'])
        ->assertRedirect()
        ->assertSessionHas('flash.token');

    expect(AccessToken::where('workspace_id', $this->user->current_workspace_id)
        ->where('name', 'Deploy key')->exists())->toBeTrue();
});

it('creates an api token that expires', function () {
    $this->actingAs($this->user)
        ->post(route('setting.api-tokens.store'), [
            'name' => 'Temporary',
            'expires_at' => now()->addDays(7)->toDateString(),
        ])
        ->assertRedirect();

    $token = AccessToken::where('name', 'Temporary')->firstOrFail();

    expect($token->expires_at)->not->toBeNull();
});

it('refuses an api token with no name', function () {
    $this->actingAs($this->user)
        ->post(route('setting.api-tokens.store'), ['name' => ''])
        ->assertSessionHasErrors('name');
});

it('refuses an expiry already in the past', function () {
    $this->actingAs($this->user)
        ->post(route('setting.api-tokens.store'), [
            'name' => 'Backdated',
            'expires_at' => now()->subDay()->toDateString(),
        ])
        ->assertSessionHasErrors('expires_at');
});

it('says so when the token to revoke is not there', function () {
    $this->actingAs($this->user)
        ->delete(route('setting.api-tokens.destroy', Str::random(80)))
        ->assertRedirect();
});

it('lists the workspace keys and not another workspace keys', function () {
    apiTokenFor($this->user, 'Mine');

    $other = User::factory()->withWorkspace()->create();
    apiTokenFor($other, 'Theirs');

    $this->actingAs($this->user)
        ->get(route('setting.api-tokens.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->has('tokens', 1)
            ->where('tokens.0.name', 'Mine')
            ->where('hasData', true)
        );
});
