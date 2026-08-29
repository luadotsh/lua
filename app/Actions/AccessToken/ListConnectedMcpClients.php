<?php

declare(strict_types=1);

namespace App\Actions\AccessToken;

use App\Models\AccessToken;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Collection;

class ListConnectedMcpClients
{
    /**
     * OAuth grants this user holds against this workspace. A grant belongs to
     * the person who authorised it, so it is scoped by both — a workspace
     * member must never see, or be able to revoke, someone else's connection.
     *
     * @return Collection<int, AccessToken>
     */
    public static function execute(User $user, Workspace $workspace): Collection
    {
        return AccessToken::query()
            ->mcpOAuth()
            ->with('client')
            ->where('user_id', $user->id)
            ->where('workspace_id', $workspace->id)
            ->where('revoked', false)
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->latest('created_at')
            ->get();
    }
}
