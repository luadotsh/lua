<?php

declare(strict_types=1);

namespace App\Enums\Link;

enum Device: string
{
    case DESKTOP = 'Desktop';
    case MOBILE = 'Mobile';
    case TABLET = 'Tablet';
}
