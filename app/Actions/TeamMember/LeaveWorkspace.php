<?php

declare(strict_types=1);

namespace App\Actions\TeamMember;

use App\Models\User;
use App\Models\Workspace;

class LeaveWorkspace
{
    /**
     * Detaches the user and moves them to another workspace they belong to,
     * or leaves them with none so SetWorkspace sends them to create one.
     * Returns the workspace they landed in, or null.
     */
    public static function execute(User $user, Workspace $workspace): ?Workspace
    {
        $user->workspaces()->detach($workspace->id);
        $user->unsetRelation('workspaces');

        $next = $user->workspaces()->first();

        if ($next) {
            $user->switchWorkspace(Workspace::findOrFail($next->id));

            return $next;
        }

        $user->forceFill(['current_workspace_id' => null])->save();

        return null;
    }

    /**
     * A workspace cannot be left without anyone in it.
     */
    public static function isLastMember(Workspace $workspace): bool
    {
        return $workspace->users()->count() === 1;
    }
}
