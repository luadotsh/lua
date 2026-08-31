<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->withWorkspace()->create();
});

/**
 * The API middleware binds a request to the workspace its key was issued from,
 * and every refusal here is the boundary between two tenants.
 */
it('refuses a request carrying no token at all', function () {
    $this->getJson(route('api.links.index'))->assertUnauthorized();
});

it('refuses a token string that resolves to nothing', function () {
    $this->withHeader('Authorization', 'Bearer not-a-real-token')
        ->getJson(route('api.links.index'))
        ->assertUnauthorized();
});

it('refuses a key whose workspace has been closed', function () {
    $token = apiTokenFor($this->user, 'Deploy key');

    // Soft-deleted after the key was minted: the key must stop resolving to it.
    Workspace::where('id', $this->user->current_workspace_id)->delete();

    $this->withToken($token)
        ->getJson(route('api.links.index'))
        ->assertStatus(401);
});

it('refuses a key held by someone no longer in that workspace', function () {
    $token = apiTokenFor($this->user, 'Deploy key');

    // They were removed after the key was minted; the key must stop working.
    $this->user->workspaces()->detach();

    $this->withToken($token)
        ->getJson(route('api.links.index'))
        ->assertForbidden();
});

it('lets a key through to the workspace it was issued from', function () {
    $token = apiTokenFor($this->user, 'Deploy key');

    $this->withToken($token)
        ->getJson(route('api.links.index'))
        ->assertOk();
});
