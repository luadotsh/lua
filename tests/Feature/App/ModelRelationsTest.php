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

/**
 * The inverse of every relation the app walks forwards. Nothing else reaches
 * them, so a rename on either side would go unnoticed until a screen broke.
 */
it('walks back from a link to who made it and what it carries', function () {
    $link = Link::factory()->create([
        'workspace_id' => $this->workspace->id,
        'user_id' => $this->user->id,
    ]);

    $tag = Tag::factory()->create(['workspace_id' => $this->workspace->id]);
    $link->tags()->sync([$tag->id]);

    $stat = LinkStat::factory()->create([
        'workspace_id' => $this->workspace->id,
        'link_id' => $link->id,
    ]);

    expect($link->user->is($this->user))->toBeTrue()
        ->and($link->workspace->is($this->workspace))->toBeTrue()
        ->and($stat->link->is($link))->toBeTrue()
        ->and($stat->workspace->is($this->workspace))->toBeTrue()
        ->and($tag->fresh()->workspace->is($this->workspace))->toBeTrue()
        ->and($tag->fresh()->links->pluck('id')->all())->toBe([$link->id]);
});

it('walks back from a domain and a plan to the workspace', function () {
    $domain = Domain::factory()->create(['workspace_id' => $this->workspace->id]);

    expect($domain->workspace->is($this->workspace))->toBeTrue()
        ->and($this->workspace->plan)->not->toBeNull();
});
