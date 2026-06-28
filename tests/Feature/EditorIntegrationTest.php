<?php

use App\Models\Issue;
use App\Models\Publication;
use App\Models\Story;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * The WYSIWYG editor is the self-hosted Tiptap component (mounted via the
 * `wysiwyg` Alpine data) rather than the old TinyMCE CDN script. These tests
 * lock in that the content forms render the new editor and never the CDN.
 */
test('content forms render the Tiptap editor and not TinyMCE', function () {
    $publication = Publication::factory()->create();
    $issue = Issue::factory()->create(['publication_id' => $publication->id]);
    $story = Story::factory()->create([
        'publication_id' => $publication->id,
        'issue_id' => $issue->id,
    ]);

    $urls = [
        route('publications.issues.create', $publication),
        route('publications.issues.edit', [$publication, $issue]),
        route('publications.issues.stories.create', [$publication, $issue]),
        route('publications.issues.stories.edit', [$publication, $issue, $story]),
    ];

    foreach ($urls as $url) {
        $this->actingAs($publication->owner)->get($url)
            ->assertOk()
            ->assertSee('x-data="wysiwyg"', false)
            ->assertSee('name="content"', false)
            ->assertDontSee('tinymce', false)
            ->assertDontSee('tiny.cloud', false);
    }
});

test('editing a story shows existing HTML content inside the editor field', function () {
    $publication = Publication::factory()->create();
    $issue = Issue::factory()->create(['publication_id' => $publication->id]);
    $story = Story::factory()->create([
        'publication_id' => $publication->id,
        'issue_id' => $issue->id,
        'content' => '<p>Existing <strong>body</strong>.</p>',
    ]);

    $this->actingAs($publication->owner)
        ->get(route('publications.issues.stories.edit', [$publication, $issue, $story]))
        ->assertOk()
        ->assertSee('Existing', false)
        ->assertSee('body', false);
});

test('content still persists as HTML when submitted through the form', function () {
    $publication = Publication::factory()->create();
    $issue = Issue::factory()->create(['publication_id' => $publication->id]);

    $this->actingAs($publication->owner)->post(
        route('publications.issues.stories.store', [$publication, $issue]),
        [
            'title' => 'Editor Story',
            'content' => '<p>Hello <em>world</em>.</p>',
            'layout' => 'standard',
        ]
    )->assertRedirect();

    expect(Story::firstWhere('title', 'Editor Story')->content)
        ->toBe('<p>Hello <em>world</em>.</p>');
});
