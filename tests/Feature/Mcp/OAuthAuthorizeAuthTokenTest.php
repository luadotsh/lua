<?php

declare(strict_types=1);

use App\Enums\User\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia;
use Symfony\Component\HttpFoundation\Response;

beforeEach(function () {
    $this->user = User::factory()->withWorkspace()->create();
    $this->workspace = $this->user->currentWorkspace;
    $this->workspace->update(['name' => 'Owner Workspace']);

    $this->clientId = mcpOauthClient('Auth Token Agent');
    DB::table('oauth_clients')->where('id', $this->clientId)->update([
        'redirect_uris' => json_encode(['https://client.example/callback']),
    ]);
});

test('workspace owner can approve oauth consent with the auth token from the consent page', function () {
    $this->actingAs($this->user)
        ->get(route('passport.authorizations.authorize', oauthAuthorizeQuery($this->clientId)))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('mcp/Authorize')
            ->has('authToken')
        );

    $authToken = session('authToken');

    expect($authToken)->toBeString()->not->toBeEmpty();

    $this->actingAs($this->user)
        ->post(route('passport.authorizations.approve'), [
            'state' => 'test-state',
            'client_id' => $this->clientId,
            'auth_token' => $authToken,
            'workspace_id' => $this->workspace->id,
        ])
        ->assertRedirect();

    expect(session()->has('authToken'))->toBeFalse();
});

test('workspace member can approve oauth consent with the auth token from the consent page', function () {
    $member = User::factory()->create();
    $this->workspace->users()->attach($member->id, ['role' => Role::ROLE_USER->value]);
    $member->update(['current_workspace_id' => $this->workspace->id]);

    $this->actingAs($member)
        ->get(route('passport.authorizations.authorize', oauthAuthorizeQuery($this->clientId)))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('mcp/Authorize')
            ->has('authToken')
        );

    $authToken = session('authToken');

    $this->actingAs($member)
        ->post(route('passport.authorizations.approve'), [
            'state' => 'test-state',
            'client_id' => $this->clientId,
            'auth_token' => $authToken,
            'workspace_id' => $this->workspace->id,
        ])
        ->assertRedirect();
});

test('owner cannot approve oauth consent without a workspace', function () {
    $this->actingAs($this->user)
        ->get(route('passport.authorizations.authorize', oauthAuthorizeQuery($this->clientId)))
        ->assertOk();

    $authToken = session('authToken');

    expect($authToken)->toBeString()->not->toBeEmpty();

    $this->actingAs($this->user)
        ->post(route('passport.authorizations.approve'), [
            'state' => 'test-state',
            'client_id' => $this->clientId,
            'auth_token' => $authToken,
            'workspace_id' => '',
        ])
        ->assertStatus(Response::HTTP_BAD_REQUEST);

    expect(DB::table('oauth_auth_codes')->where('client_id', $this->clientId)->exists())->toBeFalse();
});

test('a second authorize visit rotates auth token and rejects the stale one', function () {
    $query = oauthAuthorizeQuery($this->clientId);

    $this->actingAs($this->user)
        ->get(route('passport.authorizations.authorize', $query))
        ->assertOk();

    $staleToken = session('authToken');

    expect($staleToken)->toBeString()->not->toBeEmpty();

    $this->actingAs($this->user)
        ->get(route('passport.authorizations.authorize', $query))
        ->assertOk();

    expect(session('authToken'))->not->toBe($staleToken);

    $this->actingAs($this->user)
        ->post(route('passport.authorizations.approve'), [
            'state' => 'test-state',
            'client_id' => $this->clientId,
            'auth_token' => $staleToken,
            'workspace_id' => $this->workspace->id,
        ])
        ->assertForbidden();
});
