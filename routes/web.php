<?php

use App\Http\Controllers\BlockController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicationController;
use App\Http\Controllers\PublicSubmissionController;
use App\Http\Controllers\PublicSubscriptionController;
use App\Http\Controllers\StoryController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\SubscriberController;
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

    // Publication layout & theme (section order + colour palette)
    Route::get('publications/{publication}/structure', [PublicationController::class, 'editStructure'])->name('publications.structure.edit');
    Route::put('publications/{publication}/structure', [PublicationController::class, 'updateStructure'])->name('publications.structure.update');

    // Team invitations + membership
    Route::post('publications/{publication}/invitations', [InvitationController::class, 'store'])->name('publications.invitations.store');
    Route::delete('publications/{publication}/invitations/{invitation}', [InvitationController::class, 'destroy'])
        ->scopeBindings()->name('publications.invitations.destroy');
    Route::delete('publications/{publication}/members/{user}', [PublicationController::class, 'removeMember'])->name('publications.members.destroy');

    // Accept an invitation (must be signed in as the invited email)
    Route::post('invitations/{token}/accept', [InvitationController::class, 'accept'])->name('invitations.accept');

    // Issue routes (scoped so an issue must belong to its publication)
    Route::resource('publications.issues', IssueController::class)->scoped()->except(['index']);
    Route::get('publications/{publication}/issues', [IssueController::class, 'index'])->name('publications.issues.index');

    // Send an issue to confirmed subscribers
    Route::post('publications/{publication}/issues/{issue}/send', [IssueController::class, 'send'])
        ->scopeBindings()
        ->name('publications.issues.send');

    // Browser preview of an issue (rendered with the publication's layout + palette)
    Route::get('publications/{publication}/issues/{issue}/preview', [IssueController::class, 'preview'])
        ->scopeBindings()
        ->name('publications.issues.preview');

    // Drag-and-drop reorder of an issue's content stream (stories + blocks).
    Route::patch('publications/{publication}/issues/{issue}/reorder', [IssueController::class, 'reorder'])
        ->scopeBindings()
        ->name('publications.issues.reorder');

    // Story routes (scoped to publication + issue)
    Route::resource('publications.issues.stories', StoryController::class)->scoped()->except(['index', 'show']);

    // Content blocks (events, …) — scoped to publication + issue
    Route::resource('publications.issues.blocks', BlockController::class)
        ->scoped()->only(['create', 'store', 'edit', 'update', 'destroy']);

    // Public submission moderation queue (scoped to publication)
    Route::get('publications/{publication}/submissions', [SubmissionController::class, 'index'])->name('publications.submissions.index');
    Route::patch('publications/{publication}/submissions/{story}/approve', [SubmissionController::class, 'approve'])
        ->scopeBindings()->name('publications.submissions.approve');
    Route::patch('publications/{publication}/submissions/{story}/reject', [SubmissionController::class, 'reject'])
        ->scopeBindings()->name('publications.submissions.reject');

    // Subscriber / mailing-list routes (scoped to publication)
    Route::resource('publications.subscribers', SubscriberController::class)->scoped()->only(['index', 'store', 'destroy']);
    Route::patch('publications/{publication}/subscribers/{subscriber}/unsubscribe', [SubscriberController::class, 'unsubscribe'])
        ->scopeBindings()
        ->name('publications.subscribers.unsubscribe');
});

// Invitation landing page (viewable by guests so they can sign in / register).
Route::get('invitations/{token}', [InvitationController::class, 'show'])->name('invitations.show');

// Public, unauthenticated subscription pages (per publication, by slug).
Route::prefix('p/{publication:slug}')->name('public.')->group(function () {
    Route::get('subscribe', [PublicSubscriptionController::class, 'create'])->name('subscribe');
    Route::post('subscribe', [PublicSubscriptionController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('subscribe.store');
    Route::get('confirm/{token}', [PublicSubscriptionController::class, 'confirm'])->name('confirm');
    Route::get('unsubscribe/{token}', [PublicSubscriptionController::class, 'unsubscribeForm'])->name('unsubscribe');
    Route::post('unsubscribe/{token}', [PublicSubscriptionController::class, 'unsubscribe'])
        ->middleware('throttle:10,1')
        ->name('unsubscribe.perform');

    // Public story/photo submission.
    Route::get('submit', [PublicSubmissionController::class, 'create'])->name('submit');
    Route::post('submit', [PublicSubmissionController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('submit.store');
});

require __DIR__.'/auth.php';
