<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\NewsletterIssueController;
use App\Http\Controllers\ArticleController;
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
    
    // Newsletter routes
    Route::resource('newsletters', NewsletterController::class);
    Route::get('newsletters/{newsletter}/editors', [NewsletterController::class, 'editors'])->name('newsletters.editors');
    Route::get('newsletters/{newsletter}/recipients', [NewsletterController::class, 'recipients'])->name('newsletters.recipients');
    
    // Newsletter Issues routes
    Route::resource('newsletters.issues', NewsletterIssueController::class)->except(['index']);
    Route::get('newsletters/{newsletter}/issues', [NewsletterIssueController::class, 'index'])->name('newsletters.issues.index');
    
    // Articles routes
    Route::resource('newsletters.issues.articles', ArticleController::class)->except(['index', 'show']);
});

require __DIR__.'/auth.php';
