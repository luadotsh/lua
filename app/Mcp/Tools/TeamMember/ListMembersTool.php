<?php

declare(strict_types=1);

namespace App\Mcp\Tools\TeamMember;

use App\Mcp\Concerns\ResolvesWorkspace;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly]
#[Description('List the people in this workspace and their roles.')]
class ListMembersTool extends Tool
{
    use ResolvesWorkspace;

    public function handle(Request $request): Response|ResponseFactory
    {
        $members = $this->workspace($request)->users()->get();

        return Response::structured(
            $members->map(fn ($member) => [
                'id' => $member->id,
                'name' => $member->name,
                'email' => $member->email,
                'role' => $member->membership->role ?? null,
            ])->values()->all(),
        );
    }
}
