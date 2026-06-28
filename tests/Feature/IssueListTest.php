<?php

use App\Models\Issue;
use App\Models\Publication;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('the issues list filters by the search term', function () {
    $publication = Publication::factory()->create();
    Issue::factory()->create(['publication_id' => $publication->id, 'title' => 'Spring Harvest']);
    Issue::factory()->create(['publication_id' => $publication->id, 'title' => 'Winter Roundup']);

    $response = $this->actingAs($publication->owner)
        ->get(route('publications.issues.index', ['publication' => $publication, 'search' => 'Spring']));

    $response->assertOk()
        ->assertSee('Spring Harvest')
        ->assertDontSee('Winter Roundup');
});

test('search also matches coverage label and issue number', function () {
    $publication = Publication::factory()->create();
    Issue::factory()->create(['publication_id' => $publication->id, 'title' => 'Alpha', 'coverage_label' => 'May–June']);
    Issue::factory()->create(['publication_id' => $publication->id, 'title' => 'Beta', 'issue_number' => 42]);
    Issue::factory()->create(['publication_id' => $publication->id, 'title' => 'Gamma']);

    $this->actingAs($publication->owner)
        ->get(route('publications.issues.index', ['publication' => $publication, 'search' => 'June']))
        ->assertOk()->assertSee('Alpha')->assertDontSee('Gamma');

    $this->actingAs($publication->owner)
        ->get(route('publications.issues.index', ['publication' => $publication, 'search' => '42']))
        ->assertOk()->assertSee('Beta')->assertDontSee('Gamma');
});

test('the list can be sorted by title ascending', function () {
    $publication = Publication::factory()->create();
    Issue::factory()->create(['publication_id' => $publication->id, 'title' => 'Zulu']);
    Issue::factory()->create(['publication_id' => $publication->id, 'title' => 'Alpha']);

    $response = $this->actingAs($publication->owner)
        ->get(route('publications.issues.index', [
            'publication' => $publication,
            'sort' => 'title',
            'direction' => 'asc',
        ]));

    $response->assertOk()->assertSeeInOrder(['Alpha', 'Zulu']);
});

test('an invalid sort column falls back to the default order', function () {
    $publication = Publication::factory()->create();
    Issue::factory()->create(['publication_id' => $publication->id, 'title' => 'Only Issue']);

    // A non-whitelisted column must not reach the query (no SQL error, no injection).
    $this->actingAs($publication->owner)
        ->get(route('publications.issues.index', [
            'publication' => $publication,
            'sort' => 'id); drop table issues;--',
            'direction' => 'asc',
        ]))
        ->assertOk()
        ->assertSee('Only Issue');
});

test('an empty search shows the no-matches state', function () {
    $publication = Publication::factory()->create();
    Issue::factory()->create(['publication_id' => $publication->id, 'title' => 'Findable']);

    $this->actingAs($publication->owner)
        ->get(route('publications.issues.index', ['publication' => $publication, 'search' => 'nope-nothing']))
        ->assertOk()
        ->assertSee('No issues match')
        ->assertDontSee('Findable');
});
