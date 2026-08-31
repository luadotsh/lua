<?php

declare(strict_types=1);

use App\Models\Domain;
use App\Models\Tag;
use App\Models\User;

/**
 * The four settings lists were each a hand-rolled stack of divs. They now use
 * the same table the links and events screens do, which is what these pin: the
 * table exists, its header is the sticky one, and the list owns its scrolling.
 */
function rendersSystemTable(object $page, string $scrollTestId): void
{
    $page->assertPresent('table[data-slot="table"]')
        ->assertScript(
            "getComputedStyle(document.querySelector('[data-slot=\"table-header\"] th')).position",
            'sticky',
        )
        ->assertScript(
            "getComputedStyle(document.querySelector('[data-testid=\"{$scrollTestId}\"]')).overflowY",
            'auto',
        )
        ->assertNoJavaScriptErrors();
}

test('the tags screen uses the system table', function () {
    $user = User::factory()->withWorkspace()->create();

    // A workspace is created with three tags, so there is always a row.
    $this->actingAs($user);

    $page = visit(route('setting.tags.index'));

    rendersSystemTable($page, 'tags-scroll');
    $page->assertSee('Marketing');
});

test('the tags screen no longer offers a hand-set order', function () {
    $user = User::factory()->withWorkspace()->create();

    Tag::factory()->create([
        'workspace_id' => $user->currentWorkspace->id,
        'name' => 'Aardvark',
    ]);

    $this->actingAs($user);

    $page = visit(route('setting.tags.index'));

    // Alphabetical, and nothing to drag.
    $page->assertScript(
        "document.querySelector('#tags-body tr td').textContent.trim()",
        'Aardvark',
    )->assertNoJavaScriptErrors();
});

test('the domains screen uses the system table', function () {
    $user = User::factory()->withWorkspace()->create();

    Domain::factory()->create([
        'workspace_id' => $user->currentWorkspace->id,
        'domain' => 'links.example.com',
    ]);

    $this->actingAs($user);

    $page = visit(route('setting.domains.index'));

    rendersSystemTable($page, 'domains-scroll');
    $page->assertSee('links.example.com');
});

test('the api tokens screen uses the system table', function () {
    $user = User::factory()->withWorkspace()->create();

    // Tokens are Passport personal access tokens, issued the way the settings
    // screen issues them.
    apiTokenFor($user, 'Deploy key');

    $this->actingAs($user);

    $page = visit(route('setting.api-tokens.index'));

    rendersSystemTable($page, 'api-tokens-scroll');
    $page->assertSee('Deploy key');
});

test('the members screen uses the system table', function () {
    $user = User::factory()->withWorkspace()->create();

    $this->actingAs($user);

    $page = visit(route('setting.team-members.index'));

    rendersSystemTable($page, 'members-scroll');
    $page->assertSee($user->email);
});

test('the api token dialog picks an expiry with the app date picker', function () {
    $user = User::factory()->withWorkspace()->create();

    $this->actingAs($user);

    $page = visit(route('setting.api-tokens.index'));

    $page->click('@new-api-token');

    // The native <input type="date"> it replaced rendered the browser's own
    // control, which looked nothing like the one the link form uses.
    $page->assertPresent('[data-testid="date-time-picker"]')
        ->assertScript(
            "document.querySelectorAll('input[type=\"date\"]').length",
            0,
        )
        ->assertNoJavaScriptErrors();
});
