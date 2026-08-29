<?php

declare(strict_types=1);

namespace App\Mcp\Tools\Domain;

use App\Actions\Domain\GetDomain;
use App\Actions\Domain\UpdateDomain;
use App\Http\Resources\Api\DomainResource;
use App\Mcp\Concerns\ResolvesWorkspace;
use App\Models\Domain;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Update a custom domain. Changing the hostname sends it back to pending until DNS is verified again.')]
class UpdateDomainTool extends Tool
{
    use ResolvesWorkspace;

    /**
     * @return array<string, JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->string()->description('The domain id.')->required(),
            'domain' => $schema->string()->description('The hostname.'),
            'not_found_url' => $schema->string()->description('Where to send visitors when a short link does not exist.'),
            'expired_url' => $schema->string()->description('Where to send visitors when a link has expired.'),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        $domain = GetDomain::execute($this->workspace($request), (string) $request->get('id'));

        if (! $domain) {
            return Response::error('Domain not found in this workspace.');
        }

        $data = collect($request->all())->except('id')->all();

        $validator = Validator::make($data, [
            'domain' => ['sometimes', 'string', 'max:255', Rule::unique(Domain::class, 'domain')->ignore($domain->id)],
            'not_found_url' => ['nullable', 'url', 'max:255'],
            'expired_url' => ['nullable', 'url', 'max:255'],
        ]);

        if ($validator->fails()) {
            return Response::error($validator->errors()->first());
        }

        return Response::structured(
            (new DomainResource(UpdateDomain::execute($domain, $data)))->resolve(),
        );
    }
}
