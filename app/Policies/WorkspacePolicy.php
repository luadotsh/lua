<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\Workspace;

class WorkspacePolicy
{
    /**
     * Determine if the user has reached the link limit.
     */
    public function reachedLinkLimit(?User $user, Workspace $workspace): bool
    {
        return !$workspace->usage()['links']['reached_limit'];
    }

    /**
     * Determine if the user has reached the events limit.
     */
    public function reachedEventLimit(?User $user, Workspace $workspace): bool
    {
        return !$workspace->usage()['events']['reached_limit'];
    }

    /**
     * Determine if the user has reached the domain limit.
     */
    public function reachedDomainLimit(?User $user, Workspace $workspace): bool
    {
        return !$workspace->usage()['domains']['reached_limit'];
    }

    /**
     * Determine if the user has reached the tag limit.
     */
    public function reachedTagLimit(?User $user, Workspace $workspace): bool
    {
        return !$workspace->usage()['tags']['reached_limit'];
    }

    /**
     * Determine if the user has reached the team member limit.
     */
    public function reachedUserLimit(?User $user, Workspace $workspace): bool
    {
        return !$workspace->usage()['users']['reached_limit'];
    }





}
