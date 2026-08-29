<?php

declare(strict_types=1);

namespace App\Mcp\Tools\Domain;

use App\Actions\Domain\DeleteDomain;
use App\Mcp\Concerns\ResolvesWorkspace;
use App\Models\Domain;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Title;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsDestructive;

#[IsDestructive]
#[Title('Delete domain')]
#[Description('Remove a custom domain from this workspace. Links on that domain stop resolving.')]
class DeleteDomainTool extends Tool
{
    use ResolvesWorkspace;

    /**
     * @return array<string, JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->string()->description('The domain id.')->required(),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        $domain = Domain::where('workspace_id', $this->workspace($request)->id)
            ->find($request->get('id'));

        if (! $domain) {
            return Response::error('Domain not found in this workspace.');
        }

        DeleteDomain::execute($domain);

        return Response::text('Domain deleted.');
    }
}
