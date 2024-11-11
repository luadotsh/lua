<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

use App\Jobs\ProcessLinkStat;

use App\Models\Link;

class RedirectController extends Controller
{
    public function __invoke($key, Request $request): RedirectResponse
    {
        $link = Link::where('link', $request->url())
            ->with('workspace')
            ->firstOrFail();


        $reachEventLimit = Gate::inspect('reached-event-limit', $link->workspace);

        ProcessLinkStat::dispatchIf(
            $reachEventLimit->allowed(),
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
