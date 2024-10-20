<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;

use Carbon\Carbon;

use App\Models\Plan;
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

        $linksChartData = Link::where('workspace_id', $workspace->id)
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $eventsChartData = LinkStat::where('workspace_id', $workspace->id)
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $usage = [
            'billing' => [
                'has_subscription' => $workspace->subscribed('default'),
                'past_due' => $workspace->hasIncompletePayment('default') ?? null,
                'canceled' => $workspace->subscription('default') ? $workspace->subscription('default')->canceled() : false,
                'active' => $workspace->subscribed('default'),
            ],
            'plan' => [
                'name' => $workspace->plan->name,
                'access_level' => $workspace->plan->access_level,
                'next_tier' => Plan::where('access_level', '>', $workspace->plan->access_level)
                    ->orderBy('access_level', 'asc')
                    ->select('name')
                    ->first(),
            ],
            'current_billing_cycle' => [
                'start' => $start->format('Y-m-d'),
                'end' => $end->format('Y-m-d'),
            ],
            'current_billing_cycle_formatted' => $start->format('M j, Y') . ' - ' . $end->format('M j, Y'),
            'next_reset' => $end->format('M j, Y'),
            'links' => [
                'used' => number_format($usedLinks),
                'limit' => number_format($workspace->plan->max_links),
                'percent' => $usedLinks === 0 ? 0 : round(($usedLinks / $workspace->plan->max_links) * 100),
                'remaining' => number_format($workspace->plan->max_links - $usedLinks),
                'chart' => [
                    "total" => number_format($linksChartData->sum('count')),
                    "chart" => [
                        "data" => $linksChartData->pluck('count')->toArray(),
                        "label" => "Links",
                        "labels" => $linksChartData->pluck('date')->map(function ($date) {
                            return Carbon::parse($date)->format('d, Y');
                        })->toArray()
                    ]
                ]
            ],

            'events' => [
                'used' => number_format($usedEvents),
                'limit' => number_format($workspace->plan->max_events),
                'percent' => $usedEvents === 0 ? 0 : round(($usedEvents / $workspace->plan->max_events) * 100),
                'remaining' => number_format($workspace->plan->max_events - $usedEvents),
                'chart' => [
                    "total" => number_format($eventsChartData->sum('count')),
                    "chart" => [
                        "data" => $eventsChartData->pluck('count')->toArray(),
                        "label" => "Events",
                        "labels" => $eventsChartData->pluck('date')->map(function ($date) {
                            return Carbon::parse($date)->format('d, Y');
                        })->toArray()
                    ]
                ]
            ],

            'domains' => [
                'used' => number_format($workspace->domains->count()),
                'limit' => number_format($workspace->plan->max_domains),
                'percent' => $workspace->plan->max_domains === 0 ? 0 : round(($workspace->domains->count() / $workspace->plan->max_domains) * 100),
                'remaining' => number_format($workspace->plan->max_domains - $workspace->domains->count()),
            ],
            'tags' => [
                'used' => number_format($workspace->tags->count()),
                'limit' => number_format($workspace->plan->max_tags),
                'percent' => $workspace->plan->max_tags === 0 ? 0 : round(($workspace->tags->count() / $workspace->plan->max_tags) * 100),
                'remaining' => number_format($workspace->plan->max_tags - $workspace->tags->count()),
            ],
            'users' => [
                'used' => number_format($workspace->users->count()),
                'limit' => number_format($workspace->plan->max_users),
                'percent' => $workspace->plan->max_users === 0 ? 0 : round(($workspace->users->count() / $workspace->plan->max_users) * 100),
                'remaining' => number_format($workspace->plan->max_users - $workspace->users->count()),
            ]
        ];

        Inertia::share('usage', $usage);

        return $next($request);
    }
}
