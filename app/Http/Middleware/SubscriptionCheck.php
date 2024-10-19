<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubscriptionCheck
{
    public function handle(Request $request, Closure $next)
    {
        // get the workspace
        $workspace = auth()->user()->currentWorkspace;

        $billing = [
            'trialing' => false,
            'trial_ends_at' => null,
            'trial_its_expired' => false,
            'active' => false,
            'past_due' => false,
            'canceled' => false,
            'has_subscription' => $workspace->subscribed('default') || $workspace->subscribed('nps'),
        ];


        if ($workspace->subscription('default')) {
            if ($workspace->hasIncompletePayment('default')) {
                $billing['past_due'] = true;
            }

            if ($workspace->subscription('default')->canceled()) {
                $billing['canceled'] = true;
            }

            if ($workspace->subscribed('default')) {
                $billing['active'] = true;
            }
        }

        // check if the workspace is on trial
        if ($workspace->onTrial() && !$workspace->subscription('default')) {
            $billing['trialing'] = true;
            $billing['trial_ends_at'] = $workspace->trial_ends_at->diffInDays(now());

            if($billing['trial_ends_at'] <= 0) {
                $billing['trial_ends_at_formatted'] = "Trial Ends Today";
            }
            else if($billing['trial_ends_at'] == 1) {
                $billing['trial_ends_at_formatted'] = "Trial Ends Tomorrow";
            }
            else if($billing['trial_ends_at'] > 1) {
                $billing['trial_ends_at_formatted'] = "Trial Ends In {$billing['trial_ends_at']} Days";
            }
        }

        // check if trial it's expired
        if ($workspace->hasExpiredTrial() && !$workspace->subscription('default')) {
            $billing['trial_ends_at'] = 0;
            $billing['trial_ends_at_formatted'] = "Your Trial It's Expired";
            $billing['trial_its_expired'] = true;

            if ($workspace->hasExpiredTrial() && !request()->routeIs('setting.account.*') && !request()->routeIs('setting.billing.*')) {
                return redirect(route('setting.billing.index'));
            }
        }

        Inertia::share('billing', $billing);

        return $next($request);
    }
}
