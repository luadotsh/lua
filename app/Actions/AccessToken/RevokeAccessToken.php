<?php

declare(strict_types=1);

namespace App\Actions\AccessToken;

use App\Models\AccessToken;
use App\Models\Workspace;

class RevokeAccessToken
{
    /**
     * Revoking is scoped to the workspace the token is bound to, so a member of
     * one workspace can never revoke another workspace's key by guessing an id.
     */
    public static function execute(Workspace $workspace, string $tokenId): bool
    {
        $token = AccessToken::query()
            ->where('workspace_id', $workspace->id)
            ->find($tokenId);

        if ($token === null) {
            return false;
        }

        $token->forceFill(['revoked' => true])->saveQuietly();
        $token->refreshToken()?->forceFill(['revoked' => true])->saveQuietly();

        return true;
    }
}
