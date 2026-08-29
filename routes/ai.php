<?php

declare(strict_types=1);

use App\Mcp\Servers\LuaServer;
use Illuminate\Support\Facades\Route;
use Laravel\Mcp\Facades\Mcp;

// Dynamic client registration is unauthenticated by design, so it is throttled.
Route::middleware('throttle:mcp-oauth-registration')->group(function () {
    Mcp::oauthRoutes();
});

Mcp::web('/mcp', LuaServer::class)
    ->middleware(['auth:api', 'workspace.token:mcp'])
    ->name('mcp');
