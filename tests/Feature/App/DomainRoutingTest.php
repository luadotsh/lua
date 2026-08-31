<?php

declare(strict_types=1);

use App\Models\Domain;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Redis;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->workspace = User::factory()->withWorkspace()->create()->currentWorkspace;
    $this->redis = Redis::connection('default');
});

afterEach(function () {
    foreach (['links.example.com', 'moved.example.com'] as $host) {
        $this->redis->del($host);
    }
});

/**
 * A custom domain is announced to Redis so whatever fronts the app knows to
 * route it here. Nothing in the app reads these keys, so without a test a
 * rename or a delete could silently stop announcing and only the edge would
 * notice.
 */
it('announces a domain the moment it is added', function () {
    Domain::factory()->create([
        'workspace_id' => $this->workspace->id,
        'domain' => 'links.example.com',
    ]);

    expect($this->redis->exists('links.example.com'))->toBe(1);
});

it('moves the announcement when the hostname changes', function () {
    $domain = Domain::factory()->create([
        'workspace_id' => $this->workspace->id,
        'domain' => 'links.example.com',
    ]);

    $domain->update(['domain' => 'moved.example.com']);

    // The old key has to go, or the edge keeps routing a hostname this
    // workspace no longer owns.
    expect($this->redis->exists('links.example.com'))->toBe(0)
        ->and($this->redis->exists('moved.example.com'))->toBe(1);
});

it('withdraws the announcement when the domain is removed', function () {
    $domain = Domain::factory()->create([
        'workspace_id' => $this->workspace->id,
        'domain' => 'links.example.com',
    ]);

    $domain->delete();

    // This is what actually stops links on that domain resolving: the row is
    // only soft-deleted, so the key is the part that matters.
    expect($this->redis->exists('links.example.com'))->toBe(0);
});
