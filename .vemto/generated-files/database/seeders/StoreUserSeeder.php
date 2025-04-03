<?php

namespace Database\Seeders;

use App\Models\StoreUser;
use Illuminate\Database\Seeder;

class StoreUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        StoreUser::factory()
            ->count(5)
            ->create();
    }
}
