<?php

declare(strict_types=1);

use App\Enums\Domain\Status;
use App\Models\Domain;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\DnsRecords;

uses(RefreshDatabase::class);

beforeEach(function () {
    // No test reaches the network: the stub answers every lookup.
    DnsRecords::fake();
});

beforeEach(function () {
    $this->user = User::factory()->withWorkspace()->create();
});

it('returns a successful response', function () {
    $response = $this
        ->actingAs($this->user)
        ->get(route('setting.domains.index'));

    $response->assertStatus(200);
});

it('adds a domain from the settings screen', function () {
    $this->actingAs($this->user)
        ->post(route('setting.domains.store'), ['domain' => 'links.example.com'])
        ->assertRedirect();

    $domain = Domain::where('domain', 'links.example.com')->firstOrFail();

    // A new domain has not proved its DNS yet.
    expect($domain->workspace_id)->toBe($this->user->current_workspace_id)
        ->and($domain->status)->toBe(Status::PENDING);
});

it('refuses a hostname that is not one', function (string $bad) {
    $this->actingAs($this->user)
        ->post(route('setting.domains.store'), ['domain' => $bad])
        ->assertSessionHasErrors('domain');
})->with([
    'with a scheme' => ['https://links.example.com'],
    'with an underscore' => ['links_example.com'],
    'without a dot' => ['localhost'],
    'trailing dot' => ['example.'],
    'leading hyphen' => ['-bad.example.com'],
]);

it('refuses a domain lua already serves', function () {
    $this->actingAs($this->user)
        ->post(route('setting.domains.store'), ['domain' => config('domains.available')[0]])
        ->assertSessionHasErrors('domain');
});

it('refuses a hostname another workspace already claimed', function () {
    $other = User::factory()->withWorkspace()->create();
    Domain::factory()->create([
        'workspace_id' => $other->current_workspace_id,
        'domain' => 'links.example.com',
    ]);

    $this->actingAs($this->user)
        ->post(route('setting.domains.store'), ['domain' => 'links.example.com'])
        ->assertSessionHasErrors('domain');
});

it('turns away a new domain once the plan allowance is used', function () {
    $plan = Plan::factory()->create(['max_domains' => 0]);
    $this->user->currentWorkspace->update(['plan_id' => $plan->id]);

    $this->actingAs($this->user)
        ->post(route('setting.domains.store'), ['domain' => 'links.example.com'])
        ->assertRedirect();

    expect(Domain::where('domain', 'links.example.com')->exists())->toBeFalse();
});

it('updates a domain from the settings screen', function () {
    $domain = Domain::factory()->create([
        'workspace_id' => $this->user->current_workspace_id,
    ]);

    $this->actingAs($this->user)
        ->put(route('setting.domains.update', $domain->id), [
            'domain' => $domain->domain,
            'not_found_url' => 'https://example.com/gone',
        ])
        ->assertRedirect();

    expect($domain->fresh()->not_found_url)->toBe('https://example.com/gone');
});

it('sends a renamed domain back to pending', function () {
    $domain = Domain::factory()->create([
        'workspace_id' => $this->user->current_workspace_id,
        'status' => Status::ACTIVE,
    ]);

    // The DNS it proved was for the old hostname.
    $this->actingAs($this->user)
        ->put(route('setting.domains.update', $domain->id), ['domain' => 'moved.example.com']);

    expect($domain->fresh()->status)->toBe(Status::PENDING);
});

it('leaves an active domain active when only its urls change', function () {
    $domain = Domain::factory()->create([
        'workspace_id' => $this->user->current_workspace_id,
        'status' => Status::ACTIVE,
    ]);

    $this->actingAs($this->user)
        ->put(route('setting.domains.update', $domain->id), [
            'domain' => $domain->domain,
            'not_found_url' => 'https://example.com/gone',
        ]);

    expect($domain->fresh()->status)->toBe(Status::ACTIVE);
});

it('never updates a domain belonging to another workspace', function () {
    $other = User::factory()->withWorkspace()->create();
    $domain = Domain::factory()->create(['workspace_id' => $other->current_workspace_id]);

    $this->actingAs($this->user)
        ->put(route('setting.domains.update', $domain->id), ['domain' => 'stolen.example.com'])
        ->assertNotFound();
});

it('deletes a domain from the settings screen', function () {
    $domain = Domain::factory()->create([
        'workspace_id' => $this->user->current_workspace_id,
    ]);

    $this->actingAs($this->user)
        ->delete(route('setting.domains.destroy', $domain->id))
        ->assertRedirect();

    expect(Domain::find($domain->id))->toBeNull();
});

it('never deletes a domain belonging to another workspace', function () {
    $other = User::factory()->withWorkspace()->create();
    $domain = Domain::factory()->create(['workspace_id' => $other->current_workspace_id]);

    $this->actingAs($this->user)
        ->delete(route('setting.domains.destroy', $domain->id))
        ->assertNotFound();

    expect(Domain::find($domain->id))->not->toBeNull();
});

// --- DNS verification ------------------------------------------------------

it('marks a domain active when its cname points at us', function () {
    $domain = Domain::factory()->create([
        'workspace_id' => $this->user->current_workspace_id,
        'domain' => 'links.example.com',
        'status' => Status::PENDING,
    ]);

    DnsRecords::fake([DnsRecords::cname('links.example.com', config('domains.cname'))]);

    $this->actingAs($this->user)
        ->get(route('setting.domains.validate-dns', $domain->id))
        ->assertRedirect();

    expect($domain->fresh()->status)->toBe(Status::ACTIVE);
});

it('leaves a domain pending when the cname points elsewhere', function () {
    $domain = Domain::factory()->create([
        'workspace_id' => $this->user->current_workspace_id,
        'domain' => 'links.example.com',
        'status' => Status::PENDING,
    ]);

    DnsRecords::fake([DnsRecords::cname('links.example.com', 'somewhere.else.com')]);

    $this->actingAs($this->user)
        ->get(route('setting.domains.validate-dns', $domain->id))
        ->assertRedirect();

    expect($domain->fresh()->status)->toBe(Status::PENDING);
});

it('leaves a domain pending when it has no cname at all', function () {
    $domain = Domain::factory()->create([
        'workspace_id' => $this->user->current_workspace_id,
        'status' => Status::PENDING,
    ]);

    DnsRecords::fake();

    $this->actingAs($this->user)->get(route('setting.domains.validate-dns', $domain->id));

    expect($domain->fresh()->status)->toBe(Status::PENDING);
});

it('never verifies a domain belonging to another workspace', function () {
    $other = User::factory()->withWorkspace()->create();
    $domain = Domain::factory()->create(['workspace_id' => $other->current_workspace_id]);

    $this->actingAs($this->user)
        ->get(route('setting.domains.validate-dns', $domain->id))
        ->assertNotFound();
});
