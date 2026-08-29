<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Passport;

class PassportSeeder extends Seeder
{
    /**
     * Passport can only mint personal access tokens when a client with the
     * personal_access grant exists, so without this the first "New API token"
     * throws rather than degrading. Idempotent, so it is safe to re-run on
     * every deploy.
     */
    public function run(): void
    {
        $exists = Passport::client()->newQuery()
            ->whereJsonContains('grant_types', 'personal_access')
            ->where('revoked', false)
            ->exists();

        if ($exists) {
            return;
        }

        app(ClientRepository::class)->createPersonalAccessGrantClient(
            config('app.name').' Personal Access Client',
            'users',
        );
    }
}
