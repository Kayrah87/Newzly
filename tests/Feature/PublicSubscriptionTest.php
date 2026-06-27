<?php

use App\Mail\SubscriptionConfirmation;
use App\Models\Publication;
use App\Models\Subscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

function subscribeUrl(Publication $p): string
{
    return route('public.subscribe.store', ['publication' => $p->slug]);
}

test('the public subscribe page renders for a valid slug', function () {
    $publication = Publication::factory()->create();

    $this->get(route('public.subscribe', ['publication' => $publication->slug]))
        ->assertOk()
        ->assertSee($publication->name);
});

test('an unknown slug returns 404', function () {
    $this->get(route('public.subscribe', ['publication' => 'does-not-exist']))
        ->assertNotFound();
});

test('subscribing creates a pending subscriber and sends a confirmation email', function () {
    Mail::fake();
    $publication = Publication::factory()->create();

    $this->post(subscribeUrl($publication), [
        'email' => 'reader@example.com',
        'name' => 'Reader',
        'consent' => '1',
    ])->assertOk()->assertSee('Almost there');

    $subscriber = Subscriber::firstWhere('email', 'reader@example.com');
    expect($subscriber->status)->toBe(Subscriber::STATUS_PENDING)
        ->and($subscriber->consent_source)->toBe('public_form')
        ->and($subscriber->confirmation_token)->not->toBeNull();

    $this->assertDatabaseHas('consent_events', [
        'subscriber_id' => $subscriber->id,
        'event' => 'subscribed',
    ]);

    Mail::assertSent(SubscriptionConfirmation::class, fn ($mail) => $mail->hasTo('reader@example.com'));
});

test('consent is required to subscribe', function () {
    Mail::fake();
    $publication = Publication::factory()->create();

    $this->post(subscribeUrl($publication), [
        'email' => 'reader@example.com',
    ])->assertSessionHasErrors('consent');

    $this->assertDatabaseCount('subscribers', 0);
    Mail::assertNothingSent();
});

test('a filled honeypot is silently dropped', function () {
    Mail::fake();
    $publication = Publication::factory()->create();

    $this->post(subscribeUrl($publication), [
        'email' => 'bot@example.com',
        'consent' => '1',
        'company_website' => 'http://spam.test',
    ])->assertOk()->assertSee('Almost there');

    $this->assertDatabaseCount('subscribers', 0);
    Mail::assertNothingSent();
});

test('a valid token confirms the subscription', function () {
    $publication = Publication::factory()->create();
    $subscriber = Subscriber::factory()->create([
        'publication_id' => $publication->id,
        'status' => Subscriber::STATUS_PENDING,
    ]);

    $this->get(route('public.confirm', ['publication' => $publication->slug, 'token' => $subscriber->confirmation_token]))
        ->assertOk()
        ->assertSee("You're subscribed", false);

    $subscriber->refresh();
    expect($subscriber->status)->toBe(Subscriber::STATUS_CONFIRMED)
        ->and($subscriber->confirmed_at)->not->toBeNull()
        ->and($subscriber->consent_at)->not->toBeNull()
        ->and($subscriber->confirmation_token)->toBeNull();
});

test('an invalid confirm token does not confirm anyone', function () {
    $publication = Publication::factory()->create();
    $subscriber = Subscriber::factory()->create([
        'publication_id' => $publication->id,
        'status' => Subscriber::STATUS_PENDING,
    ]);

    $this->get(route('public.confirm', ['publication' => $publication->slug, 'token' => 'wrong-token']))
        ->assertOk()
        ->assertSee('invalid or has expired');

    expect($subscriber->fresh()->status)->toBe(Subscriber::STATUS_PENDING);
});

test('a subscriber can unsubscribe via their token', function () {
    $publication = Publication::factory()->create();
    $subscriber = Subscriber::factory()->confirmed()->create(['publication_id' => $publication->id]);

    // Landing page shows the confirm button.
    $this->get(route('public.unsubscribe', ['publication' => $publication->slug, 'token' => $subscriber->unsubscribe_token]))
        ->assertOk()
        ->assertSee('unsubscribe', false);

    // Performing it unsubscribes.
    $this->post(route('public.unsubscribe.perform', ['publication' => $publication->slug, 'token' => $subscriber->unsubscribe_token]))
        ->assertOk()
        ->assertSee('been unsubscribed');

    expect($subscriber->fresh()->status)->toBe(Subscriber::STATUS_UNSUBSCRIBED);
    $this->assertDatabaseHas('consent_events', [
        'subscriber_id' => $subscriber->id,
        'event' => 'unsubscribed',
    ]);
});

test('an unsubscribed person can resubscribe', function () {
    Mail::fake();
    $publication = Publication::factory()->create();
    $subscriber = Subscriber::factory()->unsubscribed()->create([
        'publication_id' => $publication->id,
        'email' => 'back@example.com',
    ]);

    $this->post(subscribeUrl($publication), [
        'email' => 'back@example.com',
        'consent' => '1',
    ])->assertOk();

    $subscriber->refresh();
    expect($subscriber->status)->toBe(Subscriber::STATUS_PENDING)
        ->and($subscriber->confirmation_token)->not->toBeNull();
    Mail::assertSent(SubscriptionConfirmation::class);
});

test('subscribing an already-confirmed email does not duplicate or leak', function () {
    Mail::fake();
    $publication = Publication::factory()->create();
    Subscriber::factory()->confirmed()->create([
        'publication_id' => $publication->id,
        'email' => 'member@example.com',
    ]);

    $this->post(subscribeUrl($publication), [
        'email' => 'member@example.com',
        'consent' => '1',
    ])->assertOk()->assertSee('Almost there');

    expect(Subscriber::where('email', 'member@example.com')->count())->toBe(1);
    Mail::assertNothingSent();
});
