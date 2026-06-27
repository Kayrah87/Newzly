<?php

use App\Models\Publication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('owner can update profile and sending-identity fields', function () {
    $publication = Publication::factory()->create();

    $this->actingAs($publication->owner)->patch(route('publications.update', $publication), [
        'name' => 'Farming Monthly',
        'description' => 'Agriculture news.',
        'website_url' => 'https://farmingmonthly.test',
        'from_name' => 'The Editor',
        'from_email' => 'editor@farmingmonthly.test',
        'reply_to_email' => 'replies@farmingmonthly.test',
        'social_links' => [
            'twitter' => 'https://x.com/farmingmonthly',
            'facebook' => '',                         // empty -> dropped
            'bogus' => 'https://evil.test',           // unknown platform -> dropped
        ],
    ])->assertRedirect(route('publications.show', $publication));

    $publication->refresh();

    expect($publication->website_url)->toBe('https://farmingmonthly.test')
        ->and($publication->from_name)->toBe('The Editor')
        ->and($publication->from_email)->toBe('editor@farmingmonthly.test')
        ->and($publication->reply_to_email)->toBe('replies@farmingmonthly.test')
        ->and($publication->social_links)->toBe(['twitter' => 'https://x.com/farmingmonthly']);
});

test('an invalid from email is rejected', function () {
    $publication = Publication::factory()->create(['from_email' => null]);

    $this->actingAs($publication->owner)
        ->patch(route('publications.update', $publication), [
            'name' => $publication->name,
            'from_email' => 'not-an-email',
        ])
        ->assertSessionHasErrors('from_email');

    expect($publication->fresh()->from_email)->toBeNull();
});

test('a logo can be uploaded and stored on the media disk', function () {
    Storage::fake(Publication::mediaDisk());
    $publication = Publication::factory()->create();

    $this->actingAs($publication->owner)->patch(route('publications.update', $publication), [
        'name' => $publication->name,
        'logo' => UploadedFile::fake()->image('logo.png', 200, 200),
    ])->assertRedirect();

    $path = $publication->fresh()->logo_path;
    expect($path)->not->toBeNull();
    Storage::disk(Publication::mediaDisk())->assertExists($path);
});

test('a logo can be removed', function () {
    Storage::fake(Publication::mediaDisk());
    $publication = Publication::factory()->create();

    // First upload.
    $this->actingAs($publication->owner)->patch(route('publications.update', $publication), [
        'name' => $publication->name,
        'logo' => UploadedFile::fake()->image('logo.png'),
    ]);
    $path = $publication->fresh()->logo_path;
    expect($path)->not->toBeNull();

    // Then remove.
    $this->actingAs($publication->owner)->patch(route('publications.update', $publication), [
        'name' => $publication->name,
        'remove_logo' => '1',
    ]);

    expect($publication->fresh()->logo_path)->toBeNull();
    Storage::disk(Publication::mediaDisk())->assertMissing($path);
});

test('a stranger cannot update the profile', function () {
    $publication = Publication::factory()->create();
    $stranger = User::factory()->create();

    $this->actingAs($stranger)
        ->patch(route('publications.update', $publication), ['name' => 'Hijacked'])
        ->assertForbidden();
});
