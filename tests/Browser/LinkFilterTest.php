<?php

declare(strict_types=1);

use App\Models\Link;
use App\Models\Tag;
use App\Models\User;

test('picking a tag in the filter menu narrows the links list', function () {
    $user = User::factory()->withWorkspace()->create();
    $workspace = $user->currentWorkspace;

    $tag = Tag::factory()->create([
        'workspace_id' => $workspace->id,
        'name' => 'Campaign',
    ]);

    $tagged = Link::factory()->create([
        'workspace_id' => $workspace->id,
        'url' => 'https://tagged.example/one',
    ]);
    $tagged->tags()->sync([$tag->id]);

    Link::factory()->create([
        'workspace_id' => $workspace->id,
        'url' => 'https://untagged.example/two',
    ]);

    $this->actingAs($user);

    $page = visit(route('links.index'));

    $page->assertSee('tagged.example/one')
        ->assertSee('untagged.example/two');

    // Playwright waits for each target to be actionable, so the drill-down
    // needs no explicit waiting between clicks.
    $page->click('@filter-menu')
        ->click('@filter-category-tag')
        ->click("@filter-option-{$tag->id}");

    $page->assertSee('tagged.example/one')
        ->assertDontSee('untagged.example/two')
        ->assertQueryStringHas('tag')
        ->assertNoJavaScriptErrors();
});

test('clearing the filter menu brings every link back', function () {
    $user = User::factory()->withWorkspace()->create();
    $workspace = $user->currentWorkspace;

    $tag = Tag::factory()->create(['workspace_id' => $workspace->id]);

    $tagged = Link::factory()->create([
        'workspace_id' => $workspace->id,
        'url' => 'https://tagged.example/one',
    ]);
    $tagged->tags()->sync([$tag->id]);

    Link::factory()->create([
        'workspace_id' => $workspace->id,
        'url' => 'https://untagged.example/two',
    ]);

    $this->actingAs($user);

    $page = visit(route('links.index', ['tag' => [$tag->id]]));

    $page->assertDontSee('untagged.example/two');

    $page->click('@filter-menu')
        ->click('@filter-clear');

    $page->assertSee('tagged.example/one')
        ->assertSee('untagged.example/two')
        ->assertQueryStringMissing('tag')
        ->assertNoJavaScriptErrors();
});
