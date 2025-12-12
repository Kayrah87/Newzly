<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('laravel base installation is working', function () {
    expect(app()->version())->toContain('11.');
});

test('database connection works', function () {
    expect(\DB::connection()->getDatabaseName())->not()->toBeNull();
});

test('user model can be created', function () {
    $user = \App\Models\User::factory()->create([
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);

    expect($user->name)->toBe('Test User');
    expect($user->email)->toBe('test@example.com');
});

test('custom console commands are registered', function () {
    $commands = collect(\Artisan::all())->keys()->toArray();
    
    expect($commands)->toContain('install:spatie-packages');
    expect($commands)->toContain('install:livewire');
    expect($commands)->toContain('install:frontend');
    expect($commands)->toContain('install:optional');
    expect($commands)->toContain('blueprint:walkthrough');
    expect($commands)->toContain('create:test-data');
});

test('welcome page returns successfully', function () {
    $response = $this->get('/');
    
    $response->assertStatus(200);
    $response->assertSee('Laravel Base Template');
});