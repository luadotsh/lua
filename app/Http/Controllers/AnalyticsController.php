<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Analytics\GetBreakdown;
use App\Actions\Analytics\GetOverview;
use App\Actions\Analytics\GetTimeseries;
use App\Actions\Analytics\ResolveFilters;
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
            ['key' => 'referer', 'label' => 'Referrer'],
            ['key' => 'utm_source', 'label' => 'Source'],
            ['key' => 'utm_medium', 'label' => 'Medium'],
            ['key' => 'utm_campaign', 'label' => 'Campaign'],
            ['key' => 'utm_content', 'label' => 'Content'],
            ['key' => 'utm_term', 'label' => 'Term'],
        ],
        // Order and labels follow the clickbase dashboard. The map is a tab of
        // the Locations card, not a card of its own, and it renders through a
        // slot rather than from rows.
        'locations' => [
            ['key' => 'country', 'label' => 'Countries'],
            ['key' => 'region', 'label' => 'Regions'],
            ['key' => 'city', 'label' => 'Cities'],
        ],
        'devices' => [
            ['key' => 'browser', 'label' => 'Browser'],
            ['key' => 'os', 'label' => 'Operating systems'],
            ['key' => 'device', 'label' => 'Device'],
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
            // The URL is the filter state, so a narrowed dashboard is a link
            // you can bookmark, share, or walk back out of.
            'filters' => ResolveFilters::toActive(ResolveFilters::execute($request->query())),
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

        // Resolved from the raw bag rather than the validated one: the filter
        // keys are an allowlist of dimensions, not a fixed set of fields.
        $filters = ResolveFilters::execute($request->query());

        return response()->json([
            'overview' => GetOverview::execute($workspace, $start, $end, $filters),
            'timeseries' => GetTimeseries::execute(
                $workspace,
                $start,
                $end,
                $request->validated('group'),
                $timezone,
                $filters,
            ),
            'links' => GetBreakdown::links($workspace, $start, $end, $filters),
            'breakdowns' => collect(self::CARDS)
                ->map(fn (array $tabs) => collect($tabs)->map(fn (array $tab) => [
                    ...$tab,
                    'rows' => GetBreakdown::execute($workspace, $tab['key'], $start, $end, $filters),
                ])->values())
                ->all(),
        ]);
    }
}
