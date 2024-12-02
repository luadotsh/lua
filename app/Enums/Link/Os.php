<?php

declare(strict_types=1);

namespace App\Enums\Link;

enum Os: string
{
    case ANDROID = 'Android';
    case IOS = 'iOS';
    case WINDOWS = 'Windows';
    case MACOS = 'MacOS';
    case LINUX = 'Linux';
    case UNKNOWN = 'Unknown OS';
}
