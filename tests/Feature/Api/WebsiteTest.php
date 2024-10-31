<?php

declare(strict_types=1);

it('can get a favicon from website', function () {
    $response = $this
        ->get(route('websites.favicon', ['domain' => 'github.com']));

    $response->assertStatus(200);
});
