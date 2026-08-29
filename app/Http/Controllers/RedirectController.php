<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Link\BuildDestination;
use App\Actions\Link\ResolveLinkByKey;
use App\Enums\Link\Os;
use App\Jobs\ProcessLinkStat;
use App\Models\Link;
use App\Services\UserAgentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class RedirectController extends Controller
{
    public function redirect(Request $request, $key = null): RedirectResponse
    {
        $link = ResolveLinkByKey::execute($request->getHost(), $key);

        abort_unless($link, 404);

        $reachEventLimit = Gate::inspect('reached-event-limit', $link->workspace);

        $utms = [
            'utm_source' => $request->input('utm_source'),
            'utm_medium' => $request->input('utm_medium'),
            'utm_campaign' => $request->input('utm_campaign'),
            'utm_term' => $request->input('utm_term'),
            'utm_content' => $request->input('utm_content'),
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
            $service = new UserAgentService;
            $os = $service->getOS($request->userAgent());

            if ($os === Os::IOS->value && $link->ios) {
                return redirect(BuildDestination::execute($link->ios, $link, $utms), 302);
            }

            if ($os === Os::ANDROID->value && $link->android) {
                return redirect(BuildDestination::execute($link->android, $link, $utms), 302);
            }
        }

        return redirect(BuildDestination::execute($link->url, $link, $utms), 302);
    }

    public function password(Request $request, $key)
    {
        $link = ResolveLinkByKey::execute($request->getHost(), $key);

        abort_unless($link, 404);

        return Inertia::render('Link/Password', [
            'link' => $link,
        ]);
    }

    public function validatePassword(Request $request, $key)
    {
        $link = ResolveLinkByKey::execute($request->getHost(), $key);

        abort_unless($link, 404);

        // hash_equals compares in constant time, so a wrong guess cannot be
        // narrowed down by how long the response took.
        if (is_string($link->password) && hash_equals($link->password, (string) $request->password)) {
            return Inertia::location(BuildDestination::execute($link->url, $link));
        }

        return back()->withErrors([
            'password' => 'The password is incorrect.',
        ]);
    }
}
