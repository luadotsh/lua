<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

/**
 * The design system is documented in CLAUDE.md, and documentation drifts.
 * These guard the two claims that stop being true silently: that the accent is
 * the logo's own colour, and that the contrast a reader depends on still holds.
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

/**
 * CIE76 in Lab. Cruder than CIEDE2000 but monotonic with it at this
 * magnitude, and it needs no dependency to stay honest.
 */
function labDistance(string $a, string $b): float
{
    $lab = function (string $hex): array {
        $hex = ltrim($hex, '#');
        $srgb = array_map(
            fn (float $c): float => $c <= 0.04045 ? $c / 12.92 : (($c + 0.055) / 1.055) ** 2.4,
            [
                hexdec(substr($hex, 0, 2)) / 255,
                hexdec(substr($hex, 2, 2)) / 255,
                hexdec(substr($hex, 4, 2)) / 255,
            ],
        );

        [$r, $g, $bl] = $srgb;
        $x = ($r * 0.4124 + $g * 0.3576 + $bl * 0.1805) / 0.95047;
        $y = $r * 0.2126 + $g * 0.7152 + $bl * 0.0722;
        $z = ($r * 0.0193 + $g * 0.1192 + $bl * 0.9505) / 1.08883;

        $f = fn (float $t): float => $t > 0.008856 ? $t ** (1 / 3) : (7.787 * $t + 16 / 116);

        return [116 * $f($y) - 16, 500 * ($f($x) - $f($y)), 200 * ($f($y) - $f($z))];
    };

    [$l1, $a1, $b1] = $lab($a);
    [$l2, $a2, $b2] = $lab($b);

    return sqrt(($l1 - $l2) ** 2 + ($a1 - $a2) ** 2 + ($b1 - $b2) ** 2);
}

// The app confirms deletions in four places, so "Save" and "Delete" must not
// be the same colour at a glance. This is measured rather than judged: the
// vermilion that looked best in review sits at a CIEDE2000 of 3.8 from the
// destructive red, which is the distance at which two colours are the same
// colour to anyone not comparing them side by side.
it('keeps the accent perceptually apart from the destructive red', function (): void {
    expect(labDistance('#dd4bb8', '#dc2626'))->toBeGreaterThan(25.0);
});

it('rejects the accent that was rejected, so nobody re-proposes it', function (): void {
    // Kept as a live check rather than a comment: #d83a22 reads as the obvious
    // brand colour and is the one value that cannot ship here.
    expect(labDistance('#d83a22', '#dc2626'))->toBeLessThan(15.0);
});

it('keeps both themes readable on their own primary', function (string $primary, string $foreground): void {
    // AA for normal-size text. A brand colour that fails this is a brand
    // colour no button can use.
    expect(contrast($primary, $foreground))->toBeGreaterThanOrEqual(4.5);
})->with([
    'light' => ['#fa5d19', '#1a0c04'],
    'dark' => ['#fa5d19', '#1a0c04'],
]);

// The wash is pale, so as text it only reads on a dark ground. Every use of
// it has to sit inside the hero, which carries that ground in both themes.
/**
 * The bug this catches actually happened. The hero was built as an island of
 * hardcoded hexes rather than a token scope, so the header's "Start for free"
 * drifted from every other primary button on the site: same label, different
 * colour, two clicks apart. A colour written onto a component is a colour that
 * cannot follow the theme or the brand.
 */
it('writes no colour into a site component', function (): void {
    $offenders = [];

    foreach ([resource_path('js/pages/Site'), resource_path('js/components/site'), resource_path('js/layouts/site')] as $dir) {
        foreach (File::allFiles($dir) as $file) {
            $body = File::get($file->getPathname());

            // Arbitrary-value colour utilities and raw hex in class attributes.
            if (preg_match('/(?:bg|text|border|ring|fill|stroke)-\[#[0-9a-fA-F]{3,8}\]/', $body)) {
                $offenders[] = $file->getFilename();
            }
        }
    }

    expect(array_unique($offenders))->toBe([]);
});

// The site is light, always. A `dark:` variant in a site component is a second
// palette nobody is designing, and it would surface only for a reader whose
// app theme happened to be dark.
it('writes no dark variant into a site component', function (): void {
    $offenders = [];

    foreach ([resource_path('js/pages/Site'), resource_path('js/components/site'), resource_path('js/layouts/site')] as $dir) {
        foreach (File::allFiles($dir) as $file) {
            if (str_contains(File::get($file->getPathname()), 'dark:')) {
                $offenders[] = $file->getFilename();
            }
        }

    }

    expect(array_unique($offenders))->toBe([]);
});

it('defines the display face and keeps it out of the app', function (): void {
    expect(css())->toContain('--font-display');

    $appPages = File::allFiles(resource_path('js/pages'));

    $offenders = collect($appPages)
        ->reject(fn ($file): bool => str_contains($file->getPathname(), '/pages/Site/'))
        ->filter(fn ($file): bool => str_contains(File::get($file->getPathname()), 'font-display'))
        ->map(fn ($file): string => $file->getRelativePathname())
        ->values()
        ->all();

    // Inter carries the product screens. A display face in a data table costs
    // legibility and buys nothing.
    expect($offenders)->toBe([]);
});
