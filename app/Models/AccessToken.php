<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Passport\Token;

class AccessToken extends Token
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'user_id',
        'client_id',
        'workspace_id',
        'name',
        'scopes',
        'revoked',
        'expires_at',
        'last_used_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'scopes' => 'json',
            'revoked' => 'bool',
            'expires_at' => 'datetime',
            'last_used_at' => 'datetime',
        ];
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    /**
     * Passport resolves the user model through the OAuth client's provider,
     * which breaks eager-loading `user` (the relation is built on an empty
     * token with no client). Tokens here always belong to App\Models\User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Personal access tokens are the REST API keys created from settings.
     */
    public function isPersonalAccessToken(): bool
    {
        $this->loadMissing('client');

        return $this->client !== null
            && ! $this->client->revoked
            && $this->client->hasGrantType('personal_access');
    }

    /**
     * An OAuth grant carrying mcp:use, issued to an MCP client rather than
     * minted as a personal access token.
     */
    public function isMcpOAuthGrant(): bool
    {
        $this->loadMissing('client');

        if ($this->revoked || ! in_array('mcp:use', $this->scopes ?? [], true)) {
            return false;
        }

        return $this->client !== null
            && ! $this->client->revoked
            && ! $this->client->hasGrantType('personal_access');
    }

    public function isActiveMcpGrant(): bool
    {
        return $this->isMcpOAuthGrant()
            && ($this->expires_at === null || ! $this->expires_at->isPast());
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeMcpOAuth(Builder $query): Builder
    {
        return $query
            ->whereJsonContains('scopes', 'mcp:use')
            ->whereHas(
                'client',
                fn (Builder $client): Builder => $client
                    ->where('revoked', false)
                    ->whereJsonDoesntContain('grant_types', 'personal_access'),
            );
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopePersonalAccessApiKey(Builder $query): Builder
    {
        return $query->whereHas(
            'client',
            fn (Builder $client): Builder => $client
                ->where('revoked', false)
                ->whereJsonContains('grant_types', 'personal_access'),
        );
    }
}
