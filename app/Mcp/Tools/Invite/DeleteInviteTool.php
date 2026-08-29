<?php

declare(strict_types=1);

namespace App\Mcp\Tools\Invite;

use App\Actions\Invite\GetInvite;
use App\Actions\Invite\DeleteInvite;
use App\Mcp\Concerns\ResolvesWorkspace;
use App\Models\Invite;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Title;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsDestructive;

#[IsDestructive]
#[Title('Cancel invite')]
#[Description('Cancel a pending invite for this workspace.')]
class DeleteInviteTool extends Tool
{
    use ResolvesWorkspace;

    /**
     * @return array<string, JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->string()->description('The invite id.')->required(),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        $invite = GetInvite::execute($this->workspace($request), (string) $request->get('id'));

        if (! $invite) {
            return Response::error('Invite not found in this workspace.');
        }

        DeleteInvite::execute($invite);

        return Response::text('Invite cancelled.');
    }
}
