<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Workspace;

class WorkspacePolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function reachedLinkLimit(?User $user, Workspace $workspace): bool
    {
        return !$workspace->usage()['links']['reached_limit'];
    }
}
