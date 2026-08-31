<?php

declare(strict_types=1);

/**
 * The marketing pages are the only ones a stranger sees, and the only ones
 * rendered on the server first. A hydration mismatch surfaces here as a
 * console error and nowhere else, which is what makes these worth running.
 */
it('renders every marketing page without a javascript error', function (string $route, string $text): void {
    visit(route($route))
        ->assertSee($text)
        ->assertNoJavaScriptErrors();
})->with([
    ['site.home', 'Short links, and the story of every click'],
    ['site.pricing', 'Every plan sees every click'],
    ['site.terms', 'Terms of Service'],
    ['site.privacy', 'About IP addresses'],
    ['site.alternatives.index', 'How Lua compares'],
]);

it('renders a comparison page from its config entry', function (): void {
    visit(route('site.alternatives.show', 'bitly'))
        ->assertSee('Bitly')
        ->assertSee('Feature by feature')
        ->assertSee('Stay on Bitly if you')
        ->assertNoJavaScriptErrors();
});

it('walks from the home page to a comparison and back', function (): void {
    // The header nav is `hidden md:flex`, so a narrow default viewport has
    // nothing to click.
    visit(route('site.home'))
        ->on()->desktop()
        ->click('@site-nav-alternatives')
        ->assertSee('How Lua compares')
        ->click('@alternative-bitly')
        ->assertSee('Feature by feature')
        ->click('@back-to-alternatives')
        ->assertSee('How Lua compares')
        ->assertNoJavaScriptErrors();
});
