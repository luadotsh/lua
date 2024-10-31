<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;

class Billing
{
    public function handle(Request $request, Closure $next)
    {
        Inertia::share('usage', auth()->user()->currentWorkspace->usage());
        return $next($request);
    }
}
