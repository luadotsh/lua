<?php

declare(strict_types=1);

use App\Services\UserAgentService;

beforeEach(fn () => $this->svc = new UserAgentService);

dataset('agents', [
    // Every Chromium browser carries "Chrome" in its agent, so each of these
    // used to be reported as Chrome.
    'edge' => ['Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120 Safari/537.36 Edg/120', 'Edge', 'Windows', 'Desktop'],
    'opera' => ['Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 Chrome/120 Safari/537.36 OPR/106', 'Opera', 'Windows', 'Desktop'],
    'vivaldi' => ['Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 Chrome/120 Safari/537.36 Vivaldi/6.5', 'Vivaldi', 'Linux', 'Desktop'],
    'samsung' => ['Mozilla/5.0 (Linux; Android 13) AppleWebKit/537.36 SamsungBrowser/23.0 Chrome/115 Mobile Safari/537.36', 'Samsung Internet', 'Android', 'Mobile'],
    'chrome' => ['Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 Chrome/120 Safari/537.36', 'Chrome', 'macOS', 'Desktop'],
    'safari' => ['Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 Version/17.0 Safari/605.1.15', 'Safari', 'macOS', 'Desktop'],
    'firefox' => ['Mozilla/5.0 (Windows NT 10.0; rv:121.0) Gecko/20100101 Firefox/121.0', 'Firefox', 'Windows', 'Desktop'],
    // An iPad says "like Mac OS X" and carries "Mobile", so it used to come
    // back as iOS on a Mobile device.
    'ipad' => ['Mozilla/5.0 (iPad; CPU OS 17_0 like Mac OS X) AppleWebKit/605.1.15 Version/17.0 Mobile/15E148 Safari/604.1', 'Safari', 'iPadOS', 'Tablet'],
    'iphone' => ['Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 Version/17.0 Mobile/15E148 Safari/604.1', 'Safari', 'iOS', 'Mobile'],
    'android tablet' => ['Mozilla/5.0 (Linux; Android 13; SM-X700) AppleWebKit/537.36 Chrome/120 Safari/537.36', 'Chrome', 'Android', 'Tablet'],
    'chrome os' => ['Mozilla/5.0 (X11; CrOS x86_64 14541.0.0) AppleWebKit/537.36 Chrome/120 Safari/537.36', 'Chrome', 'Chrome OS', 'Desktop'],
    'curl' => ['curl/8.4.0', 'curl', 'Unknown', 'Desktop'],
]);

it('identifies the browser, os and device', function (string $agent, string $browser, string $os, string $device) {
    expect($this->svc->getBrowser($agent))->toBe($browser)
        ->and($this->svc->getOS($agent))->toBe($os)
        ->and($this->svc->getDevice($agent))->toBe($device);
})->with('agents');

it('falls back rather than guessing on an agent it cannot read', function () {
    expect($this->svc->getBrowser('something-else'))->toBe('Unknown')
        ->and($this->svc->getOS('something-else'))->toBe('Unknown');
});
