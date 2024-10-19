<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;

use Carbon\Carbon;

use App\Models\Link;
use App\Models\LinkStat;

class PlanCheck
{
    public function handle(Request $request, Closure $next)
    {
        // get the workspace
        $workspace = auth()->user()->currentWorkspace;

        $billingCycleStart = $workspace->billing_cycle_start;
        $start = Carbon::now()->day >= $billingCycleStart ? Carbon::now()->startOfMonth()->day($billingCycleStart) : Carbon::now()->subMonth()->startOfMonth()->day($billingCycleStart);
        $end = (clone $start)->addMonth()->subDay();


        $usedLinks = Link::where('workspace_id', $workspace->id)
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $usedEvents = LinkStat::where('workspace_id', $workspace->id)
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $billing = [
            'active' => false,
            'past_due' => false,
            'canceled' => false,
            'has_subscription' => $workspace->subscribed('default') || $workspace->subscribed('nps'),

            'usage' => [
                'next_reset' => $end->format('M j, Y'),
                'links' => [
                    'limit' => $workspace->plan->max_links,
                    'used' => $usedLinks,
                    'percent' => $usedLinks === 0 ? 0 : round(($usedLinks / $workspace->plan->max_links) * 100),
                ],

                'events' => [
                    'limit' => $workspace->plan->max_events,
                    'used' =>  $usedEvents,
                    'percent' => $usedEvents === 0 ? 0 : round(($usedEvents / $workspace->plan->max_events) * 100),
                ]
            ]
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

        Inertia::share('billing', $billing);

        return $next($request);
    }
}
