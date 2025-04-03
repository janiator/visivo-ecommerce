<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'store_id' => fake()->randomNumber(0),
            'customer_id' => fake()->randomNumber(0),
            'stripe_order_id' => fake()->text(255),
            'payment_intent' => fake()->text(255),
            'status' => fake()->word(),
            'subtotal' => fake()->randomNumber(0),
            'total_amount' => fake()->randomNumber(0),
            'currency' => fake()->currencyCode(),
            'shipping_address' => fake()->text(),
            'billing_address' => fake()->text(),
            'metadata' => fake()->text(),
            'store_id' => \App\Models\Store::factory(),
            'customer_id' => \App\Models\Customer::factory(),
        ];
    }
}
