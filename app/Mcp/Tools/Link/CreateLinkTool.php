<?php

declare(strict_types=1);

namespace App\Mcp\Tools\Link;

use App\Actions\Link\CreateLink;
use App\Http\Resources\Api\LinkResource;
use App\Mcp\Concerns\ResolvesWorkspace;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Create a short link in this workspace. Leave the key empty to have one generated.')]
class CreateLinkTool extends Tool
{
    use ResolvesWorkspace;

    /**
     * @return array<string, JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'url' => $schema->string()->description('The destination URL.')->required(),
            'key' => $schema->string()->description('The short link slug. Generated when omitted. Lowercase letters, numbers and hyphens only.'),
            'domain' => $schema->string()->description('Which of the workspace domains to use. Defaults to the main domain.'),
            'ios' => $schema->string()->description('Alternative destination for iOS visitors.'),
            'android' => $schema->string()->description('Alternative destination for Android visitors.'),
            'expires_at' => $schema->string()->description('Expiry as Y-m-d H:i:s.'),
            'expired_redirect_url' => $schema->string()->description('Where to send visitors after expiry.'),
            'external_id' => $schema->string()->description('Your own identifier for this link.'),
            'utm_source' => $schema->string()->description('utm_source appended to the destination URL.'),
            'utm_medium' => $schema->string()->description('utm_medium appended to the destination URL.'),
            'utm_campaign' => $schema->string()->description('utm_campaign appended to the destination URL.'),
            'utm_term' => $schema->string()->description('utm_term appended to the destination URL.'),
            'utm_content' => $schema->string()->description('utm_content appended to the destination URL.'),
            'tags' => $schema->array()->description('Tag ids to attach.'),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        $workspace = $this->workspace($request);

        if (! Gate::inspect('reached-link-limit', $workspace)->allowed()) {
            return Response::error('This workspace has reached its link limit. Upgrade the plan to create more links.');
        }

        $data = $request->all();
        $data['domain'] = data_get($data, 'domain') ?: config('domains.main');

        // Same rules the REST endpoint enforces.
        $validator = Validator::make($data, CreateLink::rules($workspace, $data));

        if ($validator->fails()) {
            return Response::error($validator->errors()->first());
        }

        $link = CreateLink::execute($workspace, $validator->validated(), $request->user());

        return Response::structured((new LinkResource($link))->resolve());
    }
}
