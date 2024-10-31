<?php

declare(strict_types=1);

use App\Models\User;

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
