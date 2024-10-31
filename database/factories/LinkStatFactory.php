<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Services\UserAgentService;

use App\Enums\LinkStat\Event;

use App\Models\Link;
use App\Models\Workspace;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LinkStat>
 */
class LinkStatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $service = new UserAgentService();

        $userAgents = json_decode(file_get_contents(database_path('factories/data/userAgent.json')));
        $languages = json_decode(file_get_contents(database_path('factories/data/languages.json')));

        $userAgent = collect($userAgents)->random();

        $ip = $this->faker->ipv4;

        // user geo
        $geo = geoip($ip);

        return [
            'workspace_id' => Workspace::factory(),
            'link_id' => Link::factory(),
            'event' => $this->faker->randomElement([Event::CLICK, Event::QR_SCAN]),
            'country' => $geo->iso_code,
            'region' => $geo->state_name,
            'city' => $geo->city,

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
