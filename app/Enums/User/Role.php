<?php

declare(strict_types=1);

namespace App\Enums\User;

enum Role: string
{
    case ROLE_OWNER = 'OWNER';
    case ROLE_ADMIN = 'ADMIN';
    case ROLE_USER = 'USER';
}
