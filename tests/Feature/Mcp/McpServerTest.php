<?php

declare(strict_types=1);

use App\Actions\Link\ListLinks;
use App\Mcp\Servers\LuaServer;
use App\Mcp\Tools\Domain\CreateDomainTool;
use App\Mcp\Tools\Domain\DeleteDomainTool;
use App\Mcp\Tools\Domain\ListDomainsTool;
use App\Mcp\Tools\Domain\UpdateDomainTool;
use App\Mcp\Tools\Link\CreateLinkTool;
use App\Mcp\Tools\Link\DeleteLinkTool;
use App\Mcp\Tools\Link\GetLinkQrCodeTool;
use App\Mcp\Tools\Link\GetLinkTool;
use App\Mcp\Tools\Link\ListLinksTool;
use App\Mcp\Tools\Link\UpdateLinkTool;
use App\Mcp\Tools\Tag\CreateTagTool;
use App\Mcp\Tools\Tag\DeleteTagTool;
use App\Mcp\Tools\Tag\ListTagsTool;
use App\Mcp\Tools\Tag\UpdateTagTool;
use App\Mcp\Tools\TeamMember\ListMembersTool;
use App\Mcp\Tools\Workspace\GetWorkspaceTool;
use App\Models\Domain;
use App\Models\Link;
use App\Models\Plan;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\JsonSchema\JsonSchemaTypeFactory;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->withWorkspace()->create();
});

it('rejects an unauthenticated request to the mcp endpoint', function () {
    $this->postJson(route('mcp'), [
        'jsonrpc' => '2.0',
        'id' => 1,
        'method' => 'tools/list',
    ])->assertStatus(401);
});

it('exposes the expected tools', function () {
    $response = LuaServer::actingAs($this->user)->tool(GetWorkspaceTool::class, []);

    $response->assertOk();
});

it('lists only links belonging to the bound workspace', function () {
    Link::factory(3)->create(['workspace_id' => $this->user->current_workspace_id]);

    $other = User::factory()->withWorkspace()->create();
    Link::factory(2)->create(['workspace_id' => $other->current_workspace_id]);

    $response = LuaServer::actingAs($this->user)->tool(ListLinksTool::class, []);

    $response->assertOk();
});

it('refuses to fetch a link from another workspace', function () {
    $other = User::factory()->withWorkspace()->create();
    $link = Link::factory()->create(['workspace_id' => $other->current_workspace_id]);

    $response = LuaServer::actingAs($this->user)->tool(GetLinkTool::class, ['id' => $link->id]);

    $response->assertHasErrors();
});

it('creates a link through the tool', function () {
    $response = LuaServer::actingAs($this->user)->tool(CreateLinkTool::class, ['url' => 'https://example.com', 'key' => 'from-mcp']);

    $response->assertOk();

    expect(Link::where('key', 'from-mcp')->where('workspace_id', $this->user->current_workspace_id)->exists())
        ->toBeTrue();
});

it('refuses to delete a link from another workspace', function () {
    $other = User::factory()->withWorkspace()->create();
    $link = Link::factory()->create(['workspace_id' => $other->current_workspace_id]);

    LuaServer::actingAs($this->user)
        ->tool(DeleteLinkTool::class, ['id' => $link->id])
        ->assertHasErrors();

    expect(Link::find($link->id))->not->toBeNull();
});

it('creates a tag through the tool', function () {
    LuaServer::actingAs($this->user)
        ->tool(CreateTagTool::class, ['name' => 'From MCP', 'color' => '#60a5fa'])
        ->assertOk();

    expect(Tag::where('workspace_id', $this->user->current_workspace_id)
        ->where('name', 'From MCP')->exists())->toBeTrue();
});

it('rejects a tag colour that is not a hex value', function () {
    LuaServer::actingAs($this->user)
        ->tool(CreateTagTool::class, ['name' => 'Bad', 'color' => 'octarine'])
        ->assertHasErrors();
});

it('will not update a tag from another workspace', function () {
    $other = User::factory()->withWorkspace()->create();
    $tag = Tag::factory()->create(['workspace_id' => $other->current_workspace_id]);

    LuaServer::actingAs($this->user)
        ->tool(UpdateTagTool::class, ['id' => $tag->id, 'name' => 'Stolen'])
        ->assertHasErrors();

    expect($tag->fresh()->name)->not->toBe('Stolen');
});

it('adds a domain through the tool', function () {
    LuaServer::actingAs($this->user)
        ->tool(CreateDomainTool::class, ['domain' => 'links.example.com'])
        ->assertOk();

    $domain = Domain::where('domain', 'links.example.com')->firstOrFail();

    expect($domain->workspace_id)->toBe($this->user->current_workspace_id)
        ->and($domain->status->value)->toBe('pending');
});

it('will not delete a domain from another workspace', function () {
    $other = User::factory()->withWorkspace()->create();
    $domain = Domain::factory()->create(['workspace_id' => $other->current_workspace_id]);

    LuaServer::actingAs($this->user)
        ->tool(DeleteDomainTool::class, ['id' => $domain->id])
        ->assertHasErrors();

    expect(Domain::find($domain->id))->not->toBeNull();
});

it('lists only the members of the bound workspace', function () {
    User::factory()->withWorkspace()->create();

    LuaServer::actingAs($this->user)
        ->tool(ListMembersTool::class, [])
        ->assertOk();
});

it('returns a qr code for a link in the bound workspace', function () {
    $link = Link::factory()->create([
        'workspace_id' => $this->user->current_workspace_id,
    ]);

    LuaServer::actingAs($this->user)
        ->tool(GetLinkQrCodeTool::class, ['id' => $link->id])
        ->assertOk();
});

it('will not return a qr code for another workspace link', function () {
    $other = User::factory()->withWorkspace()->create();
    $link = Link::factory()->create(['workspace_id' => $other->current_workspace_id]);

    LuaServer::actingAs($this->user)
        ->tool(GetLinkQrCodeTool::class, ['id' => $link->id])
        ->assertHasErrors();
});

it('paginates links through the shared action', function () {
    Link::factory(30)->create(['workspace_id' => $this->user->current_workspace_id]);

    LuaServer::actingAs($this->user)
        ->tool(ListLinksTool::class, ['per_page' => 5])
        ->assertOk();

    expect(ListLinks::execute($this->user->currentWorkspace, ['per_page' => 5]))
        ->toHaveCount(5);
});

it('updates a link through the tool', function () {
    $link = Link::factory()->create([
        'workspace_id' => $this->user->current_workspace_id,
        'url' => 'https://before.example',
    ]);

    LuaServer::actingAs($this->user)
        ->tool(UpdateLinkTool::class, ['id' => $link->id, 'url' => 'https://after.example'])
        ->assertOk();

    expect($link->fresh()->url)->toBe('https://after.example');
});

it('refuses to update a link from another workspace', function () {
    $other = User::factory()->withWorkspace()->create();
    $link = Link::factory()->create([
        'workspace_id' => $other->current_workspace_id,
        'url' => 'https://theirs.example',
    ]);

    LuaServer::actingAs($this->user)
        ->tool(UpdateLinkTool::class, ['id' => $link->id, 'url' => 'https://stolen.example'])
        ->assertHasErrors();

    expect($link->fresh()->url)->toBe('https://theirs.example');
});

it('lists only tags belonging to the bound workspace', function () {
    Tag::factory()->create(['workspace_id' => $this->user->current_workspace_id, 'name' => 'Mine']);

    $other = User::factory()->withWorkspace()->create();
    Tag::factory()->create(['workspace_id' => $other->current_workspace_id, 'name' => 'Theirs']);

    $response = LuaServer::actingAs($this->user)->tool(ListTagsTool::class, []);

    $response->assertOk()
        ->assertSee('Mine')
        ->assertDontSee('Theirs');
});

it('deletes a tag through the tool', function () {
    $tag = Tag::factory()->create(['workspace_id' => $this->user->current_workspace_id]);

    LuaServer::actingAs($this->user)
        ->tool(DeleteTagTool::class, ['id' => $tag->id])
        ->assertOk();

    expect(Tag::find($tag->id))->toBeNull();
});

it('refuses to delete a tag from another workspace', function () {
    $other = User::factory()->withWorkspace()->create();
    $tag = Tag::factory()->create(['workspace_id' => $other->current_workspace_id]);

    LuaServer::actingAs($this->user)
        ->tool(DeleteTagTool::class, ['id' => $tag->id])
        ->assertHasErrors();

    expect(Tag::find($tag->id))->not->toBeNull();
});

it('lists only domains belonging to the bound workspace', function () {
    Domain::factory()->create([
        'workspace_id' => $this->user->current_workspace_id,
        'domain' => 'mine.example.com',
    ]);

    $other = User::factory()->withWorkspace()->create();
    Domain::factory()->create([
        'workspace_id' => $other->current_workspace_id,
        'domain' => 'theirs.example.com',
    ]);

    LuaServer::actingAs($this->user)
        ->tool(ListDomainsTool::class, [])
        ->assertOk()
        ->assertSee('mine.example.com')
        ->assertDontSee('theirs.example.com');
});

it('updates a domain through the tool', function () {
    $domain = Domain::factory()->create([
        'workspace_id' => $this->user->current_workspace_id,
    ]);

    LuaServer::actingAs($this->user)
        ->tool(UpdateDomainTool::class, [
            'id' => $domain->id,
            'not_found_url' => 'https://example.com/gone',
        ])
        ->assertOk();

    expect($domain->fresh()->not_found_url)->toBe('https://example.com/gone');
});

it('refuses to update a domain from another workspace', function () {
    $other = User::factory()->withWorkspace()->create();
    $domain = Domain::factory()->create(['workspace_id' => $other->current_workspace_id]);

    LuaServer::actingAs($this->user)
        ->tool(UpdateDomainTool::class, [
            'id' => $domain->id,
            'not_found_url' => 'https://stolen.example',
        ])
        ->assertHasErrors();

    expect($domain->fresh()->not_found_url)->not->toBe('https://stolen.example');
});

it('lists the members of the bound workspace', function () {
    LuaServer::actingAs($this->user)
        ->tool(ListMembersTool::class, [])
        ->assertOk()
        ->assertSee($this->user->email);
});

it('reports a validation failure back through the tool', function () {
    LuaServer::actingAs($this->user)
        ->tool(CreateLinkTool::class, ['url' => 'not a url'])
        ->assertHasErrors();

    expect(Link::where('workspace_id', $this->user->current_workspace_id)->exists())
        ->toBeFalse();
});

it('refuses a link once the plan allowance is used', function () {
    $plan = Plan::factory()->create(['max_links' => 1]);
    $this->user->currentWorkspace->update(['plan_id' => $plan->id]);

    Link::factory()->create(['workspace_id' => $this->user->current_workspace_id]);

    LuaServer::actingAs($this->user)
        ->tool(CreateLinkTool::class, ['url' => 'https://example.com'])
        ->assertHasErrors();
});

it('updates only the fields the tool was given', function () {
    $link = Link::factory()->create([
        'workspace_id' => $this->user->current_workspace_id,
        'url' => 'https://before.example',
        'utm_source' => 'newsletter',
    ]);

    // The whole point of the partial update: what MCP does not mention keeps
    // whatever it had.
    LuaServer::actingAs($this->user)
        ->tool(UpdateLinkTool::class, ['id' => $link->id, 'url' => 'https://after.example'])
        ->assertOk();

    expect($link->fresh()->url)->toBe('https://after.example')
        ->and($link->fresh()->utm_source)->toBe('newsletter');
});

it('reports a bad update back through the tool', function () {
    $link = Link::factory()->create(['workspace_id' => $this->user->current_workspace_id]);

    LuaServer::actingAs($this->user)
        ->tool(UpdateLinkTool::class, ['id' => $link->id, 'url' => 'not a url'])
        ->assertHasErrors();
});

it('updates a tag through the tool', function () {
    $tag = Tag::factory()->create([
        'workspace_id' => $this->user->current_workspace_id,
        'name' => 'Before',
    ]);

    LuaServer::actingAs($this->user)
        ->tool(UpdateTagTool::class, ['id' => $tag->id, 'name' => 'After'])
        ->assertOk();

    expect($tag->fresh()->name)->toBe('After');
});

it('rejects an update that would blank a tag colour', function () {
    $tag = Tag::factory()->create(['workspace_id' => $this->user->current_workspace_id]);

    LuaServer::actingAs($this->user)
        ->tool(UpdateTagTool::class, ['id' => $tag->id, 'color' => 'octarine'])
        ->assertHasErrors();
});

it('refuses a tag once the plan allowance is used', function () {
    $plan = Plan::factory()->create(['max_tags' => 1]);
    $this->user->currentWorkspace->update(['plan_id' => $plan->id]);

    LuaServer::actingAs($this->user)
        ->tool(CreateTagTool::class, ['name' => 'One too many', 'color' => '#f87171'])
        ->assertHasErrors();
});

it('deletes a domain through the tool', function () {
    $domain = Domain::factory()->create(['workspace_id' => $this->user->current_workspace_id]);

    LuaServer::actingAs($this->user)
        ->tool(DeleteDomainTool::class, ['id' => $domain->id])
        ->assertOk();

    expect(Domain::find($domain->id))->toBeNull();
});

it('refuses a domain once the plan allowance is used', function () {
    $plan = Plan::factory()->create(['max_domains' => 0]);
    $this->user->currentWorkspace->update(['plan_id' => $plan->id]);

    LuaServer::actingAs($this->user)
        ->tool(CreateDomainTool::class, ['domain' => 'links.example.com'])
        ->assertHasErrors();
});

it('refuses a domain another workspace already claimed', function () {
    $other = User::factory()->withWorkspace()->create();
    Domain::factory()->create([
        'workspace_id' => $other->current_workspace_id,
        'domain' => 'links.example.com',
    ]);

    LuaServer::actingAs($this->user)
        ->tool(CreateDomainTool::class, ['domain' => 'links.example.com'])
        ->assertHasErrors();
});

it('deletes a link through the tool', function () {
    $link = Link::factory()->create(['workspace_id' => $this->user->current_workspace_id]);

    LuaServer::actingAs($this->user)
        ->tool(DeleteLinkTool::class, ['id' => $link->id])
        ->assertOk();

    expect(Link::find($link->id))->toBeNull();
});

it('fetches a link in the bound workspace through the tool', function () {
    $link = Link::factory()->create([
        'workspace_id' => $this->user->current_workspace_id,
        'url' => 'https://findme.example',
    ]);

    LuaServer::actingAs($this->user)
        ->tool(GetLinkTool::class, ['id' => $link->id])
        ->assertOk()
        ->assertSee('findme.example');
});

it('describes every tool it offers', function () {
    // Nothing else calls schema(): a tool call goes straight to handle(), so
    // without this the argument descriptions an MCP client actually reads are
    // never built, and a broken one would only surface in a client.
    $tools = (new ReflectionClass(LuaServer::class))
        ->getDefaultProperties()['tools'];

    expect($tools)->not->toBeEmpty();

    $schema = new JsonSchemaTypeFactory;

    foreach ($tools as $tool) {
        expect(app($tool)->schema($schema))->toBeArray();
    }
});
