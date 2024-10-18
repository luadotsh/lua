<?php

namespace App\Features;

use App\Models\Workspace;

class ReachLinkLimit
{
    public $name = 'reachLinkLimit';

    /**
     * Resolve the feature's initial value.
     */
    public function resolve(Workspace $workspace): mixed
    {
        return match (true) {
            $workspace->links->count() >= 10 => true,
        };
    }
}
