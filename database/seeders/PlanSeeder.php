<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run()
    {
        // Free
        Plan::create([
            'name' => 'Free',
            'internal_id' => 'free',
            'price' => 0,
            'is_monthly' => true,
            'stripe_id' => null,
            'access_level' => 1,
            'is_private' => false,
            'max_links' => 100,
            'max_events' => 1000,
        ]);

        // Starter Monthly
        Plan::create([
            'name' => 'Starter',
            'internal_id' => 'starter-monthly',
            'price' => 19,
            'is_monthly' => true,
            'stripe_id' => 'price_1QBMNzGHHP1nttACZPuqD7rX',
            'access_level' => 2,
            'is_private' => false,
            'max_links' => 1000,
            'max_events' => 50000,
        ]);

        // Starter Yearly
        Plan::create([
            'name' => 'Starter',
            'internal_id' => 'starter-yearly',
            'price' => 190,
            'is_monthly' => false,
            'stripe_id' => 'price_1QBMNzGHHP1nttACEJyKT2Ja',
            'access_level' => 2,
            'is_private' => false,
            'max_links' => 1000,
            'max_events' => 50000,
        ]);

        // Pro Monthly
        Plan::create([
            'name' => 'Pro',
            'internal_id' => 'pro-monthly',
            'price' => 49,
            'is_monthly' => true,
            'stripe_id' => 'price_1QBMOSGHHP1nttACfXP5JL09',
            'access_level' => 3,
            'is_private' => false,
            'max_links' => 5000,
            'max_events' => 150000,
        ]);

        // Pro Yearly
        Plan::create([
            'name' => 'Pro',
            'internal_id' => 'pro-yearly',
            'price' => 490,
            'is_monthly' => false,
            'stripe_id' => 'price_1QBMOSGHHP1nttAC2LjdkShG',
            'access_level' => 3,
            'is_private' => false,
            'max_links' => 5000,
            'max_events' => 150000,
        ]);

        // Growth Monthly
        Plan::create([
            'name' => 'Growth',
            'internal_id' => 'growth-monthly',
            'price' => 149,
            'is_monthly' => true,
            'stripe_id' => 'price_1QBMP5GHHP1nttACIJT35Cl9',
            'access_level' => 4,
            'is_private' => false,
            'max_links' => 20000,
            'max_events' => 500000,
        ]);

        // Growth Yearly
        Plan::create([
            'name' => 'Growth',
            'internal_id' => 'growth-yearly',
            'price' => 1490,
            'is_monthly' => false,
            'stripe_id' => 'price_1QBMP5GHHP1nttACdwPVV0T5',
            'access_level' => 4,
            'is_private' => false,
            'max_links' => 20000,
            'max_events' => 500000,
        ]);

        // Scale Monthly
        Plan::create([
            'name' => 'Scale',
            'internal_id' => 'scale-monthly',
            'price' => 349,
            'is_monthly' => true,
            'stripe_id' => 'price_1QBMQ6GHHP1nttACm26hC47b',
            'access_level' => 5,
            'is_private' => false,
            'max_links' => 100000,
            'max_events' => 2000000,
        ]);

        // Scale Yearly
        Plan::create([
            'name' => 'Scale',
            'internal_id' => 'scale-yearly',
            'price' => 3490,
            'is_monthly' => false,
            'stripe_id' => 'price_1QBMQ6GHHP1nttACRYLwGzCB',
            'access_level' => 5,
            'is_private' => false,
            'max_links' => 100000,
            'max_events' => 2000000,
        ]);

    }
}
