<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Plan>
 */
class PlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Scale',
            'internal_id' => Str::uuid(),
            'price' => 3490,
            'is_monthly' => false,
            'stripe_id' => '',
            'access_level' => 5,
            'is_private' => false,
            'max_links' => 100000,
            'max_events' => 2000000,
            'max_users' => 20,
            'max_tags' => 1000,
            'max_domains' => 500,
        ];
    }
}
