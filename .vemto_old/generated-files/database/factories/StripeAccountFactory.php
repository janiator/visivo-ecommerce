<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\StripeAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

class StripeAccountFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = StripeAccount::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'account_id' => fake()->text(255),
            'name' => fake()->name(),
            'user_id' => fake()->randomNumber(),
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
