<?php

declare(strict_types=1);

namespace App\Actions\Domain;

use App\Models\Domain;
use App\Models\Workspace;

class GetDomain
{
    /**
     * Null when the domain belongs to another workspace.
     */
    public static function execute(Workspace $workspace, string $id): ?Domain
    {
        return Domain::where('workspace_id', $workspace->id)->find($id);
    }
}
