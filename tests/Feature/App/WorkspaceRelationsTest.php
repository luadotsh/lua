<?php

declare(strict_types=1);

use App\Models\Domain;
use App\Models\Link;
use App\Models\LinkStat;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->withWorkspace()->create();
    $this->workspace = $this->user->currentWorkspace;
});

it('reaches everything the workspace holds', function () {
    $link = Link::factory()->create(['workspace_id' => $this->workspace->id]);
    LinkStat::factory()->count(2)->create([
        'workspace_id' => $this->workspace->id,
        'link_id' => $link->id,
    ]);
    Domain::factory()->create(['workspace_id' => $this->workspace->id]);
    Tag::factory()->create(['workspace_id' => $this->workspace->id]);
    apiTokenFor($this->user, 'Deploy key');

    $workspace = $this->workspace->fresh();

    expect($workspace->links)->toHaveCount(1)
        ->and($workspace->linkStats)->toHaveCount(2)
        ->and($workspace->domains)->toHaveCount(1)
        // A workspace is created with default tags, so this counts the new one
        // among them rather than on its own.
        ->and($workspace->tags->pluck('id'))->toContain(Tag::latest('id')->first()->id)
        ->and($workspace->apiTokens)->toHaveCount(1)
        ->and($workspace->users->pluck('id')->all())->toBe([$this->user->id]);
});

it('points billing at the owner', function () {
    expect($this->workspace->owner->is($this->user))->toBeTrue()
        ->and($this->workspace->stripeEmail())->toBe($this->user->email);
});

it('has no billing email once the owner account is gone', function () {
    // owner_id is nullOnDelete, so this is null rather than a fatal — which is
    // what the old pivot lookup did.
    $this->user->workspaces()->detach();
    $this->user->delete();

    expect($this->workspace->fresh()->stripeEmail())->toBeNull();
});
