<?php

use App\Jobs\SendIssue;
use App\Mail\IssueNewsletter;
use App\Models\Issue;
use App\Models\Publication;
use App\Models\Story;
use App\Models\Subscriber;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

function publicationWithIssue(): array
{
    $publication = Publication::factory()->create();
    $issue = Issue::factory()->create(['publication_id' => $publication->id, 'status' => 'draft']);
    Story::factory()->create([
        'publication_id' => $publication->id,
        'issue_id' => $issue->id,
        'title' => 'Headline',
        'content' => '<p>Body copy.</p>',
    ]);

    return [$publication, $issue];
}

test('sending an issue delivers only to confirmed subscribers', function () {
    Mail::fake();
    [$publication, $issue] = publicationWithIssue();

    $confirmed = Subscriber::factory()->confirmed()->count(2)->create(['publication_id' => $publication->id]);
    Subscriber::factory()->create(['publication_id' => $publication->id, 'status' => Subscriber::STATUS_PENDING]);
    Subscriber::factory()->unsubscribed()->create(['publication_id' => $publication->id]);

    $this->actingAs($publication->owner)
        ->post(route('publications.issues.send', [$publication, $issue]))
        ->assertRedirect(route('publications.issues.show', [$publication, $issue]));

    Mail::assertSent(IssueNewsletter::class, 2);
    foreach ($confirmed as $sub) {
        Mail::assertSent(IssueNewsletter::class, fn ($m) => $m->hasTo($sub->email));
    }

    expect($issue->fresh()->isSent())->toBeTrue()
        ->and($issue->deliveries()->count())->toBe(2);
});

test('the issue email contains the personalised unsubscribe link', function () {
    [$publication, $issue] = publicationWithIssue();
    $subscriber = Subscriber::factory()->confirmed()->create(['publication_id' => $publication->id]);

    $mailable = new IssueNewsletter($issue, $subscriber);

    $url = route('public.unsubscribe', [
        'publication' => $publication->slug,
        'token' => $subscriber->unsubscribe_token,
    ]);

    $mailable->assertSeeInHtml($url, false);
});

test('an issue is not sent twice', function () {
    Mail::fake();
    [$publication, $issue] = publicationWithIssue();
    Subscriber::factory()->confirmed()->create(['publication_id' => $publication->id]);

    $this->actingAs($publication->owner)->post(route('publications.issues.send', [$publication, $issue]));
    Mail::assertSent(IssueNewsletter::class, 1);

    // Second attempt is blocked because the issue is already sent.
    $this->actingAs($publication->owner)
        ->post(route('publications.issues.send', [$publication, $issue]))
        ->assertSessionHas('error');

    Mail::assertSent(IssueNewsletter::class, 1);
    expect($issue->fresh()->deliveries()->count())->toBe(1);
});

test('the send job itself does not double-deliver to a subscriber', function () {
    Mail::fake();
    [$publication, $issue] = publicationWithIssue();
    Subscriber::factory()->confirmed()->create(['publication_id' => $publication->id]);

    SendIssue::dispatchSync($issue);
    SendIssue::dispatchSync($issue->fresh());

    Mail::assertSent(IssueNewsletter::class, 1);
    expect($issue->deliveries()->count())->toBe(1);
});

test('a stranger cannot send an issue', function () {
    Mail::fake();
    [$publication, $issue] = publicationWithIssue();
    $stranger = User::factory()->create();

    $this->actingAs($stranger)
        ->post(route('publications.issues.send', [$publication, $issue]))
        ->assertForbidden();

    Mail::assertNothingSent();
    expect($issue->fresh()->isSent())->toBeFalse();
});

test('smtp credentials are stored encrypted and drive the mailer name', function () {
    $publication = Publication::factory()->create();

    $this->actingAs($publication->owner)->patch(route('publications.update', $publication), [
        'name' => $publication->name,
        'smtp_host' => 'smtp.example.com',
        'smtp_port' => 587,
        'smtp_username' => 'mailer@example.com',
        'smtp_password' => 'super-secret',
        'smtp_encryption' => 'tls',
    ])->assertRedirect();

    $publication->refresh();

    expect($publication->hasSmtpConfigured())->toBeTrue()
        ->and($publication->smtp_password)->toBe('super-secret')
        ->and($publication->configuredMailerName())->toBe('publication_'.$publication->id);

    // Raw stored value is ciphertext, not the plaintext password.
    $raw = DB::table('publications')->where('id', $publication->id)->value('smtp_password');
    expect($raw)->not->toBe('super-secret');

    // The dynamic mailer config was registered.
    expect(config('mail.mailers.publication_'.$publication->id.'.host'))->toBe('smtp.example.com');
});

test('a publication without smtp falls back to the default mailer', function () {
    $publication = Publication::factory()->create();

    expect($publication->hasSmtpConfigured())->toBeFalse()
        ->and($publication->configuredMailerName())->toBe(config('mail.default'));
});

test('the scheduled command dispatches due issues only', function () {
    Mail::fake();
    $publication = Publication::factory()->create();
    Subscriber::factory()->confirmed()->create(['publication_id' => $publication->id]);

    $due = Issue::factory()->create([
        'publication_id' => $publication->id,
        'status' => 'scheduled',
        'published_at' => now()->subMinute(),
    ]);
    $future = Issue::factory()->create([
        'publication_id' => $publication->id,
        'status' => 'scheduled',
        'published_at' => now()->addDay(),
    ]);

    $this->artisan('issues:send-scheduled')->assertSuccessful();

    expect($due->fresh()->isSent())->toBeTrue()
        ->and($future->fresh()->status)->toBe('scheduled');
});
