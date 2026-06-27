<?php

use App\Models\Publication;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('the admin seeder creates a verified super admin', function () {
    $this->seed(AdminUserSeeder::class);

    $admin = User::firstWhere('email', 'admin@newzly.test');

    expect($admin)->not->toBeNull()
        ->and($admin->isAdmin())->toBeTrue()
        ->and($admin->email_verified_at)->not->toBeNull();
});

test('a super admin can access any publication', function () {
    $publication = Publication::factory()->create(); // owned by someone else
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)->get(route('publications.show', $publication))->assertOk();
    $this->actingAs($admin)->get(route('publications.edit', $publication))->assertOk();
});

test('a normal user still cannot access a publication they are not part of', function () {
    $publication = Publication::factory()->create();
    $stranger = User::factory()->create(['is_admin' => false]);

    $this->actingAs($stranger)->get(route('publications.show', $publication))->assertForbidden();
});
