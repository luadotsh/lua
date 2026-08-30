<?php

declare(strict_types=1);

namespace App\Actions\Analytics;

class ResolveFilters
{
    /**
     * Read the dashboard filters out of a raw query bag.
     *
     * Every key is one of ApplyFilters::COLUMNS, taken as a scalar or a list.
     * Unknown keys, empty strings and values that are not valid UTF-8 are
     * dropped rather than rejected: a filtered dashboard is a URL people share
     * and edit by hand, and a stale one should show everything, not a 422.
     *
     * The result is ordered by COLUMNS rather than by the query string, so
     * the pills never reshuffle between two requests carrying the same filters.
     *
     * @param  array<string, mixed>  $query
     * @return array<string, list<string>>
     */
    public static function execute(array $query): array
    {
        $filters = [];

        foreach (array_keys(ApplyFilters::COLUMNS) as $dimension) {
            $values = self::values(data_get($query, $dimension));

            if ($values !== []) {
                $filters[$dimension] = $values;
            }
        }

        return $filters;
    }

    /**
     * The shape the frontend renders: a list, so a pill never has to work out
     * whether it was handed a scalar or an array.
     *
     * @param  array<string, list<string>>  $filters
     * @return list<array{dimension: string, values: list<string>}>
     */
    public static function toActive(array $filters): array
    {
        return collect($filters)
            ->map(fn (array $values, string $dimension) => [
                'dimension' => $dimension,
                'values' => $values,
            ])
            ->values()
            ->all();
    }

    /**
     * @return list<string>
     */
    private static function values(mixed $value): array
    {
        $values = is_array($value) ? $value : [$value];

        return collect($values)
            ->filter(fn ($item) => is_string($item) && $item !== '' && self::isSafe($item))
            ->unique()
            ->values()
            ->all();
    }

    /**
     * A NUL byte or invalid UTF-8 has no business reaching the database, and
     * neither can come from a value the dashboard itself produced.
     */
    private static function isSafe(string $value): bool
    {
        return ! str_contains($value, "\0") && mb_check_encoding($value, 'UTF-8');
    }
}
