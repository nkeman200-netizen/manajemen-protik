<?php

namespace Database\Factories;

use App\Models\Document;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Document>
 */
class DocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'created_by' => User::factory(),
            'event_id' => fake()->optional(0.5)->randomElement(
                Event::pluck('id')->toArray() ?: [null]
            ),
            'letter_number' => strtoupper(fake()->unique()->bothify('??-###/PROTIK/####')),
            'title' => fake()->sentence(5),
            'letter_link' => fake()->url(),
            'scan_link' => fake()->url(),
            'activity_date' => fake()->date(),
        ];
    }
}
