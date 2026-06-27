<?php

use App\Mail\TeamInvitation;
use App\Models\Invitation;
use App\Models\Issue;
use App\Models\Publication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

test('an owner can invite a team member', function () {
    Mail::fake();
    $publication = Publication::factory()->create();

    $this->actingAs($publication->owner)->post(route('publications.invitations.store', $publication), [
        'email' => 'editor@example.com',
        'role' => 'editor',
    ])->assertRedirect(route('publications.editors', $publication));

    $this->assertDatabaseHas('invitations', [
        'publication_id' => $publication->id,
        'email' => 'editor@example.com',
        'role' => 'editor',
    ]);
    Mail::assertSent(TeamInvitation::class, fn ($m) => $m->hasTo('editor@example.com'));
});

test('a non-owner cannot invite', function () {
    $publication = Publication::factory()->create();
    $editor = User::factory()->create();
    $publication->members()->attach($editor->id, ['role' => 'editor']);

    $this->actingAs($editor)
        ->post(route('publications.invitations.store', $publication), ['email' => 'x@example.com', 'role' => 'editor'])
        ->assertForbidden();
});

test('the invited person accepts and joins with the given role', function () {
    $publication = Publication::factory()->create();
    $invitation = Invitation::factory()->create([
        'publication_id' => $publication->id,
        'email' => 'newbie@example.com',
        'role' => 'contributor',
    ]);
    $user = User::factory()->create(['email' => 'newbie@example.com']);

    $this->actingAs($user)
        ->post(route('invitations.accept', $invitation->token))
        ->assertRedirect(route('publications.show', $publication));

    expect($invitation->fresh()->isAccepted())->toBeTrue();
    $this->assertDatabaseHas('publication_users', [
        'publication_id' => $publication->id,
        'user_id' => $user->id,
        'role' => 'contributor',
    ]);
});

test('an invitation cannot be accepted by a different email', function () {
    $publication = Publication::factory()->create();
    $invitation = Invitation::factory()->create([
        'publication_id' => $publication->id,
        'email' => 'intended@example.com',
    ]);
    $other = User::factory()->create(['email' => 'someone-else@example.com']);

    $this->actingAs($other)
        ->post(route('invitations.accept', $invitation->token))
        ->assertRedirect(route('invitations.show', $invitation->token));

    $this->assertDatabaseMissing('publication_users', [
        'publication_id' => $publication->id,
        'user_id' => $other->id,
    ]);
});

test('an expired invitation cannot be accepted', function () {
    $publication = Publication::factory()->create();
    $invitation = Invitation::factory()->expired()->create([
        'publication_id' => $publication->id,
        'email' => 'late@example.com',
    ]);
    $user = User::factory()->create(['email' => 'late@example.com']);

    $this->actingAs($user)->post(route('invitations.accept', $invitation->token))->assertOk(); // invalid view

    $this->assertDatabaseMissing('publication_users', [
        'publication_id' => $publication->id,
        'user_id' => $user->id,
    ]);
});

test('an owner can revoke an invitation and remove a member', function () {
    $publication = Publication::factory()->create();
    $invitation = Invitation::factory()->create(['publication_id' => $publication->id]);
    $member = User::factory()->create();
    $publication->members()->attach($member->id, ['role' => 'editor']);

    $this->actingAs($publication->owner)
        ->delete(route('publications.invitations.destroy', [$publication, $invitation]));
    $this->assertDatabaseMissing('invitations', ['id' => $invitation->id]);

    $this->actingAs($publication->owner)
        ->delete(route('publications.members.destroy', [$publication, $member]));
    $this->assertDatabaseMissing('publication_users', ['publication_id' => $publication->id, 'user_id' => $member->id]);
});

test('the owner cannot be removed from the team', function () {
    $publication = Publication::factory()->create();
    $publication->members()->attach($publication->owner_id, ['role' => 'owner']);

    $this->actingAs($publication->owner)
        ->delete(route('publications.members.destroy', [$publication, $publication->owner]))
        ->assertSessionHasErrors('member');

    $this->assertDatabaseHas('publication_users', [
        'publication_id' => $publication->id,
        'user_id' => $publication->owner_id,
    ]);
});

/**
 * Role-based abilities.
 */
function memberWithRole(Publication $publication, string $role): User
{
    $user = User::factory()->create();
    $publication->members()->attach($user->id, ['role' => $role]);

    return $user;
}

test('a contributor can manage stories but not issues or subscribers', function () {
    $publication = Publication::factory()->create();
    $issue = Issue::factory()->create(['publication_id' => $publication->id]);
    $contributor = memberWithRole($publication, 'contributor');

    $this->actingAs($contributor)->get(route('publications.issues.stories.create', [$publication, $issue]))->assertOk();
    $this->actingAs($contributor)->get(route('publications.issues.edit', [$publication, $issue]))->assertForbidden();
    $this->actingAs($contributor)->get(route('publications.subscribers.index', $publication))->assertForbidden();
});

test('a fact checker can moderate submissions but not write stories or manage subscribers', function () {
    $publication = Publication::factory()->create();
    $issue = Issue::factory()->create(['publication_id' => $publication->id]);
    $factChecker = memberWithRole($publication, 'fact_checker');

    $this->actingAs($factChecker)->get(route('publications.submissions.index', $publication))->assertOk();
    $this->actingAs($factChecker)->get(route('publications.issues.stories.create', [$publication, $issue]))->assertForbidden();
    $this->actingAs($factChecker)->get(route('publications.subscribers.index', $publication))->assertForbidden();
});

test('an editor can manage issues, stories and subscribers', function () {
    $publication = Publication::factory()->create();
    $issue = Issue::factory()->create(['publication_id' => $publication->id]);
    $editor = memberWithRole($publication, 'editor');

    $this->actingAs($editor)->get(route('publications.issues.edit', [$publication, $issue]))->assertOk();
    $this->actingAs($editor)->get(route('publications.issues.stories.create', [$publication, $issue]))->assertOk();
    $this->actingAs($editor)->get(route('publications.subscribers.index', $publication))->assertOk();
});
