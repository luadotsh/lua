<?php

declare(strict_types=1);

namespace App\Actions\Tag;

use App\Models\Tag;
use App\Models\Workspace;
use Illuminate\Validation\ValidationException;

class CreateTag
{
    /**
     * The plan's tag allowance.
     *
     * Enforced here rather than in the controllers because the web form, the
     * REST API and the MCP tool all come through this action — a check in one
     * of them would be a limit the other two ignore.
     */
    private static function assertWithinPlanLimit(Workspace $workspace): void
    {
        $tags = $workspace->usage()['tags'];

        if (! $tags['reached_limit']) {
            return;
        }

        throw ValidationException::withMessages([
            'name' => $tags['limit'] === 1
                ? 'Your plan covers one tag. Upgrade to add more.'
                : "Your plan covers {$tags['limit']} tags and you have used them all. Upgrade to add more.",
        ]);
    }

    /**
     * @param  array{name: string, color: string}  $data
     */
    public static function execute(Workspace $workspace, array $data): Tag
    {
        self::assertWithinPlanLimit($workspace);

        return Tag::create([
            'workspace_id' => $workspace->id,
            'name' => data_get($data, 'name'),
            'color' => data_get($data, 'color'),
        ]);
    }
}
