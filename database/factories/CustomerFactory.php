<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Customer::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'store_id' => fake()->randomNumber(0),
            'name' => fake()->name(),
            'email' => fake()
                ->unique()
                ->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'billing_address' => fake()->text(),
            'shipping_address' => fake()->text(),
            'guest' => fake()->boolean(),
            'store_id' => \App\Models\Store::factory(),
        ];
    }
}
