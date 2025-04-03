<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\StripeProduct;
use Illuminate\Database\Eloquent\Factories\Factory;

class StripeProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = StripeProduct::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'active' => fake()->boolean(),
            'livemode' => fake()->boolean(),
            'created' => fake()->dateTime(),
            'updated' => fake()->dateTime(),
            'description' => fake()->sentence(15),
            'images' => fake()->text(255),
            'metadata' => fake()->text(),
            'name' => fake()->name(),
            'package_dimensions' => fake()->text(),
            'shippable' => fake()->boolean(),
            'type' => fake()->word(),
            'unit_label' => fake()->text(255),
            'url' => fake()->url(),
            'price' => fake()->randomFloat(2, 0, 9999),
            'price_id' => fake()->text(255),
        ];
    }
}
