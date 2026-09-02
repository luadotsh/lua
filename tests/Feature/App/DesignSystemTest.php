<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

/**
 * The design system is documented in CLAUDE.md, and documentation drifts.
 * These guard the two claims about the app's own palette that stop being
 * true silently: that the accent is the colour the brand settled on, and
 * that the contrast a reader depends on still holds. Recovered from
 * tests/Feature/Site/DesignSystemTest.php (removed in f02dc54 along with the
 * marketing site) because these two assertions are about the signed-in
 * app's palette, not the site's.
 */
function css(): string
{
    return File::get(resource_path('css/app.css'));
}

function contrast(string $a, string $b): float
{
    $luminance = function (string $hex): float {
        $hex = ltrim($hex, '#');
        $channel = fn (float $c): float => $c <= 0.03928 ? $c / 12.92 : (($c + 0.055) / 1.055) ** 2.4;

        return 0.2126 * $channel(hexdec(substr($hex, 0, 2)) / 255)
            + 0.7152 * $channel(hexdec(substr($hex, 2, 2)) / 255)
            + 0.0722 * $channel(hexdec(substr($hex, 4, 2)) / 255);
    };

    $one = $luminance($a);
    $two = $luminance($b);

    return (max($one, $two) + 0.05) / (min($one, $two) + 0.05);
}

/**
 * The block for one top-level selector (`:root` or `.dark`), so a variable
 * can be read from the theme that actually declares it rather than assumed.
 */
function cssBlock(string $selector): string
{
    preg_match('/(?:^|\n)'.preg_quote($selector, '/').'\s*\{(.*?)\n\}/s', css(), $matches);

    return $matches[1] ?? '';
}

function cssVariable(string $block, string $name): string
{
    preg_match('/--'.preg_quote($name, '/').':\s*(#[0-9a-fA-F]{3,8});/', $block, $matches);

    return $matches[1] ?? '';
}

it('uses the accent the brand settled on', function (): void {
    expect(css())->toContain('--primary: #fa5d19;');
});

it('keeps a theme readable on its own primary', function (string $selector): void {
    $block = cssBlock($selector);
    $primary = cssVariable($block, 'primary');
    $foreground = cssVariable($block, 'primary-foreground');

    // Read from the CSS rather than hardcoded, so a change to either token
    // that fails AA fails this test instead of passing silently.
    expect($primary)->not->toBeEmpty()
        ->and($foreground)->not->toBeEmpty()
        // AA for normal-size text. A brand colour that fails this is a brand
        // colour no button can use.
        ->and(contrast($primary, $foreground))->toBeGreaterThanOrEqual(4.5);
})->with([
    'light' => [':root'],
    'dark' => ['.dark'],
]);
