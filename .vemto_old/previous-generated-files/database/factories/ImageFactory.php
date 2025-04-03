<?php

namespace Database\Factories;

use App\Models\Image;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class ImageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Image::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'mime_type' => fake()->text(255),
            'public_url' => fake()->text(255),
            'store_id' => fake()->randomNumber(0),
            'store_id' => \App\Models\Store::factory(),
        ];
    }
}
