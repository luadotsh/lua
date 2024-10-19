<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use \App\Models\Workspace;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Link>
 */
class LinkFactory extends Factory
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
            'domain' => "https://lua.sh",
            'key' => $this->faker->slug,
            'url' => $this->faker->url,
            'link' => $this->faker->url,
            'utm_source' => $this->faker->word,
            'utm_medium' => $this->faker->word,
            'utm_campaign' => $this->faker->word,
            'utm_term' => $this->faker->word,
            'utm_content' => $this->faker->word,
            'clicks' => $this->faker->randomNumber(),
            'last_click' => $this->faker->dateTime,
        ];
    }
}
