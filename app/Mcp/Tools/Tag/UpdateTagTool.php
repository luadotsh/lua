<?php

declare(strict_types=1);

namespace App\Mcp\Tools\Tag;

use App\Actions\Tag\GetTag;
use App\Actions\Tag\UpdateTag;
use App\Enums\Tag\Color;
use App\Http\Resources\Api\TagResource;
use App\Mcp\Concerns\ResolvesWorkspace;
use App\Models\Tag;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Enum;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Update a tag in this workspace. Only the fields you pass are changed.')]
class UpdateTagTool extends Tool
{
    use ResolvesWorkspace;

    /**
     * @return array<string, JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->string()->description('The tag id.')->required(),
            'name' => $schema->string()->description('The tag name.'),
            'color' => $schema->string()->description('One of: red, orange, yellow, green, cyan, teal, blue, indigo, purple, fuchsia, pink, zinc.'),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        $tag = GetTag::execute($this->workspace($request), (string) $request->get('id'));

        if (! $tag) {
            return Response::error('Tag not found in this workspace.');
        }

        $data = collect($request->all())->except('id')->all();

        $validator = Validator::make($data, [
            'name' => ['sometimes', 'string', 'max:255', 'min:2'],
            'color' => ['sometimes', 'string', new Enum(Color::class)],
        ]);

        if ($validator->fails()) {
            return Response::error($validator->errors()->first());
        }

        return Response::structured(
            (new TagResource(UpdateTag::execute($tag, $data)))->resolve(),
        );
    }
}
