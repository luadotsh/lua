<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Carbon\CarbonPeriod;

use App\Enums\User\Role;

use App\Models\User;
use App\Models\Link;
use App\Models\LinkStat;
use App\Models\Workspace;

class LocalDevelopmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::factory([
            'name' => 'Admin',
            'email' => 'admin@lua.sh'
        ])
            ->hasAttached(
                Workspace::factory(),
                ['role' => Role::ROLE_ADMIN]
            )
            ->create();

        $workspace = Workspace::first();

        // set current workspace
        $user->current_workspace_id = $workspace->id;
        $user->save();

        // create some links
        $links = Link::factory()
            ->count(10)
            ->create([
                'workspace_id' => $workspace->id
            ]);

        $dates = CarbonPeriod::create(now()->subMonths(4), '60 minutes', now());

        foreach ($dates as $date) {
            LinkStat::factory([
                'workspace_id' => $workspace->id,
                'created_at' => $date
            ])
                ->count(rand(1, 2))
                ->for($links->random())
                ->create();
        }
    }
}
