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

/**
 * Renders the issue newsletter and returns the HTML, for structural assertions.
 */
function renderIssueHtml(Issue $issue): string
{
    $subscriber = Subscriber::factory()->confirmed()->create([
        'publication_id' => $issue->publication_id,
    ]);

    return (new IssueNewsletter($issue, $subscriber))->render();
}

test('an article with a photo uses an email-safe hero image', function () {
    Storage::fake(Publication::mediaDisk());
    $publication = Publication::factory()->create(['logo_path' => null]);
    $issue = Issue::factory()->create(['publication_id' => $publication->id]);

    $story = Story::factory()->create([
        'publication_id' => $publication->id,
        'issue_id' => $issue->id,
        'title' => 'Photo Story',
        'content' => '<p>Body.</p>',
        'layout' => 'picture',
        'status' => 'approved',
    ]);
    $story->images()->create([
        'publication_id' => $publication->id,
        'path' => UploadedFile::fake()->image('hero.jpg')->store('t', Publication::mediaDisk()),
        'caption' => 'A caption',
    ]);

    $html = renderIssueHtml($issue->fresh());

    // Hero image present with the attributes email clients (incl. Outlook) need.
    // The picture layout uses a full-bleed (600px) hero above the body block.
    expect($html)
        ->toContain('<img')
        ->toContain('width="600"')
        ->toContain('display:block')
        ->toContain('height:auto')
        ->toContain('alt="A caption"')
        ->toContain('Photo Story');
});

test('an article without a photo renders no image and stays readable', function () {
    $publication = Publication::factory()->create(['logo_path' => null]);
    $issue = Issue::factory()->create(['publication_id' => $publication->id]);

    Story::factory()->create([
        'publication_id' => $publication->id,
        'issue_id' => $issue->id,
        'title' => 'Plain Standard Story',
        'content' => '<p>Just words, no picture.</p>',
        'layout' => 'standard',
        'status' => 'approved',
        'order' => 1,
    ]);
    Story::factory()->create([
        'publication_id' => $publication->id,
        'issue_id' => $issue->id,
        'title' => 'A Headline',
        'layout' => 'title_only',
        'status' => 'approved',
        'order' => 2,
    ]);

    $html = renderIssueHtml($issue->fresh());

    expect($html)
        ->toContain('Plain Standard Story')
        ->toContain('A Headline')
        ->toContain('Just words, no picture.')
        // No publication logo and no story photos => no <img> at all.
        ->not->toContain('<img');
});

test('the picture layout falls back gracefully when no image is attached', function () {
    $publication = Publication::factory()->create(['logo_path' => null]);
    $issue = Issue::factory()->create(['publication_id' => $publication->id]);

    Story::factory()->create([
        'publication_id' => $publication->id,
        'issue_id' => $issue->id,
        'title' => 'Picture Without Photo',
        'content' => '<p>Still readable.</p>',
        'layout' => 'picture',
        'status' => 'approved',
    ]);

    $html = renderIssueHtml($issue->fresh());

    expect($html)
        ->toContain('Picture Without Photo')
        ->toContain('Still readable.')
        ->not->toContain('<img');
});

test('the email markup follows cross-client (email-safe) conventions', function () {
    $publication = Publication::factory()->create(['logo_path' => null]);
    $issue = Issue::factory()->create(['publication_id' => $publication->id]);
    Story::factory()->create([
        'publication_id' => $publication->id,
        'issue_id' => $issue->id,
        'layout' => 'standard',
        'status' => 'approved',
    ]);

    $html = renderIssueHtml($issue->fresh());

    expect($html)
        ->toContain('<!DOCTYPE html>')
        // Table-based layout, not div/flex/grid.
        ->toContain('role="presentation"')
        ->not->toContain('display:flex')
        ->not->toContain('display:grid')
        // Inline styles (survive clients that strip <head>/<style>).
        ->toContain('style="')
        // Outlook-specific hardening.
        ->toContain('<!--[if mso]>')
        ->toContain('mso-line-height-rule:exactly')
        // Outlook (Word engine) hardening that can't be checked by rendering on
        // Linux: DPI fix, width attribute Outlook honours, bgcolor fallback.
        ->toContain('PixelsPerInch')
        ->toContain('width="600"')
        ->toContain('bgcolor=')
        // Web-safe font fallback (no reliance on a single web font).
        ->toContain('Arial')
        // Mobile responsiveness + WYSIWYG content normalisation.
        ->toContain('@media only screen and (max-width:600px)')
        ->toContain('.story-content')
        // No client-stripped / spam-flagging constructs.
        ->not->toContain('<script')
        ->not->toContain('stylesheet')
        ->not->toContain('position:absolute')
        ->not->toContain('float:')
        // GDPR unsubscribe link is always present.
        ->toContain('Unsubscribe');
});
