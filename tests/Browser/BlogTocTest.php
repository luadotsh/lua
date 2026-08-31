<?php

declare(strict_types=1);
use App\Actions\Blog\ListPosts;

/**
 * The contents list is the one genuinely interactive thing on the marketing
 * site, and its failure modes are invisible to a feature test: an anchor with
 * no matching heading, a click that navigates away instead of moving down the
 * page, and a landing spot hidden under the sticky header.
 *
 * Note the shape of every test here. `visit()` returns a pending page and each
 * call made on it materialises one, so a chain broken into separate statements
 * silently reloads between steps and measures a fresh page every time.
 * Everything an assertion depends on has to stay in one chain.
 */
const TOC_POST = 'what-a-short-link-actually-records';

/**
 * The id of the last section, read from the same Action the page renders
 * from. Hardcoding a slug here made the test brittle in a way that taught
 * nothing: rewording a heading in the article broke the test without anything
 * being wrong. The last section is also the one furthest down the page, which
 * is what makes the scroll assertion meaningful.
 */
function lastSectionId(): string
{
    $headings = ListPosts::find(TOC_POST)['headings'];

    return (string) end($headings)['id'];
}

/**
 * A headless browser is a backgrounded one, and Chrome does not run a smooth
 * scroll animation in a backgrounded tab — the position simply never changes.
 * Switching to the instant behaviour measures the same jump: under test is the
 * anchor and where it lands, not the easing. It is also the exact path a
 * reader with `prefers-reduced-motion` gets.
 */
function instantScrolling(): string
{
    return <<<'JS'
    function () {
        document.documentElement.style.scrollBehavior = 'auto';

        return true;
    }
    JS;
}

/**
 * True while the heading is still well below the fold — the state the click is
 * supposed to change, asserted first so a test that never scrolls cannot pass
 * by starting where it wanted to end up.
 */
function headingIsOffScreen(string $id): string
{
    return <<<JS
    function () {
        return document.getElementById('{$id}').getBoundingClientRect().top > 500;
    }
    JS;
}

/**
 * True once the heading has come to rest in view and clear of the 4rem sticky
 * header. `scroll-mt` is what buys that clearance; without it the heading
 * lands at 0 and sits behind the header.
 */
function headingRestsBelowTheHeader(string $id): string
{
    return <<<JS
    function () {
        const top = document.getElementById('{$id}').getBoundingClientRect().top;

        return top >= 64 && top < 160;
    }
    JS;
}

it('scrolls to a section and lands clear of the sticky header', function (): void {
    visit(route('site.blog.show', TOC_POST))
        ->on()->desktop()
        ->assertScript(instantScrolling())
        ->assertScript(headingIsOffScreen(lastSectionId()))
        ->click('@toc-'.lastSectionId())
        ->assertScript('window.location.hash', '#'.lastSectionId())
        ->assertScript(headingRestsBelowTheHeader(lastSectionId()))
        ->assertNoJavaScriptErrors();
});

it('leaves the reader on the article, not on another page', function (): void {
    // Regression: the contents list used to script the scroll and write the
    // hash with `history.replaceState(null, ...)`, which erased the page
    // object Inertia keeps in `history.state` and sent the next interaction to
    // the wrong page entirely.
    visit(route('site.blog.show', TOC_POST))
        ->on()->desktop()
        ->click('@toc-'.lastSectionId())
        ->assertSee('What a short link actually records')
        ->click('@back-to-blog')
        ->assertSee('Notes from building a link shortener')
        ->assertNoJavaScriptErrors();
});

it('gives every heading in the contents list a matching anchor in the article', function (): void {
    $orphans = visit(route('site.blog.show', TOC_POST))
        ->on()->desktop()
        ->script(<<<'JS'
        function () {
            return [...document.querySelectorAll('[data-testid^="toc-"]')]
                .map((link) => link.getAttribute('href').slice(1))
                .filter((id) => document.getElementById(id) === null);
        }
        JS);

    expect($orphans)->toBe([]);
});
