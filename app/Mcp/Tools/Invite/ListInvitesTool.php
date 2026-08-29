<?php

declare(strict_types=1);

namespace App\Mcp\Tools\Invite;

use App\Mcp\Concerns\ResolvesWorkspace;
use App\Models\Invite;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly]
#[Description('List the pending invites for this workspace.')]
class ListInvitesTool extends Tool
{
    use ResolvesWorkspace;

    public function handle(Request $request): Response|ResponseFactory
    {
        $invites = Invite::where('workspace_id', $this->workspace($request)->id)->get();

        return Response::structured(
            $invites->map(fn (Invite $invite) => [
                'id' => $invite->id,
                'email' => $invite->email,
                'role' => $invite->role,
                'created_at' => $invite->created_at?->toIso8601String(),
            ])->values()->all(),
        );
    }
}
