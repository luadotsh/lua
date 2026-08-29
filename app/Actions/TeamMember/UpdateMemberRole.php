<?php

declare(strict_types=1);

namespace App\Actions\TeamMember;

use App\Models\User;
use App\Models\Workspace;

class UpdateMemberRole
{
    public static function execute(Workspace $workspace, User $user, string $role): void
    {
        $user->workspaces()->updateExistingPivot($workspace->id, ['role' => $role]);
    }
}
