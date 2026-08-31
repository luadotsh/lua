<?php

declare(strict_types=1);

namespace App\Actions\TeamMember;

use App\Models\User;
use App\Models\Workspace;

class RemoveMember
{
    /**
     * Detaching alone left the removed person's current_workspace_id pointing
     * at the workspace they had just been removed from, and every controller
     * trusts currentWorkspace — so they kept reading and writing its data on
     * the next request. They are moved on the way out, the way leaving does it.
     */
    public static function execute(Workspace $workspace, User $user): void
    {
        $user->workspaces()->detach($workspace->id);
        $user->unsetRelation('workspaces');

        if ($user->current_workspace_id !== $workspace->id) {
            return;
        }

        $next = $user->workspaces()->first();

        $user->forceFill(['current_workspace_id' => $next?->id])->save();
    }
}
