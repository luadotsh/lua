<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Link;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Link>
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
        // The host requests actually arrive on, so `domain` and `link` agree.
        $domain = parse_url((string) config('app.url'), PHP_URL_HOST) ?: config('domains.main');

        return [
            'workspace_id' => Workspace::factory(),
            'domain' => $domain,
            'key' => $slug,
            'url' => $this->faker->url,
            'link' => "https://{$domain}/{$slug}",
            'ios' => $this->faker->url,
            'android' => $this->faker->url,
            // Left unset: most links carry no campaign, and the redirect now
            // appends whatever is here to the destination — a factory that
            // always filled them made every redirect assertion carry five
            // parameters nobody asked for.
        ];
    }
}
