<?php

declare(strict_types=1);

use App\Actions\TeamMember\LeaveWorkspace;
use App\Actions\TeamMember\RemoveMember;
use App\Actions\TeamMember\UpdateMemberRole;
use App\Enums\User\Role;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->owner = User::factory()->withWorkspace()->create();
    $this->workspace = $this->owner->currentWorkspace;
});

/** withWorkspace() always attaches as OWNER, so a teammate is attached by hand. */
function joinWorkspace(Workspace $workspace, string $role = Role::ROLE_USER->value): User
{
    $member = User::factory()->create(['current_workspace_id' => $workspace->id]);
    $member->workspaces()->attach($workspace->id, ['role' => $role]);

    return $member;
}

it('moves a removed member off the workspace they were removed from', function () {
    $member = joinWorkspace($this->workspace);

    RemoveMember::execute($this->workspace, $member);

    // Detaching alone left current_workspace_id pointing at the workspace, and
    // every controller trusts currentWorkspace — so they kept reading its data.
    expect($member->fresh()->current_workspace_id)->toBeNull()
        ->and($member->fresh()->workspaces)->toBeEmpty();
});

it('moves a removed member to a workspace they still belong to', function () {
    $member = joinWorkspace($this->workspace);

    $elsewhere = Workspace::factory()->create();
    $member->workspaces()->attach($elsewhere->id, ['role' => Role::ROLE_USER->value]);

    RemoveMember::execute($this->workspace, $member);

    expect($member->fresh()->current_workspace_id)->toBe($elsewhere->id);
});

it('leaves the current workspace alone when removing someone from another one', function () {
    $member = joinWorkspace($this->workspace);

    $elsewhere = Workspace::factory()->create();
    $member->workspaces()->attach($elsewhere->id, ['role' => Role::ROLE_USER->value]);
    $member->forceFill(['current_workspace_id' => $this->workspace->id])->save();

    RemoveMember::execute($elsewhere, $member);

    expect($member->fresh()->current_workspace_id)->toBe($this->workspace->id);
});

it('changes a member role', function () {
    $member = joinWorkspace($this->workspace);

    UpdateMemberRole::execute($this->workspace, $member, Role::ROLE_ADMIN->value);

    expect($member->fresh()->workspaces->first()->membership->role)
        ->toBe(Role::ROLE_ADMIN->value);
});

it('refuses to leave a workspace with nobody else in it', function () {
    expect(LeaveWorkspace::isLastMember($this->workspace))->toBeTrue();

    joinWorkspace($this->workspace);

    expect(LeaveWorkspace::isLastMember($this->workspace->fresh()))->toBeFalse();
});

it('switches you to another workspace when you leave one', function () {
    $elsewhere = Workspace::factory()->create();
    $this->owner->workspaces()->attach($elsewhere->id, ['role' => Role::ROLE_ADMIN->value]);

    $landed = LeaveWorkspace::execute($this->owner, $this->workspace);

    expect($landed?->id)->toBe($elsewhere->id)
        ->and($this->owner->fresh()->current_workspace_id)->toBe($elsewhere->id);
});

it('leaves you without a workspace when it was your only one', function () {
    $landed = LeaveWorkspace::execute($this->owner, $this->workspace);

    expect($landed)->toBeNull()
        ->and($this->owner->fresh()->current_workspace_id)->toBeNull();
});

// --- the web surface -------------------------------------------------------

it('changes a member role from the settings screen', function () {
    $member = joinWorkspace($this->workspace);

    $this->actingAs($this->owner)
        ->put(route('setting.team-members.role', $member->id), [
            'role' => Role::ROLE_ADMIN->value,
        ])
        ->assertRedirect();

    expect($member->fresh()->workspaces->first()->membership->role)
        ->toBe(Role::ROLE_ADMIN->value);
});

it('refuses a role that is not one of ours', function () {
    $member = joinWorkspace($this->workspace);

    $this->actingAs($this->owner)
        ->put(route('setting.team-members.role', $member->id), ['role' => 'EMPEROR'])
        ->assertSessionHasErrors('role');
});

it('never changes the role of someone outside the workspace', function () {
    $stranger = User::factory()->withWorkspace()->create();

    $this->actingAs($this->owner)
        ->put(route('setting.team-members.role', $stranger->id), [
            'role' => Role::ROLE_ADMIN->value,
        ])
        ->assertNotFound();
});

it('removes a member from the settings screen', function () {
    $member = joinWorkspace($this->workspace);

    $this->actingAs($this->owner)
        ->delete(route('setting.team-members.destroy', $member->id))
        ->assertRedirect();

    expect($member->fresh()->workspaces)->toBeEmpty();
});

it('never removes someone who is not in the workspace', function () {
    $stranger = User::factory()->withWorkspace()->create();

    $this->actingAs($this->owner)
        ->delete(route('setting.team-members.destroy', $stranger->id))
        ->assertNotFound();

    expect($stranger->fresh()->workspaces)->not->toBeEmpty();
});

it('refuses to let the only member leave', function () {
    $this->actingAs($this->owner)
        ->delete(route('setting.team-members.leave'))
        ->assertRedirect();

    // A workspace cannot be left with nobody in it.
    expect($this->owner->fresh()->current_workspace_id)->toBe($this->workspace->id);
});

it('sends you to your next workspace after leaving one', function () {
    $member = joinWorkspace($this->workspace);

    $elsewhere = Workspace::factory()->create(['owner_id' => $member->id]);
    $member->workspaces()->attach($elsewhere->id, ['role' => Role::ROLE_ADMIN->value]);

    $this->actingAs($member)
        ->delete(route('setting.team-members.leave'))
        ->assertRedirect(route('links.index'));

    expect($member->fresh()->current_workspace_id)->toBe($elsewhere->id);
});

it('sends you to create one after leaving your last workspace', function () {
    $member = joinWorkspace($this->workspace);

    $this->actingAs($member)
        ->delete(route('setting.team-members.leave'))
        ->assertRedirect(route('workspaces.create'));

    expect($member->fresh()->current_workspace_id)->toBeNull();
});

it('will not let the owner walk away from their own workspace', function () {
    joinWorkspace($this->workspace);

    // Billing and stripeEmail() point at the owner; the workspace cannot be
    // left without one.
    $this->actingAs($this->owner)
        ->delete(route('setting.team-members.leave'))
        ->assertRedirect();

    expect($this->owner->fresh()->current_workspace_id)->toBe($this->workspace->id);
});
