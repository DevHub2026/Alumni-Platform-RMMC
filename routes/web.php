<?php

/**
 * Routes for Alumni Platform Web Application
 * 
 * Routes are organized by feature:
 * - Home: Public landing page
 * - Chatbot: AI assistant for logged-in users
 * - Alumni Profile: Personal profile & directory
 * - Announcements: News and updates
 * - Events: Alumni events with registration
 * - Gallery: Event photos
 * - Dashboard: Authenticated user dashboard
 * - Auth: Login, register, password reset (imported from auth.php)
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AlumniProfileController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SearchController;




Route::get('/search', [SearchController::class, 'index'])->name('search.index');

// ============ PUBLIC ROUTES ============

// Homepage — shows latest announcements, events, and alumni count
Route::get('/', [HomeController::class, 'index'])->name('home');

// ============ CHATBOT (requires authentication) ============

// AI chatbot endpoint — answers questions about the platform
Route::post('/chatbot/ask', [ChatbotController::class, 'ask'])
     ->middleware('auth')
     ->name('chatbot.ask');

// ============ ALUMNI PROFILE ROUTES ============

// Protected profile routes (requires auth)
Route::middleware(['auth'])->group(function () {
    // View logged-in user's own profile
    Route::get('/profile', [AlumniProfileController::class, 'show'])->name('profile.show');
    // Show profile edit form   
    Route::get('/profile/edit', [AlumniProfileController::class, 'edit'])->name('profile.edit');
    // Save profile updates
    Route::put('/profile', [AlumniProfileController::class, 'update'])->name('profile.update');
});

// Public alumni directory — browse all alumni profiles
Route::get('/alumni', [AlumniProfileController::class, 'index'])->name('alumni.index');

// ============ POSTS ============

// Posts — public
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');

// Posts — auth required
Route::middleware(['auth'])->group(function () {
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
    Route::post('/posts/{post}/flag', [PostController::class, 'flag'])->name('posts.flag');
    Route::post('/posts/{post}/react', [PostController::class, 'react'])->name('posts.react');
    Route::post('/posts/{post}/comment', [PostController::class, 'comment'])->name('posts.comment');
    Route::delete('/comments/{comment}', [PostController::class, 'deleteComment'])->name('posts.comment.delete');
});

// ============ ANNOUNCEMENTS ============

// List all published announcements
Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
// View single announcement detail
Route::get('/announcements/{announcement}', [AnnouncementController::class, 'show'])->name('announcements.show');

// ============ EVENTS ============

// List all upcoming events
Route::get('/events', [EventController::class, 'index'])->name('events.index');
// View single event detail
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');

// Event registration (requires authentication)
Route::middleware(['auth'])->group(function () {
    // Register for an event
    Route::post('/events/{event}/register', [EventController::class, 'register'])->name('events.register');
    // Unregister from an event
    Route::delete('/events/{event}/unregister', [EventController::class, 'unregister'])->name('events.unregister');
});

// ============ GALLERY ============

// Gallery — public
Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');
Route::get('/gallery/{event}', [GalleryController::class, 'show'])->name('gallery.show');

// Gallery — auth required
Route::middleware(['auth'])->group(function () {
    Route::post('/gallery/{event}/upload', [GalleryController::class, 'store'])
         ->name('gallery.store');
    Route::delete('/gallery/photo/{gallery}', [GalleryController::class, 'destroy'])
         ->name('gallery.destroy');
});

// ============ NOTIFICATIONS ============

Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread', [NotificationController::class, 'unread'])->name('notifications.unread');
    Route::post('/notifications/mark-read', [NotificationController::class, 'markRead'])->name('notifications.markRead');
});

// ============ DASHBOARD ============

// User dashboard (requires auth and verified email)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ============ AUTHENTICATION ============
// Login, register, password reset routes — see auth.php
require __DIR__.'/auth.php';