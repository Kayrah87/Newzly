<?php

namespace Database\Factories;

use App\Models\Issue;
use App\Models\Story;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Story>
 */
class StoryFactory extends Factory
{
    public function definition(): array
    {
        // Tie the story to a freshly-created issue and inherit its publication
        // so the tenant scoping stays consistent.
        $issue = Issue::factory()->create();

        return [
            'publication_id' => $issue->publication_id,
            'issue_id' => $issue->id,
            'title' => fake()->sentence(5),
            'content' => fake()->paragraphs(2, true),
            'author_id' => User::factory(),
            'order' => 0,
        ];
    }
}
