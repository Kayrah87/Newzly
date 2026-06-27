<?php

use App\Http\Controllers\IssueController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicationController;
use App\Http\Controllers\StoryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Publication routes
    Route::resource('publications', PublicationController::class);
    Route::get('publications/{publication}/editors', [PublicationController::class, 'editors'])->name('publications.editors');

    // Issue routes (scoped so an issue must belong to its publication)
    Route::resource('publications.issues', IssueController::class)->scoped()->except(['index']);
    Route::get('publications/{publication}/issues', [IssueController::class, 'index'])->name('publications.issues.index');

    // Story routes (scoped to publication + issue)
    Route::resource('publications.issues.stories', StoryController::class)->scoped()->except(['index', 'show']);
});

require __DIR__.'/auth.php';
