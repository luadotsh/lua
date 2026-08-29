<?php

declare(strict_types=1);

namespace App\Actions\Workspace;

use App\Models\Workspace;

class UpdateWorkspace
{
    /**
     * @param  array<string, mixed>  $data
     */
    public static function execute(Workspace $workspace, array $data): Workspace
    {
        if (array_key_exists('name', $data)) {
            $workspace->name = $data['name'];
        }

        $workspace->save();

        return $workspace;
    }
}
