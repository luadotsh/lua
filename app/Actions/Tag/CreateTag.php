<?php

declare(strict_types=1);

namespace App\Actions\Tag;

use App\Models\Tag;
use App\Models\Workspace;

class CreateTag
{
    /**
     * @param  array{name: string, color: string}  $data
     */
    public static function execute(Workspace $workspace, array $data): Tag
    {
        return Tag::create([
            'workspace_id' => $workspace->id,
            'name' => data_get($data, 'name'),
            'color' => data_get($data, 'color'),
            // New tags go to the end of the workspace's ordering.
            'sort' => Tag::where('workspace_id', $workspace->id)->count() + 1,
        ]);
    }
}
