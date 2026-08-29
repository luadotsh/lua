<?php

declare(strict_types=1);

namespace App\Actions\Domain;

use App\Models\Domain;
use App\Models\Workspace;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ListDomains
{
    /**
     * @return Collection<int, Domain>
     */
    public static function execute(Workspace $workspace): Collection
    {
        return Domain::where('workspace_id', $workspace->id)->get();
    }

    /**
     * Newest first and paginated, for the REST endpoint.
     *
     * @return LengthAwarePaginator<int, Domain>
     */
    public static function paginate(Workspace $workspace, ?int $perPage = null): LengthAwarePaginator
    {
        $perPage = min(max((int) ($perPage ?: config('app.pagination.default')), 1), 100);

        return Domain::where('workspace_id', $workspace->id)
            ->latest()
            ->paginate($perPage);
    }
}
