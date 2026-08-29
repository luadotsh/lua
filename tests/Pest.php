<?php

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

pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature');

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
function apiTokenFor(App\Models\User $user, ?string $name = 'Test key'): string
{
    // Personal access grants need a client; create one per test database.
    if (! Laravel\Passport\Passport::client()->newQuery()
        ->whereJsonContains('grant_types', 'personal_access')
        ->exists()) {
        app(Laravel\Passport\ClientRepository::class)->createPersonalAccessGrantClient(
            'Test Personal Access Client',
            'users',
        );
    }

    return App\Actions\ApiKey\CreateApiKey::execute(
        $user,
        $user->currentWorkspace,
        ['name' => $name],
    )['plain_token'];
}
