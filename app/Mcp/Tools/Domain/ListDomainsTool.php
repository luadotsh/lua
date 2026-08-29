<?php

declare(strict_types=1);

namespace App\Mcp\Tools\Domain;

use App\Http\Resources\Api\DomainResource;
use App\Mcp\Concerns\ResolvesWorkspace;
use App\Models\Domain;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly]
#[Description('List the custom domains registered in this workspace, with their verification status.')]
class ListDomainsTool extends Tool
{
    use ResolvesWorkspace;

    public function handle(Request $request): Response|ResponseFactory
    {
        $domains = Domain::where('workspace_id', $this->workspace($request)->id)->get();

        return Response::structured(DomainResource::collection($domains)->resolve());
    }
}
