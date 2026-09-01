<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

/**
 * A route whose controller method does not exist registers fine, boots fine and
 * only fails when someone requests it — as a 500, in production, with nothing
 * upstream to catch it.
 *
 * This happened: `setting.billing.swap-free-plan` pointed at
 * BillingController::swapFreePlan for months after the method was deleted, and
 * nothing noticed because no screen linked to it.
 */
it('points every route at a controller action that exists', function (): void {
    $missing = [];

    foreach (Route::getRoutes() as $route) {
        $action = $route->getAction('uses');

        // Closures and view/redirect routes have nothing to resolve.
        if (! is_string($action) || ! str_contains($action, '@')) {
            continue;
        }

        [$class, $method] = explode('@', $action, 2);

        // Vendor packages own their own routes; only assert on ours.
        if (! str_starts_with($class, 'App\\')) {
            continue;
        }

        if (! class_exists($class)) {
            $missing[] = "{$route->uri()} → {$class} (class not found)";

            continue;
        }

        if (! method_exists($class, $method)) {
            $missing[] = "{$route->uri()} → {$class}::{$method}()";
        }
    }

    expect($missing)->toBe([]);
});
