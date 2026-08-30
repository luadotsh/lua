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
