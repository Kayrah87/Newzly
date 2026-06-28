<?php

namespace Database\Factories;

use App\Models\Block;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    public function definition(): array
    {
        return [
            'block_id' => Block::factory(),
            'name' => fake()->sentence(3),
            'date' => fake()->dateTimeBetween('now', '+3 months')->format('Y-m-d'),
            'location' => fake()->city(),
            'description' => fake()->optional()->sentence(),
            'order' => 0,
        ];
    }
}
