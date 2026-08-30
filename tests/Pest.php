<?php

use App\Actions\ApiKey\CreateApiKey;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Passport;
use Tests\BrowserTestCase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

pest()->extend(BrowserTestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Browser');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function something()
{
    // ..
}

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

/**
 * Issue a Passport personal access token bound to the user's current
 * workspace, the same way the settings screen does, and return the plain
 * token to send as a bearer.
 */
function apiTokenFor(User $user, ?string $name = 'Test key'): string
{
    // Personal access grants need a client; create one per test database.
    if (! Passport::client()->newQuery()
        ->whereJsonContains('grant_types', 'personal_access')
        ->exists()) {
        app(ClientRepository::class)->createPersonalAccessGrantClient(
            'Test Personal Access Client',
            'users',
        );
    }

    return CreateApiKey::execute(
        $user,
        $user->currentWorkspace,
        ['name' => $name],
    )['plain_token'];
}
