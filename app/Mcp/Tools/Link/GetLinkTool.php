<?php

declare(strict_types=1);

namespace App\Mcp\Tools\Link;

use App\Http\Resources\Api\LinkResource;
use App\Mcp\Concerns\ResolvesWorkspace;
use App\Models\Link;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly]
#[Description('Get one short link in this workspace by its id.')]
class GetLinkTool extends Tool
{
    use ResolvesWorkspace;

    /**
     * @return array<string, JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->string()->description('The link id.')->required(),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        $link = Link::where('workspace_id', $this->workspace($request)->id)
            ->with('tags')
            ->find($request->get('id'));

        if (! $link) {
            return Response::error('Link not found in this workspace.');
        }

        return Response::structured((new LinkResource($link))->resolve());
    }
}
