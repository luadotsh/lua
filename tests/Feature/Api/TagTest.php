<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Enums\Tag\Color;

use App\Models\User;
use App\Models\Tag;
use App\Models\ApiToken;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->withWorkspace()->create();
    $this->token = ApiToken::factory()->create([
        'workspace_id' => $this->user->current_workspace_id,
    ]);
});


it('can list tags', function () {

    Tag::factory(10)->create([
        'workspace_id' => $this->user->current_workspace_id,
    ]);

    $response = $this
        ->withToken($this->token->token)
        ->get(route('api.tags.index'));

    $response->assertStatus(200)
        ->assertJsonCount(13, 'data'); // 10 + 3 default tags
});

it('can create a new tag', function () {
    $response = $this
        ->withToken($this->token->token)
        ->post(route('api.tags.store'), [
            'name' => 'New Tag',
            'color' => Color::BLUE->value,
        ]);

    $response->assertStatus(201);
});

it('can update a tag', function () {
    $tag = Tag::factory()->create([
        'workspace_id' => $this->user->current_workspace_id,
    ]);

    $response = $this
        ->withToken($this->token->token)
        ->put(route('api.tags.update', $tag->id), [
            'name' => 'Updated Tag',
            'color' => Color::GREEN->value,
        ]);

    $response->assertStatus(200)
        ->assertJsonFragment([
            'name' => 'Updated Tag',
            'color' => Color::GREEN->value,
        ]);
});

it('can delete a tag', function () {
    $tag = Tag::factory()->create([
        'workspace_id' => $this->user->current_workspace_id,
    ]);

    $response = $this
        ->withToken($this->token->token)
        ->delete(route('api.tags.destroy', $tag->id));

    $response->assertStatus(200);
});
