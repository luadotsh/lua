<?php

declare(strict_types=1);

namespace App\Mcp\Tools\Link;

use App\Actions\Link\CreateLink;
use App\Http\Resources\Api\LinkResource;
use App\Mcp\Concerns\ResolvesWorkspace;
use App\Models\Link;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
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
            'tags' => $schema->array()->description('Tag ids to attach.'),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        $workspace = $this->workspace($request);

        if (! Gate::inspect('reached-link-limit', $workspace)->allowed()) {
            return Response::error('This workspace has reached its link limit. Upgrade the plan to create more links.');
        }

        $domains = array_merge(
            $workspace->domains->pluck('domain')->toArray(),
            config('domains.available'),
        );

        $data = $request->all();
        $data['domain'] = data_get($data, 'domain') ?: config('domains.main');

        $validator = Validator::make($data, [
            'url' => ['required', 'url', 'max:255', 'min:2'],
            'domain' => ['required', 'string', Rule::in($domains)],
            'key' => [
                'nullable', 'string', 'max:255', 'regex:/^[a-z0-9-]+$/',
                Rule::unique('links')->where('domain', $data['domain']),
            ],
            'ios' => ['nullable', 'url', 'max:255'],
            'android' => ['nullable', 'url', 'max:255'],
            'expired_redirect_url' => ['nullable', 'url', 'max:255'],
            'tags' => ['nullable', 'array'],
        ]);

        if ($validator->fails()) {
            return Response::error($validator->errors()->first());
        }

        $link = CreateLink::execute($workspace, $validator->validated() + [
            'external_id' => $request->get('external_id'),
            'expires_at' => $request->get('expires_at'),
        ]);

        return Response::structured((new LinkResource($link))->resolve());
    }
}
