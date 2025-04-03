<?php

namespace Database\Factories;

use App\Models\OrderItem;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OrderItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => fake()->randomNumber(0),
            'product_id' => fake()->randomNumber(0),
            'product_variant_id' => fake()->randomNumber(0),
            'quantity' => fake()->randomNumber(),
            'unit_price' => fake()->randomNumber(0),
            'total_price' => fake()->randomNumber(0),
            'name' => fake()->name(),
            'order_id' => \App\Models\Order::factory(),
            'product_id' => \App\Models\Product::factory(),
        ];
    }
}
