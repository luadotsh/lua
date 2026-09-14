<?php

use App\Actions\ApiKey\CreateApiKey;
use App\Enums\User\Role;
use App\Models\AccessToken;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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

/**
 * Attach another workspace the user can pick on the MCP consent screen.
 *
 * @param  array<string, mixed>  $attributes
 */
function attachWorkspaceTo(User $user, array $attributes = []): Workspace
{
    $workspace = Workspace::factory()->create($attributes);
    $workspace->forceFill(['owner_id' => $user->id])->save();
    $user->workspaces()->attach($workspace->id, ['role' => Role::ROLE_ADMIN->value]);

    return $workspace->fresh();
}

/**
 * Insert an OAuth client suitable for MCP connection tests.
 */
function mcpOauthClient(string $name = 'My Agent'): string
{
    $id = (string) Str::uuid();

    DB::table('oauth_clients')->insert([
        'id' => $id,
        'name' => $name,
        'secret' => null,
        'provider' => null,
        'redirect_uris' => '[]',
        'grant_types' => json_encode(['authorization_code', 'refresh_token']),
        'revoked' => false,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return $id;
}

/**
 * @return array<string, string>
 */
function oauthAuthorizeQuery(
    string $clientId,
    string $redirectUri = 'https://client.example/callback',
    string $prompt = 'consent',
): array {
    $verifier = Str::random(64);
    $challenge = rtrim(strtr(base64_encode(hash('sha256', $verifier, true)), '+/', '-_'), '=');

    return [
        'client_id' => $clientId,
        'redirect_uri' => $redirectUri,
        'response_type' => 'code',
        'scope' => 'mcp:use',
        'state' => 'test-state',
        'code_challenge' => $challenge,
        'code_challenge_method' => 'S256',
        'prompt' => $prompt,
    ];
}

/**
 * Create an active OAuth access token for MCP connection tests.
 *
 * @param  list<string>  $scopes
 */
function mcpAccessToken(
    User $user,
    string $clientId,
    ?Workspace $workspace = null,
    array $scopes = ['mcp:use'],
): AccessToken {
    $token = new AccessToken;
    $token->forceFill([
        'id' => Str::random(80),
        'user_id' => $user->id,
        'client_id' => $clientId,
        'workspace_id' => $workspace?->id,
        'name' => 'MCP',
        'scopes' => $scopes,
        'revoked' => false,
        'expires_at' => now()->addYear(),
    ])->save();

    return $token->refresh();
}
