<?php

declare(strict_types=1);

use App\Models\User;

/**
 * With nothing uploaded the sidebar draws initials locally. It used to point an
 * <img> at api.dicebear.com with the person's real name in the query string, so
 * every page view handed a third party the name, from every viewer's browser.
 */
test('the sidebar draws initials rather than fetching an avatar', function () {
    $user = User::factory()->withWorkspace()->create(['name' => 'Ada Lovelace']);
    $user->currentWorkspace->update(['name' => 'Bletchley Park']);

    $this->actingAs($user);

    $page = visit(route('links.index'));

    // No request leaves for an avatar service, and none of the rendered markup
    // names one.
    $page->assertScript(
        'Array.from(document.images).filter(i => /dicebear|gravatar|ui-avatars/.test(i.src)).length',
        0,
    )->assertSourceMissing('dicebear');

    // The workspace tile carries the workspace's own initials. The old fallback
    // seeded dicebear with "url" glued before the name, so it never did.
    $page->assertScript(
        "document.querySelector('[data-slot=\"avatar\"]').textContent.trim()",
        'BP',
    )->assertNoJavaScriptErrors();
});

test('a name with one word gets a single initial', function () {
    $user = User::factory()->withWorkspace()->create(['name' => 'Madonna']);
    $user->currentWorkspace->update(['name' => 'Madonna']);

    $this->actingAs($user);

    visit(route('links.index'))
        ->assertScript(
            "document.querySelector('[data-slot=\"avatar\"]').textContent.trim()",
            'M',
        )
        ->assertNoJavaScriptErrors();
});
