<?php

declare(strict_types=1);

namespace App\Actions\AccessToken;

use App\Models\AccessToken;
use App\Models\Workspace;
use Illuminate\Support\Collection;

class ListWorkspaceApiKeys
{
    /**
     * @return Collection<int, AccessToken>
     */
    public static function execute(Workspace $workspace): Collection
    {
        return AccessToken::query()
            ->personalAccessApiKey()
            ->where('workspace_id', $workspace->id)
            ->where('revoked', false)
            ->latest('created_at')
            ->get();
    }
}
