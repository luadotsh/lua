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
    $client = Laravel\Passport\Passport::client()->forceFill([
        'id' => Illuminate\Support\Str::uuid()->toString(),
        'name' => 'Some MCP client',
        'redirect_uris' => ['https://example.com/callback'],
        'grant_types' => ['authorization_code', 'refresh_token'],
        'revoked' => false,
    ]);
    $client->save();

    $theirToken = AccessToken::query()->create([
        'id' => Illuminate\Support\Str::random(80),
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
