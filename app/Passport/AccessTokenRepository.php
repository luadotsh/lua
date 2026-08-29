<?php

declare(strict_types=1);

namespace App\Passport;

use App\Models\AccessToken;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Contracts\Events\Dispatcher;
use Laravel\Passport\Bridge\AccessTokenRepository as PassportAccessTokenRepository;
use Laravel\Passport\Events\AccessTokenCreated;
use Laravel\Passport\Passport;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;

/**
 * Binds workspace_id onto OAuth tokens at issue time. Personal access tokens
 * stay null here so CreateApiKey can bind them afterwards from the request.
 *
 * Authorization-code grants take the workspace from the consent code only.
 * Refresh grants inherit it from the token being refreshed, and only while the
 * user still belongs to that workspace. Anything else fails closed.
 */
class AccessTokenRepository extends PassportAccessTokenRepository
{
    public function __construct(
        Dispatcher $events,
        private OAuthPayloadDecryptor $decryptor,
    ) {
        parent::__construct($events);
    }

    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity): void
    {
        $id = $accessTokenEntity->getIdentifier();
        $userId = $accessTokenEntity->getUserIdentifier();
        $clientId = $accessTokenEntity->getClient()->getIdentifier();
        $workspaceId = null;

        if ($this->clientRequiresWorkspace($clientId)) {
            $workspaceId = $this->resolveWorkspaceId($userId);

            if ($workspaceId === null) {
                throw OAuthServerException::invalidGrant(
                    'Unable to bind this connection to a workspace. Reconnect from a workspace you belong to.',
                );
            }
        }

        Passport::token()->forceFill([
            'id' => $id,
            'user_id' => $userId,
            'client_id' => $clientId,
            'workspace_id' => $workspaceId,
            'scopes' => $accessTokenEntity->getScopes(),
            'revoked' => false,
            'expires_at' => $accessTokenEntity->getExpiryDateTime(),
        ])->save();

        $this->events->dispatch(new AccessTokenCreated($id, $userId, $clientId));
    }

    private function clientRequiresWorkspace(string $clientId): bool
    {
        $client = Passport::client()->newQuery()->find($clientId);

        return $client !== null && ! $client->hasGrantType('personal_access');
    }

    private function resolveWorkspaceId(?string $userId): ?string
    {
        $user = $userId ? User::query()->find($userId) : null;

        if (! $user instanceof User) {
            return null;
        }

        return match (request('grant_type')) {
            'authorization_code' => $this->ownedWorkspace($user, $this->workspaceFromAuthCode()),
            'refresh_token' => $this->ownedWorkspace($user, $this->workspaceFromRefreshedToken()),
            default => null,
        };
    }

    private function workspaceFromAuthCode(): mixed
    {
        return AuthCode::query()
            ->find($this->payloadId('code', 'auth_code_id'))?->workspace_id;
    }

    private function workspaceFromRefreshedToken(): mixed
    {
        return AccessToken::query()
            ->find($this->payloadId('refresh_token', 'access_token_id'))?->workspace_id;
    }

    private function payloadId(string $input, string $key): mixed
    {
        $encrypted = request($input);

        if (! is_string($encrypted) || $encrypted === '') {
            return null;
        }

        return data_get($this->decryptor->decrypt($encrypted), $key) ?: null;
    }

    private function ownedWorkspace(User $user, mixed $workspaceId): ?string
    {
        if (blank($workspaceId)) {
            return null;
        }

        $workspace = Workspace::query()->find($workspaceId);

        return $workspace && $user->belongsToWorkspace($workspace)
            ? $workspace->id
            : null;
    }
}
