<?php

declare(strict_types=1);

namespace App\Enums\Link;

/**
 * Values match the keys resolves/browsers.ts maps to icons.
 */
enum Browser: string
{
    case EDGE = 'Edge';
    case OPERA = 'Opera';
    case SAMSUNG_INTERNET = 'Samsung Internet';
    case YANDEX = 'Yandex Browser';
    case UC = 'UC Browser';
    case HUAWEI = 'Huawei Browser';
    case DUCKDUCKGO = 'DuckDuckGo';
    case VIVALDI = 'Vivaldi';
    case BRAVE = 'Brave';
    case CHROME = 'Chrome';
    case FIREFOX = 'Firefox';
    case SAFARI = 'Safari';
    case CURL = 'curl';
    case UNKNOWN = 'Unknown';
}
