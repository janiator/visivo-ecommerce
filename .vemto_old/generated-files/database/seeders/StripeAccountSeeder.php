<?php

namespace Database\Seeders;

use App\Models\StripeAccount;
use Illuminate\Database\Seeder;

class StripeAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        StripeAccount::factory()
            ->count(5)
            ->create();
    }
}
