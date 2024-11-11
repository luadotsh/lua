<?php

declare(strict_types=1);

namespace App\Models\Traits;

use App\Models\Link;
use App\Models\LinkStat;
use App\Models\Plan;

use Carbon\Carbon;

trait WorkspaceUsage
{
    public function usage()
    {
        $billingCycleStart = $this->billing_cycle_start;
        $start = Carbon::now()->day >= $billingCycleStart ? Carbon::now()->startOfMonth()->day($billingCycleStart) : Carbon::now()->subMonth()->startOfMonth()->day($billingCycleStart);
        $end = (clone $start)->addMonth()->subDay();

        $usedLinks = Link::where('workspace_id', $this->id)
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $usedEvents = LinkStat::where('workspace_id', $this->id)
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $linksChartData = Link::where('workspace_id', $this->id)
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $eventsChartData = LinkStat::where('workspace_id', $this->id)
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        return [
            'billing' => [
                'has_subscription' => $this->subscribed('default'),
                'past_due' => $this->hasIncompletePayment('default') ?? null,
                'canceled' => $this->subscription('default') ? $this->subscription('default')->canceled() : false,
                'active' => $this->subscribed('default'),
            ],
            'plan' => [
                'name' => $this->plan->name,
                'access_level' => $this->plan->access_level,
                'next_tier' => Plan::where('access_level', '>', $this->plan->access_level)
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
                'limit' => number_format($this->plan->max_links),
                'percent' => $usedLinks === 0 ? 0 : round(($usedLinks / $this->plan->max_links) * 100),
                'remaining' => number_format($this->plan->max_links - $usedLinks),
                'reached_limit' => $usedLinks >= $this->plan->max_links,
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
                'limit' => number_format($this->plan->max_events),
                'percent' => $usedEvents === 0 ? 0 : round(($usedEvents / $this->plan->max_events) * 100),
                'remaining' => number_format($this->plan->max_events - $usedEvents),
                'reached_limit' => $usedEvents >= $this->plan->max_events,
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
                'used' => number_format($this->domains->count()),
                'limit' => number_format($this->plan->max_domains),
                'percent' => $this->plan->max_domains === 0 ? 0 : round(($this->domains->count() / $this->plan->max_domains) * 100),
                'remaining' => number_format($this->plan->max_domains - $this->domains->count()),
                'reached_limit' => $this->domains->count() >= $this->plan->max_domains,
            ],
            'tags' => [
                'used' => number_format($this->tags->count()),
                'limit' => number_format($this->plan->max_tags),
                'percent' => $this->plan->max_tags === 0 ? 0 : round(($this->tags->count() / $this->plan->max_tags) * 100),
                'remaining' => number_format($this->plan->max_tags - $this->tags->count()),
                'reached_limit' => $this->tags->count() >= $this->plan->max_tags,
            ],
            'users' => [
                'used' => number_format($this->users->count()),
                'limit' => number_format($this->plan->max_users),
                'percent' => $this->plan->max_users === 0 ? 0 : round(($this->users->count() / $this->plan->max_users) * 100),
                'remaining' => number_format($this->plan->max_users - $this->users->count()),
                'reached_limit' => $this->users->count() >= $this->plan->max_users,
            ]
        ];
    }
}
