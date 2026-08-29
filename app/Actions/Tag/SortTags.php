<?php

declare(strict_types=1);

namespace App\Actions\Tag;

use App\Models\Tag;
use App\Models\Workspace;
use Illuminate\Support\Facades\DB;

class SortTags
{
    /**
     * Reorders by the position of each id in the given list. Scoped to the
     * workspace so an id from elsewhere cannot be reordered into it.
     *
     * @param  array<int, array{id: string|int}|string|int>  $tags
     */
    public static function execute(Workspace $workspace, array $tags): void
    {
        DB::transaction(function () use ($workspace, $tags): void {
            foreach ($tags as $position => $tag) {
                $id = is_array($tag) ? data_get($tag, 'id') : $tag;

                Tag::where('workspace_id', $workspace->id)
                    ->where('id', $id)
                    ->update(['sort' => $position + 1]);
            }
        });
    }
}
