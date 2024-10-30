<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomDomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // dd($request->url());

        // if($request->getHost() === config('domains.main')) {

        /**
         * When key is not present in the request, we will redirect to root domain.
         */
        if(!$request->key) {

        }
        return $next($request);
    }
}
