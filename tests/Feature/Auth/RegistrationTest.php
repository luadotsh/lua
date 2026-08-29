<?php

declare(strict_types=1);

use App\Enums\User\Role;
use App\Models\Invite;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertStatus(200);
});

test('new users can register', function () {
    $response = $this->post(route('register'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('links.index', absolute: false));
});

test('registering creates a personal workspace named after the user', function () {
    $this->post(route('register'), [
        'name' => 'Paulo Castellano',
        'email' => 'paulo@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $user = User::where('email', 'paulo@example.com')->firstOrFail();
    $workspace = $user->workspaces()->firstOrFail();

    expect($workspace->name)->toBe("Paulo's Workspace")
        ->and($workspace->membership->role)->toBe(Role::ROLE_OWNER->value)
        ->and($user->current_workspace_id)->toBe($workspace->id);
});

test('a user without a usable name falls back to a generic workspace name', function () {
    $this->post(route('register'), [
        'name' => '   ',
        'email' => 'blank@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    // A blank name fails validation, so nothing is created at all.
    expect(User::where('email', 'blank@example.com')->exists())->toBeFalse();

    // The fallback still has to hold for the paths that do not validate a
    // name the same way, e.g. an OAuth profile with no display name.
    expect(\App\Actions\Workspace\CreateWorkspace::defaultNameFor('   '))->toBe('My Workspace')
        ->and(\App\Actions\Workspace\CreateWorkspace::defaultNameFor('Lucas'))->toBe("Lucas's Workspace");
});

test('registration persists utm parameters and ad click ids captured on the landing url', function () {
    $this->get(route('register').'?'.http_build_query([
        'utm_source' => 'google',
        'utm_medium' => 'cpc',
        'utm_campaign' => 'launch',
        'gclid' => 'EAIaIQobChMI-opaque-token',
        'fbclid' => '',
    ]));

    $this->post(route('register'), [
        'name' => 'Attributed User',
        'email' => 'attributed@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $user = User::where('email', 'attributed@example.com')->firstOrFail();

    expect($user->utm_source)->toBe('google')
        ->and($user->utm_medium)->toBe('cpc')
        ->and($user->utm_campaign)->toBe('launch')
        ->and($user->gclid)->toBe('EAIaIQobChMI-opaque-token')
        // present but empty must not overwrite the column with ''
        ->and($user->fbclid)->toBeNull()
        ->and($user->utm_term)->toBeNull();
});

test('registering without attribution parameters leaves the columns null', function () {
    $this->get(route('register'));

    $this->post(route('register'), [
        'name' => 'Organic User',
        'email' => 'organic@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $user = User::where('email', 'organic@example.com')->firstOrFail();

    expect($user->utm_source)->toBeNull()
        ->and($user->gclid)->toBeNull();
});

test('an invited user joins the inviting workspace instead of getting a personal one', function () {
    $workspace = Workspace::factory()->create();

    $invite = Invite::factory()->create([
        'workspace_id' => $workspace->id,
        'email' => 'invited@example.com',
        'role' => Role::ROLE_USER->value,
    ]);

    $this->post(route('auth.invites.accept', $invite->id), [
        'name' => 'Invited User',
        'email' => 'invited@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $user = User::where('email', 'invited@example.com')->firstOrFail();

    expect($user->workspaces)->toHaveCount(1)
        ->and($user->workspaces->first()->id)->toBe($workspace->id)
        ->and($user->current_workspace_id)->toBe($workspace->id);
});
