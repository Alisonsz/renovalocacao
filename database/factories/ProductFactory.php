<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->words(3, true);

        return [
            'category_id' => Category::factory(),
            'name' => $name,
            'slug' => Str::slug($name.'-'.fake()->unique()->randomNumber(4)),
            'description' => fake()->optional()->paragraph(),
            'short_description' => fake()->optional()->sentence(),
            'availability' => 'available',
            'price_per_day' => fake()->optional()->randomFloat(2, 50, 5000),
            'is_active' => true,
            'order' => 0,
        ];
    }
}
