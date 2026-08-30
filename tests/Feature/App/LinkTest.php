<?php

declare(strict_types=1);

use App\Actions\Link\ListLinks;
use App\Models\Link;
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
        ->get(route('links.index'));

    $response->assertStatus(200);
});

it('can create a link with an expired redirect URL', function () {
    Link::factory()->create([
        'expires_at' => now()->subDay(),
        'expired_redirect_url' => 'https://example.com',
    ]);

    $this->assertDatabaseCount('links', 1);
});

it('can create a link', function () {
    Link::factory()->create();

    $this->assertDatabaseCount('links', 1);
});

it('cannot create a link without url', function () {

    $response = $this
        ->actingAs($this->user)
        ->from(route('links.index'))
        ->post(route('links.store'), [
            'domain' => config('domains.main'),
            'key' => 'new-link',
            'url' => null,
        ]);

    $response->assertInvalid(['url']);
});

it('accepts a mixed case custom back-half', function () {
    $this
        ->actingAs($this->user)
        ->post(route('links.store'), [
            'url' => 'https://example.com',
            'domain' => config('domains.main'),
            'key' => 'MyLink-01',
        ])
        ->assertSessionHasNoErrors();

    expect(Link::where('key', 'MyLink-01')->exists())->toBeTrue();
});

it('rejects a back-half with characters that cannot sit in a path', function () {
    $this
        ->actingAs($this->user)
        ->post(route('links.store'), [
            'url' => 'https://example.com',
            'domain' => config('domains.main'),
            'key' => 'my link/01',
        ])
        ->assertSessionHasErrors('key');
});

it('rejects a back-half with unicode letters', function () {
    // alpha_dash without :ascii would let this through, and a path cannot
    // carry it without percent-encoding.
    $this
        ->actingAs($this->user)
        ->post(route('links.store'), [
            'url' => 'https://example.com',
            'domain' => config('domains.main'),
            'key' => 'promoção',
        ])
        ->assertSessionHasErrors('key');
});

it('finds a link regardless of the case typed into search', function () {
    Link::factory()->create([
        'workspace_id' => $this->user->current_workspace_id,
        'url' => 'https://github.com/luadotsh',
    ]);

    $found = ListLinks::execute(
        $this->user->currentWorkspace,
        ['search' => 'GITHUB'],
    );

    expect($found->total())->toBe(1);
});

it('can still edit a link whose domain has left the workspace', function () {
    $link = Link::factory()->create([
        'workspace_id' => $this->user->current_workspace_id,
        'domain' => 'retired.example',
        'key' => 'keeper',
        'link' => 'https://retired.example/keeper',
    ]);

    $this
        ->actingAs($this->user)
        ->put(route('links.update', $link->id), [
            'domain' => 'retired.example',
            'key' => 'keeper',
            'url' => 'https://example.com/new-destination',
        ])
        ->assertSessionHasNoErrors();

    expect($link->fresh()->url)->toBe('https://example.com/new-destination');
});

it('still refuses a domain that belongs to nobody', function () {
    $link = Link::factory()->create([
        'workspace_id' => $this->user->current_workspace_id,
    ]);

    $this
        ->actingAs($this->user)
        ->put(route('links.update', $link->id), [
            'domain' => 'someone-elses.example',
            'key' => $link->key,
            'url' => 'https://example.com',
        ])
        ->assertSessionHasErrors('domain');
});

it('sends you to the edit screen after creating, to finish the link', function () {
    $this
        ->actingAs($this->user)
        ->post(route('links.store'), [
            'url' => 'https://example.com/just-created',
        ])
        ->assertRedirect(route('links.edit', Link::latest()->first()->id));
});

it('records who created a link', function () {
    $this
        ->actingAs($this->user)
        ->post(route('links.store'), ['url' => 'https://example.com/mine']);

    expect(Link::where('url', 'https://example.com/mine')->first()->user_id)
        ->toBe($this->user->id);
});

it('filters the list by tag, domain and creator', function () {
    $workspace = $this->user->currentWorkspace;
    $tag = Tag::factory()->create(['workspace_id' => $workspace->id]);

    $mine = Link::factory()->create([
        'workspace_id' => $workspace->id,
        'user_id' => $this->user->id,
        'domain' => 'mine.example',
    ]);
    $mine->tags()->sync([$tag->id]);

    $theirs = Link::factory()->create([
        'workspace_id' => $workspace->id,
        'user_id' => null,
        'domain' => 'theirs.example',
    ]);

    $by = fn (array $filters) => ListLinks::execute($workspace, $filters)
        ->pluck('id')->all();

    expect($by(['tag' => [$tag->id]]))->toBe([$mine->id])
        ->and($by(['domain' => ['theirs.example']]))->toBe([$theirs->id])
        ->and($by(['user' => [$this->user->id]]))->toBe([$mine->id]);
});

it('widens the list when a filter holds several values', function () {
    $workspace = $this->user->currentWorkspace;

    $mine = Link::factory()->create([
        'workspace_id' => $workspace->id,
        'domain' => 'mine.example',
    ]);

    $theirs = Link::factory()->create([
        'workspace_id' => $workspace->id,
        'domain' => 'theirs.example',
    ]);

    $ids = ListLinks::execute($workspace, [
        'domain' => ['mine.example', 'theirs.example'],
    ])->pluck('id')->all();

    expect($ids)->toHaveCount(2)
        ->and($ids)->toContain($mine->id, $theirs->id);
});
