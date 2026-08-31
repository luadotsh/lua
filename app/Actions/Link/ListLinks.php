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
     * Tag, domain and user each accept a list: picking two tags widens the
     * result rather than narrowing it to links carrying both.
     *
     * @param  array{search?: string|null, tag?: list<string>|null, domain?: list<string>|null, user?: list<string>|null, per_page?: int|null}  $filters
     * @return LengthAwarePaginator<int, Link>
     */
    public static function execute(Workspace $workspace, array $filters = []): LengthAwarePaginator
    {
        $search = data_get($filters, 'search');
        $tags = array_filter((array) (data_get($filters, 'tag') ?? []));
        $domains = array_filter((array) (data_get($filters, 'domain') ?? []));
        $users = array_filter((array) (data_get($filters, 'user') ?? []));
        $perPage = (int) (data_get($filters, 'per_page') ?: config('lua.pagination.default'));
        $perPage = min(max($perPage, 1), 100);

        return Link::where('workspace_id', $workspace->id)
            ->withClickTotals()
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
            // Scoped to the workspace already, so the ids cannot reach across
            // into another workspace's tags or members.
            ->when($tags !== [], fn ($query) => $query->whereHas(
                'tags',
                fn ($related) => $related->whereIn('tags.id', $tags),
            ))
            ->when($domains !== [], fn ($query) => $query->whereIn('domain', $domains))
            ->when($users !== [], fn ($query) => $query->whereIn('user_id', $users))
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
