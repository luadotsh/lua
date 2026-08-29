<?php

declare(strict_types=1);

namespace App\Actions\Tag;

use App\Models\Tag;
use App\Models\Workspace;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ListTags
{
    /**
     * In the workspace's own display order.
     *
     * @return Collection<int, Tag>
     */
    public static function execute(Workspace $workspace): Collection
    {
        return Tag::where('workspace_id', $workspace->id)
            ->orderBy('sort')
            ->get();
    }

    /**
     * Newest first and paginated, for the REST endpoint.
     *
     * @return LengthAwarePaginator<int, Tag>
     */
    public static function paginate(Workspace $workspace, ?int $perPage = null): LengthAwarePaginator
    {
        $perPage = min(max((int) ($perPage ?: config('app.pagination.default')), 1), 100);

        return Tag::where('workspace_id', $workspace->id)
            ->latest()
            ->paginate($perPage);
    }
}
