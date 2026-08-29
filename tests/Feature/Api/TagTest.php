<?php

declare(strict_types=1);

use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->withWorkspace()->create();
    $this->token = apiTokenFor($this->user);
});

it('can list tags', function () {

    Tag::factory(10)->create([
        'workspace_id' => $this->user->current_workspace_id,
    ]);

    $response = $this
        ->withToken($this->token)
        ->get(route('api.tags.index'));

    $response->assertStatus(200)
        ->assertJsonCount(13, 'data'); // 10 + 3 default tags
});

it('can create a new tag', function () {
    $response = $this
        ->withToken($this->token)
        ->post(route('api.tags.store'), [
            'name' => 'New Tag',
            'color' => '#60a5fa',
        ]);

    $response->assertStatus(201);
});

it('can update a tag', function () {
    $tag = Tag::factory()->create([
        'workspace_id' => $this->user->current_workspace_id,
    ]);

    $response = $this
        ->withToken($this->token)
        ->put(route('api.tags.update', $tag->id), [
            'name' => 'Updated Tag',
            'color' => '#4ade80',
        ]);

    $response->assertStatus(200)
        ->assertJsonFragment([
            'name' => 'Updated Tag',
            'color' => '#4ade80',
        ]);
});

it('can delete a tag', function () {
    $tag = Tag::factory()->create([
        'workspace_id' => $this->user->current_workspace_id,
    ]);

    $response = $this
        ->withToken($this->token)
        ->delete(route('api.tags.destroy', $tag->id));

    $response->assertStatus(200);
});

it('refuses a colour that is not a hex value', function () {
    $this
        ->withToken($this->token)
        ->postJson(route('api.tags.store'), ['name' => 'Bad', 'color' => 'green'])
        ->assertStatus(422)
        ->assertJsonValidationErrors('color');
});
