<?php

declare(strict_types=1);

namespace App\Actions\Link;

use App\Models\Link;
use App\Models\Workspace;

class GetLink
{
    /**
     * Returns null when the link belongs to another workspace, so a caller
     * cannot tell "not yours" apart from "does not exist".
     */
    public static function execute(Workspace $workspace, string $id): ?Link
    {
        return Link::where('workspace_id', $workspace->id)
            ->with('tags')
            ->find($id);
    }
}
