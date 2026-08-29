<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\AccessToken;
use App\Passport\AccessTokenRepository;
use App\Passport\AuthCode;
use App\Passport\AuthCodeRepository;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Bridge\AccessTokenRepository as PassportAccessTokenRepository;
use Laravel\Passport\Bridge\AuthCodeRepository as PassportAuthCodeRepository;
use Laravel\Passport\Passport;

class PassportServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Both overrides exist to persist workspace_id alongside the token.
        $this->app->bind(PassportAuthCodeRepository::class, AuthCodeRepository::class);
        $this->app->bind(PassportAccessTokenRepository::class, AccessTokenRepository::class);
    }

    public function boot(): void
    {
        Passport::useTokenModel(AccessToken::class);
        Passport::useAuthCodeModel(AuthCode::class);

        // API keys may be created with no expiry. Passport still embeds a JWT
        // `exp`, so keep that far out and enforce the optional
        // oauth_access_tokens.expires_at in LoadWorkspaceFromToken instead.
        Passport::personalAccessTokensExpireIn(now()->addYears(100));

        Passport::tokensCan([
            'mcp:use' => 'Use the MCP server',
        ]);
    }
}
