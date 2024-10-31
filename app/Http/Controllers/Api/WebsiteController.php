<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

class WebsiteController extends Controller
{
    public function __invoke(Request $request)
    {
        try {
            $favicon = file_get_contents("https://t1.gstatic.com/faviconV2?client=SOCIAL&type=FAVICON&fallback_opts=TYPE,SIZE,URL&url={$request->url}&size=128");
        } catch (\Throwable $th) {
            $favicon = file_get_contents(public_path('/images/websites/favicon.png'));
        }

        return response($favicon)->header('Content-Type', 'image/png');
    }
}
