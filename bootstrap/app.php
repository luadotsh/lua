<?php

declare(strict_types=1);

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Sentry\Laravel\Integration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Inertia\Inertia;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        apiPrefix: '/api',
        commands: __DIR__.'/../routes/console.php',
        then: function () {
            // MCP server + its OAuth endpoints.
            Route::middleware('web')->group(base_path('routes/ai.php'));
        },
        channels: __DIR__.'/../routes/channels.php',
        health: 'up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');

        // The api group carries no rate limit of its own in Laravel 11+.
        $middleware->throttleApi();
        $middleware->preventRequestForgery(except: [
            'stripe/*'
        ]);

        $middleware->redirectGuestsTo('/login');
        $middleware->redirectUsersTo('/links');


        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->alias([
            'billing' => \App\Http\Middleware\Billing::class,
            'set-workspace' => \App\Http\Middleware\SetWorkspace::class,
            'custom-domain' => \App\Http\Middleware\CustomDomain::class,
            'workspace.token' => \App\Http\Middleware\Api\LoadWorkspaceFromToken::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        Integration::handles($exceptions);

        // Handle 500 Internal Server Error, 503 Service Unavailable, etc.
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if (in_array($e->getStatusCode(), [500, 503, 403, 404])) {
                return Inertia::render('Error', ['status' => $e->getStatusCode()])
                    ->toResponse($request)
                    ->setStatusCode($e->getStatusCode());
            }
        });
    })->create();
