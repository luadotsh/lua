<?php

declare(strict_types=1);

namespace App\Actions\ApiKey;

use App\Models\AccessToken;
use App\Models\User;
use App\Models\Workspace;

class CreateApiKey
{
    /**
     * @return list<string>
     */
    public static function expiresAtRules(): array
    {
        return ['nullable', 'date', 'after_or_equal:today'];
    }

    /**
     * The plain token is returned once and never stored; the caller is
     * responsible for showing it to the user before it is lost.
     *
     * @param  array{name: string, expires_at?: string|null}  $data
     * @return array{token: AccessToken, plain_token: string}
     */
    public static function execute(User $user, Workspace $workspace, array $data): array
    {
        $result = $user->createToken(data_get($data, 'name'));
        $token = AccessToken::query()->findOrFail($result->token->id);
        $expiresAt = data_get($data, 'expires_at');

        $token->forceFill([
            'workspace_id' => $workspace->id,
            // Empty means never expire, overriding the JWT lifetime Passport
            // writes by default.
            'expires_at' => filled($expiresAt)
                ? now()->parse($expiresAt)->endOfDay()
                : null,
        ])->saveQuietly();

        return [
            'token' => $token->refresh(),
            'plain_token' => $result->accessToken,
        ];
    }
}
