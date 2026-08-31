<?php

declare(strict_types=1);

use App\Actions\AccessToken\ListConnectedMcpClients;
use App\Actions\AccessToken\RevokeAccessToken;
use App\Enums\User\Role;
use App\Models\AccessToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Laravel\Passport\Passport;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->withWorkspace()->create([
        'password' => Hash::make('password'),
    ]);
});

it('renders the authentication screen', function () {
    actingAs($this->user)->get(route('setting.authentication.edit'))->assertOk();
});

it('updates the password when the current one is right', function () {
    actingAs($this->user)
        ->put(route('setting.authentication.password'), [
            'current_password' => 'password',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])
        ->assertSessionHasNoErrors();

    expect(Hash::check('new-password-123', $this->user->fresh()->password))->toBeTrue();
});

it('refuses to change the password without the current one', function () {
    actingAs($this->user)
        ->put(route('setting.authentication.password'), [
            'current_password' => 'wrong',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])
        ->assertSessionHasErrors('current_password');
});

it('lets a social-only user set a first password with no current one', function () {
    $social = User::factory()->withWorkspace()->create(['password' => null, 'google_id' => 'g-1']);

    actingAs($social)
        ->put(route('setting.authentication.password'), [
            'password' => 'first-password-123',
            'password_confirmation' => 'first-password-123',
        ])
        ->assertSessionHasNoErrors();

    expect($social->fresh()->password)->not->toBeNull();
});

it('will not disconnect the only sign-in method', function () {
    $social = User::factory()->withWorkspace()->create(['password' => null, 'google_id' => 'g-1']);

    actingAs($social)->delete(route('setting.authentication.providers.destroy', 'google'));

    expect($social->fresh()->google_id)->toBe('g-1');
});

it('disconnects a provider once a password exists', function () {
    $this->user->forceFill(['google_id' => 'g-1'])->save();

    actingAs($this->user)->delete(route('setting.authentication.providers.destroy', 'google'));

    expect($this->user->fresh()->google_id)->toBeNull();
});

it('lists only this user connections, not a teammate one', function () {
    apiTokenFor($this->user);

    $teammate = User::factory()->create();
    $teammate->workspaces()->attach($this->user->current_workspace_id, ['role' => Role::ROLE_USER->value]);
    $teammate->forceFill(['current_workspace_id' => $this->user->current_workspace_id])->save();
    apiTokenFor($teammate);

    // Present them both as MCP grants on the shared workspace.
    AccessToken::query()->update(['scopes' => json_encode(['mcp:use'])]);

    $clients = ListConnectedMcpClients::execute($this->user, $this->user->currentWorkspace);

    expect($clients->pluck('user_id')->all())->not->toContain($teammate->id);
});

it('will not let one member revoke another member oauth grant', function () {
    $teammate = User::factory()->withWorkspace()->create();

    // A real OAuth client, not a personal-access one: that distinction is what
    // separates a personal MCP grant from a workspace API key.
    $client = Passport::client()->forceFill([
        'id' => Str::uuid()->toString(),
        'name' => 'Some MCP client',
        'redirect_uris' => ['https://example.com/callback'],
        'grant_types' => ['authorization_code', 'refresh_token'],
        'revoked' => false,
    ]);
    $client->save();

    $theirToken = AccessToken::query()->create([
        'id' => Str::random(80),
        'user_id' => $teammate->id,
        'client_id' => $client->id,
        'workspace_id' => $this->user->current_workspace_id,
        'scopes' => ['mcp:use'],
        'revoked' => false,
    ]);

    $revoked = RevokeAccessToken::execute(
        $this->user,
        $this->user->currentWorkspace,
        $theirToken->id,
    );

    expect($revoked)->toBeFalse()
        ->and($theirToken->fresh()->revoked)->toBeFalse();
});

it('signs out the other sessions but keeps this one', function () {
    config(['session.driver' => 'database']);

    $keep = 'this-session';
    $other = 'other-session';

    foreach ([$keep, $other] as $id) {
        DB::table('sessions')->insert([
            'id' => $id,
            'user_id' => $this->user->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test',
            'payload' => '',
            'last_activity' => now()->timestamp,
        ]);
    }

    actingAs($this->user)
        ->withSession(['_token' => 'x'])
        ->delete(route('setting.authentication.sessions.destroy'), [
            'password' => 'password',
        ])
        ->assertSessionHasNoErrors();

    expect(DB::table('sessions')->where('id', $other)->exists())->toBeFalse();
});

it('refuses to sign out the other sessions with a wrong password', function () {
    config(['session.driver' => 'database']);

    DB::table('sessions')->insert([
        'id' => 'other-session',
        'user_id' => $this->user->id,
        'ip_address' => '127.0.0.1',
        'user_agent' => 'Test',
        'payload' => '',
        'last_activity' => now()->timestamp,
    ]);

    actingAs($this->user)
        ->delete(route('setting.authentication.sessions.destroy'), [
            'password' => 'wrong',
        ])
        ->assertSessionHasErrors('password');

    expect(DB::table('sessions')->where('id', 'other-session')->exists())->toBeTrue();
});

it('revokes a personal access token that has no refresh token', function () {
    apiTokenFor($this->user, 'Deploy key');

    $token = AccessToken::query()
        ->where('workspace_id', $this->user->current_workspace_id)
        ->firstOrFail();

    // The happy path had never been exercised: every existing test returned
    // before reaching the revoke itself, so a call to the relation instead of
    // its result went out as a 500.
    $revoked = RevokeAccessToken::execute(
        $this->user,
        $this->user->currentWorkspace,
        $token->id,
    );

    expect($revoked)->toBeTrue()
        ->and($token->fresh()->revoked)->toBeTrue();
});

it('revokes an oauth grant together with its refresh token', function () {
    $client = Passport::client()->forceFill([
        'id' => Str::uuid()->toString(),
        'name' => 'Some MCP client',
        'redirect_uris' => ['https://example.com/callback'],
        'grant_types' => ['authorization_code', 'refresh_token'],
        'revoked' => false,
    ]);
    $client->save();

    $token = AccessToken::query()->create([
        'id' => Str::random(80),
        'user_id' => $this->user->id,
        'client_id' => $client->id,
        'workspace_id' => $this->user->current_workspace_id,
        'scopes' => ['mcp:use'],
        'revoked' => false,
    ]);

    $refresh = Passport::refreshToken()->forceFill([
        'id' => Str::random(80),
        'access_token_id' => $token->id,
        'revoked' => false,
    ]);
    $refresh->save();

    // A live refresh token would mint a new access token, so revoking one
    // without the other leaves the connection working.
    expect(RevokeAccessToken::execute($this->user, $this->user->currentWorkspace, $token->id))
        ->toBeTrue()
        ->and($token->fresh()->revoked)->toBeTrue()
        ->and($refresh->fresh()->revoked)->toBeTrue();
});

it('deletes an api token through the settings screen', function () {
    apiTokenFor($this->user, 'Deploy key');

    $token = AccessToken::query()
        ->where('workspace_id', $this->user->current_workspace_id)
        ->firstOrFail();

    $this->actingAs($this->user)
        ->delete(route('setting.api-tokens.destroy', $token->id))
        ->assertRedirect();

    expect($token->fresh()->revoked)->toBeTrue();
});

it('lists the sessions this account has open', function () {
    config(['session.driver' => 'database']);

    DB::table('sessions')->insert([
        'id' => 'other-session',
        'user_id' => $this->user->id,
        'ip_address' => '203.0.113.7',
        'user_agent' => 'Firefox on Linux',
        'payload' => '',
        'last_activity' => now()->subMinutes(5)->timestamp,
    ]);

    $this->actingAs($this->user)
        ->get(route('setting.authentication.edit'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('sessions', 1)
            ->where('sessions.0.ip_address', '203.0.113.7')
            ->where('sessions.0.user_agent', 'Firefox on Linux')
            ->where('sessions.0.is_current', false)
            ->has('sessions.0.last_active')
            ->etc()
        );
});

it('lists no sessions when they are not kept in the database', function () {
    // Nothing to list from an array or cookie driver, and the screen says so
    // rather than guessing.
    config(['session.driver' => 'array']);

    $this->actingAs($this->user)
        ->get(route('setting.authentication.edit'))
        ->assertInertia(fn (Assert $page) => $page->has('sessions', 0)->etc());
});

it('reaches the workspace and the person a token was issued to', function () {
    apiTokenFor($this->user, 'Deploy key');

    $token = AccessToken::query()
        ->where('workspace_id', $this->user->current_workspace_id)
        ->firstOrFail();

    expect($token->workspace->is($this->user->currentWorkspace))->toBeTrue()
        ->and($token->user->is($this->user))->toBeTrue();
});

it('counts an expired mcp grant as no longer active', function () {
    $client = Passport::client()->forceFill([
        'id' => Str::uuid()->toString(),
        'name' => 'Some MCP client',
        'redirect_uris' => ['https://example.com/callback'],
        'grant_types' => ['authorization_code', 'refresh_token'],
        'revoked' => false,
    ]);
    $client->save();

    $make = fn (?string $expires) => AccessToken::query()->create([
        'id' => Str::random(80),
        'user_id' => $this->user->id,
        'client_id' => $client->id,
        'workspace_id' => $this->user->current_workspace_id,
        'scopes' => ['mcp:use'],
        'revoked' => false,
        'expires_at' => $expires,
    ]);

    expect($make(null)->isActiveMcpGrant())->toBeTrue()
        ->and($make(now()->addDay()->toDateTimeString())->isActiveMcpGrant())->toBeTrue()
        ->and($make(now()->subDay()->toDateTimeString())->isActiveMcpGrant())->toBeFalse();
});

it('refuses a password change without the current one', function () {
    $this->actingAs($this->user)
        ->put(route('setting.authentication.password'), [
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])
        ->assertSessionHasErrors('current_password');
});

it('refuses a password change when the current one is wrong', function () {
    $this->actingAs($this->user)
        ->put(route('setting.authentication.password'), [
            'current_password' => 'not-my-password',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])
        ->assertSessionHasErrors('current_password');
});
