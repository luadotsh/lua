<?php

declare(strict_types=1);

use App\Models\User;

test('the login page boots the app without javascript errors', function () {
    $page = visit(route('login'));

    $page->assertSee('Sign in')
        ->assertNoJavaScriptErrors();
});

test('an authenticated visit reaches the links list', function () {
    $this->actingAs(User::factory()->withWorkspace()->create());

    $page = visit(route('links.index'));

    $page->assertSee('Links')
        ->assertNoJavaScriptErrors();
});

test('submitting an empty login form shows the backend validation errors', function () {
    $page = visit(route('login'));

    // No HTML5 `required` stands in the way, so the browser actually posts and
    // the server's messages are what the user sees.
    $page->click('@login-submit');

    $page->assertSee('The email field is required.')
        ->assertSee('The password field is required.')
        ->assertNoJavaScriptErrors();
});

test('the sign up link points at the register route', function () {
    $page = visit(route('login'));

    $page->click('@login-register-link')
        ->assertRoute('register')
        ->assertNoJavaScriptErrors();
});

test('the login page shows no social buttons when no provider is configured', function () {
    config([
        'lua.auth.google' => false,
        'lua.auth.github' => false,
    ]);

    $page = visit(route('login'));

    $page->assertDontSee('Continue with')
        ->assertDontSee('Or continue with')
        ->assertNoJavaScriptErrors();
});

test('the login page shows a configured provider on its own', function () {
    config([
        'lua.auth.google' => true,
        'lua.auth.github' => false,
        'services.google.client_id' => 'id',
        'services.google.client_secret' => 'secret',
    ]);

    $page = visit(route('login'));

    $page->assertSee('Continue with Google')
        ->assertDontSee('Continue with GitHub')
        ->assertNoJavaScriptErrors();
});
