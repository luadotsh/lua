<?php

declare(strict_types=1);

namespace App\Actions\AccessToken;

use App\Models\AccessToken;
use App\Models\User;
use App\Models\Workspace;

class RevokeAccessToken
{
    /**
     * Scoped to the workspace, and for OAuth grants also to the user who
     * authorised them, so no one can revoke another person's connection by
     * guessing an id. Workspace API keys stay revocable by any member, since
     * they act for the workspace rather than for a person.
     */
    public static function execute(User $user, Workspace $workspace, string $tokenId): bool
    {
        $token = AccessToken::query()
            ->where('workspace_id', $workspace->id)
            ->find($tokenId);

        if ($token === null) {
            return false;
        }

        if ($token->isMcpOAuthGrant() && $token->user_id !== $user->id) {
            return false;
        }

        $token->forceFill(['revoked' => true])->saveQuietly();

        // The property, not the method: refreshToken() hands back the HasOne
        // itself, which has no forceFill. The null-safe operator hid nothing
        // either — a relation object is never null, only its result is.
        $token->refreshToken?->forceFill(['revoked' => true])->saveQuietly();

        return true;
    }
}
