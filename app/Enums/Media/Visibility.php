<?php

declare(strict_types=1);

namespace App\Enums\Media;

enum Visibility: string
{
    case PUBLIC = 'public';
    case PRIVATE = 'private';
}
