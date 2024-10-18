<?php

namespace App\Features;

use App\Models\Workspace;

class CustomDomain
{
    public $name = 'customDomain';

    /**
     * Resolve the feature's initial value.
     */
    public function resolve(Workspace $workspace): mixed
    {
        return match (true) {
            $workspace->plan->access_level == 1 => false,
            $workspace->plan->access_level >= 2 => true
        };
    }
}
