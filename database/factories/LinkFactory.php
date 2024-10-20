<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use Illuminate\Support\Str;

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
        $slug = Str::random(7);

        return [
            'workspace_id' => Workspace::factory(),
            'domain' => config('domains.main'),
            'key' => $slug,
            'url' => $this->faker->url,
            'link' => config('domains.main').$slug,
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
