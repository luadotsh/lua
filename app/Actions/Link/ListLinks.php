<?php

declare(strict_types=1);

namespace App\Actions\Link;

use App\Models\Link;
use App\Models\Workspace;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListLinks
{
    /**
     * The columns a free-text search looks through.
     *
     * @var list<string>
     */
    private const SEARCHABLE = [
        'link',
        'url',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'utm_content',
    ];

    /**
     * Newest first, always scoped to one workspace — the scoping lives here so
     * no caller can forget it.
     *
     * @param  array{search?: string|null, per_page?: int|null}  $filters
     * @return LengthAwarePaginator<int, Link>
     */
    public static function execute(Workspace $workspace, array $filters = []): LengthAwarePaginator
    {
        $search = data_get($filters, 'search');
        $perPage = (int) (data_get($filters, 'per_page') ?: config('app.pagination.default'));
        $perPage = min(max($perPage, 1), 100);

        return Link::where('workspace_id', $workspace->id)
            ->with('tags')
            // whereLike is case-insensitive by default, which a raw LIKE is
            // not on Postgres: searching "GitHub" used to miss "github.com".
            ->when(filled($search), fn ($query) => $query->where(
                function ($query) use ($search): void {
                    foreach (self::SEARCHABLE as $i => $column) {
                        $i === 0
                            ? $query->whereLike($column, "%{$search}%")
                            : $query->orWhereLike($column, "%{$search}%");
                    }
                },
            ))
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Whether the workspace has any links at all, for telling an empty search
     * apart from an empty workspace.
     */
    public static function hasAny(Workspace $workspace): bool
    {
        return Link::where('workspace_id', $workspace->id)->exists();
    }
}
