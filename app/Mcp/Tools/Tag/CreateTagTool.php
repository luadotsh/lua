<?php

declare(strict_types=1);

namespace App\Mcp\Tools\Tag;

use App\Actions\Tag\CreateTag;
use App\Http\Resources\Api\TagResource;
use App\Mcp\Concerns\ResolvesWorkspace;
use App\Models\Tag;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Create a tag in this workspace.')]
class CreateTagTool extends Tool
{
    use ResolvesWorkspace;

    /**
     * @return array<string, JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'name' => $schema->string()->description('The tag name.')->required(),
            'color' => $schema->string()->description('A hex colour, e.g. #22c55e.')->required(),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        $workspace = $this->workspace($request);

        if (! Gate::inspect('reached-tag-limit', $workspace)->allowed()) {
            return Response::error('This workspace has reached its tag limit.');
        }

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255', 'min:2'],
            'color' => ['required', 'string', 'regex:'.Tag::COLOR_PATTERN],
        ]);

        if ($validator->fails()) {
            return Response::error($validator->errors()->first());
        }

        return Response::structured(
            (new TagResource(CreateTag::execute($workspace, $validator->validated())))->resolve(),
        );
    }
}
