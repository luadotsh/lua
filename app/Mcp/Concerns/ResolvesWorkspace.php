<?php

declare(strict_types=1);

namespace App\Mcp\Concerns;

use App\Models\Workspace;
use Laravel\Mcp\Request;
use RuntimeException;

trait ResolvesWorkspace
{
    /**
     * The workspace this OAuth grant is bound to. LoadWorkspaceFromToken has
     * already resolved and authorised it, so this never falls back to the
     * user's workspace switcher.
     */
    protected function workspace(Request $request): Workspace
    {
        $workspace = $request->user()?->currentWorkspace;

        if (! $workspace instanceof Workspace) {
            throw new RuntimeException('This connection is not bound to a workspace.');
        }

        return $workspace;
    }
}
