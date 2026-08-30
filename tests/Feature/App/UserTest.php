<?php

declare(strict_types=1);

use App\Actions\TeamMember\ListMembers;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->withWorkspace()->create();
});

it('returns a successful response', function () {
    $response = $this
        ->actingAs($this->user)
        ->get(route('setting.team-members.index'));

    $response->assertStatus(200);
});

it('finds members by name or email regardless of case', function () {
    $workspace = $this->user->currentWorkspace;

    $ada = User::factory()->create([
        'name' => 'Ada Lovelace',
        'email' => 'ada@example.com',
    ]);
    $workspace->users()->attach($ada, ['role' => 'member']);

    $names = fn (string $search) => ListMembers::execute($workspace, ['search' => $search])
        ->pluck('name')->all();

    // Upper-case input has to match a lower-case row on both engines, which is
    // what whereLike buys us over a raw `like`.
    expect($names('LOVELACE'))->toBe(['Ada Lovelace'])
        ->and($names('ADA@EXAMPLE.COM'))->toBe(['Ada Lovelace'])
        ->and($names('nobody'))->toBe([]);
});
