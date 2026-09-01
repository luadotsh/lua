<?php

declare(strict_types=1);

/**
 * SSR runs these modules in Node, where there is no window and where a module
 * is evaluated once and shared by every request it serves. Break either and
 * the render throws, Inertia falls back to client rendering without a word,
 * and the only trace is a line in the SSR process log.
 *
 * Both rules come from one production failure in useCurrentUrl.
 *
 * Datasets are built before the application boots, so this walks the paths
 * directly rather than through resource_path().
 */
dataset('shared modules', function () {
    $root = dirname(__DIR__, 2).'/resources/js';

    foreach (['composables', 'lib'] as $directory) {
        foreach (glob("{$root}/{$directory}/*.ts") ?: [] as $path) {
            yield basename($path) => [(string) file_get_contents($path)];
        }
    }
});

// `window?.x` reads as a guard and is not one: optional chaining protects a
// declared binding holding null, while an undeclared window throws
// ReferenceError before the `?.` is ever reached. Use typeof instead.
it('guards browser globals with typeof', function (string $code): void {
    expect($code)->not->toMatch('/\b(window|document|localStorage)\s*\?\./');
})->with('shared modules');

// A page captured at module scope belongs to whichever request loaded the
// module first, and anything computed from it caches that request's URL for
// all the others. Inside a component's setup this is fine.
it('calls usePage inside the composable', function (string $code): void {
    expect($code)->not->toMatch('/^(?:const|let|var)\s+\w+\s*=\s*usePage\(\)/m');
})->with('shared modules');
