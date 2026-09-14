<?php

declare(strict_types=1);

/**
 * Reverb speaks the Pusher protocol. `@laravel/echo-vue` imports `pusher-js`
 * for that transport; without the package in package.json the production
 * Echo chunk throws `Could not resolve "pusher-js"` in the browser.
 */
it('declares pusher-js so the echo client can resolve the reverb transport', function () {
    $root = dirname(__DIR__, 2);
    $package = json_decode((string) file_get_contents("{$root}/package.json"), true, 512, JSON_THROW_ON_ERROR);
    $lock = json_decode((string) file_get_contents("{$root}/package-lock.json"), true, 512, JSON_THROW_ON_ERROR);

    expect($package['devDependencies']['pusher-js'] ?? null)->toBeString()
        ->and($lock['packages']['node_modules/pusher-js'] ?? null)->toBeArray();
});
