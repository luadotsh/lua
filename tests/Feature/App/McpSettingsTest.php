<?php

declare(strict_types=1);

use App\Models\AccessToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Laravel\Passport\Passport;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->withWorkspace()->create();
});

function mcpGrantFor(User $user, string $clientName = 'Some MCP client'): AccessToken
{
    $client = Passport::client()->forceFill([
        'id' => Str::uuid()->toString(),
        'name' => $clientName,
        'redirect_uris' => ['https://example.com/callback'],
        'grant_types' => ['authorization_code', 'refresh_token'],
        'revoked' => false,
    ]);
    $client->save();

    return AccessToken::query()->create([
        'id' => Str::random(80),
        'user_id' => $user->id,
        'client_id' => $client->id,
        'workspace_id' => $user->current_workspace_id,
        'scopes' => ['mcp:use'],
        'revoked' => false,
    ]);
}

it('lists the apps connected over mcp', function () {
    mcpGrantFor($this->user, 'Claude');

    $this->actingAs($this->user)
        ->get(route('setting.mcp.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Setting/Mcp/Index')
            ->has('connectedClients', 1)
            ->where('connectedClients.0.name', 'Claude')
        );
});

it('disconnects an app from the mcp screen', function () {
    $grant = mcpGrantFor($this->user);

    $this->actingAs($this->user)
        ->delete(route('setting.mcp.destroy', $grant->id))
        ->assertRedirect();

    expect($grant->fresh()->revoked)->toBeTrue();
});

it('says so when there is nothing to disconnect', function () {
    $this->actingAs($this->user)
        ->delete(route('setting.mcp.destroy', Str::random(80)))
        ->assertRedirect();
});

it('shows the usage screen', function () {
    $this->actingAs($this->user)
        ->get(route('setting.usage.index'))
        ->assertOk();
});
