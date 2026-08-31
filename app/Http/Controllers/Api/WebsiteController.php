<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WebsiteController extends Controller
{
    /**
     * Proxies a site's favicon so the browser never asks Google directly,
     * which would hand it every destination a visitor is looking at.
     *
     * Through the HTTP client rather than file_get_contents: that had no
     * timeout, so a slow answer from Google held a PHP worker for as long as
     * it took. Anything other than a usable image falls back to ours.
     */
    public function __invoke(Request $request)
    {
        $favicon = $this->fetch((string) $request->url) ?? $this->fallback();

        return response($favicon)->header('Content-Type', 'image/png');
    }

    private function fetch(string $url): ?string
    {
        if ($url === '') {
            return null;
        }

        try {
            $response = Http::timeout(3)->get('https://t1.gstatic.com/faviconV2', [
                'client' => 'SOCIAL',
                'type' => 'FAVICON',
                'fallback_opts' => 'TYPE,SIZE,URL',
                'url' => $url,
                'size' => 128,
            ]);
        } catch (\Throwable) {
            return null;
        }

        return $response->successful() && $response->body() !== ''
            ? $response->body()
            : null;
    }

    private function fallback(): string
    {
        return (string) file_get_contents(public_path('/images/websites/favicon.png'));
    }
}
