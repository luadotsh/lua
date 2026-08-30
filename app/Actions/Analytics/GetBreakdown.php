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
     * Dimensions that sit inside a country, so each row can carry the flag of
     * the country it belongs to.
     *
     * @var list<string>
     */
    private const NESTED_IN_COUNTRY = ['region', 'city'];

    /**
     * The top values for one dimension, with the share each holds of the
     * period's total, so a row reads without needing the total beside it.
     *
     * @param  array<string, list<string>>  $filters
     * @return Collection<int, array{value: string, country: string|null, events: int, visitors: int, share: float}>
     */
    public static function execute(
        Workspace $workspace,
        string $dimension,
        CarbonImmutable $start,
        CarbonImmutable $end,
        array $filters = [],
        int $limit = 10,
    ): Collection {
        $column = self::DIMENSIONS[$dimension]
            ?? throw new InvalidArgumentException("Unknown breakdown dimension: {$dimension}");

        $base = LinkStat::where('workspace_id', $workspace->id)
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull($column)
            ->where($column, '!=', '');

        // A filter narrows every card, its own included: filtering to Brazil
        // and still seeing the other countries listed would say the filter had
        // not taken.
        ApplyFilters::execute($base->getQuery(), $filters);

        $total = (clone $base)->count();

        // A region or city is shown with its country's flag, so the country
        // travels with the row. Grouping by it too keeps the aggregate honest
        // when two countries share a region name.
        $nested = in_array($dimension, self::NESTED_IN_COUNTRY, true);

        $query = $base
            ->select($column.' as value')
            ->selectRaw('count(*) as events')
            ->selectRaw('count(distinct ip) as visitors')
            ->groupBy($column);

        if ($nested) {
            $query->addSelect('country')->groupBy('country');
        }

        return $query
            ->orderByDesc('events')
            ->limit($limit)
            ->get()
            ->map(fn ($row) => [
                'value' => (string) $row->value,
                'country' => $nested ? $row->country : null,
                'events' => (int) $row->events,
                'visitors' => (int) $row->visitors,
                'share' => $total > 0 ? round(((int) $row->events / $total) * 100, 1) : 0.0,
            ]);
    }

    /**
     * The top links themselves, which are a breakdown by relation rather than
     * by column.
     *
     * @param  array<string, list<string>>  $filters
     * @return Collection<int, array{value: string, url: string, events: int, visitors: int, share: float}>
     */
    public static function links(
        Workspace $workspace,
        CarbonImmutable $start,
        CarbonImmutable $end,
        array $filters = [],
        int $limit = 10,
    ): Collection {
        $base = LinkStat::where('link_stats.workspace_id', $workspace->id)
            ->whereBetween('link_stats.created_at', [$start, $end]);

        // Qualified: this query joins links, and every filtered column lives on
        // link_stats.
        ApplyFilters::execute($base->getQuery(), $filters, 'link_stats.');

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
