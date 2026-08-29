<?php

declare(strict_types=1);

namespace App\Mcp\Servers;

use App\Mcp\Tools\Domain\ListDomainsTool;
use App\Mcp\Tools\Link\CreateLinkTool;
use App\Mcp\Tools\Link\DeleteLinkTool;
use App\Mcp\Tools\Link\GetLinkTool;
use App\Mcp\Tools\Link\ListLinksTool;
use App\Mcp\Tools\Link\UpdateLinkTool;
use App\Mcp\Tools\Tag\ListTagsTool;
use App\Mcp\Tools\Workspace\GetWorkspaceTool;
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;

#[Name('Lua')]
#[Version('1.0.0')]
#[Instructions('Lua is a link shortener with analytics. Use this server to create, inspect and manage short links, and to read the tags and domains available in the workspace this connection is bound to.')]
class LuaServer extends Server
{
    public int $defaultPaginationLength = 100;

    protected array $tools = [
        // Links
        ListLinksTool::class,
        GetLinkTool::class,
        CreateLinkTool::class,
        UpdateLinkTool::class,
        DeleteLinkTool::class,

        // Read-only workspace metadata
        ListTagsTool::class,
        ListDomainsTool::class,
        GetWorkspaceTool::class,
    ];

    protected array $resources = [];

    protected array $prompts = [];
}
