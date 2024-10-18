<?php

declare(strict_types=1);

namespace App\Enums\Domain;

enum Status: string
{
    case ACTIVE = 'active';
    case PENDING = 'pending';
}
