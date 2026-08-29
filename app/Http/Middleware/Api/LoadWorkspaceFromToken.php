<?php

declare(strict_types=1);

namespace App\Http\Middleware\Api;

use App\Models\AccessToken;
use App\Models\Workspace;
use Closure;
use Illuminate\Http\Request;
use Laravel\Passport\AccessToken as PassportAccessToken;
use Symfony\Component\HttpFoundation\Response;

class LoadWorkspaceFromToken
{
    public function handle(Request $request, Closure $next, ?string $context = null): Response
    {
        $user = $request->user();
        $authenticatedToken = $user?->token();

        if (! $authenticatedToken instanceof PassportAccessToken) {
            return response()->json(['message' => 'Token not found.'], Response::HTTP_UNAUTHORIZED);
        }

        $token = AccessToken::query()->find($authenticatedToken->oauth_access_token_id);

        if ($token === null) {
            return response()->json(['message' => 'Token not found.'], Response::HTTP_UNAUTHORIZED);
        }

        if ($token->expires_at?->isPast()) {
            return response()->json(['message' => 'Token expired.'], Response::HTTP_UNAUTHORIZED);
        }

        // A token is for the workspace the user had selected when it was
        // created. REST keys get that written by CreateApiKey; an MCP grant is
        // stamped the first time it is used, and from then on the binding
        // holds even if the user switches workspace in the browser.
        $workspaceId = $token->workspace_id ?: $user->current_workspace_id;

        $workspace = $workspaceId
            ? Workspace::query()->find($workspaceId)
            : null;

        if (! $workspace) {
            return response()->json(['message' => 'No workspace selected.'], Response::HTTP_UNAUTHORIZED);
        }

        if (! $user->belongsToWorkspace($workspace)) {
            return response()->json(['message' => 'Workspace access denied.'], Response::HTTP_FORBIDDEN);
        }

        if ($context === 'mcp') {
            if (! $token->isActiveMcpGrant() || ! $authenticatedToken->can('mcp:use')) {
                return response()->json(['message' => 'MCP OAuth authorization required.'], Response::HTTP_FORBIDDEN);
            }
        } elseif (! $token->isPersonalAccessToken()) {
            return response()->json(['message' => 'Personal access token required.'], Response::HTTP_FORBIDDEN);
        }

        $user->setRelation('currentWorkspace', $workspace);
        $user->current_workspace_id = $workspace->id;

        $request->merge(['workspace' => $workspace]);

        $token->forceFill([
            'workspace_id' => $workspace->id,
            'last_used_at' => now(),
        ])->saveQuietly();

        return $next($request);
    }
}
