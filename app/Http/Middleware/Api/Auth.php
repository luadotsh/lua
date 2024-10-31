<?php

declare(strict_types=1);

namespace App\Http\Middleware\Api;

use Closure;
use Illuminate\Http\Request;

use App\Models\Workspace;
use App\Models\ApiToken;


class Auth
{
    /**
     * Handle an incoming request.
     *
     * @param  string|null  ...$guards
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // check if the token is present
        $token = $request->header('authorization') ? $request->bearerToken() : null;
        if(!$token) {
            return response()->json(['status' => 'error', 'message' => 'Token not provided'], 401);
        }

        // get the token
        $token = ApiToken::where('token', $token)->with('workspace')->first();
        if (!$token) {
            return response()->json(['status' => 'error', 'message' => 'Invalid token'], 401);
        }

        // update the last used time
        $token->update([
            'last_used_at' => now(),
        ]);

        $request->merge([
            'workspace' => $token->workspace,
        ]);

        return $next($request);
    }
}
