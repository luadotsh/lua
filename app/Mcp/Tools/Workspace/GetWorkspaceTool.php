<?php

declare(strict_types=1);

namespace App\Mcp\Tools\Workspace;

use App\Mcp\Concerns\ResolvesWorkspace;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly]
#[Description('Get the workspace this connection is bound to, including its plan.')]
class GetWorkspaceTool extends Tool
{
    use ResolvesWorkspace;

    public function handle(Request $request): Response|ResponseFactory
    {
        $workspace = $this->workspace($request);

        return Response::structured([
            'id' => $workspace->id,
            'name' => $workspace->name,
            'plan' => $workspace->plan?->name,
            'created_at' => $workspace->created_at?->toIso8601String(),
        ]);
    }
}
