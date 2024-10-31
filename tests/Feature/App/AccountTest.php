<?php

declare(strict_types=1);

use App\Models\User;

use function Pest\Laravel\actingAs;

use Illuminate\Foundation\Testing\RefreshDatabase;

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
            'theme' => $this->user->theme->value
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
            'theme' => $this->user->theme->value
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('setting.account.edit'));

    $this->user->refresh();

    expect($this->user->email_verified_at)->not->toBeNull();
});
