<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->withWorkspace()->create();
});

test('profile page is displayed', function () {
    actingAs($this->user)
        ->get(route('setting.account.edit'))
        ->assertOk();
});

test('profile information can be updated', function () {
    actingAs($this->user)
        ->from(route('setting.account.edit'))
        ->post(route('setting.account.update'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('setting.account.edit'));

    $this->user->refresh();

    expect($this->user->name)->toBe('Test User')
        ->and($this->user->email)->toBe('test@example.com')
        ->and($this->user->email_verified_at)->toBeNull();
});

test('email verification status is unchanged when email address is unchanged', function () {
    actingAs($this->user)
        ->from(route('setting.account.edit'))
        ->post(route('setting.account.update'), [
            'name' => 'Test User',
            'email' => $this->user->email,
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('setting.account.edit'));

    $this->user->refresh();

    expect($this->user->email_verified_at)->not->toBeNull();
});

it('does not change the password from the profile screen', function () {
    $user = User::factory()->withWorkspace()->create([
        'password' => Hash::make('original-password'),
    ]);

    // The profile form used to accept a password without confirming the
    // current one; it must ignore the field entirely now.
    Pest\Laravel\actingAs($user)->post(route('setting.account.update'), [
        'name' => $user->name,
        'email' => $user->email,
        'password' => 'hijacked-password',
        'password_confirmation' => 'hijacked-password',
    ]);

    expect(Hash::check('original-password', $user->fresh()->password))->toBeTrue();
});
