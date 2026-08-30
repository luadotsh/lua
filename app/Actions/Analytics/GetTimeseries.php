<?php

declare(strict_types=1);

namespace App\Actions\Analytics;

use App\Enums\LinkStat\Event;
use App\Models\LinkStat;
use App\Models\Workspace;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class GetTimeseries
{
    /**
     * PostgreSQL date_trunc units, keyed by the grouping the client asks for.
     */
    private const UNITS = [
        'minute' => 'minute',
        'hour' => 'hour',
        'day' => 'day',
        'month' => 'month',
    ];

    /**
     * MySQL has no date_trunc, so the bucket is built with DATE_FORMAT: the
     * parts below the unit are frozen rather than truncated away, which comes
     * out to the same instant.
     */
    private const MYSQL_FORMATS = [
        'minute' => '%Y-%m-%d %H:%i:00',
        'hour' => '%Y-%m-%d %H:00:00',
        'day' => '%Y-%m-%d 00:00:00',
        'month' => '%Y-%m-01 00:00:00',
    ];

    /**
     * One row per bucket across the whole period, including the buckets with
     * no traffic — a chart with holes in it reads as missing data rather than
     * as a quiet day.
     *
     * @param  array<string, list<string>>  $filters
     * @return Collection<int, array{bucket: string, events: int, clicks: int, qr_scans: int, visitors: int}>
     */
    public static function execute(
        Workspace $workspace,
        CarbonImmutable $start,
        CarbonImmutable $end,
        string $group,
        string $timezone,
        array $filters = [],
    ): Collection {
        [$bucket, $bindings] = self::bucketExpression($group, $timezone);

        $query = LinkStat::where('workspace_id', $workspace->id)
            ->whereBetween('created_at', [$start, $end]);

        ApplyFilters::execute($query->getQuery(), $filters);

        // `count(*) filter (where ...)` is PostgreSQL-only; the CASE form says
        // the same thing on both engines.
        $rows = $query
            ->selectRaw("{$bucket} as bucket", $bindings)
            ->selectRaw('count(*) as events')
            ->selectRaw('count(case when event = ? then 1 end) as clicks', [Event::CLICK->value])
            ->selectRaw('count(case when event = ? then 1 end) as qr_scans', [Event::QR_SCAN->value])
            ->selectRaw('count(distinct ip) as visitors')
            // Grouped by the output alias, which both engines accept and
            // which keeps the expression's bindings in one place.
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
     * The SQL that rounds a UTC timestamp down to the viewer's local bucket.
     *
     * The driver is a parameter so the mapping can be asserted without standing
     * up every engine — the MySQL arm is the one no local run ever reaches.
     *
     * Both halves of this are engine-specific: PostgreSQL has date_trunc and
     * `AT TIME ZONE`, MySQL has DATE_FORMAT and CONVERT_TZ. A self-hosted MySQL
     * needs its timezone tables loaded (`mysql_tzinfo_to_sql`) for CONVERT_TZ
     * to resolve a named zone.
     *
     * @return array{0: string, 1: list<string>}
     */
    public static function bucketExpression(string $group, string $timezone, ?string $driver = null): array
    {
        $driver ??= DB::connection()->getDriverName();

        return match ($driver) {
            'pgsql' => [
                "date_trunc(?, created_at at time zone 'UTC' at time zone ?)",
                [self::UNITS[$group] ?? 'day', $timezone],
            ],
            'mysql', 'mariadb' => [
                "date_format(convert_tz(created_at, '+00:00', ?), ?)",
                [$timezone, self::MYSQL_FORMATS[$group] ?? self::MYSQL_FORMATS['day']],
            ],
            default => throw new RuntimeException(
                "Analytics has no bucket expression for the [{$driver}] driver.",
            ),
        };
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
