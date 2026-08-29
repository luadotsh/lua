<?php

declare(strict_types=1);

namespace App\Mcp\Tools\Invite;

use App\Actions\Invite\CreateInvite;
use App\Enums\User\Role;
use App\Mcp\Concerns\ResolvesWorkspace;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Enum;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Invite someone to this workspace. Sends them an email; if they already have an account they are added straight away.')]
class CreateInviteTool extends Tool
{
    use ResolvesWorkspace;

    /**
     * @return array<string, JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'email' => $schema->string()->description('Who to invite.')->required(),
            'role' => $schema->string()->description('OWNER, ADMIN or USER.')->required(),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'max:255'],
            'role' => ['required', new Enum(Role::class)],
        ]);

        if ($validator->fails()) {
            return Response::error($validator->errors()->first());
        }

        $invite = CreateInvite::execute($this->workspace($request), $validator->validated());

        return Response::text($invite === null
            ? 'That person already had an account and was added to the workspace.'
            : 'Invite sent.');
    }
}
