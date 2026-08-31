<?php

declare(strict_types=1);

namespace App\Mcp\Tools\Invite;

use App\Actions\Invite\DeleteInvite;
use App\Actions\Invite\GetInvite;
use App\Mcp\Concerns\ResolvesWorkspace;
use App\Models\Invite;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Gate;
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
        $workspace = $this->workspace($request);

        // Same rule as the settings screen: running the workspace is what an
        // invite is, whichever surface it arrives through.
        if (! Gate::forUser($request->user())->allows('administer', $workspace)) {
            return Response::error('Only a workspace admin can manage invites.');
        }

        $invite = GetInvite::execute($this->workspace($request), (string) $request->get('id'));

        if (! $invite) {
            return Response::error('Invite not found in this workspace.');
        }

        DeleteInvite::execute($invite);

        return Response::text('Invite cancelled.');
    }
}
