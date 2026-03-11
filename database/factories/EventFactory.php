<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('now', '+3 months');
        $endDate   = fake()->dateTimeBetween($startDate, '+3 months');

        return [
            'category_event'    => fake()->randomElement(['online', 'onsite']),
            'event_name'        => fake()->sentence(3),
            'location'          => fake()->city() . ', Malaysia',
            'start_date'        => $startDate->format('Y-m-d'),
            'end_date'          => $endDate->format('Y-m-d'),
            'unique_identifier' => $this->generateUniqueIdentifier(),
        ];
    }

    /**
     * Generate a unique identifier in the format EVENT-XXXXXXXXXX
     * where X is an uppercase alphanumeric character.
     */
    private function generateUniqueIdentifier(): string
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $code  = '';
        for ($i = 0; $i < 10; $i++) {
            $code .= $chars[random_int(0, strlen($chars) - 1)];
        }

        return 'EVENT-' . $code;
    }
}
