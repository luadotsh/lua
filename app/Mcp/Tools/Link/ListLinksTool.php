<?php

declare(strict_types=1);

namespace App\Mcp\Tools\Link;

use App\Http\Resources\Api\LinkResource;
use App\Mcp\Concerns\ResolvesWorkspace;
use App\Models\Link;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly]
#[Description('List the short links in this workspace, newest first. Optionally filter by a search term matching the destination URL or the key.')]
class ListLinksTool extends Tool
{
    use ResolvesWorkspace;

    /**
     * @return array<string, JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'search' => $schema->string()
                ->description('Filter links whose destination URL or key contains this text.'),
            'per_page' => $schema->integer()
                ->description('How many links to return (1-100). Defaults to 25.'),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        $search = $request->get('search');
        $perPage = min(max((int) ($request->get('per_page') ?: 25), 1), 100);

        $links = Link::where('workspace_id', $this->workspace($request)->id)
            ->when($search, fn ($query) => $query->where(
                fn ($q) => $q->where('url', 'like', "%{$search}%")
                    ->orWhere('key', 'like', "%{$search}%"),
            ))
            ->with('tags')
            ->latest()
            ->paginate($perPage);

        return Response::structured([
            'data' => LinkResource::collection($links->items())->resolve(),
            'total' => $links->total(),
            'per_page' => $links->perPage(),
            'current_page' => $links->currentPage(),
        ]);
    }
}
