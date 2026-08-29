<?php

declare(strict_types=1);

namespace App\Actions\TeamMember;

use App\Models\User;
use App\Models\Workspace;

class RemoveMember
{
    public static function execute(Workspace $workspace, User $user): void
    {
        $user->workspaces()->detach($workspace->id);
    }
}
