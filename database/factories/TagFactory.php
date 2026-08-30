<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Tag;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tag>
 */
class TagFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'name' => $this->faker->word,
            'color' => $this->faker->hexColor(),
        ];
    }
}
