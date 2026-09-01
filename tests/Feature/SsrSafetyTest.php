<?php

declare(strict_types=1);

use Symfony\Component\Finder\Finder;

/**
 * SSR runs the same modules in Node, where `window` and `document` do not
 * exist. A module that reaches for them is not a client-only nuisance: it
 * throws while rendering, Inertia falls back to client rendering silently, and
 * the only trace is an entry in the SSR process log.
 *
 * Both rules here come from a real production failure in `useCurrentUrl`.
 */
function sharedFrontendFiles(): Finder
{
    return Finder::create()
        ->files()
        ->in([
            resource_path('js/composables'),
            resource_path('js/lib'),
        ])
        ->name('*.ts')
        ->notName('*.d.ts');
}

it('never guards a browser global with optional chaining', function (): void {
    $offenders = [];

    foreach (sharedFrontendFiles() as $file) {
        $body = (string) $file->getContents();

        // `window?.x` reads as a guard and is not one: optional chaining only
        // protects a declared binding holding null/undefined. An undeclared
        // `window` throws ReferenceError before the `?.` is ever reached, so
        // this fails on the server exactly where it looks safest.
        if (preg_match('/\b(window|document|localStorage)\s*\?\./', $body)) {
            $offenders[] = $file->getRelativePathname();
        }
    }

    expect($offenders)->toBe([], 'Use `typeof window === \'undefined\'` instead of optional chaining.');
});

it('never captures usePage at module scope in a shared module', function (): void {
    $offenders = [];

    foreach (sharedFrontendFiles() as $file) {
        $body = (string) $file->getContents();

        // A module is evaluated once in the SSR process and shared by every
        // request it serves. A page object captured at module scope therefore
        // belongs to whichever request loaded the module first, and anything
        // computed from it caches that request's URL for all the others.
        // Inside a component's setup this is fine — hence composables only.
        if (preg_match('/^(?:const|let|var)\s+\w+\s*=\s*usePage\(\)/m', $body)) {
            $offenders[] = $file->getRelativePathname();
        }
    }

    expect($offenders)->toBe([], 'Call usePage() inside the composable, not at module scope.');
});
