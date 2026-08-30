<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Warning;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Warning>
 */
class WarningFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'admin_id' => User::factory(),
            'reason' => fake()->paragraph(),
            'date' => fake()->dateTimeBetween('-6 months', 'now'),
        ];
    }
}
