<?php

declare(strict_types=1);

/**
 * The answers are in the DOM whether or not a panel is open, so a crawler and
 * find-in-page reach all of them. What the click changes is only whether the
 * panel has height, which is the part worth asserting.
 */
it('opens an answer and closes it again', function (): void {
    visit(route('site.faq'))
        ->on()->desktop()
        ->assertScript("function () { return document.querySelector('[data-testid=\"faq-toggle-0\"]').getAttribute('aria-expanded'); }", 'false')
        ->click('@faq-toggle-0')
        ->assertScript("function () { return document.querySelector('[data-testid=\"faq-toggle-0\"]').getAttribute('aria-expanded'); }", 'true')
        ->click('@faq-toggle-0')
        ->assertScript("function () { return document.querySelector('[data-testid=\"faq-toggle-0\"]').getAttribute('aria-expanded'); }", 'false')
        ->assertNoJavaScriptErrors();
});

it('ships the answers as structured data', function (): void {
    $questions = visit(route('site.faq'))
        ->on()->desktop()
        ->script(<<<'JS'
        function () {
            const node = [...document.querySelectorAll('script[type="application/ld+json"]')]
                .map((el) => JSON.parse(el.textContent))
                .find((data) => data['@type'] === 'FAQPage');

            return node ? node.mainEntity.length : 0;
        }
        JS);

    expect($questions)->toBe(collect(config('faq'))->flatten(1)->count());
});
