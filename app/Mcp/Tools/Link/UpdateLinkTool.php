<?php

declare(strict_types=1);

namespace App\Mcp\Tools\Link;

use App\Actions\Link\UpdateLink;
use App\Http\Resources\Api\LinkResource;
use App\Mcp\Concerns\ResolvesWorkspace;
use App\Models\Link;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Update a short link in this workspace. Only the fields you pass are changed.')]
class UpdateLinkTool extends Tool
{
    use ResolvesWorkspace;

    /**
     * @return array<string, JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->string()->description('The link id.')->required(),
            'url' => $schema->string()->description('The destination URL.'),
            'key' => $schema->string()->description('The short link slug.'),
            'domain' => $schema->string()->description('Which of the workspace domains to use.'),
            'ios' => $schema->string()->description('Alternative destination for iOS visitors.'),
            'android' => $schema->string()->description('Alternative destination for Android visitors.'),
            'expires_at' => $schema->string()->description('Expiry as Y-m-d H:i:s.'),
            'expired_redirect_url' => $schema->string()->description('Where to send visitors after expiry.'),
            'external_id' => $schema->string()->description('Your own identifier for this link.'),
            'tags' => $schema->array()->description('Tag ids to attach, replacing the current ones.'),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        $workspace = $this->workspace($request);

        $link = Link::where('workspace_id', $workspace->id)->find($request->get('id'));

        if (! $link) {
            return Response::error('Link not found in this workspace.');
        }

        $domains = array_merge(
            $workspace->domains->pluck('domain')->toArray(),
            config('domains.available'),
        );

        $data = collect($request->all())->except('id')->all();

        $validator = Validator::make($data, [
            'url' => ['sometimes', 'url', 'max:255', 'min:2'],
            'domain' => ['sometimes', 'string', Rule::in($domains)],
            'key' => [
                'sometimes', 'string', 'max:255', 'regex:/^[a-z0-9-]+$/',
                Rule::unique('links')
                    ->where('domain', data_get($data, 'domain', $link->domain))
                    ->ignore($link->id),
            ],
            'ios' => ['nullable', 'url', 'max:255'],
            'android' => ['nullable', 'url', 'max:255'],
            'expired_redirect_url' => ['nullable', 'url', 'max:255'],
            'tags' => ['nullable', 'array'],
        ]);

        if ($validator->fails()) {
            return Response::error($validator->errors()->first());
        }

        return Response::structured(
            (new LinkResource(UpdateLink::execute($link, $data)))->resolve(),
        );
    }
}
