<?php

declare(strict_types=1);

namespace App\Enums\Link;

/**
 * Values match the keys resolves/os.ts maps to icons, so every stored value
 * has a mark beside it in the analytics breakdown.
 */
enum Os: string
{
    case ANDROID = 'Android';
    case IOS = 'iOS';
    case IPADOS = 'iPadOS';
    case WINDOWS = 'Windows';
    case MACOS = 'macOS';
    case CHROME_OS = 'Chrome OS';
    case LINUX = 'Linux';
    case UNKNOWN = 'Unknown';
}
