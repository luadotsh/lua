<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\User;
use App\Models\Link;
use App\Models\ApiToken;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->withWorkspace()->create();
    $this->token = ApiToken::factory()->create([
        'workspace_id' => $this->user->current_workspace_id,
    ]);
});

it('can list links', function () {
    Link::factory(10)->create([
        'workspace_id' => $this->user->current_workspace_id,
    ]);

    $response = $this
        ->withToken($this->token->token)
        ->get(route('api.links.index'));

    $response->assertStatus(200)
        ->assertJsonCount(10, 'data');
});

it('can create a new link', function () {
    $response = $this
        ->withToken($this->token->token)
        ->post(route('api.links.store'), [
            'key' => 'new-link',
            'domain' => 'lua.sh',
            'url' => 'https://lua.sh',
        ]);

    $response->assertStatus(201);
});

it('can update a link', function () {
    $link = Link::factory()->create([
        'workspace_id' => $this->user->current_workspace_id,
    ]);

    $response = $this
        ->withToken($this->token->token)
        ->put(route('api.links.update', $link->id), [
            'key' => 'updated-link',
            'domain' => 'lua.sh',
            'url' => 'https://lua.sh',
        ]);

    $response->assertStatus(200);
});

it('can delete a link', function () {
    $link = Link::factory()->create([
        'workspace_id' => $this->user->current_workspace_id,
    ]);

    $response = $this
        ->withToken($this->token->token)
        ->delete(route('api.links.destroy', $link->id));

    $response->assertStatus(200);
});
