<?php

declare(strict_types=1);

namespace App\Mcp\Tools\Link;

use App\Actions\Link\DeleteLink;
use App\Actions\Link\GetLink;
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
#[Title('Delete link')]
#[Description('Permanently delete a short link in this workspace. The link stops resolving immediately.')]
class DeleteLinkTool extends Tool
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
        $link = GetLink::execute($this->workspace($request), (string) $request->get('id'));

        if (! $link) {
            return Response::error('Link not found in this workspace.');
        }

        DeleteLink::execute($link);

        return Response::text('Link deleted.');
    }
}
