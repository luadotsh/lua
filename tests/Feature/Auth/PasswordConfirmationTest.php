<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('confirm password screen can be rendered', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('password.confirm'));

    $response->assertStatus(200);
});

test('password can be confirmed', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('password.confirm.store'), [
        'password' => 'password',
    ]);

    $response->assertRedirect();
    $response->assertSessionHasNoErrors();
});

test('password is not confirmed with invalid password', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('password.confirm.store'), [
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrors();
});

test('an empty password is rejected as missing rather than as wrong', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('password.confirm.store'), [
        'password' => '',
    ]);

    // Without the FormRequest this fell through to the credential check and
    // came back as "these credentials do not match", which is not what went
    // wrong. The inputs carry no HTML5 `required`, so this is reachable.
    $response->assertSessionHasErrors('password');
    expect(session('errors')->first('password'))
        ->toBe('The password field is required.');
});
