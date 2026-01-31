<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Plan::insert([
            [
                'name' => 'Free',
                'price' => 0,
                'monthly_limit' => 100,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Pro',
                'price' => 999,
                'monthly_limit' => 10000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Enterprise',
                'price' => 4999,
                'monthly_limit' => null, // unlimited
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
