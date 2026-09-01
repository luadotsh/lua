<?php

declare(strict_types=1);

/**
 * The code samples are the one place on the site a developer will check
 * against reality, so the tab switching has to work and the endpoint has to be
 * one that exists.
 *
 * The assertions after a click use `assertSee`, which retries until the text
 * appears. `assertScript` evaluates once and immediately, so it races the
 * re-render the click triggers and fails intermittently — the same trap the
 * delete tests fell into.
 */
it('switches between language samples', function (): void {
    visit(route('site.home'))
        ->on()->desktop()
        ->assertSee('curl -X POST')
        // The page is server-rendered, so every tab is in the markup before Vue
        // has attached a single listener. A click inside that window hits inert
        // HTML and does nothing, and the assertion after it times out looking
        // for a change that was never triggered. The plugin's Webpage API
        // exposes only wait/waitForText/waitForEvent/waitForKey — no predicate
        // wait — so this is a margin for hydration, not a poll.
        ->wait(1)
        ->click('@api-tab-php')
        ->assertSee('Http::withToken')
        ->assertDontSee('curl -X POST')
        ->click('@api-tab-curl')
        ->assertSee('curl -X POST')
        ->assertNoJavaScriptErrors();
});

it('shows an endpoint the API actually serves', function (): void {
    $code = visit(route('site.home'))
        ->on()->desktop()
        ->script("function () { return document.querySelector('[data-testid=\"api-code\"]').textContent; }");

    expect($code)->toContain('/api/links');

    // And that route is registered, so the sample cannot drift from the API.
    expect(route('api.links.store', absolute: false))->toBe('/api/links');
});
