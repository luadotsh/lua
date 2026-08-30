<?php

declare(strict_types=1);

namespace App\Actions\TeamMember;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Collection;

class ListMembers
{
    /**
     * The people in a workspace, by name, each carrying their membership role.
     * An optional search matches name or email.
     *
     * @param  array{search?: string|null}  $filters
     * @return Collection<int, User>
     */
    public static function execute(Workspace $workspace, array $filters = []): Collection
    {
        $search = data_get($filters, 'search');

        return $workspace->users()
            ->orderBy('name')
            // whereLike keeps the search case-insensitive on both engines:
            // Laravel emits `ilike` on PostgreSQL and a plain `like` on MySQL,
            // where the default collation already ignores case.
            ->when(filled($search), fn ($query) => $query->where(
                fn ($q) => $q->whereLike('name', "%{$search}%")
                    ->orWhereLike('email', "%{$search}%"),
            ))
            ->get();
    }
}
