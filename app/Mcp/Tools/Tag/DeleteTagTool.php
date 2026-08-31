<?php

declare(strict_types=1);

namespace App\Mcp\Tools\Tag;

use App\Actions\Tag\DeleteTag;
use App\Actions\Tag\GetTag;
use App\Mcp\Concerns\ResolvesWorkspace;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Title;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsDestructive;

#[IsDestructive]
#[Title('Delete tag')]
#[Description('Permanently delete a tag in this workspace. Links keep existing but lose the tag.')]
class DeleteTagTool extends Tool
{
    use ResolvesWorkspace;

    /**
     * @return array<string, JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->string()->description('The tag id.')->required(),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        $tag = GetTag::execute($this->workspace($request), (string) $request->get('id'));

        if (! $tag) {
            return Response::error('Tag not found in this workspace.');
        }

        DeleteTag::execute($tag);

        return Response::text('Tag deleted.');
    }
}
