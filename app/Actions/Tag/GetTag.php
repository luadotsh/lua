<?php

declare(strict_types=1);

namespace App\Actions\Tag;

use App\Models\Tag;
use App\Models\Workspace;

class GetTag
{
    /**
     * Null when the tag belongs to another workspace, so a caller cannot tell
     * "not yours" apart from "does not exist".
     */
    public static function execute(Workspace $workspace, string $id): ?Tag
    {
        return Tag::where('workspace_id', $workspace->id)->find($id);
    }
}
