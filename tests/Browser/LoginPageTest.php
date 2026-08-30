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
