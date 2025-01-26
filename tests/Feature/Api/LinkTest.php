<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\User;
use App\Models\Link;
use App\Models\ApiToken;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->withWorkspace()->create();
    $this->token = ApiToken::factory()->create([
        'workspace_id' => $this->user->current_workspace_id,
    ]);
});

it('can list links', function () {
    Link::factory(10)->create([
        'workspace_id' => $this->user->current_workspace_id,
    ]);

    $response = $this
        ->withToken($this->token->token)
        ->get(route('api.links.index'));

    $response->assertStatus(200)
        ->assertJsonCount(10, 'data');
});

it('can create a new link', function () {
    $response = $this
        ->withToken($this->token->token)
        ->post(route('api.links.store'), [
            'key' => 'new-link',
            'domain' => 'lua.sh',
            'url' => 'https://lua.sh',
        ]);

    $response->assertStatus(201);
});

it('can create a new link with ios and android', function () {
    $response = $this
        ->withToken($this->token->token)
        ->post(route('api.links.store'), [
            'key' => 'new-link',
            'domain' => 'lua.sh',
            'url' => 'https://lua.sh',
            'ios' => 'https://apps.apple.com/app/333903271',
            'android' => 'https://play.google.com/store/apps/details?id=com.twitter.android',
        ]);

    $response->assertStatus(201)
        ->assertJsonFragment([
            'ios' => 'https://apps.apple.com/app/333903271',
            'android' => 'https://play.google.com/store/apps/details?id=com.twitter.android',
        ]);
});

it('can create a new link with expires_at', function () {

    $expiresAt = now()->addDay()->format('Y-m-d H:i:s');

    $response = $this
        ->withToken($this->token->token)
        ->post(route('api.links.store'), [
            'key' => 'new-link',
            'domain' => 'lua.sh',
            'url' => 'https://lua.sh',
            'expires_at' => $expiresAt,
        ]);

    $response->assertStatus(201)
        ->assertJsonFragment([
            'url' => 'https://lua.sh',
            'expires_at' => $expiresAt,
        ]);
});

it('can update a link', function () {
    $link = Link::factory()->create([
        'workspace_id' => $this->user->current_workspace_id,
    ]);

    $response = $this
        ->withToken($this->token->token)
        ->put(route('api.links.update', $link->id), [
            'key' => 'updated-link',
            'domain' => 'lua.sh',
            'url' => 'https://lua.sh',
        ]);

    $response->assertStatus(200);
});

it('can delete a link', function () {
    $link = Link::factory()->create([
        'workspace_id' => $this->user->current_workspace_id,
    ]);

    $response = $this
        ->withToken($this->token->token)
        ->delete(route('api.links.destroy', $link->id));

    $response->assertStatus(200);
});

it('can create a new link with password', function () {
    $response = $this
        ->withToken($this->token->token)
        ->post(route('api.links.store'), [
            'key' => 'new-link',
            'domain' => 'lua.sh',
            'url' => 'https://lua.sh',
            'password' => 'password',
        ]);

    $response->assertStatus(201);
});

it('can validate password', function () {
    $link = Link::factory()->create([
        'workspace_id' => $this->user->current_workspace_id,
        'password' => 'password',
    ]);

    $response = $this
        ->withToken($this->token->token)
        ->post(route('links.password.validate', $link->key), [
            'password' => 'password',
        ]);

    $response->assertRedirect($link->url);
});

it('can not validate password', function () {
    $link = Link::factory()->create([
        'workspace_id' => $this->user->current_workspace_id,
        'password' => 'password',
    ]);

    $response = $this
        ->withToken($this->token->token)
        ->from(route('links.password', $link->key))
        ->post(route('links.password.validate', $link->key), [
            'password' => 'wrong-password',
        ]);

    $response
        ->assertSessionHasErrors('password')
        ->assertRedirect(route('links.password', $link->key));
});
