<?php

declare(strict_types=1);

use App\Models\Invite;
use App\Models\Link;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Password;

uses(RefreshDatabase::class);

/**
 * None of these forms carry HTML5 `required` any more, so an empty submit
 * actually reaches the server. These lock in that the server is the one
 * refusing it, and doing so by name.
 */
test('the forgot password form rejects an empty email', function () {
    $this->post(route('password.email'), ['email' => ''])
        ->assertSessionHasErrors('email');
});

test('the reset password form rejects empty fields', function () {
    $user = User::factory()->create();
    $token = Password::createToken($user);

    $this->post(route('password.store'), [
        'token' => $token,
        'email' => $user->email,
        'password' => '',
        'password_confirmation' => '',
    ])->assertSessionHasErrors('password');
});

test('the invitation form rejects empty fields', function () {
    $inviter = User::factory()->withWorkspace()->create();

    $invite = Invite::factory()->create([
        'workspace_id' => $inviter->currentWorkspace->id,
    ]);

    $this->post(route('auth.invites.accept', $invite->id), [
        'email' => '',
        'name' => '',
        'password' => '',
    ])->assertSessionHasErrors(['email', 'name', 'password']);
});

test('the workspace form rejects an empty name', function () {
    $user = User::factory()->withWorkspace()->create();

    $this->actingAs($user)
        ->post(route('workspaces.store'), ['name' => ''])
        ->assertSessionHasErrors('name');
});

test('an empty password does not unlock a password protected link', function () {
    $workspace = User::factory()->withWorkspace()->create()->currentWorkspace;

    $link = Link::factory()->create([
        'workspace_id' => $workspace->id,
        'domain' => 'lua.test',
        'password' => 'let-me-in',
    ]);

    // No FormRequest here on purpose: the comparison is the validation, and an
    // empty guess is simply a wrong one.
    $this->post(route('links.password.validate', $link->key), ['password' => ''])
        ->assertSessionHasErrors('password');
});
