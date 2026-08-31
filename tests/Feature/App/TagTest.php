<?php

declare(strict_types=1);

use App\Models\Link;
use App\Models\Plan;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->withWorkspace()->create();
});

it('returns a successful response', function () {
    $response = $this
        ->actingAs($this->user)
        ->get(route('setting.tags.index'));

    $response->assertStatus(200);
});

it('creates a tag from the settings screen', function () {
    $this->actingAs($this->user)
        ->post(route('setting.tags.store'), ['name' => 'Campaign', 'color' => '#f87171'])
        ->assertRedirect();

    expect(Tag::where('workspace_id', $this->user->current_workspace_id)
        ->where('name', 'Campaign')->exists())->toBeTrue();
});

it('refuses a tag with no name', function () {
    $this->actingAs($this->user)
        ->post(route('setting.tags.store'), ['name' => '', 'color' => '#f87171'])
        ->assertSessionHasErrors('name');
});

it('refuses a tag colour that is not a hex value', function () {
    $this->actingAs($this->user)
        ->post(route('setting.tags.store'), ['name' => 'Campaign', 'color' => 'octarine'])
        ->assertSessionHasErrors('color');
});

it('turns away a new tag once the plan allowance is used', function () {
    $plan = Plan::factory()->create(['max_tags' => 1]);
    $this->user->currentWorkspace->update(['plan_id' => $plan->id]);

    // The workspace is seeded with default tags, so it is already over a
    // one-tag allowance. The controller checks the gate before the action
    // does, so this comes back as the banner rather than a validation error.
    $this->actingAs($this->user)
        ->post(route('setting.tags.store'), ['name' => 'One too many', 'color' => '#f87171'])
        ->assertRedirect();

    expect(Tag::where('name', 'One too many')->exists())->toBeFalse();
});

it('renames a tag from the settings screen', function () {
    $tag = Tag::factory()->create(['workspace_id' => $this->user->current_workspace_id]);

    $this->actingAs($this->user)
        ->put(route('setting.tags.update', $tag->id), ['name' => 'Renamed', 'color' => '#60a5fa'])
        ->assertRedirect();

    expect($tag->fresh()->name)->toBe('Renamed')
        ->and($tag->fresh()->color)->toBe('#60a5fa');
});

it('never renames a tag belonging to another workspace', function () {
    $other = User::factory()->withWorkspace()->create();
    $tag = Tag::factory()->create([
        'workspace_id' => $other->current_workspace_id,
        'name' => 'Theirs',
    ]);

    $this->actingAs($this->user)
        ->put(route('setting.tags.update', $tag->id), ['name' => 'Stolen', 'color' => '#60a5fa'])
        ->assertNotFound();

    expect($tag->fresh()->name)->toBe('Theirs');
});

it('deletes a tag from the settings screen', function () {
    $tag = Tag::factory()->create(['workspace_id' => $this->user->current_workspace_id]);

    $this->actingAs($this->user)
        ->delete(route('setting.tags.destroy', $tag->id))
        ->assertRedirect();

    expect(Tag::find($tag->id))->toBeNull();
});

it('never deletes a tag belonging to another workspace', function () {
    $other = User::factory()->withWorkspace()->create();
    $tag = Tag::factory()->create(['workspace_id' => $other->current_workspace_id]);

    $this->actingAs($this->user)
        ->delete(route('setting.tags.destroy', $tag->id))
        ->assertNotFound();

    expect(Tag::find($tag->id))->not->toBeNull();
});

it('leaves the link standing when its tag is deleted', function () {
    $tag = Tag::factory()->create(['workspace_id' => $this->user->current_workspace_id]);
    $link = Link::factory()->create(['workspace_id' => $this->user->current_workspace_id]);
    $link->tags()->sync([$tag->id]);

    $this->actingAs($this->user)->delete(route('setting.tags.destroy', $tag->id));

    // The tool that does this says so out loud: links keep working, they just
    // lose the tag.
    expect(Link::find($link->id))->not->toBeNull()
        ->and($link->fresh()->tags)->toBeEmpty();
});
