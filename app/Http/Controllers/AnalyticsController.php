<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Analytics\GetBreakdown;
use App\Actions\Analytics\GetOverview;
use App\Actions\Analytics\GetTimeseries;
use App\Http\Requests\Analytics\StatisticsRequest;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AnalyticsController extends Controller
{
    /**
     * Which breakdowns each card shows, and the icon set beside their rows.
     * Adding a slice is an entry here plus one in GetBreakdown::DIMENSIONS.
     */
    private const CARDS = [
        'sources' => [
            ['key' => 'referer', 'label' => 'Referrer', 'icon' => 'favicon'],
            ['key' => 'utm_source', 'label' => 'Source'],
            ['key' => 'utm_medium', 'label' => 'Medium'],
            ['key' => 'utm_campaign', 'label' => 'Campaign'],
            ['key' => 'utm_content', 'label' => 'Content'],
            ['key' => 'utm_term', 'label' => 'Term'],
        ],
        'locations' => [
            ['key' => 'country', 'label' => 'Country', 'icon' => 'country'],
            ['key' => 'region', 'label' => 'Region'],
            ['key' => 'city', 'label' => 'City'],
        ],
        'devices' => [
            ['key' => 'device', 'label' => 'Device', 'icon' => 'device'],
            ['key' => 'browser', 'label' => 'Browser', 'icon' => 'browser'],
            ['key' => 'os', 'label' => 'OS', 'icon' => 'os'],
            ['key' => 'language', 'label' => 'Language'],
        ],
    ];

    public function index(Request $request): Response
    {
        $start = CarbonImmutable::parse($request->start ?: now()->subDays(29))->startOfDay();
        $end = CarbonImmutable::parse($request->end ?: now())->endOfDay();

        return Inertia::render('Analytics/Index', [
            'start' => $start->toDateString(),
            'end' => $end->toDateString(),
        ]);
    }

    public function statistics(StatisticsRequest $request)
    {
        $workspace = auth()->user()->currentWorkspace;
        $timezone = $request->validated('timezone');

        $start = CarbonImmutable::createFromFormat('Y-m-d', $request->validated('start'), $timezone)
            ->startOfDay()->setTimezone('UTC');
        $end = CarbonImmutable::createFromFormat('Y-m-d', $request->validated('end'), $timezone)
            ->endOfDay()->setTimezone('UTC');

        return response()->json([
            'overview' => GetOverview::execute($workspace, $start, $end),
            'timeseries' => GetTimeseries::execute(
                $workspace,
                $start,
                $end,
                $request->validated('group'),
                $timezone,
            ),
            'links' => GetBreakdown::links($workspace, $start, $end),
            'breakdowns' => collect(self::CARDS)
                ->map(fn (array $tabs) => collect($tabs)->map(fn (array $tab) => [
                    ...$tab,
                    'rows' => GetBreakdown::execute($workspace, $tab['key'], $start, $end),
                ])->values())
                ->all(),
        ]);
    }
}
