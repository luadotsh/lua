<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Tag colours used to be one of twelve names rendered as `bg-{name}-400`.
     * A class built at runtime is a class Tailwind never sees, so the dot was
     * colourless anyway — and twelve choices is a poor palette. The column now
     * holds the hex itself.
     *
     * @var array<string, string>
     */
    private const NAME_TO_HEX = [
        'red' => '#f87171',
        'orange' => '#fb923c',
        'yellow' => '#facc15',
        'green' => '#4ade80',
        'cyan' => '#22d3ee',
        'teal' => '#2dd4bf',
        'blue' => '#60a5fa',
        'indigo' => '#818cf8',
        'purple' => '#c084fc',
        'fuchsia' => '#e879f9',
        'pink' => '#f472b6',
        'zinc' => '#a1a1aa',
    ];

    public function up(): void
    {
        foreach (self::NAME_TO_HEX as $name => $hex) {
            DB::table('tags')->where('color', $name)->update(['color' => $hex]);
        }
    }

    public function down(): void
    {
        foreach (self::NAME_TO_HEX as $name => $hex) {
            DB::table('tags')->where('color', $hex)->update(['color' => $name]);
        }
    }
};
