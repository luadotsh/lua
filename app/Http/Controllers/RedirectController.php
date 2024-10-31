<?php

declare(strict_types=1);

namespace App\Http\Controllers;


use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

use App\Jobs\ProcessLinkStat;

use App\Models\Link;

class RedirectController extends Controller
{
    public function __invoke($key, Request $request): RedirectResponse
    {
        // find the link
        $link = Link::where('link', $request->url())->firstOrFail();

        // dispatch job
        ProcessLinkStat::dispatch(
            $link,
            $request->userAgent(),
            $request->getLanguages(),
            $request->ip(),
            $request->input('qr') ? true : false,
            $request->header('Referer')
        );
        return redirect($link->url, 302);
    }
}
