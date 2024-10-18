<?php

declare(strict_types=1);

namespace App\Models\Traits;

use App\Enums\User\Role as UserRole;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Builder;

trait HasWorkspaces
{
    public function workspaces()
    {
        return $this->belongsToMany(Workspace::class)
            ->withPivot('role')
            ->wherePivotNotNull('role')
            ->as('membership')
            ->withTimestamps();
    }

    public function isCurrentWorkspace($workspace)
    {
        return $workspace->id === $this->currentWorkspace->id;
    }

    public function allWorkspaces()
    {
        return $this->workspaces->sortBy('name');
    }

    public function currentWorkspace()
    {
        return $this->belongsTo(Workspace::class, 'current_workspace_id');
    }

    public function belongsToWorkspace($workspace)
    {
        return $this->workspaces->contains(function ($t) use ($workspace) {
            return $t->id === $workspace->id;
        });
    }

    public function workspaceRole($workspace)
    {
        if (! $this->belongsToWorkspace($workspace)) {
            return;
        }

        $user = $this->whereHas('workspaces', function (Builder $query) use ($workspace) {
            $query->where('workspaces.id', $workspace->id);
        })
            ->where('id', $this->id)
            ->with('workspaces', function ($query) use ($workspace) {
                $query->where('workspaces.id', $workspace->id);
            })
            ->first();

        return $user->workspaces->first()->membership->role;
    }

    public function ownsWorkspace($workspace)
    {
        if (is_null($workspace)) {
            return false;
        }

        return $this->workspaceRole($workspace) === UserRole::ROLE_OWNER->value;
    }

    public function isAdminOnStore($workspace)
    {
        if (is_null($workspace)) {
            return false;
        }

        return $this->workspaceRole($workspace) !== UserRole::ROLE_USER->value;
    }

    public function hasWorkspaceRole($workspace, $role)
    {
        if (! $this->belongsToWorkspace($workspace)) {
            return;
        }

        $user = $this->whereHas('workspaces', function (Builder $query) use ($workspace) {
            $query->where('workspaces.id', $workspace->id);
        })
            ->where('id', $this->id)
            ->with('workspaces')
            ->first();

        return $user->workspaces->first()->membership->role === $role;
    }

    public function switchWorkspace($workspace)
    {
        if (!$this->belongsToWorkspace($workspace)) {
            return false;
        }

        $this->forceFill([
            'current_workspace_id' => $workspace->id,
        ])->save();

        $this->setRelation('currentWorkspace', $workspace);

        return true;
    }
}
