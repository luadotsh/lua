<?php

declare(strict_types=1);

use App\Enums\User\Role;
use App\Models\User;
use App\Models\Workspace;

use function Pest\Laravel\actingAs;

test('create workspace page can be displayed', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(route('workspaces.create'))
        ->assertOk();
});

test('can create workspace', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->from(route('workspaces.create'))
        ->post(route('workspaces.store'), [
            'name' => 'Lua.sh',
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('links.index'));
});

test('cannot create workspace without name', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->from(route('workspaces.create'))
        ->post(route('workspaces.store'), [
        ])
        ->assertSessionHasErrors(['name'])
        ->assertRedirect(route('workspaces.create'));
});

test('user can update workspace', function () {
    $user = User::factory()->withWorkspace()->create();

    actingAs($user)
        ->from(route('setting.workspace.edit'))
        ->put(route('setting.workspace.update'), [
            'id' => $user->currentWorkspace->id,
            'name' => $user->currentWorkspace->name,
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('setting.workspace.edit'));
});

test('user cant update workspace without name', function () {
    $user = User::factory()->withWorkspace()->create();

    actingAs($user)
        ->from(route('setting.workspace.edit'))
        ->put(route('setting.workspace.update'), [
            'name' => null,
        ])
        ->assertSessionHasErrors(['name'])
        ->assertRedirect(route('setting.workspace.edit'));
});

it('switches to a workspace you belong to', function () {
    $user = User::factory()->withWorkspace()->create();
    $elsewhere = Workspace::factory()->create();
    $user->workspaces()->attach($elsewhere->id, ['role' => Role::ROLE_ADMIN->value]);

    actingAs($user)->put(route('workspaces.update-current'), [
        'workspace_id' => $elsewhere->id,
    ]);

    expect($user->fresh()->current_workspace_id)->toBe($elsewhere->id);
});

it('refuses to switch to a workspace you do not belong to', function () {
    $user = User::factory()->withWorkspace()->create();
    $theirs = Workspace::factory()->create();
    $before = $user->current_workspace_id;

    actingAs($user)
        ->put(route('workspaces.update-current'), ['workspace_id' => $theirs->id])
        ->assertForbidden();

    expect($user->fresh()->current_workspace_id)->toBe($before);
});

it('404s switching to a workspace that does not exist', function () {
    $user = User::factory()->withWorkspace()->create();

    actingAs($user)
        ->put(route('workspaces.update-current'), [
            'workspace_id' => '00000000-0000-0000-0000-000000000000',
        ])
        ->assertNotFound();
});

it('renames a workspace', function () {
    $user = User::factory()->withWorkspace()->create();

    actingAs($user)
        ->put(route('setting.workspace.update'), ['name' => 'Renamed'])
        ->assertRedirect();

    expect($user->currentWorkspace->fresh()->name)->toBe('Renamed');
});

it('shows the workspace settings screen', function () {
    actingAs(User::factory()->withWorkspace()->create())
        ->get(route('setting.workspace.edit'))
        ->assertOk();
});
