<?php

declare(strict_types=1);

use App\Actions\Link\GetLink;
use App\Actions\Link\ListLinks;
use App\Enums\LinkStat\Event;
use App\Models\Link;
use App\Models\LinkStat;
use App\Models\Plan;
use App\Models\Tag;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

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

it('shows a link its own dashboard', function () {
    $workspace = $this->user->currentWorkspace;
    $link = Link::factory()->create(['workspace_id' => $workspace->id]);

    $this->actingAs($this->user)
        ->get(route('links.show', $link->id))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Link/Show')
            ->where('link.id', $link->id)
            ->has('table')
        );
});

it('never shows a link belonging to another workspace', function () {
    $link = Link::factory()->create([
        'workspace_id' => Workspace::factory()->create()->id,
    ]);

    $this->actingAs($this->user)
        ->get(route('links.show', $link->id))
        ->assertNotFound();
});

it('lists only that link events on its dashboard', function () {
    $workspace = $this->user->currentWorkspace;

    $mine = Link::factory()->create(['workspace_id' => $workspace->id]);
    $theirs = Link::factory()->create(['workspace_id' => $workspace->id]);

    LinkStat::factory()->count(2)->create([
        'workspace_id' => $workspace->id,
        'link_id' => $mine->id,
    ]);
    LinkStat::factory()->create([
        'workspace_id' => $workspace->id,
        'link_id' => $theirs->id,
    ]);

    $this->actingAs($this->user)
        ->get(route('links.show', $mine->id))
        ->assertInertia(fn (Assert $page) => $page->has('table.data', 2));
});

it('counts a link clicks from its events rather than from a column', function () {
    $workspace = $this->user->currentWorkspace;
    $link = Link::factory()->create(['workspace_id' => $workspace->id]);

    LinkStat::factory()->count(3)->create([
        'workspace_id' => $workspace->id,
        'link_id' => $link->id,
        'event' => Event::CLICK,
    ]);

    // A scan is not a click: the dashboard has always drawn that line, and the
    // counter this replaced did not.
    LinkStat::factory()->count(2)->create([
        'workspace_id' => $workspace->id,
        'link_id' => $link->id,
        'event' => Event::QR_SCAN,
    ]);

    $listed = ListLinks::execute($workspace)->firstWhere('id', $link->id);

    expect($listed->clicks)->toBe(3)
        ->and($listed->last_click)->not->toBeNull();
});

it('reports no clicks for a link nothing has reached', function () {
    $link = Link::factory()->create([
        'workspace_id' => $this->user->currentWorkspace->id,
    ]);

    $listed = ListLinks::execute($this->user->currentWorkspace)->firstWhere('id', $link->id);

    expect($listed->clicks)->toBe(0)
        ->and($listed->last_click)->toBeNull();
});

it('counts one click per event even when two arrive at once', function () {
    $workspace = $this->user->currentWorkspace;
    $link = Link::factory()->create(['workspace_id' => $workspace->id]);

    // The counter this replaced read, added one and wrote back, so two jobs
    // running together both stored the same total and one click vanished.
    // Counting cannot lose one.
    LinkStat::factory()->count(50)->create([
        'workspace_id' => $workspace->id,
        'link_id' => $link->id,
        'event' => Event::CLICK,
    ]);

    expect(GetLink::execute($workspace, $link->id)->clicks)->toBe(50);
});

it('opens the edit screen with the password readable', function () {
    $link = Link::factory()->create([
        'workspace_id' => $this->user->currentWorkspace->id,
        'password' => 'sesame',
    ]);

    $this->actingAs($this->user)
        ->get(route('links.edit', $link->id))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Link/Edit')
            // The owner set this password and may need to read it back.
            ->where('link.password', 'sesame')
            ->has('domains')
            ->has('tags')
        );
});

it('never opens the edit screen for another workspace link', function () {
    $link = Link::factory()->create([
        'workspace_id' => Workspace::factory()->create()->id,
    ]);

    $this->actingAs($this->user)
        ->get(route('links.edit', $link->id))
        ->assertNotFound();
});

it('deletes a link from the edit screen', function () {
    $link = Link::factory()->create(['workspace_id' => $this->user->currentWorkspace->id]);

    $this->actingAs($this->user)
        ->delete(route('links.destroy', $link->id))
        ->assertRedirect();

    expect(Link::find($link->id))->toBeNull();
});

it('never deletes another workspace link', function () {
    $link = Link::factory()->create([
        'workspace_id' => Workspace::factory()->create()->id,
    ]);

    $this->actingAs($this->user)
        ->delete(route('links.destroy', $link->id))
        ->assertNotFound();

    expect(Link::find($link->id))->not->toBeNull();
});

it('takes the click history with the link', function () {
    $workspace = $this->user->currentWorkspace;
    $link = Link::factory()->create(['workspace_id' => $workspace->id]);

    LinkStat::factory()->count(3)->create([
        'workspace_id' => $workspace->id,
        'link_id' => $link->id,
    ]);

    $this->actingAs($this->user)->delete(route('links.destroy', $link->id));

    expect(LinkStat::where('link_id', $link->id)->count())->toBe(0);
});

it('turns away a new link once the plan allowance is used', function () {
    $plan = Plan::factory()->create(['max_links' => 1]);
    $this->user->currentWorkspace->update(['plan_id' => $plan->id]);

    Link::factory()->create(['workspace_id' => $this->user->currentWorkspace->id]);

    $this->actingAs($this->user)
        ->post(route('links.store'), ['url' => 'https://example.com/too-many'])
        ->assertRedirect();

    expect(Link::where('url', 'https://example.com/too-many')->exists())->toBeFalse();
});

it('narrows a link dashboard to the period asked for', function () {
    $workspace = $this->user->currentWorkspace;
    $link = Link::factory()->create(['workspace_id' => $workspace->id]);

    LinkStat::factory()->create([
        'workspace_id' => $workspace->id,
        'link_id' => $link->id,
        'created_at' => now()->subYear(),
    ]);

    $this->actingAs($this->user)
        ->get(route('links.show', [
            $link->id,
            'start' => now()->subDays(7)->toDateString(),
            'end' => now()->toDateString(),
        ]))
        ->assertInertia(fn (Assert $page) => $page->has('table.data', 0));
});
