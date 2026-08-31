<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Tag;
use App\Models\Workspace;

class WorkspaceObserver
{
    /**
     * A few tags to show what they look like, trimmed to what the plan allows.
     *
     * Seeding the full set regardless would hand a plan with a smaller
     * allowance a workspace that is already over its own limit, unable to
     * create a tag and with nothing on screen saying why.
     *
     * @var list<array{name: string, color: string}>
     */
    private const DEFAULT_TAGS = [
        ['name' => 'Marketing', 'color' => '#f87171'],
        ['name' => 'Sales', 'color' => '#60a5fa'],
        ['name' => 'Development', 'color' => '#4ade80'],
    ];

    /**
     * Handle the Workspace "created" event.
     */
    public function created(Workspace $workspace): void
    {
        $allowance = (int) $workspace->plan?->max_tags;

        foreach (array_slice(self::DEFAULT_TAGS, 0, max($allowance, 0)) as $tag) {
            Tag::create([...$tag, 'workspace_id' => $workspace->id]);
        }
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
