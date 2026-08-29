<?php

declare(strict_types=1);

namespace App\Mcp\Tools\Tag;

use App\Http\Resources\Api\TagResource;
use App\Mcp\Concerns\ResolvesWorkspace;
use App\Models\Tag;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly]
#[Description('List the tags available in this workspace, for use when creating or updating links.')]
class ListTagsTool extends Tool
{
    use ResolvesWorkspace;

    public function handle(Request $request): Response|ResponseFactory
    {
        $tags = Tag::where('workspace_id', $this->workspace($request)->id)
            ->orderBy('sort')
            ->get();

        return Response::structured(TagResource::collection($tags)->resolve());
    }
}
