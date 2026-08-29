<?php

declare(strict_types=1);

namespace App\Actions\Invite;

use App\Models\Invite;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Collection;

class ListInvites
{
    /**
     * Invites still waiting to be accepted for this workspace.
     *
     * @return Collection<int, Invite>
     */
    public static function execute(Workspace $workspace): Collection
    {
        return Invite::where('workspace_id', $workspace->id)
            ->latest()
            ->get();
    }
}
