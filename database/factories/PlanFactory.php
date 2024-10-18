<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Enums\User\Role;
use \App\Models\Workspace;

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
            'name' => 'Starter',
            'internal_id' => 'starter-yearly',
            'price' => 250,
            'is_monthly' => false,
            'stripe_id' => 'price_1QBMNzGHHP1nttACEJyKT2Ja',
            'access_level' => 2,
            'is_private' => false,
        ];
    }
}
