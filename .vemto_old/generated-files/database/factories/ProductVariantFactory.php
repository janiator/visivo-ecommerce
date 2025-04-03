<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductVariantFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProductVariant::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => fake()->randomNumber(0),
            'name' => fake()->name(),
            'price' => fake()->randomFloat(2, 0, 9999),
            'grouping_attribute' => fake()->text(255),
            'short_description' => fake()->text(),
            'description' => fake()->sentence(15),
            'metadata' => fake()->text(),
            'status' => fake()->word(),
            'product_id' => \App\Models\Product::factory(),
        ];
    }
}
