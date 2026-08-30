<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Tag;
use App\Models\Workspace;

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
            'name' => 'Marketing',
            'color' => '#f87171',
        ]);

        Tag::create([
            'workspace_id' => $workspace->id,
            'name' => 'Sales',
            'color' => '#60a5fa',
        ]);

        Tag::create([
            'workspace_id' => $workspace->id,
            'name' => 'Development',
            'color' => '#4ade80',
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
