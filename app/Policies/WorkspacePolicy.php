<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\Workspace;

class WorkspacePolicy
{
    /**
     * Who may run the workspace: members, invites, settings and API keys.
     *
     * These were enforced in Vue and nowhere else, so a USER could promote
     * themselves, remove the owner, invite at any role, rename the workspace
     * and mint API keys by sending the request the screen would not show them.
     */
    public function administer(User $user, Workspace $workspace): bool
    {
        return $user->belongsToWorkspace($workspace)
            && $user->isAdminOnStore($workspace);
    }

    /**
     * Ownership is one person, and only they may hand it on or close the
     * workspace. An admin runs it; the owner answers for it.
     */
    public function own(User $user, Workspace $workspace): bool
    {
        return $user->ownsWorkspace($workspace);
    }

    /**
     * The day-to-day work: links, tags and domains. Every member may.
     */
    public function contribute(User $user, Workspace $workspace): bool
    {
        return $user->belongsToWorkspace($workspace);
    }

    /**
     * The owner cannot be removed or demoted out of their own workspace —
     * that is what ownership means, and it is what keeps stripeEmail() and
     * billing pointing at someone.
     */
    public function manageMember(User $user, Workspace $workspace, User $member): bool
    {
        return $this->administer($user, $workspace)
            && ! $member->ownsWorkspace($workspace);
    }

    /**
     * Determine if the user has reached the link limit.
     */
    public function reachedLinkLimit(?User $user, Workspace $workspace): bool
    {
        return ! $workspace->usage()['links']['reached_limit'];
    }

    /**
     * Determine if the user has reached the events limit.
     */
    public function reachedEventLimit(?User $user, Workspace $workspace): bool
    {
        return ! $workspace->usage()['events']['reached_limit'];
    }

    /**
     * Determine if the user has reached the domain limit.
     */
    public function reachedDomainLimit(?User $user, Workspace $workspace): bool
    {
        return ! $workspace->usage()['domains']['reached_limit'];
    }

    /**
     * Determine if the user has reached the tag limit.
     */
    public function reachedTagLimit(?User $user, Workspace $workspace): bool
    {
        return ! $workspace->usage()['tags']['reached_limit'];
    }

    /**
     * Determine if the user has reached the team member limit.
     */
    public function reachedUserLimit(?User $user, Workspace $workspace): bool
    {
        return ! $workspace->usage()['users']['reached_limit'];
    }
}
