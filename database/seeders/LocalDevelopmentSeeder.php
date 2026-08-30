<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\User\Role;
use App\Models\Link;
use App\Models\LinkStat;
use App\Models\Plan;
use App\Models\Tag;
use App\Models\User;
use App\Models\Workspace;
use Carbon\CarbonPeriod;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class LocalDevelopmentSeeder extends Seeder
{
    /**
     * Enough links that the list actually pages, and varied enough that the
     * columns have something to show: tags, campaigns, passwords, expiries and
     * platform targeting each land on a slice of them rather than on all or
     * none.
     */
    private const LINKS = 250;

    /**
     * Seeds into an existing workspace when `SEED_WORKSPACE_ID` names one, so
     * the data lands where you are already signed in. Otherwise it builds the
     * admin account and seeds there.
     */
    public function run(): void
    {
        $workspace = $this->workspace();

        $tags = Tag::where('workspace_id', $workspace->id)->get();

        $links = collect(range(1, self::LINKS))->map(function (int $i) use ($workspace, $tags): Link {
            $link = Link::factory()->create([
                'workspace_id' => $workspace->id,
                'clicks' => 0,
                'last_click' => null,
                // Spread over four months so "Created" is not one timestamp.
                'created_at' => now()->subMinutes(random_int(0, 60 * 24 * 120)),

                // Platform targeting on a fifth of them; the factory fills both
                // by default, which would put the icon on every single row.
                'ios' => $i % 5 === 0 ? 'https://apps.apple.com/app/333903271' : null,
                'android' => $i % 5 === 0 ? 'https://play.google.com/store/apps/details?id=com.twitter.android' : null,

                'password' => $i % 11 === 0 ? 'sesame' : null,
                'expires_at' => $i % 7 === 0 ? now()->addDays(random_int(-30, 90)) : null,

                'utm_source' => $i % 3 === 0 ? fake()->randomElement(['newsletter', 'twitter', 'partner']) : null,
                'utm_medium' => $i % 3 === 0 ? fake()->randomElement(['email', 'social', 'cpc']) : null,
                'utm_campaign' => $i % 3 === 0 ? fake()->randomElement(['launch', 'black-friday', 'onboarding']) : null,
            ]);

            if ($tags->isNotEmpty() && $i % 2 === 0) {
                $link->tags()->sync($tags->random(random_int(1, min(2, $tags->count())))->pluck('id'));
            }

            return $link;
        });

        $this->command?->info(self::LINKS.' links seeded into '.$workspace->name);

        $this->stats($workspace, $links);
    }

    private function workspace(): Workspace
    {
        if ($id = env('SEED_WORKSPACE_ID')) {
            return Workspace::findOrFail($id);
        }

        $user = User::factory([
            'name' => 'Admin',
            'email' => 'admin@lua.sh',
        ])
            ->hasAttached(
                Workspace::factory([
                    'plan_id' => Plan::where('internal_id', 'free')->first()->id,
                ]),
                ['role' => Role::ROLE_ADMIN],
            )
            ->create();

        $workspace = $user->workspaces()->first();

        $user->current_workspace_id = $workspace->id;
        $user->save();

        return $workspace;
    }

    /**
     * @param  Collection<int, Link>  $links
     */
    private function stats(Workspace $workspace, $links): void
    {
        $dates = CarbonPeriod::create(now()->subMonths(4), '60 minutes', now());

        foreach ($dates as $date) {
            $link = $links->random();

            LinkStat::factory([
                'link_id' => $link->id,
                'workspace_id' => $workspace->id,
                'created_at' => $date,
            ])->create();
        }

        // One pass at the end rather than a save per stat.
        foreach ($links as $link) {
            $link->clicks = LinkStat::where('link_id', $link->id)->count();
            $link->last_click = LinkStat::where('link_id', $link->id)->max('created_at');
            $link->save();
        }
    }
}
