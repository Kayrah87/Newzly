<?php

use App\Mail\IssueNewsletter;
use App\Models\Block;
use App\Models\Event;
use App\Models\Issue;
use App\Models\Publication;
use App\Models\Story;
use App\Models\Subscriber;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('an editor can add an events block with events', function () {
    $publication = Publication::factory()->create();
    $issue = Issue::factory()->create(['publication_id' => $publication->id]);

    $this->actingAs($publication->owner)
        ->post(route('publications.issues.blocks.store', [$publication, $issue]), [
            'title' => 'Upcoming Events',
            'title_style' => 'accent',
            'intro' => 'Here is what is coming up.',
            'events' => [
                ['name' => 'Old Stores Meetup', 'date' => '2026-05-17', 'location' => 'Caernarfon'],
                ['name' => '', 'location' => 'ignored — no name'],
                ['name' => 'Coffee & Bikes', 'date' => '2026-06-02', 'location' => 'Bangor', 'description' => 'Casual'],
            ],
        ])
        ->assertRedirect(route('publications.issues.show', [$publication, $issue]));

    $block = $issue->blocks()->first();

    expect($block)->not->toBeNull()
        ->and($block->type)->toBe('events')
        ->and($block->title)->toBe('Upcoming Events')
        ->and($block->title_style)->toBe('accent')
        // The blank-name row is dropped; the rest keep submitted order.
        ->and($block->events)->toHaveCount(2)
        ->and($block->events->pluck('name')->all())->toBe(['Old Stores Meetup', 'Coffee & Bikes'])
        ->and($block->events->first()->location)->toBe('Caernarfon');
});

test('updating a block replaces its events', function () {
    $publication = Publication::factory()->create();
    $issue = Issue::factory()->create(['publication_id' => $publication->id]);
    $block = Block::factory()->create([
        'publication_id' => $publication->id,
        'issue_id' => $issue->id,
    ]);
    $block->events()->createMany([
        ['name' => 'Old Event', 'order' => 1],
        ['name' => 'Another Old', 'order' => 2],
    ]);

    $this->actingAs($publication->owner)
        ->patch(route('publications.issues.blocks.update', [$publication, $issue, $block]), [
            'title' => 'Diary',
            'title_style' => 'plain',
            'events' => [['name' => 'Brand New Event', 'date' => '2026-07-01']],
        ])
        ->assertRedirect();

    $block->refresh();
    expect($block->title)->toBe('Diary')
        ->and($block->title_style)->toBe('plain')
        ->and($block->events->pluck('name')->all())->toBe(['Brand New Event']);
});

test('the add-block menu lists the events block type', function () {
    $publication = Publication::factory()->create();
    $issue = Issue::factory()->create(['publication_id' => $publication->id]);

    $this->actingAs($publication->owner)
        ->get(route('publications.issues.show', [$publication, $issue]))
        ->assertOk()
        ->assertSee('Add Block')
        ->assertSee('Events Block')
        ->assertSee(route('publications.issues.blocks.create', [$publication, $issue, 'type' => 'events']), false);
});

test('an unknown block type is rejected', function () {
    $publication = Publication::factory()->create();
    $issue = Issue::factory()->create(['publication_id' => $publication->id]);

    // The create form 404s for an unknown type.
    $this->actingAs($publication->owner)
        ->get(route('publications.issues.blocks.create', [$publication, $issue, 'type' => 'carousel']))
        ->assertNotFound();

    // Storing an unknown type is a validation error.
    $this->actingAs($publication->owner)
        ->post(route('publications.issues.blocks.store', [$publication, $issue]), [
            'type' => 'carousel',
            'title_style' => 'accent',
        ])
        ->assertSessionHasErrors('type');
});

test('an invalid title style is rejected', function () {
    $publication = Publication::factory()->create();
    $issue = Issue::factory()->create(['publication_id' => $publication->id]);

    $this->actingAs($publication->owner)
        ->post(route('publications.issues.blocks.store', [$publication, $issue]), [
            'title_style' => 'rainbow',
        ])
        ->assertSessionHasErrors('title_style');
});

test('deleting a block cascades its events', function () {
    $publication = Publication::factory()->create();
    $issue = Issue::factory()->create(['publication_id' => $publication->id]);
    $block = Block::factory()->create(['publication_id' => $publication->id, 'issue_id' => $issue->id]);
    $block->events()->create(['name' => 'Doomed', 'order' => 1]);

    $this->actingAs($publication->owner)
        ->delete(route('publications.issues.blocks.destroy', [$publication, $issue, $block]))
        ->assertRedirect();

    expect(Block::count())->toBe(0)
        ->and(Event::count())->toBe(0);
});

test('a contributor without manage-stories cannot add a block', function () {
    $publication = Publication::factory()->create();
    $issue = Issue::factory()->create(['publication_id' => $publication->id]);
    $factChecker = User::factory()->create();
    $publication->members()->attach($factChecker->id, ['role' => 'fact_checker']);

    $this->actingAs($factChecker)
        ->post(route('publications.issues.blocks.store', [$publication, $issue]), [
            'title_style' => 'accent',
        ])
        ->assertForbidden();
});

test('blocks and stories interleave in the content order', function () {
    $publication = Publication::factory()->create();
    $issue = Issue::factory()->create(['publication_id' => $publication->id]);
    $story = Story::factory()->create([
        'publication_id' => $publication->id, 'issue_id' => $issue->id, 'order' => 1,
    ]);
    $block = Block::factory()->create([
        'publication_id' => $publication->id, 'issue_id' => $issue->id, 'order' => 2,
    ]);

    // Reorder so the block comes before the story.
    $this->actingAs($publication->owner)
        ->patchJson(route('publications.issues.reorder', [$publication, $issue]), [
            'order' => ["block:{$block->id}", "story:{$story->id}"],
        ])
        ->assertOk();

    $ordered = $issue->orderedContent();
    expect($ordered->first())->toBeInstanceOf(Block::class)
        ->and($ordered->last())->toBeInstanceOf(Story::class);
});

test('the events block renders in the email with the reference layout', function () {
    $publication = Publication::factory()->create([
        'palette' => ['accent' => '#6aa84f', 'accent_text' => '#ffffff', 'body_text' => '#666666'],
    ]);
    $issue = Issue::factory()->create(['publication_id' => $publication->id]);
    $subscriber = Subscriber::factory()->confirmed()->create(['publication_id' => $publication->id]);
    $block = Block::factory()->create([
        'publication_id' => $publication->id,
        'issue_id' => $issue->id,
        'title' => 'Upcoming Events',
        'title_style' => 'accent',
        'order' => 1,
    ]);
    $block->events()->create([
        'name' => 'Old Stores Meetup',
        'date' => '2026-05-17',
        'location' => 'The Old Stores, Caernarfon',
        'order' => 1,
    ]);

    $html = (new IssueNewsletter($issue, $subscriber))->render();

    expect($html)
        // Accent title banner.
        ->toContain('Upcoming Events')
        ->toContain('bgcolor="#6aa84f"')
        // Event card (date, location, name) with a left accent bar.
        ->toContain('17th May')
        ->toContain('The Old Stores, Caernarfon')
        ->toContain('Old Stores Meetup')
        ->toContain('border-left:4px solid #6aa84f');
});

test('the event calendar colour falls back to the accent but can be customised', function () {
    // Unset → follows the main accent.
    $fallback = Publication::factory()->create(['palette' => ['accent' => '#123456']]);
    expect($fallback->paletteColors()['event_accent'])->toBe('#123456');

    // Set → used independently of the accent for the event card + date.
    $publication = Publication::factory()->create([
        'palette' => ['accent' => '#000000', 'event_accent' => '#6aa84f'],
    ]);
    $issue = Issue::factory()->create(['publication_id' => $publication->id]);
    $subscriber = Subscriber::factory()->confirmed()->create(['publication_id' => $publication->id]);
    $block = Block::factory()->create([
        'publication_id' => $publication->id,
        'issue_id' => $issue->id,
        'title_style' => 'none',
        'order' => 1,
    ]);
    $block->events()->create(['name' => 'Ride-out', 'date' => '2026-05-17', 'order' => 1]);

    $html = (new IssueNewsletter($issue, $subscriber))->render();

    expect($publication->paletteColors()['event_accent'])->toBe('#6aa84f')
        ->and($html)->toContain('border-left:4px solid #6aa84f');
});

test('the plain title style renders the title in the accent colour without a banner', function () {
    $publication = Publication::factory()->create([
        'palette' => ['accent' => '#6aa84f'],
    ]);
    $issue = Issue::factory()->create(['publication_id' => $publication->id]);
    $subscriber = Subscriber::factory()->confirmed()->create(['publication_id' => $publication->id]);
    $block = Block::factory()->create([
        'publication_id' => $publication->id,
        'issue_id' => $issue->id,
        'title' => 'Plain Events',
        'title_style' => 'plain',
        'order' => 1,
    ]);
    $block->events()->create(['name' => 'A thing', 'order' => 1]);

    $html = (new IssueNewsletter($issue, $subscriber))->render();

    expect($html)
        ->toContain('Plain Events')
        ->toContain('color:#6aa84f')
        ->not->toContain('bgcolor="#6aa84f"');
});
