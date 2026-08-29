<?php

declare(strict_types=1);

namespace App\Mcp\Tools\Link;

use App\Actions\Link\CreateLink;
use App\Actions\Link\GetLink;
use App\Actions\Link\UpdateLink;
use App\Http\Resources\Api\LinkResource;
use App\Mcp\Concerns\ResolvesWorkspace;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Validator;
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
            'utm_source' => $schema->string()->description('utm_source appended to the destination URL.'),
            'utm_medium' => $schema->string()->description('utm_medium appended to the destination URL.'),
            'utm_campaign' => $schema->string()->description('utm_campaign appended to the destination URL.'),
            'utm_term' => $schema->string()->description('utm_term appended to the destination URL.'),
            'utm_content' => $schema->string()->description('utm_content appended to the destination URL.'),
            'tags' => $schema->array()->description('Tag ids to attach, replacing the current ones.'),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        $workspace = $this->workspace($request);

        $link = GetLink::execute($workspace, (string) $request->get('id'));

        if (! $link) {
            return Response::error('Link not found in this workspace.');
        }

        $data = collect($request->all())->except('id')->all();
        $data['domain'] = data_get($data, 'domain', $link->domain);
        $data['url'] = data_get($data, 'url', $link->url);

        $validator = Validator::make($data, CreateLink::rules($workspace, $data, $link->id));

        if ($validator->fails()) {
            return Response::error($validator->errors()->first());
        }

        return Response::structured(
            (new LinkResource(UpdateLink::execute($link, $data)))->resolve(),
        );
    }
}
