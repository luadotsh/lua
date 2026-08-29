<?php

declare(strict_types=1);

use App\Mcp\Servers\LuaServer;
use App\Models\Link;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
    $response = LuaServer::actingAs($this->user)->tool(App\Mcp\Tools\Workspace\GetWorkspaceTool::class, []);

    $response->assertOk();
});

it('lists only links belonging to the bound workspace', function () {
    Link::factory(3)->create(['workspace_id' => $this->user->current_workspace_id]);

    $other = User::factory()->withWorkspace()->create();
    Link::factory(2)->create(['workspace_id' => $other->current_workspace_id]);

    $response = LuaServer::actingAs($this->user)->tool(App\Mcp\Tools\Link\ListLinksTool::class, []);

    $response->assertOk();
});

it('refuses to fetch a link from another workspace', function () {
    $other = User::factory()->withWorkspace()->create();
    $link = Link::factory()->create(['workspace_id' => $other->current_workspace_id]);

    $response = LuaServer::actingAs($this->user)->tool(App\Mcp\Tools\Link\GetLinkTool::class, ['id' => $link->id]);

    $response->assertHasErrors();
});

it('creates a link through the tool', function () {
    $response = LuaServer::actingAs($this->user)->tool(App\Mcp\Tools\Link\CreateLinkTool::class, ['url' => 'https://example.com', 'key' => 'from-mcp']);

    $response->assertOk();

    expect(Link::where('key', 'from-mcp')->where('workspace_id', $this->user->current_workspace_id)->exists())
        ->toBeTrue();
});

it('refuses to delete a link from another workspace', function () {
    $other = User::factory()->withWorkspace()->create();
    $link = Link::factory()->create(['workspace_id' => $other->current_workspace_id]);

    LuaServer::actingAs($this->user)
        ->tool(App\Mcp\Tools\Link\DeleteLinkTool::class, ['id' => $link->id])
        ->assertHasErrors();

    expect(Link::find($link->id))->not->toBeNull();
});

it('creates a tag through the tool', function () {
    LuaServer::actingAs($this->user)
        ->tool(App\Mcp\Tools\Tag\CreateTagTool::class, ['name' => 'From MCP', 'color' => 'blue'])
        ->assertOk();

    expect(App\Models\Tag::where('workspace_id', $this->user->current_workspace_id)
        ->where('name', 'From MCP')->exists())->toBeTrue();
});

it('rejects a tag colour outside the enum', function () {
    LuaServer::actingAs($this->user)
        ->tool(App\Mcp\Tools\Tag\CreateTagTool::class, ['name' => 'Bad', 'color' => 'octarine'])
        ->assertHasErrors();
});

it('will not update a tag from another workspace', function () {
    $other = App\Models\User::factory()->withWorkspace()->create();
    $tag = App\Models\Tag::factory()->create(['workspace_id' => $other->current_workspace_id]);

    LuaServer::actingAs($this->user)
        ->tool(App\Mcp\Tools\Tag\UpdateTagTool::class, ['id' => $tag->id, 'name' => 'Stolen'])
        ->assertHasErrors();

    expect($tag->fresh()->name)->not->toBe('Stolen');
});

it('adds a domain through the tool', function () {
    LuaServer::actingAs($this->user)
        ->tool(App\Mcp\Tools\Domain\CreateDomainTool::class, ['domain' => 'links.example.com'])
        ->assertOk();

    $domain = App\Models\Domain::where('domain', 'links.example.com')->firstOrFail();

    expect($domain->workspace_id)->toBe($this->user->current_workspace_id)
        ->and($domain->status->value)->toBe('pending');
});

it('will not delete a domain from another workspace', function () {
    $other = App\Models\User::factory()->withWorkspace()->create();
    $domain = App\Models\Domain::factory()->create(['workspace_id' => $other->current_workspace_id]);

    LuaServer::actingAs($this->user)
        ->tool(App\Mcp\Tools\Domain\DeleteDomainTool::class, ['id' => $domain->id])
        ->assertHasErrors();

    expect(App\Models\Domain::find($domain->id))->not->toBeNull();
});

it('lists only the members of the bound workspace', function () {
    App\Models\User::factory()->withWorkspace()->create();

    LuaServer::actingAs($this->user)
        ->tool(App\Mcp\Tools\TeamMember\ListMembersTool::class, [])
        ->assertOk();
});
