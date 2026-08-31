<?php

declare(strict_types=1);

use App\Http\Middleware\Api\LoadWorkspaceFromToken;
use App\Http\Middleware\Billing;
use App\Http\Middleware\CustomDomain;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\SetWorkspace;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
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
            'stripe/*',
        ]);

        $middleware->redirectGuestsTo('/login');
        $middleware->redirectUsersTo('/links');

        $middleware->web(append: [
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->alias([
            'billing' => Billing::class,
            'set-workspace' => SetWorkspace::class,
            'custom-domain' => CustomDomain::class,
            'workspace.token' => LoadWorkspaceFromToken::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Handle 500 Internal Server Error, 503 Service Unavailable, etc.
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if (in_array($e->getStatusCode(), [500, 503, 403, 404])) {
                return Inertia::render('Error', ['status' => $e->getStatusCode()])
                    ->toResponse($request)
                    ->setStatusCode($e->getStatusCode());
            }
        });
    })->create();
