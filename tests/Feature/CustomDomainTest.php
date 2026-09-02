<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// routes/site.php still registers '/' on the main domain (config('domains.main'))
// ahead of the {key?} catch-all, so a bare request to the main domain itself
// renders Site/Home rather than reaching this middleware — that is covered in
// tests/Feature/App/RedirectTest.php and changes only when Task 17 removes the
// site routes. These tests exercise the middleware's own branches directly, on
// hosts that do reach it.

it('sends a bare request on a domain lua provides to the website', function (): void {
    config([
        'app.website' => 'https://www.lua.sh',
        'domains.available' => ['go.lua.test'],
    ]);

    $this->get('https://go.lua.test')
        ->assertRedirect('https://www.lua.sh');
});

it('sends an unknown domain to the website', function (): void {
    config(['app.website' => 'https://www.lua.sh']);

    $this->get('http://not-a-customer-domain.test/')
        ->assertRedirect('https://www.lua.sh');
});
