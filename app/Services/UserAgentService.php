<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\Link\Os;

class UserAgentService
{
    /**
     * Get the browser from the User-Agent.
     *
     * @param string $userAgent
     * @return string
     */
    public function getBrowser(string $userAgent): string
    {
        $browserArray = [
            'Chrome' => 'Chrome',
            'Firefox' => 'Firefox',
            'Safari' => 'Safari',
            'Edge' => 'Edge',
            'Opera' => 'Opera',
            'Internet Explorer' => 'MSIE|Trident',
        ];

        foreach ($browserArray as $key => $pattern) {
            if (preg_match("/$pattern/i", $userAgent)) {
                return $key;
            }
        }

        return 'Unknown Browser';
    }

    /**
     * Get the OS from the User-Agent.
     *
     * @param string $userAgent
     * @return string
     */
    public function getOS(string $userAgent): string
    {
        $osArray = [
            Os::ANDROID->value => 'Android',
            Os::IOS->value => 'iPhone|iPad',
            Os::WINDOWS->value => 'Windows',
            Os::MACOS->value => '(Mac_PowerPC)|(Macintosh)',
            Os::LINUX->value => 'Linux',
        ];

        foreach ($osArray as $key => $pattern) {
            if (preg_match("/$pattern/i", $userAgent)) {
                return $key;
            }
        }

        return Os::UNKNOWN->value;
    }

    /**
     * Get the device type from the User-Agent.
     *
     * @param string $userAgent
     * @return string
     */
    public function getDevice(string $userAgent): string
    {
        if (preg_match("/Mobile|Android|iPhone|iPad/i", $userAgent)) {
            return 'Mobile';
        } elseif (preg_match("/Tablet|iPad/i", $userAgent)) {
            return 'Tablet';
        }

        return 'Desktop';
    }

    /**
     * Get the language from the request headers.
     *
     * @param string $acceptLanguage
     * @return string
     */
    public function getLanguage(array $languages): string
    {
        // Check if the array is not empty and return the first preferred language
        if (!empty($languages)) {
            // Trim any white space and return the first element
            return trim($languages[0]);
        }

        // Return a fallback language if the array is empty or not valid
        return 'Unknown Language';
    }

    /**
     * Get the referers from the request headers.
     *
     * @param string|null $referers
     * @return string
     */
    public function getReferer(?string $referers): string
    {
        // Check if referers is present and not empty
        if ($referers && !empty($referers)) {
            return $referers;
        }

        return 'Direct';
    }

    /**
     * Get a summary of the user details based on the User-Agent.
     *
     * @param string $userAgent
     * @param string $languages
     * @return array
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
