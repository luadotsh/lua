<?php

declare(strict_types=1);

namespace App\Actions\AccessToken;

use App\Models\AccessToken;
use App\Models\Workspace;
use Illuminate\Support\Collection;

class ListConnectedMcpClients
{
    /**
     * OAuth grants an MCP client currently holds against this workspace.
     *
     * @return Collection<int, AccessToken>
     */
    public static function execute(Workspace $workspace): Collection
    {
        return AccessToken::query()
            ->mcpOAuth()
            ->with('client')
            ->where('workspace_id', $workspace->id)
            ->where('revoked', false)
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->latest('created_at')
            ->get();
    }
}
