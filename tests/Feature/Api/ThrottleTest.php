<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('rate limits the api', function () {
    // The limiter returns Limit::none() locally, so the test asks for the
    // environment the limit actually applies in.
    app()->detectEnvironment(fn () => 'production');

    $user = User::factory()->withWorkspace()->create();
    $token = apiTokenFor($user);

    $last = null;
    for ($i = 0; $i < 65; $i++) {
        $last = $this->withToken($token)->json('GET', route('api.links.index'));
        if ($last->getStatusCode() === 429) {
            break;
        }
    }

    expect($last->getStatusCode())->toBe(429);
});
