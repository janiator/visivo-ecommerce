<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'store_id' => fake()->randomNumber(0),
            'status' => fake()->word(),
            'name' => fake()->name(),
            'type' => fake()->word(),
            'description' => fake()->sentence(15),
            'price' => fake()->randomFloat(2, 0, 9999),
            'short_description' => fake()->text(),
            'stripe_product_id' => fake()->text(255),
            'deleted_at' => fake()->dateTime(),
            'metadata' => [],
            'store_id' => \App\Models\Store::factory(),
        ];
    }
}
