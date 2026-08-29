<?php

declare(strict_types=1);

namespace App\Actions\Invite;

use App\Models\Invite;
use App\Models\Workspace;

class GetInvite
{
    /**
     * Null when the invite belongs to another workspace.
     */
    public static function execute(Workspace $workspace, string $id): ?Invite
    {
        return Invite::where('workspace_id', $workspace->id)->find($id);
    }
}
