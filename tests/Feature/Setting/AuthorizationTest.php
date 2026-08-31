<?php

declare(strict_types=1);

use App\Enums\User\Role;
use App\Mcp\Servers\LuaServer;
use App\Mcp\Tools\Invite\CreateInviteTool;
use App\Mcp\Tools\Invite\DeleteInviteTool;
use App\Models\Invite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

beforeEach(function () {
    Mail::fake();

    $this->owner = User::factory()->withWorkspace()->create();
    $this->workspace = $this->owner->currentWorkspace;

    $this->member = User::factory()->create(['current_workspace_id' => $this->workspace->id]);
    $this->member->workspaces()->attach($this->workspace->id, ['role' => Role::ROLE_USER->value]);
});

/**
 * These were enforced in Vue and nowhere else, so a USER could do all of it by
 * sending the request the screen would not show them.
 */
it('will not let a member change anyone role', function () {
    $this->actingAs($this->member)
        ->put(route('setting.team-members.role', $this->owner->id), [
            'role' => Role::ROLE_USER->value,
        ])
        ->assertForbidden();

    expect($this->owner->fresh()->workspaces->first()->membership->role)
        ->toBe(Role::ROLE_ADMIN->value);
});

it('will not let a member promote themselves', function () {
    $this->actingAs($this->member)
        ->put(route('setting.team-members.role', $this->member->id), [
            'role' => Role::ROLE_ADMIN->value,
        ])
        ->assertForbidden();

    expect($this->member->fresh()->workspaces->first()->membership->role)
        ->toBe(Role::ROLE_USER->value);
});

it('will not let a member remove anyone', function () {
    $this->actingAs($this->member)
        ->delete(route('setting.team-members.destroy', $this->owner->id))
        ->assertForbidden();

    expect($this->owner->fresh()->workspaces)->not->toBeEmpty();
});

it('will not let anyone remove the owner', function () {
    $admin = User::factory()->create(['current_workspace_id' => $this->workspace->id]);
    $admin->workspaces()->attach($this->workspace->id, ['role' => Role::ROLE_ADMIN->value]);

    // Not even an admin: billing points at the owner, so the workspace cannot
    // be left without one.
    $this->actingAs($admin)
        ->delete(route('setting.team-members.destroy', $this->owner->id))
        ->assertForbidden();

    expect($this->owner->fresh()->workspaces)->not->toBeEmpty();
});

it('will not let anyone demote the owner', function () {
    $admin = User::factory()->create(['current_workspace_id' => $this->workspace->id]);
    $admin->workspaces()->attach($this->workspace->id, ['role' => Role::ROLE_ADMIN->value]);

    $this->actingAs($admin)
        ->put(route('setting.team-members.role', $this->owner->id), [
            'role' => Role::ROLE_USER->value,
        ])
        ->assertForbidden();
});

it('will not let a member invite anyone', function () {
    $this->actingAs($this->member)
        ->post(route('setting.invites.store'), [
            'email' => 'newcomer@example.com',
            'role' => Role::ROLE_ADMIN->value,
        ])
        ->assertForbidden();

    expect(Invite::count())->toBe(0);
});

it('will not let a member cancel an invite', function () {
    $invite = Invite::factory()->create(['workspace_id' => $this->workspace->id]);

    $this->actingAs($this->member)
        ->delete(route('setting.invites.destroy', $invite->id))
        ->assertForbidden();

    expect(Invite::find($invite->id))->not->toBeNull();
});

it('will not let a member rename the workspace', function () {
    $this->actingAs($this->member)
        ->put(route('setting.workspace.update'), ['name' => 'Renamed by a user'])
        ->assertForbidden();

    expect($this->workspace->fresh()->name)->not->toBe('Renamed by a user');
});

it('will not let a member mint an api key', function () {
    // A key acts for the whole workspace.
    $this->actingAs($this->member)
        ->post(route('setting.api-tokens.store'), ['name' => 'Backdoor'])
        ->assertForbidden();
});

it('will not let a member invite through mcp either', function () {
    LuaServer::actingAs($this->member)
        ->tool(CreateInviteTool::class, [
            'email' => 'newcomer@example.com',
            'role' => Role::ROLE_ADMIN->value,
        ])
        ->assertHasErrors();

    expect(Invite::count())->toBe(0);
});

it('will not let a member cancel an invite through mcp either', function () {
    $invite = Invite::factory()->create(['workspace_id' => $this->workspace->id]);

    LuaServer::actingAs($this->member)
        ->tool(DeleteInviteTool::class, ['id' => $invite->id])
        ->assertHasErrors();

    expect(Invite::find($invite->id))->not->toBeNull();
});

// --- what a member may still do -------------------------------------------

it('lets a member do the day to day work', function () {
    $this->actingAs($this->member)
        ->post(route('links.store'), ['url' => 'https://example.com'])
        ->assertRedirect();

    $this->actingAs($this->member)
        ->post(route('setting.tags.store'), ['name' => 'Campaign', 'color' => '#f87171'])
        ->assertRedirect()
        ->assertSessionHasNoErrors();
});

it('lets an admin run the workspace', function () {
    $admin = User::factory()->create(['current_workspace_id' => $this->workspace->id]);
    $admin->workspaces()->attach($this->workspace->id, ['role' => Role::ROLE_ADMIN->value]);

    $this->actingAs($admin)
        ->put(route('setting.workspace.update'), ['name' => 'Renamed by an admin'])
        ->assertRedirect();

    expect($this->workspace->fresh()->name)->toBe('Renamed by an admin');
});
