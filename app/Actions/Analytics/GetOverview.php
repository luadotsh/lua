<?php

declare(strict_types=1);

namespace App\Actions\Analytics;

use App\Enums\LinkStat\Event;
use App\Models\LinkStat;
use App\Models\Workspace;
use Carbon\CarbonImmutable;

class GetOverview
{
    /**
     * Headline numbers for a period, each against the same span immediately
     * before it, so every figure carries a direction as well as a value.
     *
     * @param  array<string, list<string>>  $filters
     * @return array<string, array{value: int, previous: int, change: float|null}>
     */
    public static function execute(
        Workspace $workspace,
        CarbonImmutable $start,
        CarbonImmutable $end,
        array $filters = [],
    ): array {
        // The comparison window is the same length, ending where this one
        // begins, so a 7-day view is compared with the 7 days before it.
        $length = $start->diffInSeconds($end);
        $previousEnd = $start->subSecond();
        $previousStart = $previousEnd->subSeconds($length);

        $current = self::totals($workspace, $start, $end, $filters);
        $previous = self::totals($workspace, $previousStart, $previousEnd, $filters);

        return collect($current)
            ->map(fn (int $value, string $key) => [
                'value' => $value,
                'previous' => $previous[$key],
                'change' => self::change($value, $previous[$key]),
            ])
            ->all();
    }

    /**
     * @param  array<string, list<string>>  $filters
     * @return array<string, int>
     */
    private static function totals(
        Workspace $workspace,
        CarbonImmutable $start,
        CarbonImmutable $end,
        array $filters,
    ): array {
        $query = LinkStat::where('workspace_id', $workspace->id)
            ->whereBetween('created_at', [$start, $end]);

        ApplyFilters::execute($query->getQuery(), $filters);

        // `count(*) filter (where ...)` is PostgreSQL-only; the CASE form says
        // the same thing on both engines.
        $row = $query
            ->selectRaw('count(*) as events')
            ->selectRaw('count(case when event = ? then 1 end) as clicks', [Event::CLICK->value])
            ->selectRaw('count(case when event = ? then 1 end) as qr_scans', [Event::QR_SCAN->value])
            // A visitor is one address inside the window. Without a cookie
            // this is the closest the click data gets to a person.
            ->selectRaw('count(distinct ip) as visitors')
            ->first();

        return [
            'events' => (int) $row->events,
            'clicks' => (int) $row->clicks,
            'qr_scans' => (int) $row->qr_scans,
            'visitors' => (int) $row->visitors,
        ];
    }

    /**
     * Percentage change, or null when there is no baseline to compare with —
     * growth from zero is not a percentage.
     */
    private static function change(int $current, int $previous): ?float
    {
        if ($previous === 0) {
            return null;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }
}
