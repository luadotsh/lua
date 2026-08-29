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
            ->when(filled($search), fn ($query) => $query->where(
                fn ($q) => $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%"),
            ))
            ->get();
    }
}
