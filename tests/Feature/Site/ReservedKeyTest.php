<?php

declare(strict_types=1);

use App\Actions\Link\CreateLink;
use App\Enums\Domain\Status;
use App\Models\Domain;
use App\Models\User;

beforeEach(function (): void {
    $this->user = User::factory()->withWorkspace()->create();
    $this->workspace = $this->user->currentWorkspace;
});

it('refuses a back-half that a real route already answers', function (string $key): void {
    $this->actingAs($this->user)
        ->postJson(route('links.store'), [
            'key' => $key,
            'url' => 'https://example.com',
            'domain' => config('domains.main'),
        ])
        ->assertJsonValidationErrors('key');
})->with(['pricing', 'terms', 'privacy', 'alternatives', 'login', 'register', 'links', 'analytics']);

it('allows a reserved word as a back-half on a custom domain', function (): void {
    // The site and app routes are scoped to the main domain, so nothing on a
    // customer's own domain shadows `pricing`.
    Domain::factory()->create([
        'workspace_id' => $this->workspace->id,
        'domain' => 'links.example.com',
        'status' => Status::ACTIVE,
    ]);

    $this->actingAs($this->user)
        ->postJson(route('links.store'), [
            'key' => 'pricing',
            'url' => 'https://example.com',
            'domain' => 'links.example.com',
        ])
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('links', ['key' => 'pricing', 'domain' => 'links.example.com']);
});

it('still allows an ordinary back-half on the main domain', function (): void {
    $this->actingAs($this->user)
        ->postJson(route('links.store'), [
            'key' => 'launch',
            'url' => 'https://example.com',
            'domain' => config('domains.main'),
        ])
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('links', ['key' => 'launch', 'domain' => config('domains.main')]);
});

// A hand-written list would go stale; this is what proves it is derived.
it('derives the reserved list from the registered routes', function (): void {
    expect(CreateLink::reservedKeys())
        ->toContain('pricing', 'alternatives', 'login', 'links')
        ->not->toContain('')
        ->and(collect(CreateLink::reservedKeys())->filter(fn (string $key): bool => str_starts_with($key, '{')))
        ->toBeEmpty();
});
