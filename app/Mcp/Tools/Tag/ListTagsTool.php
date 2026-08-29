<?php

declare(strict_types=1);

namespace App\Mcp\Tools\Tag;

use App\Actions\Tag\ListTags;
use App\Http\Resources\Api\TagResource;
use App\Mcp\Concerns\ResolvesWorkspace;
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
        $tags = ListTags::execute($this->workspace($request));

        return Response::structured(TagResource::collection($tags)->resolve());
    }
}
