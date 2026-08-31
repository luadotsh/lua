<?php

declare(strict_types=1);

/**
 * The tools are the only pages on the site that do something rather than say
 * something, so they are the only ones where a feature test proves nothing.
 */
it('builds a tagged url as you type', function (): void {
    visit(route('site.tools.utm-builder'))
        ->on()->desktop()
        ->assertScript(
            "function () { return document.querySelector('[data-testid=\"utm-result\"]').textContent.includes('utm_source=newsletter'); }",
        )
        ->fill('@utm-utm_source', 'partner-blog')
        // assertSee waits for the text; assertScript evaluates once and races
        // the re-render the keystroke triggers. Asserting the typed value with
        // assertScript was an intermittent failure, not a product bug.
        ->assertSee('utm_source=partner-blog')
        ->assertNoJavaScriptErrors();
});

it('drops an empty parameter rather than tagging it blank', function (): void {
    // `?utm_term=` is worse than no utm_term: it reports an empty placement.
    visit(route('site.tools.utm-builder'))
        ->on()->desktop()
        ->assertScript(
            "function () { return document.querySelector('[data-testid=\"utm-result\"]').textContent.includes('utm_term'); }",
            false,
        )
        ->assertNoJavaScriptErrors();
});

it('renders a QR code in the browser', function (): void {
    visit(route('site.tools.qr-generator'))
        ->on()->desktop()
        ->wait(2)
        ->assertScript(
            "function () { const img = document.querySelector('[data-testid=\"qr-image\"]'); return img !== null && img.src.startsWith('data:image/png'); }",
        )
        ->assertNoJavaScriptErrors();
});

it('reports a refusal rather than failing silently', function (): void {
    visit(route('site.tools.link-checker'))
        ->on()->desktop()
        ->fill('@check-url', 'http://127.0.0.1/admin')
        ->click('@check-submit')
        ->wait(2)
        ->assertScript(
            "function () { return document.querySelector('[data-testid=\"check-hops\"]') !== null; }",
        )
        ->assertNoJavaScriptErrors();
});
