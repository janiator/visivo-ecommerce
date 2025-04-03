<?php

namespace Database\Seeders;

use App\Models\StripeProduct;
use Illuminate\Database\Seeder;

class StripeProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        StripeProduct::factory()
            ->count(5)
            ->create();
    }
}
