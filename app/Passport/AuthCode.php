<?php

declare(strict_types=1);

namespace App\Passport;

use Laravel\Passport\AuthCode as PassportAuthCode;

class AuthCode extends PassportAuthCode
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'user_id',
        'client_id',
        'workspace_id',
        'scopes',
        'revoked',
        'expires_at',
    ];
}
