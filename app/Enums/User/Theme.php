<?php

declare(strict_types=1);

namespace App\Enums\User;

enum Theme: string
{
    case LIGHT = 'LIGHT';
    case DARK = 'DARK';
    case SYSTEM = 'SYSTEM';
}
