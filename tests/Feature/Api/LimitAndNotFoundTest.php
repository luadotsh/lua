<?php

declare(strict_types=1);

use App\Models\Domain;
use App\Models\Link;
use App\Models\Plan;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->withWorkspace()->create();
    $this->workspace = $this->user->currentWorkspace;
    $this->token = apiTokenFor($this->user, 'Deploy key');
});

$missing = '00000000-0000-0000-0000-000000000000';

/**
 * The API answers a limit with 403 and a stranger's id with 404 — never with
 * the 422 the action would throw, because the controller checks first.
 */
it('answers 403 once the link allowance is used', function () {
    $this->workspace->update(['plan_id' => Plan::factory()->create(['max_links' => 1])->id]);
    Link::factory()->create(['workspace_id' => $this->workspace->id]);

    $this->withToken($this->token)
        ->postJson(route('api.links.store'), ['url' => 'https://example.com'])
        ->assertForbidden()
        ->assertJson(['message' => 'You have reached the link limit']);
});

it('answers 403 once the tag allowance is used', function () {
    $this->workspace->update(['plan_id' => Plan::factory()->create(['max_tags' => 1])->id]);

    $this->withToken($this->token)
        ->postJson(route('api.tags.store'), ['name' => 'One too many', 'color' => '#f87171'])
        ->assertForbidden();
});

it('answers 403 once the domain allowance is used', function () {
    $this->workspace->update(['plan_id' => Plan::factory()->create(['max_domains' => 0])->id]);

    $this->withToken($this->token)
        ->postJson(route('api.domains.store'), ['domain' => 'links.example.com'])
        ->assertForbidden();
});

it('answers 404 for a link that is not this workspace', function () use ($missing) {
    $theirs = Link::factory()->create([
        'workspace_id' => User::factory()->withWorkspace()->create()->current_workspace_id,
    ]);

    foreach ([$theirs->id, $missing] as $id) {
        $this->withToken($this->token)
            ->getJson(route('api.links.show', $id))
            ->assertNotFound()
            ->assertJson(['message' => 'Link not found']);
    }
});

it('answers 404 for a tag that is not this workspace', function () {
    $theirs = Tag::factory()->create([
        'workspace_id' => User::factory()->withWorkspace()->create()->current_workspace_id,
    ]);

    $this->withToken($this->token)
        ->putJson(route('api.tags.update', $theirs->id), ['name' => 'Stolen', 'color' => '#f87171'])
        ->assertNotFound();

    $this->withToken($this->token)
        ->deleteJson(route('api.tags.destroy', $theirs->id))
        ->assertNotFound();

    expect(Tag::find($theirs->id))->not->toBeNull();
});

it('answers 404 for a domain that is not this workspace', function () {
    $theirs = Domain::factory()->create([
        'workspace_id' => User::factory()->withWorkspace()->create()->current_workspace_id,
    ]);

    $this->withToken($this->token)
        ->putJson(route('api.domains.update', $theirs->id), ['domain' => 'stolen.example.com'])
        ->assertNotFound();

    $this->withToken($this->token)
        ->deleteJson(route('api.domains.destroy', $theirs->id))
        ->assertNotFound();

    expect(Domain::find($theirs->id))->not->toBeNull();
});
