<?php

use App\Mail\IssueNewsletter;
use App\Models\Issue;
use App\Models\Publication;
use App\Models\Story;
use App\Models\Subscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('issue metadata is persisted', function () {
    $publication = Publication::factory()->create();

    $this->actingAs($publication->owner)->post(route('publications.issues.store', $publication), [
        'title' => 'Spring Issue',
        'issue_number' => 7,
        'coverage_label' => 'Spring/Summer',
        'release_date' => '2026-04-01',
        'status' => 'draft',
    ])->assertRedirect();

    $issue = Issue::firstWhere('title', 'Spring Issue');
    expect($issue->issue_number)->toBe(7)
        ->and($issue->coverage_label)->toBe('Spring/Summer')
        ->and($issue->release_date->format('Y-m-d'))->toBe('2026-04-01');
});

test('a story can be created with a layout and images', function () {
    Storage::fake(Publication::mediaDisk());
    $publication = Publication::factory()->create();
    $issue = Issue::factory()->create(['publication_id' => $publication->id]);

    $this->actingAs($publication->owner)->post(route('publications.issues.stories.store', [$publication, $issue]), [
        'title' => 'Big Photo Story',
        'content' => '<p>Words.</p>',
        'layout' => 'picture',
        'images' => [UploadedFile::fake()->image('hero.jpg')],
    ])->assertRedirect();

    $story = Story::firstWhere('title', 'Big Photo Story');
    expect($story->layout)->toBe('picture')
        ->and($story->source)->toBe(Story::SOURCE_ADMIN)
        ->and($story->status)->toBe(Story::STATUS_APPROVED)
        ->and($story->images)->toHaveCount(1);

    Storage::disk(Publication::mediaDisk())->assertExists($story->heroImage()->path);
});

test('an image can be removed when editing a story', function () {
    Storage::fake(Publication::mediaDisk());
    $publication = Publication::factory()->create();
    $issue = Issue::factory()->create(['publication_id' => $publication->id]);
    $story = Story::factory()->create(['publication_id' => $publication->id, 'issue_id' => $issue->id]);
    $image = $story->images()->create([
        'publication_id' => $publication->id,
        'path' => UploadedFile::fake()->image('x.jpg')->store('t', Publication::mediaDisk()),
    ]);

    $this->actingAs($publication->owner)->patch(route('publications.issues.stories.update', [$publication, $issue, $story]), [
        'title' => $story->title,
        'content' => $story->content,
        'layout' => 'standard',
        'status' => 'approved',
        'remove_images' => [$image->id],
    ])->assertRedirect();

    expect($story->fresh()->images)->toHaveCount(0);
    Storage::disk(Publication::mediaDisk())->assertMissing($image->path);
});

test('the clear layout renders the title in the accent colour with no filled banner', function () {
    $publication = Publication::factory()->create([
        'logo_path' => null,
        'palette' => ['accent' => '#6aa84f', 'body_bg' => '#efefef'],
    ]);
    $issue = Issue::factory()->create(['publication_id' => $publication->id]);
    Story::factory()->create([
        'publication_id' => $publication->id,
        'issue_id' => $issue->id,
        'title' => 'Clear Headline',
        'content' => '<p>Body copy.</p>',
        'layout' => 'standard_clear',
        'status' => 'approved',
    ]);

    $html = renderIssueHtml($issue->fresh());

    expect($html)
        ->toContain('Clear Headline')
        // Title text is the accent colour…
        ->toContain('color:#6aa84f')
        // …and it sits on the article background, not a filled accent banner.
        ->toContain('background:#efefef')
        ->not->toContain('bgcolor="#6aa84f"');
});

test('the email renders each layout and only approved stories', function () {
    $publication = Publication::factory()->create();
    $issue = Issue::factory()->create(['publication_id' => $publication->id]);
    $subscriber = Subscriber::factory()->confirmed()->create(['publication_id' => $publication->id]);

    Story::factory()->create([
        'publication_id' => $publication->id, 'issue_id' => $issue->id,
        'title' => 'Approved Standard', 'layout' => 'standard', 'status' => 'approved', 'order' => 1,
    ]);
    Story::factory()->create([
        'publication_id' => $publication->id, 'issue_id' => $issue->id,
        'title' => 'Title Only Headline', 'layout' => 'title_only', 'status' => 'approved', 'order' => 2,
    ]);
    Story::factory()->create([
        'publication_id' => $publication->id, 'issue_id' => $issue->id,
        'title' => 'Pending Draft', 'layout' => 'standard', 'status' => 'pending', 'order' => 3,
    ]);

    $mailable = new IssueNewsletter($issue, $subscriber);

    $mailable->assertSeeInHtml('Approved Standard');
    $mailable->assertSeeInHtml('Title Only Headline');
    $mailable->assertDontSeeInHtml('Pending Draft');
});
