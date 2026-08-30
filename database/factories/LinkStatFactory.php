<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\LinkStat\Event;
use App\Models\Link;
use App\Models\LinkStat;
use App\Models\Workspace;
use App\Services\UserAgentService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LinkStat>
 */
class LinkStatFactory extends Factory
{
    /**
     * Real places, so a seeded row reads like a visit rather than like noise.
     * The three fields have to agree — "Tokyo, Scotland, JP" would make the
     * location breakdowns nonsense to look at.
     *
     * @var array<int, array{0: string, 1: string, 2: string}>
     */
    private const PLACES = [
        ['US', 'California', 'San Francisco'],
        ['US', 'New York', 'Buffalo'],
        ['GB', 'England', 'London'],
        ['GB', 'Scotland', 'Glasgow'],
        ['BR', 'Sao Paulo', 'Sao Paulo'],
        ['BR', 'Rio de Janeiro', 'Niteroi'],
        ['DE', 'Bavaria', 'Munich'],
        ['FR', 'Ile-de-France', 'Versailles'],
        ['ES', 'Catalonia', 'Barcelona'],
        ['PT', 'Porto', 'Porto'],
        ['JP', 'Tokyo', 'Tokyo'],
        ['IN', 'Karnataka', 'Bengaluru'],
        ['CA', 'Ontario', 'Toronto'],
        ['AU', 'Victoria', 'Melbourne'],
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $service = new UserAgentService;

        $userAgents = json_decode(file_get_contents(database_path('factories/data/userAgent.json')));
        $languages = json_decode(file_get_contents(database_path('factories/data/languages.json')));

        $userAgent = collect($userAgents)->random();

        // Was a live `geoip()` lookup on a random IP — a package the app no
        // longer has, which made every call to this factory throw.
        [$country, $region, $city] = $this->faker->randomElement(self::PLACES);

        return [
            'workspace_id' => Workspace::factory(),
            'link_id' => Link::factory(),
            'event' => $this->faker->randomElement([Event::CLICK, Event::QR_SCAN]),
            'country' => $country,
            'region' => $region,
            'city' => $city,

            'browser' => $service->getBrowser($userAgent),
            'os' => $service->getOS($userAgent),
            'device' => $service->getDevice($userAgent),
            'language' => $service->getLanguage($languages),

            'ip' => $this->faker->ipv4,

            'utm_medium' => collect(['cpc', 'feed', 'newsletter', null])->random(),
            'utm_source' => collect(['google', 'facebook', 'twitter', null])->random(),
            'utm_campaign' => collect(['cQ1', 'cQ2', 'cQ3', 'cQ4', null])->random(),
            'utm_content' => collect(['banner_top', 'banner_left', null])->random(),
            'utm_term' => collect(['my+keyword', 'nice+keyword', null])->random(),

            'referer' => $service->getReferer($this->faker->url),
        ];
    }
}
