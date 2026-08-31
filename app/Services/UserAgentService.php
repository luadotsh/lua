<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\Link\Browser;
use App\Enums\Link\Device;
use App\Enums\Link\Os;

class UserAgentService
{
    /**
     * Browsers, most specific first. Edge, Opera, Vivaldi, Brave and the rest
     * all carry "Chrome" in their user agent, so matching Chrome first would
     * swallow every one of them — which is exactly what used to happen.
     *
     * @var array<string, string>
     */
    private const BROWSERS = [
        Browser::EDGE->value => 'Edg[A-Z]?\/|Edge\/',
        Browser::OPERA->value => 'OPR\/|Opera',
        Browser::SAMSUNG_INTERNET->value => 'SamsungBrowser',
        Browser::YANDEX->value => 'YaBrowser',
        Browser::UC->value => 'UCBrowser',
        Browser::HUAWEI->value => 'HuaweiBrowser',
        Browser::DUCKDUCKGO->value => 'DuckDuckGo',
        Browser::VIVALDI->value => 'Vivaldi',
        Browser::BRAVE->value => 'Brave',
        Browser::FIREFOX->value => 'Firefox|FxiOS',
        Browser::CHROME->value => 'Chrome|CriOS',
        Browser::SAFARI->value => 'Safari',
        Browser::CURL->value => 'curl',
    ];

    /**
     * Operating systems, most specific first: an iPad reports "like Mac OS X",
     * so macOS has to be checked after it.
     *
     * @var array<string, string>
     */
    private const OPERATING_SYSTEMS = [
        Os::IPADOS->value => 'iPad',
        Os::IOS->value => 'iPhone|iPod',
        Os::ANDROID->value => 'Android',
        Os::CHROME_OS->value => 'CrOS',
        Os::WINDOWS->value => 'Windows',
        Os::MACOS->value => 'Mac_PowerPC|Macintosh|Mac OS X',
        Os::LINUX->value => 'Linux|X11',
    ];

    public function getBrowser(string $userAgent): string
    {
        foreach (self::BROWSERS as $name => $pattern) {
            if (preg_match("/{$pattern}/i", $userAgent)) {
                return $name;
            }
        }

        return Browser::UNKNOWN->value;
    }

    public function getOS(string $userAgent): string
    {
        foreach (self::OPERATING_SYSTEMS as $name => $pattern) {
            if (preg_match("/{$pattern}/i", $userAgent)) {
                return $name;
            }
        }

        return Os::UNKNOWN->value;
    }

    /**
     * Tablets are checked first: an iPad's user agent also contains "Mobile",
     * so the old order made Tablet unreachable.
     */
    public function getDevice(string $userAgent): string
    {
        if (preg_match('/iPad|Tablet|PlayBook|Silk|(Android(?!.*Mobile))/i', $userAgent)) {
            return Device::TABLET->value;
        }

        if (preg_match('/Mobile|Android|iPhone|iPod/i', $userAgent)) {
            return Device::MOBILE->value;
        }

        return Device::DESKTOP->value;
    }

    /**
     * Get the language from the request headers.
     *
     * @param  string  $acceptLanguage
     */
    public function getLanguage(array $languages): string
    {
        // Check if the array is not empty and return the first preferred language
        if (! empty($languages)) {
            // Trim any white space and return the first element
            return trim($languages[0]);
        }

        // Return a fallback language if the array is empty or not valid
        return 'Unknown Language';
    }

    /**
     * Get the referers from the request headers.
     */
    public function getReferer(?string $referers): string
    {
        // Check if referers is present and not empty
        if ($referers && ! empty($referers)) {
            return $referers;
        }

        return 'Direct';
    }

    /**
     * Get a summary of the user details based on the User-Agent.
     *
     * @param  string  $languages
     */
    public function getUserDetails(string $userAgent, array $languages): array
    {
        return [
            'browser' => $this->getBrowser($userAgent),
            'os' => $this->getOS($userAgent),
            'device' => $this->getDevice($userAgent),
            'language' => $this->getLanguage($languages),
        ];
    }
}
