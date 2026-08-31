<?php

declare(strict_types=1);

use App\Actions\Link\CreateLink;
use App\Actions\Link\UpdateLink;
use App\Models\Link;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->withWorkspace()->create();
    $this->workspace = $this->user->currentWorkspace;
});

it('attaches the workspace own tags when creating a link', function () {
    $tag = Tag::factory()->create(['workspace_id' => $this->workspace->id]);

    $link = CreateLink::execute($this->workspace, [
        'url' => 'https://example.com',
        'tags' => [$tag->id],
    ]);

    expect($link->tags->pluck('id')->all())->toBe([$tag->id]);
});

it('never attaches another workspace tag to a new link', function () {
    $other = User::factory()->withWorkspace()->create();
    $theirs = Tag::factory()->create(['workspace_id' => $other->current_workspace_id]);

    // The rules only check that `tags` is an array, so an unfiltered sync would
    // pin their tag on our link — and its name would read back on our list.
    $link = CreateLink::execute($this->workspace, [
        'url' => 'https://example.com',
        'tags' => [$theirs->id],
    ]);

    expect($link->tags)->toBeEmpty();
});

it('never attaches another workspace tag when updating a link', function () {
    $link = Link::factory()->create(['workspace_id' => $this->workspace->id]);

    $other = User::factory()->withWorkspace()->create();
    $theirs = Tag::factory()->create(['workspace_id' => $other->current_workspace_id]);

    UpdateLink::execute($link, ['tags' => [$theirs->id]]);

    expect($link->fresh()->tags)->toBeEmpty();
});

it('keeps the workspace own tags while dropping a foreign one', function () {
    $link = Link::factory()->create(['workspace_id' => $this->workspace->id]);
    $mine = Tag::factory()->create(['workspace_id' => $this->workspace->id]);

    $other = User::factory()->withWorkspace()->create();
    $theirs = Tag::factory()->create(['workspace_id' => $other->current_workspace_id]);

    UpdateLink::execute($link, ['tags' => [$mine->id, $theirs->id]]);

    expect($link->fresh()->tags->pluck('id')->all())->toBe([$mine->id]);
});

it('clears the tags when handed an empty list', function () {
    $link = Link::factory()->create(['workspace_id' => $this->workspace->id]);
    $tag = Tag::factory()->create(['workspace_id' => $this->workspace->id]);
    $link->tags()->sync([$tag->id]);

    UpdateLink::execute($link, ['tags' => []]);

    expect($link->fresh()->tags)->toBeEmpty();
});

it('leaves the tags alone when the update does not mention them', function () {
    $link = Link::factory()->create(['workspace_id' => $this->workspace->id]);
    $tag = Tag::factory()->create(['workspace_id' => $this->workspace->id]);
    $link->tags()->sync([$tag->id]);

    // This is the whole point of the action checking array_key_exists: a
    // partial update from MCP must not wipe what it never mentioned.
    UpdateLink::execute($link, ['url' => 'https://moved.example']);

    expect($link->fresh()->tags->pluck('id')->all())->toBe([$tag->id]);
});

it('refuses to empty a link back half', function () {
    $link = Link::factory()->create([
        'workspace_id' => $this->workspace->id,
        'domain' => 'lua.test',
        'key' => 'keepme',
    ]);

    // An empty key derived the short link down to "https://lua.test/", which is
    // the redirect root rather than a link.
    UpdateLink::execute($link, ['key' => '']);

    expect($link->fresh()->key)->toBe('keepme')
        ->and($link->fresh()->link)->toBe('https://lua.test/keepme');
});

it('rebuilds the short link when the back half changes', function () {
    $link = Link::factory()->create([
        'workspace_id' => $this->workspace->id,
        'domain' => 'lua.test',
        'key' => 'before',
    ]);

    UpdateLink::execute($link, ['key' => 'after']);

    expect($link->fresh()->link)->toBe('https://lua.test/after');
});
