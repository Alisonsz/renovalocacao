<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = fake()->dateTimeBetween('+1 week', '+3 months');

        return [
            'product_id' => Product::factory(),
            'customer_name' => fake()->name(),
            'customer_email' => fake()->safeEmail(),
            'customer_phone' => sprintf('(%s) %s-%s', fake()->numerify('##'), fake()->numerify('#####'), fake()->numerify('####')),
            'customer_company' => fake()->optional()->company(),
            'customer_zip_code' => fake()->numerify('#####-###'),
            'customer_city' => fake()->city(),
            'customer_street' => fake()->streetName(),
            'customer_number' => (string) fake()->numberBetween(1, 99999),
            'customer_reference' => fake()->sentence(4),
            'start_date' => $start,
            'end_date' => fake()->optional()->dateTimeBetween($start, '+4 months'),
            'message' => fake()->optional()->paragraph(),
            'status' => fake()->randomElement(['pending', 'contacted', 'confirmed', 'cancelled']),
            'admin_notes' => null,
            'notified_at' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(['status' => 'pending']);
    }

    public function confirmed(): static
    {
        return $this->state(['status' => 'confirmed']);
    }
}
