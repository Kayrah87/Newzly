<?php

namespace Database\Factories;

use App\Models\Block;
use App\Models\Issue;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Block>
 */
class BlockFactory extends Factory
{
    public function definition(): array
    {
        // Tie the block to a freshly-created issue and inherit its publication
        // so the tenant scoping stays consistent.
        $issue = Issue::factory()->create();

        return [
            'issue_id' => $issue->id,
            'publication_id' => $issue->publication_id,
            'type' => Block::TYPE_EVENTS,
            'title' => 'Upcoming Events',
            'title_style' => 'accent',
            'intro' => null,
            'order' => 0,
        ];
    }
}
