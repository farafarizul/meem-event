<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Use a unique integer sequence to generate meem_code
        static $sequence = 0;
        $sequence++;

        return [
            'fullname'     => fake()->name(),
            'phone_number' => fake()->numerify('601########'),
            'meem_code'    => 'MEEM' . str_pad($sequence, 6, '0', STR_PAD_LEFT),
            'email'        => fake()->unique()->safeEmail(),
            'password'     => bcrypt('password'),
            'is_admin'     => false,
        ];
    }

    /**
     * Mark the user as an admin.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_admin' => true,
        ]);
    }

    /**
     * Create a user without email/password (walk-in participant style).
     */
    public function anonymous(): static
    {
        return $this->state(fn (array $attributes) => [
            'email'    => null,
            'password' => null,
        ]);
    }
}
