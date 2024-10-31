<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\DB;

use Carbon\Carbon;

use App\Enums\LinkStat\Event;
use App\Models\LinkStat;

class CalculateStat
{
    public $format;

    public function __construct()
    {
        $this->format = [
            'minute' => '%Y-%m-%d %H:%i:00',
            'hour' => '%Y-%m-%d %H:00:00',
            'day' => '%Y-%m-%d',
            'month' => '%Y-%m',
            'year' => '%Y',
        ];
    }

    public function events($workspace, $timezone, $start, $end, $group)
    {
        // Fetch clicks data with Eloquent using groupBy and date formatting
        $clicks = LinkStat::selectRaw("DATE_FORMAT(created_at, '{$this->format[$group]}') as formatted_date, COUNT(*) as value")
        ->where('workspace_id', $workspace->id)
            ->whereBetween('created_at', [$start, $end])
            ->groupByRaw("DATE_FORMAT(created_at, '{$this->format[$group]}')")
            ->orderByRaw("DATE_FORMAT(created_at, '{$this->format[$group]}') asc")
            ->get();

        // Total clicks for the current period
        $total = LinkStat::where('workspace_id', $workspace->id)
            ->whereBetween('created_at', [$start, $end])
            ->count();

        // Formatting labels based on the grouping
        $labels = $clicks->pluck('formatted_date')->map(function ($label) use ($group, $timezone) {
            switch ($group) {
                case 'hour':
                    return Carbon::createFromFormat('Y-m-d H:i:s', $label)->tz($timezone)->format('ga');
                case 'day':
                    return Carbon::createFromFormat('Y-m-d', $label)->tz($timezone)->format('d M');
                case 'month':
                    return Carbon::createFromFormat('Y-m', $label)->tz($timezone)->format('F Y'); // Handle 'Y-m' format
                case 'year':
                    return Carbon::createFromFormat('Y', $label)->tz($timezone)->format('Y'); // Handle 'Y' format
                default:
                    return $label;
            }
        });

        // Prepare the data for response
        $data = [
            'total' => $total,
            'chart' => [
                'data' => $clicks->pluck('value')->toArray(),
                'label' => 'Clicks',
                'labels' => $labels,
            ],
        ];

        return $data;
    }

    public function clicks($workspace, $timezone, $start, $end, $group)
    {
        // Fetch clicks data with Eloquent using groupBy and date formatting
        $clicks = LinkStat::selectRaw("DATE_FORMAT(created_at, '{$this->format[$group]}') as formatted_date, COUNT(*) as value")
            ->where('workspace_id', $workspace->id)
            ->where('event', Event::CLICK)
            ->whereBetween('created_at', [$start, $end])
            ->groupByRaw("DATE_FORMAT(created_at, '{$this->format[$group]}')")
            ->orderByRaw("DATE_FORMAT(created_at, '{$this->format[$group]}') asc")
            ->get();

        // Total clicks for the current period
        $total = LinkStat::where('workspace_id', $workspace->id)
            ->where('event', Event::CLICK)
            ->whereBetween('created_at', [$start, $end])
            ->count();

        // Formatting labels based on the grouping
        $labels = $clicks->pluck('formatted_date')->map(function ($label) use ($group, $timezone) {
            switch ($group) {
                case 'hour':
                    return Carbon::createFromFormat('Y-m-d H:i:s', $label)->tz($timezone)->format('ga');
                case 'day':
                    return Carbon::createFromFormat('Y-m-d', $label)->tz($timezone)->format('d M');
                case 'month':
                    return Carbon::createFromFormat('Y-m', $label)->tz($timezone)->format('F Y'); // Handle 'Y-m' format
                case 'year':
                    return Carbon::createFromFormat('Y', $label)->tz($timezone)->format('Y'); // Handle 'Y' format
                default:
                    return $label;
            }
        });

        // Prepare the data for response
        $data = [
            'total' => $total,
            'chart' => [
                'data' => $clicks->pluck('value')->toArray(),
                'label' => 'Clicks',
                'labels' => $labels,
            ],
        ];

        return $data;
    }

    public function qrScans($workspace, $timezone, $start, $end, $group)
    {
        // Fetch clicks data with Eloquent using groupBy and date formatting
        $clicks = LinkStat::selectRaw("DATE_FORMAT(created_at, '{$this->format[$group]}') as formatted_date, COUNT(*) as value")
        ->where('workspace_id', $workspace->id)
            ->where('event', Event::QR_SCAN)
            ->whereBetween('created_at', [$start, $end])
            ->groupByRaw("DATE_FORMAT(created_at, '{$this->format[$group]}')")
            ->orderByRaw("DATE_FORMAT(created_at, '{$this->format[$group]}') asc")
            ->get();

        // Total clicks for the current period
        $total = LinkStat::where('workspace_id', $workspace->id)
            ->where('event', Event::QR_SCAN)
            ->whereBetween('created_at', [$start, $end])
            ->count();

        // Formatting labels based on the grouping
        $labels = $clicks->pluck('formatted_date')->map(function ($label) use ($group, $timezone) {
            switch ($group) {
                case 'hour':
                    return Carbon::createFromFormat('Y-m-d H:i:s', $label)->tz($timezone)->format('ga');
                case 'day':
                    return Carbon::createFromFormat('Y-m-d', $label)->tz($timezone)->format('d M');
                case 'month':
                    return Carbon::createFromFormat('Y-m', $label)->tz($timezone)->format('F Y'); // Handle 'Y-m' format
                case 'year':
                    return Carbon::createFromFormat('Y', $label)->tz($timezone)->format('Y'); // Handle 'Y' format
                default:
                    return $label;
            }
        });

        // Prepare the data for response
        $data = [
            'total' => $total,
            'chart' => [
                'data' => $clicks->pluck('value')->toArray(),
                'label' => 'Clicks',
                'labels' => $labels,
            ],
        ];

        return $data;
    }

    public function linkStats($workspaceId, $start, $end, $limit = 10)
    {
        return LinkStat::where('links.workspace_id', $workspaceId)
            ->join('links', 'link_stats.link_id', '=', 'links.id')
            ->select([
                DB::raw('COUNT(*) as y'),
                DB::raw('links.link as x')
            ])
            ->whereBetween('link_stats.created_at', [$start, $end])
            ->groupBy('links.link')
            ->orderBy('y', 'desc')
            ->take($limit)
            ->get();
    }

    public function refererStats($workspaceId, $start, $end, $limit = 10)
    {
        return LinkStat::where('workspace_id', $workspaceId)
            ->select(DB::raw("count(*) as y, CASE WHEN referer IS NULL THEN 'Direct' ELSE referer END AS x"))
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('x')
            ->orderBy('y', 'desc')
            ->take($limit)
            ->get();
    }

    public function utmSourceStats($workspaceId, $start, $end, $limit = 10)
    {
        return LinkStat::where('workspace_id', $workspaceId)
            ->select('utm_source as x', DB::raw('count(*) as y'))
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('utm_source')
            ->groupBy('utm_source')
            ->orderBy('y', 'desc')
            ->take($limit)
            ->get();
    }

    public function utmMediumStats($workspaceId, $start, $end, $limit = 10)
    {
        return LinkStat::where('workspace_id', $workspaceId)
            ->select('utm_medium as x', DB::raw('count(*) as y'))
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('utm_medium')
            ->groupBy('utm_medium')
            ->orderBy('y', 'desc')
            ->take($limit)
            ->get();
    }

    public function utmCampaignStats($workspaceId, $start, $end, $limit = 10)
    {
        return LinkStat::where('workspace_id', $workspaceId)
            ->select('utm_campaign as x', DB::raw('count(*) as y'))
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('utm_campaign')
            ->groupBy('utm_campaign')
            ->orderBy('y', 'desc')
            ->take($limit)
            ->get();
    }

    public function utmContentStats($workspaceId, $start, $end, $limit = 10)
    {
        return LinkStat::where('workspace_id', $workspaceId)
            ->select('utm_content as x', DB::raw('count(*) as y'))
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('utm_content')
            ->groupBy('utm_content')
            ->orderBy('y', 'desc')
            ->take($limit)
            ->get();
    }

    public function utmTermStats($workspaceId, $start, $end, $limit = 10)
    {
        return LinkStat::where('workspace_id', $workspaceId)
            ->select('utm_term as x', DB::raw('count(*) as y'))
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('utm_term')
            ->groupBy('utm_term')
            ->orderBy('y', 'desc')
            ->take($limit)
            ->get();
    }

    public function browserStats($workspaceId, $start, $end, $limit = 10)
    {
        return LinkStat::where('workspace_id', $workspaceId)
            ->select('browser as x', DB::raw('count(*) as y'))
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('browser')
            ->groupBy('browser')
            ->orderBy('y', 'desc')
            ->take($limit)
            ->get();
    }

    public function osStats($workspaceId, $start, $end, $limit = 10)
    {
        return LinkStat::where('workspace_id', $workspaceId)
            ->select('os as x', DB::raw('count(*) as y'))
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('os')
            ->groupBy('os')
            ->orderBy('y', 'desc')
            ->take($limit)
            ->get();
    }

    public function languageStats($workspaceId, $start, $end, $limit = 10)
    {
        return LinkStat::where('workspace_id', $workspaceId)
            ->select('language as x', DB::raw('count(*) as y'))
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('language')
            ->groupBy('language')
            ->orderBy('y', 'desc')
            ->take($limit)
            ->get();
    }

    public function deviceStats($workspaceId, $start, $end, $limit = 10)
    {
        return LinkStat::where('workspace_id', $workspaceId)
            ->select('device as x', DB::raw('count(*) as y'))
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('device')
            ->groupBy('device')
            ->orderBy('y', 'desc')
            ->take($limit)
            ->get();
    }

    public function countryStats($workspaceId, $start, $end, $limit = 10)
    {
        return LinkStat::where('workspace_id', $workspaceId)
            ->select('country as x', DB::raw('count(*) as y'))
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('country')
            ->groupBy('country')
            ->orderBy('y', 'desc')
            ->take($limit)
            ->get();
    }

    public function regionStats($workspaceId, $start, $end, $limit = 10)
    {
        return LinkStat::where('workspace_id', $workspaceId)
            ->select('region as x', DB::raw('count(*) as y'))
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('region')
            ->groupBy('region')
            ->orderBy('y', 'desc')
            ->take($limit)
            ->get();
    }

    public function cityStats($workspaceId, $start, $end, $limit = 10)
    {
        return LinkStat::where('workspace_id', $workspaceId)
            ->select('city as x', DB::raw('count(*) as y'))
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('city')
            ->groupBy('city')
            ->orderBy('y', 'desc')
            ->take($limit)
            ->get();
    }
}
