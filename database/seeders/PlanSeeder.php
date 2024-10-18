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
        ]);

        // Starter Monthly
        Plan::create([
            'name' => 'Starter',
            'internal_id' => 'starter-monthly',
            'price' => 25,
            'is_monthly' => true,
            'stripe_id' => 'price_1QBMNzGHHP1nttACZPuqD7rX',
            'access_level' => 2,
            'is_private' => false,
        ]);

        // Starter Yearly
        Plan::create([
            'name' => 'Starter',
            'internal_id' => 'starter-yearly',
            'price' => 250,
            'is_monthly' => false,
            'stripe_id' => 'price_1QBMNzGHHP1nttACEJyKT2Ja',
            'access_level' => 2,
            'is_private' => false,
        ]);

        // Pro Monthly
        Plan::create([
            'name' => 'Pro',
            'internal_id' => 'pro-monthly',
            'price' => 50,
            'is_monthly' => true,
            'stripe_id' => 'price_1QBMOSGHHP1nttACfXP5JL09',
            'access_level' => 3,
            'is_private' => false,
        ]);

        // Pro Yearly
        Plan::create([
            'name' => 'Pro',
            'internal_id' => 'pro-yearly',
            'price' => 500,
            'is_monthly' => false,
            'stripe_id' => 'price_1QBMOSGHHP1nttAC2LjdkShG',
            'access_level' => 3,
            'is_private' => false,
        ]);

        // Growth Monthly
        Plan::create([
            'name' => 'Growth',
            'internal_id' => 'growth-monthly',
            'price' => 99,
            'is_monthly' => true,
            'stripe_id' => 'price_1QBMP5GHHP1nttACIJT35Cl9',
            'access_level' => 4,
            'is_private' => false,
        ]);

        // Growth Yearly
        Plan::create([
            'name' => 'Growth',
            'internal_id' => 'growth-yearly',
            'price' => 990,
            'is_monthly' => false,
            'stripe_id' => 'price_1QBMP5GHHP1nttACdwPVV0T5',
            'access_level' => 4,
            'is_private' => false,
        ]);

        // Business Monthly
        Plan::create([
            'name' => 'Business',
            'internal_id' => 'business-monthly',
            'price' => 299,
            'is_monthly' => true,
            'stripe_id' => 'price_1QBMPkGHHP1nttACE78cGLxj',
            'access_level' => 5,
            'is_private' => false,
        ]);

        // Business Yearly
        Plan::create([
            'name' => 'Business',
            'internal_id' => 'business-yearly',
            'price' => 2990,
            'is_monthly' => false,
            'stripe_id' => 'price_1QBMPkGHHP1nttACizI0WDbD',
            'access_level' => 5,
            'is_private' => false,
        ]);

        // Scale Monthly
        Plan::create([
            'name' => 'Scale',
            'internal_id' => 'scale-monthly',
            'price' => 499,
            'is_monthly' => true,
            'stripe_id' => 'price_1QBMQ6GHHP1nttACm26hC47b',
            'access_level' => 5,
            'is_private' => false,
        ]);

        // Scale Yearly
        Plan::create([
            'name' => 'Scale',
            'internal_id' => 'scale-yearly',
            'price' => 4990,
            'is_monthly' => false,
            'stripe_id' => 'price_1QBMQ6GHHP1nttACRYLwGzCB',
            'access_level' => 5,
            'is_private' => false,
        ]);

    }
}
