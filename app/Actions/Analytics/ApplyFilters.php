<?php

declare(strict_types=1);

namespace App\Actions\Analytics;

use Illuminate\Contracts\Database\Query\Builder;

class ApplyFilters
{
    /**
     * Every filter key, mapped to the column that holds it.
     *
     * The breakdown dimensions all filter by themselves. `link` is the one
     * that does not come from that list: the links card ranks links through a
     * relation rather than a column, but narrowing the dashboard to a single
     * link is the most useful filter there is — it is what the link's own
     * screen is built on.
     *
     * @var array<string, string>
     */
    public const COLUMNS = [
        ...GetBreakdown::DIMENSIONS,
        'link' => 'link_id',
    ];

    /**
     * Narrow a click-data query to the dashboard's active filters.
     *
     * Only the column name is chosen dynamically, and only from the allowlist
     * above; every value travels as a binding.
     *
     * @param  array<string, list<string>>  $filters
     *
     * @template TBuilder of Builder
     *
     * @param  TBuilder  $query
     * @return TBuilder
     */
    public static function execute(Builder $query, array $filters, string $prefix = ''): Builder
    {
        foreach ($filters as $dimension => $values) {
            $column = self::COLUMNS[$dimension] ?? null;

            if ($column === null || $values === []) {
                continue;
            }

            $query->whereIn($prefix.$column, $values);
        }

        return $query;
    }
}
