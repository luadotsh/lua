<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\UserAgentService;

use Illuminate\Support\Facades\Gate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

use App\Enums\Link\Os;

use Inertia\Inertia;

use App\Jobs\ProcessLinkStat;

use App\Models\Link;

class RedirectController extends Controller
{
    public function redirect(Request $request, $key = null): RedirectResponse
    {
        $link = Link::where('link', $request->url())
            ->with('workspace')
            ->firstOrFail();

        $reachEventLimit = Gate::inspect('reached-event-limit', $link->workspace);

        $utms = [
            'utm_source' => $request->input('utm_source'),
            'utm_medium' => $request->input('utm_medium'),
            'utm_campaign' => $request->input('utm_campaign'),
            'utm_term' => $request->input('utm_term'),
            'utm_content' => $request->input('utm_content')
        ];

        ProcessLinkStat::dispatchIf(
            $reachEventLimit->allowed(),
            $link,
            $request->userAgent(),
            $request->getLanguages(),
            $request->ip(),
            $request->input('qr') ? true : false,
            $utms,
            $request->header('Referer')
        );

        /**
         * Link Password
         */
        if ($link->password) {
            return redirect(route('links.password', $link->key));
        }


        /**
         * Expired Links
         */
        if ($link->isExpired()) {

            // If has expired redirect URL, redirect to it.
            if ($link->expired_redirect_url) {
                return redirect($link->expired_redirect_url, 302);
            }

            return abort(404);
        }

        /**
         * If has iOS or Android redirect URL, check the user's OS and redirect to the appropriate URL.
         */
        if ($link->ios || $link->android) {
            $service = new UserAgentService();
            $os = $service->getOS($request->userAgent());

            if ($os === Os::IOS->value && $link->ios) {
                return redirect($link->ios, 302);
            }

            if ($os === Os::ANDROID->value && $link->android) {
                return redirect($link->android, 302);
            }
        }

        return redirect($link->url, 302);
    }

    public function password(Request $request, $key)
    {
        $link = Link::where('key', $key)
            ->firstOrFail();

        return Inertia::render('Link/Password', [
            'link' => $link,
        ]);
    }

    public function validatePassword(Request $request, $key)
    {
        $link = Link::where('key', $key)->firstOrFail();

        if ($request->password === $link->password) {
            return Inertia::location($link->url);
        }

        return back()->withErrors([
            'password' => 'The password is incorrect.'
        ]);
    }
}
