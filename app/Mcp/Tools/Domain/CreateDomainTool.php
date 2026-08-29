<?php

declare(strict_types=1);

namespace App\Mcp\Tools\Domain;

use App\Actions\Domain\CreateDomain;
use App\Http\Resources\Api\DomainResource;
use App\Mcp\Concerns\ResolvesWorkspace;
use App\Models\Domain;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Add a custom domain to this workspace. It starts pending until its DNS is verified.')]
class CreateDomainTool extends Tool
{
    use ResolvesWorkspace;

    /**
     * @return array<string, JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'domain' => $schema->string()->description('The hostname, e.g. links.example.com.')->required(),
            'not_found_url' => $schema->string()->description('Where to send visitors when a short link does not exist.'),
            'expired_url' => $schema->string()->description('Where to send visitors when a link has expired.'),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        $workspace = $this->workspace($request);

        if (! Gate::inspect('reached-domain-limit', $workspace)->allowed()) {
            return Response::error('This workspace has reached its domain limit.');
        }

        $validator = Validator::make($request->all(), [
            'domain' => ['required', 'string', 'max:255', Rule::unique(Domain::class, 'domain')],
            'not_found_url' => ['nullable', 'url', 'max:255'],
            'expired_url' => ['nullable', 'url', 'max:255'],
        ]);

        if ($validator->fails()) {
            return Response::error($validator->errors()->first());
        }

        return Response::structured(
            (new DomainResource(CreateDomain::execute($workspace, $validator->validated())))->resolve(),
        );
    }
}
