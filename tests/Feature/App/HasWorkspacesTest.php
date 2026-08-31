<?php

declare(strict_types=1);

use App\Enums\User\Role;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->withWorkspace()->create();
    $this->workspace = $this->user->currentWorkspace;
});

it('knows which workspace is the current one', function () {
    $elsewhere = Workspace::factory()->create();

    expect($this->user->isCurrentWorkspace($this->workspace))->toBeTrue()
        ->and($this->user->isCurrentWorkspace($elsewhere))->toBeFalse();
});

it('lists every workspace by name', function () {
    $this->workspace->update(['name' => 'Zebra']);
    $first = Workspace::factory()->create(['name' => 'Aardvark']);
    $this->user->workspaces()->attach($first->id, ['role' => Role::ROLE_USER->value]);

    expect($this->user->fresh()->allWorkspaces()->pluck('name')->values()->all())
        ->toBe(['Aardvark', 'Zebra']);
});

it('knows whether it belongs to a workspace', function () {
    expect($this->user->belongsToWorkspace($this->workspace))->toBeTrue()
        ->and($this->user->belongsToWorkspace(Workspace::factory()->create()))->toBeFalse();
});

it('reads the role it holds in a workspace', function () {
    expect($this->user->workspaceRole($this->workspace))->toBe(Role::ROLE_OWNER->value);
});

it('has no role in a workspace it does not belong to', function () {
    expect($this->user->workspaceRole(Workspace::factory()->create()))->toBeNull();
});

it('knows whether it owns a workspace', function () {
    $member = User::factory()->create();
    $member->workspaces()->attach($this->workspace->id, ['role' => Role::ROLE_USER->value]);

    expect($this->user->ownsWorkspace($this->workspace))->toBeTrue()
        ->and($member->fresh()->ownsWorkspace($this->workspace))->toBeFalse()
        ->and($this->user->ownsWorkspace(null))->toBeFalse();
});

it('counts anyone above USER as an admin', function () {
    $member = User::factory()->create();
    $member->workspaces()->attach($this->workspace->id, ['role' => Role::ROLE_USER->value]);

    expect($this->user->isAdminOnStore($this->workspace))->toBeTrue()
        ->and($member->fresh()->isAdminOnStore($this->workspace))->toBeFalse()
        ->and($this->user->isAdminOnStore(null))->toBeFalse();
});

it('matches an exact role', function () {
    expect($this->user->hasWorkspaceRole($this->workspace, Role::ROLE_OWNER->value))->toBeTrue()
        ->and($this->user->hasWorkspaceRole($this->workspace, Role::ROLE_USER->value))->toBeFalse()
        ->and($this->user->hasWorkspaceRole(Workspace::factory()->create(), Role::ROLE_OWNER->value))
        ->toBeNull();
});

it('switches to a workspace it belongs to and refuses one it does not', function () {
    $mine = Workspace::factory()->create();
    $this->user->workspaces()->attach($mine->id, ['role' => Role::ROLE_OWNER->value]);

    expect($this->user->fresh()->switchWorkspace($mine))->toBeTrue()
        ->and($this->user->fresh()->current_workspace_id)->toBe($mine->id)
        ->and($this->user->fresh()->switchWorkspace(Workspace::factory()->create()))->toBeFalse();
});
