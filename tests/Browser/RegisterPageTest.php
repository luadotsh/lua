<?php

declare(strict_types=1);

test('submitting an empty register form shows the backend validation errors', function () {
    $page = visit(route('register'));

    // No HTML5 `required` stands in the way, so the browser actually posts and
    // the server's messages are what the user sees.
    $page->click('@register-submit');

    $page->assertSee('The name field is required.')
        ->assertSee('The email field is required.')
        ->assertSee('The password field is required.')
        ->assertNoJavaScriptErrors();
});

test('the sign in link points at the login route', function () {
    $page = visit(route('register'));

    $page->click('@register-login-link')
        ->assertRoute('login')
        ->assertNoJavaScriptErrors();
});
