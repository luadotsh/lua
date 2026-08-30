<?php

declare(strict_types=1);

namespace App\Actions\Analytics;

use Illuminate\Contracts\Database\Query\Builder;

class ApplyFilters
{
    /**
     * Narrow a click-data query to the dashboard's active filters.
     *
     * Only the column name is chosen dynamically, and only from the allowlist
     * in GetBreakdown; every value travels as a binding.
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
            $column = GetBreakdown::DIMENSIONS[$dimension] ?? null;

            if ($column === null || $values === []) {
                continue;
            }

            $query->whereIn($prefix.$column, $values);
        }

        return $query;
    }
}
