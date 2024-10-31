<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Workspace;
use App\Models\Tag;

class WorkspaceObserver
{
    /**
     * Handle the Workspace "created" event.
     */
    public function created(Workspace $workspace): void
    {
        // tags
        Tag::create([
            'workspace_id' => $workspace->id,
            'sort' => 1,
            'name' => 'Marketing',
            'color' => 'red'
        ]);

        Tag::create([
            'workspace_id' => $workspace->id,
            'sort' => 2,
            'name' => 'Sales',
            'color' => 'blue'
        ]);

        Tag::create([
            'workspace_id' => $workspace->id,
            'sort' => 3,
            'name' => 'Development',
            'color' => 'green'
        ]);
    }

    /**
     * Handle the Workspace "updated" event.
     */
    public function updated(Workspace $workspace): void
    {
        //
    }

    /**
     * Handle the Workspace "deleted" event.
     */
    public function deleted(Workspace $workspace): void
    {
        //
    }

    /**
     * Handle the Workspace "restored" event.
     */
    public function restored(Workspace $workspace): void
    {
        //
    }

    /**
     * Handle the Workspace "force deleted" event.
     */
    public function forceDeleted(Workspace $workspace): void
    {
        //
    }
}
