<?php

declare(strict_types=1);

use App\Models\Link;
use App\Models\LinkStat;
use App\Models\User;

/**
 * Names the nearest scrollable ancestor of the table body.
 *
 * Counting scrollable ancestors would not catch the bug this guards: before
 * the fix there was exactly one too — the app shell, which also holds the
 * metrics and the chart, so reaching the last column dragged them sideways.
 * What matters is *which* element scrolls.
 */
function nearestScrollerTestId(string $itemsElement): string
{
    return <<<JS
    function () {
        let el = document.querySelector('{$itemsElement}');

        while (el && el !== document.documentElement) {
            const style = getComputedStyle(el);
            const scrollsX = el.scrollWidth > el.clientWidth && /auto|scroll/.test(style.overflowX);
            const scrollsY = el.scrollHeight > el.clientHeight && /auto|scroll/.test(style.overflowY);

            if (scrollsX || scrollsY) {
                return el.dataset.testid ?? 'unnamed';
            }

            el = el.parentElement;
        }

        return 'none';
    }
    JS;
}

test('the events table scrolls inside itself, not inside the app shell', function () {
    $user = User::factory()->withWorkspace()->create();
    $workspace = $user->currentWorkspace;
    $link = Link::factory()->create(['workspace_id' => $workspace->id]);

    // Enough rows for the table to overflow its container vertically.
    LinkStat::factory()->count(40)->create([
        'workspace_id' => $workspace->id,
        'link_id' => $link->id,
    ]);

    $this->actingAs($user);

    $page = visit(route('events.index'));

    $page->assertScript(nearestScrollerTestId('#events-body'), 'events-scroll')
        // The page itself never scrolls sideways: that is the table's job.
        ->assertScript('document.documentElement.scrollWidth <= document.documentElement.clientWidth')
        ->assertNoJavaScriptErrors();
});

test('the links table scrolls inside itself, not inside the app shell', function () {
    $user = User::factory()->withWorkspace()->create();

    Link::factory()->count(40)->create([
        'workspace_id' => $user->currentWorkspace->id,
    ]);

    $this->actingAs($user);

    $page = visit(route('links.index'));

    $page->assertScript(nearestScrollerTestId('#links-body'), 'links-scroll')
        ->assertScript('document.documentElement.scrollWidth <= document.documentElement.clientWidth')
        ->assertNoJavaScriptErrors();
});
