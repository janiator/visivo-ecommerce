<?php

namespace Database\Factories;

use App\Models\Collection;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class CollectionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Collection::class;

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
            'visible' => fake()->boolean(),
            'store_id' => \App\Models\Store::factory(),
        ];
    }
}
