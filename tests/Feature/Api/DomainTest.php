<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

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
