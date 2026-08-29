<?php

declare(strict_types=1);

namespace App\Mcp\Servers;

use App\Mcp\Tools\Domain\CreateDomainTool;
use App\Mcp\Tools\Domain\DeleteDomainTool;
use App\Mcp\Tools\Domain\ListDomainsTool;
use App\Mcp\Tools\Domain\UpdateDomainTool;
use App\Mcp\Tools\Invite\CreateInviteTool;
use App\Mcp\Tools\Invite\DeleteInviteTool;
use App\Mcp\Tools\Invite\ListInvitesTool;
use App\Mcp\Tools\Link\CreateLinkTool;
use App\Mcp\Tools\Link\DeleteLinkTool;
use App\Mcp\Tools\Link\GetLinkTool;
use App\Mcp\Tools\Link\ListLinksTool;
use App\Mcp\Tools\Link\UpdateLinkTool;
use App\Mcp\Tools\Tag\CreateTagTool;
use App\Mcp\Tools\Tag\DeleteTagTool;
use App\Mcp\Tools\Tag\ListTagsTool;
use App\Mcp\Tools\Tag\UpdateTagTool;
use App\Mcp\Tools\TeamMember\ListMembersTool;
use App\Mcp\Tools\Workspace\GetWorkspaceTool;
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;

#[Name('Lua')]
#[Version('1.0.0')]
#[Instructions('Lua is a link shortener with analytics. Use this server to manage short links, tags, custom domains and team invites in the workspace this connection is bound to.')]
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

        // Tags
        ListTagsTool::class,
        CreateTagTool::class,
        UpdateTagTool::class,
        DeleteTagTool::class,

        // Domains
        ListDomainsTool::class,
        CreateDomainTool::class,
        UpdateDomainTool::class,
        DeleteDomainTool::class,

        // Team
        ListMembersTool::class,
        ListInvitesTool::class,
        CreateInviteTool::class,
        DeleteInviteTool::class,

        // Workspace
        GetWorkspaceTool::class,
    ];

    protected array $resources = [];

    protected array $prompts = [];
}
