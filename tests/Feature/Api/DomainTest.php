<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\Domain;
use App\Models\User;
use App\Models\ApiToken;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->withWorkspace()->create();
    $this->token = ApiToken::factory()->create([
        'workspace_id' => $this->user->current_workspace_id,
    ]);
});

it('can validate a domain', function () {
    $response = $this
        ->get(route('api.domains.validate', ['domain' => 'git.now']));

    $response->assertStatus(200)
        ->assertJson([
            'valid' => true
        ]);
});

it('cannot validate a invalid domain', function () {
    $response = $this
        ->get(route('api.domains.validate', ['domain' => 'invalid.domain']));

    $response->assertStatus(404)
        ->assertJson([
            'valid' => false
        ]);
});

it('can list domains', function () {
    Domain::factory(10)->create([
        'workspace_id' => $this->user->current_workspace_id,
    ]);

    $response = $this
        ->withToken($this->token->token)
        ->get(route('api.domains.index'));

    $response->assertStatus(200)
        ->assertJsonCount(10, 'data');
});

it('can create a new domain', function () {
    $response = $this
        ->withToken($this->token->token)
        ->post(route('api.domains.store'), [
            'domain' => 'new-domain.com',
            'not_found_url' => 'https://new-domain.com/404',
            'expired_url' => 'https://new-domain.com/expired',
        ]);

    $response->assertStatus(201);
});

it('can update a domain', function () {
    $domain = Domain::factory()->create([
        'workspace_id' => $this->user->current_workspace_id,
    ]);

    $response = $this
        ->withToken($this->token->token)
        ->put(route('api.domains.update', $domain->id), [
            'domain' => 'updated-domain.com',
            'not_found_url' => 'https://updated-domain.com/404',
            'expired_url' => 'https://updated-domain.com/expired',
        ]);

    $response->assertStatus(200);
});

it('can delete a domain', function () {
    $domain = Domain::factory()->create([
        'workspace_id' => $this->user->current_workspace_id,
    ]);

    $response = $this
        ->withToken($this->token->token)
        ->delete(route('api.domains.destroy', $domain->id));

    $response->assertStatus(200);
});
