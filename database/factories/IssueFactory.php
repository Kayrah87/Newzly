<?php

namespace Database\Factories;

use App\Models\Issue;
use App\Models\Publication;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Issue>
 */
class IssueFactory extends Factory
{
    public function definition(): array
    {
        return [
            'publication_id' => Publication::factory(),
            'title' => fake()->sentence(4),
            'content' => fake()->paragraph(),
            'status' => 'draft',
            'published_at' => null,
        ];
    }
}
