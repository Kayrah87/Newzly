<?php

use App\Models\Issue;
use App\Models\Publication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('a user can create a publication and becomes its owner', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('publications.store'), [
        'name' => 'Farming Monthly',
        'description' => 'All things agriculture.',
    ]);

    $publication = Publication::firstWhere('name', 'Farming Monthly');

    expect($publication)->not->toBeNull()
        ->and($publication->slug)->toBe('farming-monthly')
        ->and($publication->owner_id)->toBe($user->id);

    $this->assertDatabaseHas('publication_users', [
        'publication_id' => $publication->id,
        'user_id' => $user->id,
        'role' => 'owner',
    ]);

    $response->assertRedirect(route('publications.show', $publication));
});

test('slugs are made unique', function () {
    $a = Publication::factory()->create(['name' => 'Spring Times', 'slug' => 'spring-times']);
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('publications.store'), ['name' => 'Spring Times']);

    expect(Publication::where('name', 'Spring Times')->pluck('slug')->all())
        ->toContain('spring-times', 'spring-times-2');
});

test('the owner can view their publication but others cannot', function () {
    $publication = Publication::factory()->create();
    $stranger = User::factory()->create();

    $this->actingAs($publication->owner)->get(route('publications.show', $publication))->assertOk();
    $this->actingAs($stranger)->get(route('publications.show', $publication))->assertForbidden();
});

test('a stranger cannot update a publication', function () {
    $publication = Publication::factory()->create();
    $stranger = User::factory()->create();

    $this->actingAs($stranger)
        ->put(route('publications.update', $publication), ['name' => 'Hijacked'])
        ->assertForbidden();

    $this->assertDatabaseMissing('publications', ['name' => 'Hijacked']);
});

test('an issue cannot be viewed under a publication it does not belong to', function () {
    $owner = User::factory()->create();
    $publicationA = Publication::factory()->create(['owner_id' => $owner->id]);
    $publicationB = Publication::factory()->create(['owner_id' => $owner->id]);
    $issue = Issue::factory()->create(['publication_id' => $publicationA->id]);

    // Correct parent resolves.
    $this->actingAs($owner)
        ->get(route('publications.issues.show', [$publicationA, $issue]))
        ->assertOk();

    // Wrong parent is rejected by the scoped binding (tenant isolation).
    $this->actingAs($owner)
        ->get(route('publications.issues.show', [$publicationB, $issue]))
        ->assertNotFound();
});
