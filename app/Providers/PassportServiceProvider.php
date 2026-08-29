<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\AccessToken;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class PassportServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Passport::useTokenModel(AccessToken::class);

        // API keys may be created with no expiry. Passport still embeds a JWT
        // `exp`, so keep that far out and enforce the optional
        // oauth_access_tokens.expires_at in LoadWorkspaceFromToken instead.
        Passport::personalAccessTokensExpireIn(now()->addYears(100));

        Passport::tokensCan([
            'mcp:use' => 'Use the MCP server',
        ]);
    }
}
