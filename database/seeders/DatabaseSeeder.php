<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run these seeders in all environments
        $this->call([
            PlanSeeder::class,
        ]);
    }
}
