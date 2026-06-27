<?php

use App\Models\Issue;
use App\Models\Publication;
use App\Models\Story;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

function submitUrl(Publication $p): string
{
    return route('public.submit.store', ['publication' => $p->slug]);
}

test('the public submission form renders for a valid slug', function () {
    $publication = Publication::factory()->create();

    $this->get(route('public.submit', ['publication' => $publication->slug]))
        ->assertOk()
        ->assertSee('Submit a story');
});

test('a member of the public can submit a story with photos', function () {
    Storage::fake(Publication::mediaDisk());
    $publication = Publication::factory()->create();

    $this->post(submitUrl($publication), [
        'title' => 'Local hero saves cat',
        'content' => 'A heartwarming tale.',
        'submitter_name' => 'Jane Reader',
        'submitter_email' => 'jane@example.com',
        'consent' => '1',
        'images' => [UploadedFile::fake()->image('cat.jpg')],
    ])->assertOk()->assertSee('Thanks for your submission');

    $story = Story::firstWhere('title', 'Local hero saves cat');
    expect($story->source)->toBe(Story::SOURCE_PUBLIC)
        ->and($story->status)->toBe(Story::STATUS_PENDING)
        ->and($story->issue_id)->toBeNull()
        ->and($story->submitter_name)->toBe('Jane Reader')
        ->and($story->images)->toHaveCount(1);
});

test('submission requires consent', function () {
    $publication = Publication::factory()->create();

    $this->post(submitUrl($publication), [
        'title' => 'No consent',
        'content' => 'x',
    ])->assertSessionHasErrors('consent');

    $this->assertDatabaseCount('stories', 0);
});

test('a filled honeypot drops the submission', function () {
    $publication = Publication::factory()->create();

    $this->post(submitUrl($publication), [
        'title' => 'Spam',
        'content' => 'x',
        'consent' => '1',
        'company_website' => 'http://spam.test',
    ])->assertOk();

    $this->assertDatabaseCount('stories', 0);
});

test('an editor can see pending submissions but a stranger cannot', function () {
    $publication = Publication::factory()->create();
    Story::factory()->create([
        'publication_id' => $publication->id,
        'issue_id' => null,
        'source' => Story::SOURCE_PUBLIC,
        'status' => Story::STATUS_PENDING,
    ]);

    $this->actingAs($publication->owner)
        ->get(route('publications.submissions.index', $publication))
        ->assertOk();

    $this->actingAs(User::factory()->create())
        ->get(route('publications.submissions.index', $publication))
        ->assertForbidden();
});

test('approving a submission assigns it to an issue and marks it approved', function () {
    $publication = Publication::factory()->create();
    $issue = Issue::factory()->create(['publication_id' => $publication->id]);
    $submission = Story::factory()->create([
        'publication_id' => $publication->id,
        'issue_id' => null,
        'source' => Story::SOURCE_PUBLIC,
        'status' => Story::STATUS_PENDING,
    ]);

    $this->actingAs($publication->owner)
        ->patch(route('publications.submissions.approve', [$publication, $submission]), ['issue_id' => $issue->id])
        ->assertRedirect(route('publications.submissions.index', $publication));

    $submission->refresh();
    expect($submission->status)->toBe(Story::STATUS_APPROVED)
        ->and($submission->issue_id)->toBe($issue->id);
});

test('a submission cannot be approved into another publication issue', function () {
    $publication = Publication::factory()->create();
    $otherIssue = Issue::factory()->create(); // belongs to a different publication
    $submission = Story::factory()->create([
        'publication_id' => $publication->id,
        'issue_id' => null,
        'source' => Story::SOURCE_PUBLIC,
        'status' => Story::STATUS_PENDING,
    ]);

    $this->actingAs($publication->owner)
        ->patch(route('publications.submissions.approve', [$publication, $submission]), ['issue_id' => $otherIssue->id])
        ->assertNotFound();

    expect($submission->fresh()->status)->toBe(Story::STATUS_PENDING);
});

test('rejecting a submission marks it rejected', function () {
    $publication = Publication::factory()->create();
    $submission = Story::factory()->create([
        'publication_id' => $publication->id,
        'issue_id' => null,
        'source' => Story::SOURCE_PUBLIC,
        'status' => Story::STATUS_PENDING,
    ]);

    $this->actingAs($publication->owner)
        ->patch(route('publications.submissions.reject', [$publication, $submission]))
        ->assertRedirect();

    expect($submission->fresh()->status)->toBe(Story::STATUS_REJECTED);
});

test('submissions are scoped to their publication', function () {
    $owner = User::factory()->create();
    $publicationA = Publication::factory()->create(['owner_id' => $owner->id]);
    $publicationB = Publication::factory()->create(['owner_id' => $owner->id]);
    $submission = Story::factory()->create([
        'publication_id' => $publicationA->id,
        'issue_id' => null,
        'source' => Story::SOURCE_PUBLIC,
        'status' => Story::STATUS_PENDING,
    ]);

    $this->actingAs($owner)
        ->patch(route('publications.submissions.reject', [$publicationB, $submission]))
        ->assertNotFound();
});
