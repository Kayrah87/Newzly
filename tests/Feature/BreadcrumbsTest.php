<?php

use App\Models\Issue;
use App\Models\Publication;
use App\Models\Story;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('the issue page shows the full publication trail', function () {
    $publication = Publication::factory()->create(['name' => 'Farming Monthly']);
    $issue = Issue::factory()->create(['publication_id' => $publication->id, 'title' => 'May Edition']);

    $response = $this->actingAs($publication->owner)
        ->get(route('publications.issues.show', [$publication, $issue]));

    $response->assertOk()
        ->assertSee('Breadcrumb', false)
        ->assertSeeInOrder(['Publications', 'Farming Monthly', 'Issues', 'May Edition'], false)
        ->assertSee(route('publications.index'), false)
        ->assertSee(route('publications.show', $publication), false)
        ->assertSee(route('publications.issues.index', $publication), false);
});

test('the story edit page trails down to the story', function () {
    $publication = Publication::factory()->create(['name' => 'Farming Monthly']);
    $issue = Issue::factory()->create(['publication_id' => $publication->id, 'title' => 'May Edition']);
    $story = Story::factory()->create([
        'publication_id' => $publication->id,
        'issue_id' => $issue->id,
        'title' => 'Tractor Roundup',
    ]);

    $response = $this->actingAs($publication->owner)
        ->get(route('publications.issues.stories.edit', [$publication, $issue, $story]));

    $response->assertOk()
        ->assertSeeInOrder(['Publications', 'Farming Monthly', 'Issues', 'May Edition', 'Tractor Roundup', 'Edit'], false)
        ->assertSee(route('publications.issues.show', [$publication, $issue]), false);
});
