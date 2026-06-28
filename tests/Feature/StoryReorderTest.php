<?php

use App\Models\Issue;
use App\Models\Publication;
use App\Models\Story;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;

uses(RefreshDatabase::class);

/**
 * Create an issue with three stories (orders 1,2,3) under a fresh publication.
 *
 * @return array{0: Publication, 1: Issue, 2: Collection<int, Story>}
 */
function issueWithThreeStories(): array
{
    $publication = Publication::factory()->create();
    $issue = Issue::factory()->create(['publication_id' => $publication->id]);

    $stories = collect(['One', 'Two', 'Three'])->map(fn ($title, $i) => Story::factory()->create([
        'publication_id' => $publication->id,
        'issue_id' => $issue->id,
        'title' => $title,
        'order' => $i + 1,
    ]));

    return [$publication, $issue, $stories];
}

test('an editor can reorder an issue\'s stories', function () {
    [$publication, $issue, $stories] = issueWithThreeStories();
    [$one, $two, $three] = [$stories[0], $stories[1], $stories[2]];

    $this->actingAs($publication->owner)
        ->patchJson(route('publications.issues.stories.reorder', [$publication, $issue]), [
            'order' => [$three->id, $one->id, $two->id],
        ])
        ->assertOk();

    expect($three->fresh()->order)->toBe(1)
        ->and($one->fresh()->order)->toBe(2)
        ->and($two->fresh()->order)->toBe(3);

    // The relation (ordered by `order`) now returns the new sequence.
    expect($issue->fresh()->stories->pluck('title')->all())
        ->toBe(['Three', 'One', 'Two']);
});

test('ids that do not belong to the issue are ignored', function () {
    [$publication, $issue, $stories] = issueWithThreeStories();
    [$one, $two, $three] = [$stories[0], $stories[1], $stories[2]];
    $foreign = Story::factory()->create(); // belongs to a different issue

    $this->actingAs($publication->owner)
        ->patchJson(route('publications.issues.stories.reorder', [$publication, $issue]), [
            'order' => [$two->id, $foreign->id, $three->id, $one->id],
        ])
        ->assertOk();

    expect($two->fresh()->order)->toBe(1)
        ->and($three->fresh()->order)->toBe(2)
        ->and($one->fresh()->order)->toBe(3)
        // The foreign story is untouched.
        ->and($foreign->fresh()->order)->toBe($foreign->order);
});

test('a member without manage-stories permission cannot reorder', function () {
    [$publication, $issue, $stories] = issueWithThreeStories();
    $factChecker = User::factory()->create();
    $publication->members()->attach($factChecker->id, ['role' => 'fact_checker']);

    $this->actingAs($factChecker)
        ->patchJson(route('publications.issues.stories.reorder', [$publication, $issue]), [
            'order' => $stories->pluck('id')->all(),
        ])
        ->assertForbidden();
});

test('an outsider cannot reorder', function () {
    [$publication, $issue, $stories] = issueWithThreeStories();

    $this->actingAs(User::factory()->create())
        ->patchJson(route('publications.issues.stories.reorder', [$publication, $issue]), [
            'order' => $stories->pluck('id')->all(),
        ])
        ->assertForbidden();
});

test('the order payload is required', function () {
    [$publication, $issue] = issueWithThreeStories();

    $this->actingAs($publication->owner)
        ->patchJson(route('publications.issues.stories.reorder', [$publication, $issue]), [])
        ->assertStatus(422);
});
