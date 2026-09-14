<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * @return list<string>
 */
function mcpNativeAgentSchemes(): array
{
    return [
        'antigravity',
        'chatgpt',
        'claude',
        'claude-cli',
        'claude-code',
        'claude-desktop',
        'claudeai',
        'codex',
        'codium',
        'continue',
        'cursor',
        'devin',
        'goose',
        'grok',
        'hermes',
        'jetbrains',
        'kiro',
        'lmstudio',
        'opencode',
        'raycast',
        'trae',
        'vscode',
        'vscode-insiders',
        'warp',
        'windsurf',
        'xai',
        'zed',
    ];
}

test('dynamic oauth client registration is not wrapped in the web middleware group', function () {
    $route = collect(app('router')->getRoutes())->first(
        fn ($route): bool => in_array('POST', $route->methods(), true)
            && $route->uri() === 'oauth/register',
    );

    expect($route)->not->toBeNull()
        ->and($route->gatherMiddleware())->not->toContain('web');
});

test('dynamic oauth client registration is rate limited', function () {
    $payload = [
        'client_name' => 'MCP Client',
        'redirect_uris' => ['https://client.example/callback'],
        'grant_types' => ['authorization_code'],
        'response_types' => ['code'],
        'token_endpoint_auth_method' => 'none',
    ];

    for ($attempt = 0; $attempt < 30; $attempt++) {
        $this->postJson('/oauth/register', $payload)->assertSuccessful();
    }

    $this->postJson('/oauth/register', $payload)->assertTooManyRequests();
});

test('mcp custom schemes config lists every supported native agent', function () {
    expect(config('mcp.custom_schemes'))->toEqual(mcpNativeAgentSchemes());
});

test('dynamic oauth registration accepts every configured custom callback scheme', function (string $scheme) {
    $this->postJson('/oauth/register', [
        'client_name' => 'Native MCP Client',
        'redirect_uris' => ["{$scheme}://oauth/callback"],
        'grant_types' => ['authorization_code'],
        'response_types' => ['code'],
        'token_endpoint_auth_method' => 'none',
    ])->assertCreated();
})->with(mcpNativeAgentSchemes());

test('dynamic oauth registration accepts documented native and loopback callbacks', function (string $redirectUri) {
    $this->postJson('/oauth/register', [
        'client_name' => 'Native MCP Client',
        'redirect_uris' => [$redirectUri],
        'grant_types' => ['authorization_code'],
        'response_types' => ['code'],
        'token_endpoint_auth_method' => 'none',
    ])->assertCreated();
})->with([
    'cursor anysphere' => 'cursor://anysphere.cursor-mcp/oauth/callback',
    'cursor mcp host' => 'cursor://cursor.mcp/oauth/callback',
    'raycast oauth' => 'raycast://oauth',
    'cursor loopback' => 'http://localhost:8787/callback',
    'opencode loopback' => 'http://127.0.0.1:19876/mcp/oauth/callback',
    'hermes loopback' => 'http://127.0.0.1:8765/callback',
    'claude https' => 'https://claude.ai/api/mcp/auth_callback',
    'chatgpt https' => 'https://chatgpt.com/aip/mcp/oauth/callback',
    'cursor web' => 'https://www.cursor.com/agents/mcp/oauth/callback',
    'antigravity https' => 'https://antigravity.google/oauth-callback',
]);

test('dynamic oauth registration rejects unknown and unsafe callback schemes', function (string $redirectUri) {
    $this->postJson('/oauth/register', [
        'client_name' => 'Native MCP Client',
        'redirect_uris' => [$redirectUri],
        'grant_types' => ['authorization_code'],
        'response_types' => ['code'],
        'token_endpoint_auth_method' => 'none',
    ])
        ->assertBadRequest()
        ->assertJson([
            'error' => 'invalid_redirect_uri',
        ]);
})->with([
    'javascript' => 'javascript:alert(1)',
    'data' => 'data:text/html,hi',
    'file' => 'file:///etc/passwd',
    'unknown' => 'notanagent://oauth/callback',
    'cursor missing host' => 'cursor:/callback',
]);

test('dynamic oauth registration accepts extra custom schemes from config', function () {
    config()->set('mcp.custom_schemes', [...config('mcp.custom_schemes'), 'myagent']);

    $this->postJson('/oauth/register', [
        'client_name' => 'Custom Agent',
        'redirect_uris' => ['myagent://oauth/callback'],
        'grant_types' => ['authorization_code'],
        'response_types' => ['code'],
        'token_endpoint_auth_method' => 'none',
    ])->assertCreated();
});

test('mcp oauth consent page lists every workspace the user can access', function () {
    $user = User::factory()->withWorkspace()->create();
    $alpha = $user->currentWorkspace;
    $alpha->update(['name' => 'Alpha']);
    $beta = attachWorkspaceTo($user, ['name' => 'Beta']);
    $user->update(['current_workspace_id' => $alpha->id]);

    $clientId = mcpOauthClient('Claude');
    DB::table('oauth_clients')->where('id', $clientId)->update([
        'redirect_uris' => json_encode(['https://client.example/callback']),
    ]);

    $this->actingAs($user)
        ->get(route('passport.authorizations.authorize', oauthAuthorizeQuery($clientId)))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('mcp/Authorize')
            ->where('selectedWorkspaceId', (string) $alpha->id)
            ->has('workspaces', 2)
            ->where('workspaces.0.name', 'Alpha')
            ->where('workspaces.1.name', 'Beta')
            ->where('workspaces.0.id', (string) $alpha->id)
            ->where('workspaces.1.id', (string) $beta->id));
});

test('mcp oauth always shows consent even when scopes were previously granted', function () {
    $user = User::factory()->withWorkspace()->create();
    $workspace = $user->currentWorkspace;

    $clientId = mcpOauthClient('Reconnect Agent');
    DB::table('oauth_clients')->where('id', $clientId)->update([
        'redirect_uris' => json_encode(['https://client.example/callback']),
    ]);
    mcpAccessToken($user, $clientId, $workspace);

    $query = oauthAuthorizeQuery($clientId);
    unset($query['prompt']);

    $this->actingAs($user)
        ->get(route('passport.authorizations.authorize', $query))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('mcp/Authorize')
            ->where('client.name', 'Reconnect Agent')
            ->where('selectedWorkspaceId', (string) $workspace->id));
});

test('guests are redirected to login before an unknown oauth client is rejected', function () {
    $unknownClientId = (string) Str::uuid();

    $this->get(route('passport.authorizations.authorize', oauthAuthorizeQuery($unknownClientId)))
        ->assertRedirect(route('login'));
});

test('guests are redirected to login before consent for a registered oauth client', function () {
    $clientId = mcpOauthClient('Guest Login Agent');
    DB::table('oauth_clients')->where('id', $clientId)->update([
        'redirect_uris' => json_encode(['https://client.example/callback']),
    ]);

    $this->get(route('passport.authorizations.authorize', oauthAuthorizeQuery($clientId)))
        ->assertRedirect(route('login'));
});

test('authenticated users still receive invalid_client for an unknown oauth client', function () {
    $user = User::factory()->withWorkspace()->create();
    $unknownClientId = (string) Str::uuid();

    $this->actingAs($user)
        ->getJson(route('passport.authorizations.authorize', oauthAuthorizeQuery($unknownClientId)))
        ->assertUnauthorized()
        ->assertJson([
            'error' => 'invalid_client',
        ]);
});

test('browser requests render an inertia error page for an unknown oauth client', function () {
    $user = User::factory()->withWorkspace()->create();
    $unknownClientId = (string) Str::uuid();

    $this->actingAs($user)
        ->get(route('passport.authorizations.authorize', oauthAuthorizeQuery($unknownClientId)))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('mcp/AuthorizeError')
            ->where('error', 'invalid_client')
            ->where('errorDescription', 'Client authentication failed'));
});

test('prompt=none guests receive login_required redirect instead of an inertia error page', function () {
    $redirectUri = 'https://client.example/callback';
    $clientId = mcpOauthClient('Silent Auth Guest');
    DB::table('oauth_clients')->where('id', $clientId)->update([
        'redirect_uris' => json_encode([$redirectUri]),
    ]);

    $response = $this->get(route(
        'passport.authorizations.authorize',
        oauthAuthorizeQuery($clientId, $redirectUri, prompt: 'none'),
    ));

    $response->assertRedirect();
    expect($response->headers->get('Location'))
        ->toStartWith($redirectUri)
        ->toContain('error=login_required');
});

test('prompt=none authenticated users receive consent_required redirect instead of an inertia error page', function () {
    $user = User::factory()->withWorkspace()->create();
    $redirectUri = 'https://client.example/callback';
    $clientId = mcpOauthClient('Silent Auth User');
    DB::table('oauth_clients')->where('id', $clientId)->update([
        'redirect_uris' => json_encode([$redirectUri]),
    ]);

    $response = $this->actingAs($user)
        ->get(route(
            'passport.authorizations.authorize',
            oauthAuthorizeQuery($clientId, $redirectUri, prompt: 'none'),
        ));

    $response->assertRedirect();
    expect($response->headers->get('Location'))
        ->toStartWith($redirectUri)
        ->toContain('error=consent_required');
});
