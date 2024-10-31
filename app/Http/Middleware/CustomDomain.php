<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Inertia\Inertia;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Enums\Domain\Status;
use App\Models\Domain;

class CustomDomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If the 'key' parameter is present in the request, proceed with the request.
        if($request->key) {
            return $next($request);
        }

        // Get the host from the request.
        $host = $request->getHost();

        // If the domain is provided by lua, we redirect to the website.
        if(in_array($host, config('domains.available'))) {
            return Inertia::location(config('app.website'));
        }

        // Check if the domain exists in the database and is active.
        $domain = Domain::where('domain', $host)
            ->where('status', Status::ACTIVE)
            ->first();

        // If the domain is not found, we redirect to the website.
        if(!$domain) {
            return $next(config('app.website'));
        }

        // if domain provides a not found url, we redirect to that url.
        if($domain->not_found_url) {
            return Inertia::location($domain->not_found_url);
        }

        // default route
        return Inertia::location(config('app.website'));
    }
}
