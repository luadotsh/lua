<?php

declare(strict_types=1);

use App\Actions\ApiKey\CreateApiKey;
use App\Models\AccessToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->withWorkspace()->create();
});

it('rejects a request with no token', function () {
    $this->json('GET', route('api.links.index'))->assertStatus(401);
});

it('rejects a made up bearer token', function () {
    $this->withToken('not-a-real-token')
        ->json('GET', route('api.links.index'))
        ->assertStatus(401);
});

it('binds the issued key to the workspace it was created from', function () {
    $token = apiTokenFor($this->user);

    $stored = AccessToken::query()->personalAccessApiKey()->firstOrFail();

    expect($stored->workspace_id)->toBe($this->user->current_workspace_id)
        ->and($stored->expires_at)->toBeNull();

    $this->withToken($token)->json('GET', route('api.links.index'))->assertOk();
});

it('records when a key was last used', function () {
    $token = apiTokenFor($this->user);

    expect(AccessToken::query()->personalAccessApiKey()->first()->last_used_at)->toBeNull();

    $this->withToken($token)->json('GET', route('api.links.index'))->assertOk();

    expect(AccessToken::query()->personalAccessApiKey()->first()->last_used_at)->not->toBeNull();
});

it('refuses a key whose expiry has passed', function () {
    $token = apiTokenFor($this->user);

    AccessToken::query()->personalAccessApiKey()->first()
        ->forceFill(['expires_at' => now()->subDay()])->saveQuietly();

    $this->withToken($token)
        ->json('GET', route('api.links.index'))
        ->assertStatus(401)
        ->assertJson(['message' => 'Token expired.']);
});

it('refuses a revoked key', function () {
    $token = apiTokenFor($this->user);

    AccessToken::query()->personalAccessApiKey()->first()
        ->forceFill(['revoked' => true])->saveQuietly();

    $this->withToken($token)->json('GET', route('api.links.index'))->assertStatus(401);
});

it('never lets a key reach a workspace it is not bound to', function () {
    $other = User::factory()->withWorkspace()->create();
    $token = apiTokenFor($this->user);

    // Even if the owner switches their session to another workspace, the token
    // stays pinned to the one it was issued for.
    $this->user->forceFill(['current_workspace_id' => $other->current_workspace_id])->save();

    $this->withToken($token)->json('GET', route('api.links.index'))->assertOk();

    expect(AccessToken::query()->personalAccessApiKey()->first()->workspace_id)
        ->toBe($this->user->workspaces()->first()->id);
});

it('honours an expiry date given at creation', function () {
    // Ensures the personal access client exists before creating directly.
    apiTokenFor($this->user);

    CreateApiKey::execute(
        $this->user,
        $this->user->currentWorkspace,
        ['name' => 'Expiring key', 'expires_at' => now()->addDays(7)->toDateString()],
    );

    $expiring = AccessToken::query()->personalAccessApiKey()
        ->where('name', 'Expiring key')->firstOrFail();

    expect($expiring->expires_at)->not->toBeNull()
        ->and($expiring->expires_at->isFuture())->toBeTrue();
});
