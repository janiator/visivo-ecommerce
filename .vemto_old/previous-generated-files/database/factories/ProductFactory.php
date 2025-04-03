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
            'main_variant_id' => fake()->randomNumber(0),
            'status' => fake()->word(),
            'name' => fake()->name(),
            'type' => fake()->word(),
            'description' => fake()->sentence(15),
            'price' => fake()->randomFloat(2, 0, 9999),
            'short_description' => fake()->text(),
            'store_id' => \App\Models\Store::factory(),
            'main_variant_id' => \App\Models\ProductVariant::factory(),
        ];
    }
}
