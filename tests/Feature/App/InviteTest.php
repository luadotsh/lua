<?php

declare(strict_types=1);

use App\Actions\Invite\CreateInvite;
use App\Enums\User\Role;
use App\Mail\Team\SendUserInvite;
use App\Mcp\Servers\LuaServer;
use App\Mcp\Tools\Invite\CreateInviteTool;
use App\Mcp\Tools\Invite\DeleteInviteTool;
use App\Mcp\Tools\Invite\ListInvitesTool;
use App\Models\Invite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

beforeEach(function () {
    Mail::fake();

    $this->user = User::factory()->withWorkspace()->create();
    $this->workspace = $this->user->currentWorkspace;
});

// --- the action ------------------------------------------------------------

it('emails an invite to someone without an account', function () {
    $invite = CreateInvite::execute($this->workspace, [
        'email' => 'newcomer@example.com',
        'role' => Role::ROLE_ADMIN->value,
    ]);

    expect($invite)->not->toBeNull()
        ->and($invite->workspace_id)->toBe($this->workspace->id);

    // The mailable is ShouldQueue, so Mail::fake records it as queued.
    Mail::assertQueued(SendUserInvite::class);
});

it('adds an existing account to the workspace instead of emailing it', function () {
    $existing = User::factory()->create(['email' => 'known@example.com']);

    // No invite to accept: they already have an account, so they simply join.
    $invite = CreateInvite::execute($this->workspace, [
        'email' => 'known@example.com',
        'role' => Role::ROLE_USER->value,
    ]);

    expect($invite)->toBeNull()
        ->and($existing->fresh()->workspaces->pluck('id'))->toContain($this->workspace->id);

    Mail::assertNothingQueued();
});

it('joins an existing account with the role it was given', function () {
    $existing = User::factory()->create(['email' => 'known@example.com']);

    CreateInvite::execute($this->workspace, [
        'email' => 'known@example.com',
        'role' => Role::ROLE_USER->value,
    ]);

    expect($existing->fresh()->workspaces->first()->membership->role)
        ->toBe(Role::ROLE_USER->value);
});

// --- the web surface -------------------------------------------------------

it('creates an invite from the settings screen', function () {
    $this->actingAs($this->user)
        ->post(route('setting.invites.store'), [
            'email' => 'newcomer@example.com',
            'role' => Role::ROLE_ADMIN->value,
        ])
        ->assertRedirect();

    expect(Invite::where('email', 'newcomer@example.com')
        ->where('workspace_id', $this->workspace->id)->exists())->toBeTrue();
});

it('refuses an invite without an email', function () {
    $this->actingAs($this->user)
        ->post(route('setting.invites.store'), ['email' => '', 'role' => Role::ROLE_ADMIN->value])
        ->assertSessionHasErrors('email');
});

it('refuses a second invite to the same address', function () {
    Invite::factory()->create([
        'workspace_id' => $this->workspace->id,
        'email' => 'newcomer@example.com',
    ]);

    $this->actingAs($this->user)
        ->post(route('setting.invites.store'), [
            'email' => 'newcomer@example.com',
            'role' => Role::ROLE_ADMIN->value,
        ])
        ->assertSessionHasErrors('email');
});

it('cancels an invite from the settings screen', function () {
    $invite = Invite::factory()->create(['workspace_id' => $this->workspace->id]);

    $this->actingAs($this->user)
        ->delete(route('setting.invites.destroy', $invite->id))
        ->assertRedirect();

    expect(Invite::find($invite->id))->toBeNull();
});

it('never cancels an invite belonging to another workspace', function () {
    $other = User::factory()->withWorkspace()->create();
    $invite = Invite::factory()->create(['workspace_id' => $other->current_workspace_id]);

    $this->actingAs($this->user)
        ->delete(route('setting.invites.destroy', $invite->id))
        ->assertNotFound();

    expect(Invite::find($invite->id))->not->toBeNull();
});

// --- MCP -------------------------------------------------------------------

it('creates an invite through the tool', function () {
    LuaServer::actingAs($this->user)
        ->tool(CreateInviteTool::class, [
            'email' => 'newcomer@example.com',
            'role' => Role::ROLE_ADMIN->value,
        ])
        ->assertOk();

    expect(Invite::where('email', 'newcomer@example.com')->exists())->toBeTrue();
});

it('lists only invites belonging to the bound workspace', function () {
    Invite::factory()->create([
        'workspace_id' => $this->workspace->id,
        'email' => 'mine@example.com',
    ]);

    $other = User::factory()->withWorkspace()->create();
    Invite::factory()->create([
        'workspace_id' => $other->current_workspace_id,
        'email' => 'theirs@example.com',
    ]);

    LuaServer::actingAs($this->user)
        ->tool(ListInvitesTool::class, [])
        ->assertOk()
        ->assertSee('mine@example.com')
        ->assertDontSee('theirs@example.com');
});

it('refuses to cancel an invite from another workspace through the tool', function () {
    $other = User::factory()->withWorkspace()->create();
    $invite = Invite::factory()->create(['workspace_id' => $other->current_workspace_id]);

    LuaServer::actingAs($this->user)
        ->tool(DeleteInviteTool::class, ['id' => $invite->id])
        ->assertHasErrors();

    expect(Invite::find($invite->id))->not->toBeNull();
});
