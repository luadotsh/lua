<?php

declare(strict_types=1);

namespace App\Actions\Analytics;

use App\Models\LinkStat;
use App\Models\Workspace;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class GetBreakdown
{
    /**
     * Every dimension the click data can be sliced by, mapped to the column
     * that holds it. Adding a breakdown is one entry here.
     */
    public const DIMENSIONS = [
        'referer' => 'referer',
        'utm_source' => 'utm_source',
        'utm_medium' => 'utm_medium',
        'utm_campaign' => 'utm_campaign',
        'utm_content' => 'utm_content',
        'utm_term' => 'utm_term',
        'country' => 'country',
        'region' => 'region',
        'city' => 'city',
        'browser' => 'browser',
        'os' => 'os',
        'device' => 'device',
        'language' => 'language',
    ];

    /**
     * The top values for one dimension, with the share each holds of the
     * period's total, so a row reads without needing the total beside it.
     *
     * @return Collection<int, array{value: string, events: int, visitors: int, share: float}>
     */
    public static function execute(
        Workspace $workspace,
        string $dimension,
        CarbonImmutable $start,
        CarbonImmutable $end,
        int $limit = 10,
    ): Collection {
        $column = self::DIMENSIONS[$dimension]
            ?? throw new InvalidArgumentException("Unknown breakdown dimension: {$dimension}");

        $base = LinkStat::where('workspace_id', $workspace->id)
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull($column)
            ->where($column, '!=', '');

        $total = (clone $base)->count();

        return $base
            ->select($column.' as value')
            ->selectRaw('count(*) as events')
            ->selectRaw('count(distinct ip) as visitors')
            ->groupBy($column)
            ->orderByDesc('events')
            ->limit($limit)
            ->get()
            ->map(fn ($row) => [
                'value' => (string) $row->value,
                'events' => (int) $row->events,
                'visitors' => (int) $row->visitors,
                'share' => $total > 0 ? round(((int) $row->events / $total) * 100, 1) : 0.0,
            ]);
    }

    /**
     * The top links themselves, which are a breakdown by relation rather than
     * by column.
     *
     * @return Collection<int, array{value: string, url: string, events: int, visitors: int, share: float}>
     */
    public static function links(
        Workspace $workspace,
        CarbonImmutable $start,
        CarbonImmutable $end,
        int $limit = 10,
    ): Collection {
        $base = LinkStat::where('link_stats.workspace_id', $workspace->id)
            ->whereBetween('link_stats.created_at', [$start, $end]);

        $total = (clone $base)->count();

        return $base
            ->join('links', 'links.id', '=', 'link_stats.link_id')
            ->select('links.link as value', 'links.url as url')
            ->selectRaw('count(*) as events')
            ->selectRaw('count(distinct link_stats.ip) as visitors')
            ->groupBy('links.link', 'links.url')
            ->orderByDesc('events')
            ->limit($limit)
            ->get()
            ->map(fn ($row) => [
                'value' => (string) $row->value,
                'url' => (string) $row->url,
                'events' => (int) $row->events,
                'visitors' => (int) $row->visitors,
                'share' => $total > 0 ? round(((int) $row->events / $total) * 100, 1) : 0.0,
            ]);
    }
}
