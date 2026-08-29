<?php

declare(strict_types=1);

namespace App\Actions\Analytics;

use App\Enums\LinkStat\Event;
use App\Models\LinkStat;
use App\Models\Workspace;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

class GetTimeseries
{
    /**
     * Postgres truncation units, keyed by the grouping the client asks for.
     */
    private const UNITS = [
        'minute' => 'minute',
        'hour' => 'hour',
        'day' => 'day',
        'month' => 'month',
    ];

    /**
     * One row per bucket across the whole period, including the buckets with
     * no traffic — a chart with holes in it reads as missing data rather than
     * as a quiet day.
     *
     * @return Collection<int, array{bucket: string, events: int, clicks: int, qr_scans: int, visitors: int}>
     */
    public static function execute(
        Workspace $workspace,
        CarbonImmutable $start,
        CarbonImmutable $end,
        string $group,
        string $timezone,
    ): Collection {
        $unit = self::UNITS[$group] ?? 'day';

        $rows = LinkStat::where('workspace_id', $workspace->id)
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw("date_trunc(?, created_at at time zone 'UTC' at time zone ?) as bucket", [$unit, $timezone])
            ->selectRaw('count(*) as events')
            ->selectRaw('count(*) filter (where event = ?) as clicks', [Event::CLICK->value])
            ->selectRaw('count(*) filter (where event = ?) as qr_scans', [Event::QR_SCAN->value])
            ->selectRaw('count(distinct ip) as visitors')
            ->groupBy('bucket')
            ->orderBy('bucket')
            ->get()
            ->keyBy(fn ($row) => CarbonImmutable::parse($row->bucket)->format('Y-m-d H:i:s'));

        return self::buckets($start, $end, $group, $timezone)
            ->map(function (CarbonImmutable $bucket) use ($rows) {
                $row = $rows->get($bucket->format('Y-m-d H:i:s'));

                return [
                    'bucket' => $bucket->toIso8601String(),
                    'events' => (int) ($row->events ?? 0),
                    'clicks' => (int) ($row->clicks ?? 0),
                    'qr_scans' => (int) ($row->qr_scans ?? 0),
                    'visitors' => (int) ($row->visitors ?? 0),
                ];
            })
            ->values();
    }

    /**
     * @return Collection<int, CarbonImmutable>
     */
    private static function buckets(
        CarbonImmutable $start,
        CarbonImmutable $end,
        string $group,
        string $timezone,
    ): Collection {
        $cursor = $start->setTimezone($timezone);
        $last = $end->setTimezone($timezone);

        $cursor = match ($group) {
            'minute' => $cursor->startOfMinute(),
            'hour' => $cursor->startOfHour(),
            'month' => $cursor->startOfMonth(),
            default => $cursor->startOfDay(),
        };

        $step = match ($group) {
            'minute' => fn (CarbonImmutable $d) => $d->addMinute(),
            'hour' => fn (CarbonImmutable $d) => $d->addHour(),
            'month' => fn (CarbonImmutable $d) => $d->addMonth(),
            default => fn (CarbonImmutable $d) => $d->addDay(),
        };

        $buckets = collect();

        // A minute-level view over a long range would produce tens of
        // thousands of points; cap it at what a chart can actually draw.
        while ($cursor <= $last && $buckets->count() < 1500) {
            $buckets->push($cursor);
            $cursor = $step($cursor);
        }

        return $buckets;
    }
}
