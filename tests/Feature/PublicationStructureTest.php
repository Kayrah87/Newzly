<?php

use App\Mail\IssueNewsletter;
use App\Models\Issue;
use App\Models\Publication;
use App\Models\Story;
use App\Models\Subscriber;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('the structure & theme editor is viewable by the owner', function () {
    $publication = Publication::factory()->create();

    $this->actingAs($publication->owner)
        ->get(route('publications.structure.edit', $publication))
        ->assertOk()
        ->assertSee('Layout & Theme')
        ->assertSee('Section order')
        // The order posts as a real array (structure[]), not a JSON string,
        // so it satisfies the `array` validation rule.
        ->assertSee('name="structure[]"', false);
});

test('updating the structure persists the section order and palette', function () {
    $publication = Publication::factory()->create();

    $this->actingAs($publication->owner)
        ->put(route('publications.structure.update', $publication), [
            'structure' => ['footer', 'content', 'header'],
            'palette' => [
                'header_bg' => '#000000',
                'accent' => '#ABCDEF',
            ],
        ])
        ->assertRedirect(route('publications.structure.edit', $publication))
        ->assertSessionHas('success');

    $publication->refresh();

    expect($publication->structureOrder())->toBe(['footer', 'content', 'header'])
        ->and($publication->paletteColors()['header_bg'])->toBe('#000000')
        ->and($publication->paletteColors()['accent'])->toBe('#ABCDEF')
        // Unset colours fall back to their defaults.
        ->and($publication->paletteColors()['footer_text'])->toBe(Publication::PALETTE_FIELDS['footer_text']['default']);
});

test('the section order is normalised so every section is always present exactly once', function () {
    $publication = Publication::factory()->create();

    $this->actingAs($publication->owner)
        ->put(route('publications.structure.update', $publication), [
            // Duplicate + missing sections.
            'structure' => ['footer', 'footer', 'content'],
        ])
        ->assertRedirect();

    expect($publication->refresh()->structureOrder())->toBe(['footer', 'content', 'header']);
});

test('an invalid palette colour is rejected', function () {
    $publication = Publication::factory()->create();

    $this->actingAs($publication->owner)
        ->put(route('publications.structure.update', $publication), [
            'structure' => ['header', 'content', 'footer'],
            'palette' => ['header_bg' => 'red'],
        ])
        ->assertSessionHasErrors('palette.header_bg');
});

test('an unknown section value is rejected', function () {
    $publication = Publication::factory()->create();

    $this->actingAs($publication->owner)
        ->put(route('publications.structure.update', $publication), [
            'structure' => ['header', 'bogus'],
        ])
        ->assertSessionHasErrors('structure.1');
});

test('a non-member cannot view or update the structure', function () {
    $publication = Publication::factory()->create();
    $outsider = User::factory()->create();

    $this->actingAs($outsider)
        ->get(route('publications.structure.edit', $publication))
        ->assertForbidden();

    $this->actingAs($outsider)
        ->put(route('publications.structure.update', $publication), [
            'structure' => ['header', 'content', 'footer'],
        ])
        ->assertForbidden();
});

test('the rendered issue email follows the publication structure order', function () {
    $publication = Publication::factory()->create([
        'structure' => ['footer', 'content', 'header'],
    ]);
    $issue = Issue::factory()->create(['publication_id' => $publication->id]);
    $subscriber = Subscriber::factory()->confirmed()->create(['publication_id' => $publication->id]);
    Story::factory()->create([
        'publication_id' => $publication->id,
        'issue_id' => $issue->id,
        'title' => 'Reorderable Lead',
        'status' => 'approved',
    ]);

    $html = (new IssueNewsletter($issue, $subscriber))->render();

    $footerPos = strpos($html, "You're receiving this because");
    $storyPos = strpos($html, 'Reorderable Lead');

    // Footer is configured first, so it appears before the story content.
    expect($footerPos)->toBeLessThan($storyPos);
});

test('the rendered email is themed by the publication palette', function () {
    $publication = Publication::factory()->create([
        'palette' => [
            'header_bg' => '#000000',
            'subbar_bg' => '#333333',
            'accent' => '#6aa84f',
            'accent_text' => '#ffffff',
            'body_bg' => '#efefef',
            'body_text' => '#666666',
            'footer_bg' => '#000000',
        ],
    ]);
    $issue = Issue::factory()->create([
        'publication_id' => $publication->id,
        'issue_number' => 1,
    ]);
    $subscriber = Subscriber::factory()->confirmed()->create(['publication_id' => $publication->id]);
    Story::factory()->create([
        'publication_id' => $publication->id,
        'issue_id' => $issue->id,
        'title' => 'Banner Title',
        'layout' => 'standard',
        'status' => 'approved',
    ]);

    $html = (new IssueNewsletter($issue, $subscriber))->render();

    expect($html)
        // The story title sits in an accent-coloured banner.
        ->toContain('bgcolor="#6aa84f"')
        ->toContain('Banner Title')
        // Article body block + text use the configured colours.
        ->toContain('#efefef')
        ->toContain('#666666')
        // Header/footer use the configured dark background.
        ->toContain('bgcolor="#000000"')
        // The issue bar has its own configurable colour.
        ->toContain('bgcolor="#333333"');
});

test('the issue preview renders with the layout and includes draft stories', function () {
    $publication = Publication::factory()->create();
    $issue = Issue::factory()->create(['publication_id' => $publication->id]);
    Story::factory()->create([
        'publication_id' => $publication->id,
        'issue_id' => $issue->id,
        'title' => 'Draft In Preview',
        'status' => 'pending',
    ]);

    $this->actingAs($publication->owner)
        ->get(route('publications.issues.preview', [$publication, $issue]))
        ->assertOk()
        ->assertSee('Draft In Preview')
        ->assertSee($publication->name);
});
