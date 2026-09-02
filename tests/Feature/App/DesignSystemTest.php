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

it('uses the accent the brand settled on', function (): void {
    expect(css())->toContain('--primary: #fa5d19;');
});

it('keeps both themes readable on their own primary', function (string $primary, string $foreground): void {
    // AA for normal-size text. A brand colour that fails this is a brand
    // colour no button can use.
    expect(contrast($primary, $foreground))->toBeGreaterThanOrEqual(4.5);
})->with([
    'light' => ['#fa5d19', '#1a0c04'],
    'dark' => ['#fa5d19', '#1a0c04'],
]);
