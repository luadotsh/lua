<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SetWorkspace
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()->workspaces->count() == 0 && !request()->routeIs('workspaces.*')) {
            return redirect(route('workspaces.create'));
        }
        return $next($request);
    }
}
