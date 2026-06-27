<?php

use App\Models\Publication;
use App\Models\Subscriber;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('creating a subscriber auto-generates tokens', function () {
    $subscriber = Subscriber::factory()->create(['status' => Subscriber::STATUS_PENDING]);

    expect($subscriber->unsubscribe_token)->not->toBeNull()
        ->and($subscriber->confirmation_token)->not->toBeNull();
});

test('a confirmed subscriber has no confirmation token', function () {
    $subscriber = Subscriber::factory()->confirmed()->create();

    expect($subscriber->unsubscribe_token)->not->toBeNull()
        ->and($subscriber->confirmation_token)->toBeNull()
        ->and($subscriber->isMailable())->toBeTrue();
});

test('owner can manually add a subscriber with consent attestation', function () {
    $publication = Publication::factory()->create();

    $this->actingAs($publication->owner)->post(route('publications.subscribers.store', $publication), [
        'email' => 'reader@example.com',
        'name' => 'Reader',
        'consent' => '1',
    ])->assertRedirect(route('publications.subscribers.index', $publication));

    $subscriber = Subscriber::firstWhere('email', 'reader@example.com');

    expect($subscriber->status)->toBe(Subscriber::STATUS_CONFIRMED)
        ->and($subscriber->consent_source)->toBe('manual')
        ->and($subscriber->consent_at)->not->toBeNull();

    $this->assertDatabaseHas('consent_events', [
        'subscriber_id' => $subscriber->id,
        'publication_id' => $publication->id,
        'event' => 'subscribed',
    ]);
});

test('manual add requires the consent attestation', function () {
    $publication = Publication::factory()->create();

    $this->actingAs($publication->owner)
        ->post(route('publications.subscribers.store', $publication), [
            'email' => 'reader@example.com',
        ])
        ->assertSessionHasErrors('consent');

    $this->assertDatabaseCount('subscribers', 0);
});

test('the same email cannot be added twice to one publication but can to another', function () {
    $publicationA = Publication::factory()->create();
    $publicationB = Publication::factory()->create();
    Subscriber::factory()->create(['publication_id' => $publicationA->id, 'email' => 'dup@example.com']);

    // Duplicate in the same publication is rejected.
    $this->actingAs($publicationA->owner)
        ->post(route('publications.subscribers.store', $publicationA), [
            'email' => 'dup@example.com',
            'consent' => '1',
        ])
        ->assertSessionHasErrors('email');

    // Same email in a different publication is fine.
    $this->actingAs($publicationB->owner)
        ->post(route('publications.subscribers.store', $publicationB), [
            'email' => 'dup@example.com',
            'consent' => '1',
        ])
        ->assertSessionHasNoErrors();

    expect(Subscriber::where('email', 'dup@example.com')->count())->toBe(2);
});

test('unsubscribing marks the subscriber and logs an event', function () {
    $subscriber = Subscriber::factory()->confirmed()->create();
    $publication = $subscriber->publication;

    $this->actingAs($publication->owner)
        ->patch(route('publications.subscribers.unsubscribe', [$publication, $subscriber]));

    expect($subscriber->fresh()->status)->toBe(Subscriber::STATUS_UNSUBSCRIBED);
    $this->assertDatabaseHas('consent_events', [
        'subscriber_id' => $subscriber->id,
        'event' => 'unsubscribed',
    ]);
});

test('deleting a subscriber erases them and their audit trail', function () {
    $subscriber = Subscriber::factory()->confirmed()->create();
    $subscriber->recordEvent('subscribed');
    $publication = $subscriber->publication;

    $this->actingAs($publication->owner)
        ->delete(route('publications.subscribers.destroy', [$publication, $subscriber]));

    $this->assertDatabaseMissing('subscribers', ['id' => $subscriber->id]);
    $this->assertDatabaseMissing('consent_events', ['subscriber_id' => $subscriber->id]);
});

test('a stranger cannot view or manage the mailing list', function () {
    $publication = Publication::factory()->create();
    $stranger = User::factory()->create();

    $this->actingAs($stranger)
        ->get(route('publications.subscribers.index', $publication))
        ->assertForbidden();
});

test('subscribers are scoped to their publication in the URL', function () {
    $owner = User::factory()->create();
    $publicationA = Publication::factory()->create(['owner_id' => $owner->id]);
    $publicationB = Publication::factory()->create(['owner_id' => $owner->id]);
    $subscriber = Subscriber::factory()->confirmed()->create(['publication_id' => $publicationA->id]);

    // Wrong parent publication 404s via scoped binding.
    $this->actingAs($owner)
        ->delete(route('publications.subscribers.destroy', [$publicationB, $subscriber]))
        ->assertNotFound();

    $this->assertDatabaseHas('subscribers', ['id' => $subscriber->id]);
});
