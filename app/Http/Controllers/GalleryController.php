<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Gallery;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * GalleryController
 *
 * Handles event photo galleries — browsing, uploading, and deleting photos.
 *
 * Public endpoints:
 * - index: List all events that have at least one gallery photo
 * - show:  View all photos for a specific event + lightbox
 *
 * Auth-protected endpoints:
 * - store:   Upload photos to an event gallery
 * - destroy: Delete a single photo
 *
 * Upload permission rules:
 * - Admins can upload to any published event
 * - Verified alumni can upload only if they have a confirmed registration
 *   for that specific event
 */
class GalleryController extends Controller
{
    /**
     * List all published events that have at least one gallery photo.
     * Orders by newest event first so recent events appear at the top.
     */
    public function index()
    {
        $events = Event::where('is_published', true)
                    ->whereHas('galleries')       // Only events with photos
                    ->withCount('galleries')      // Attach photo count for display
                    ->with('galleries')           // Eager-load photos (used for cover thumbnail)
                    ->latest()
                    ->get();

        return view('gallery.index', compact('events'));
    }

    /**
     * Show all photos for a specific event.
     * Also determines whether the logged-in user is allowed to upload.
     *
     * $canUpload is true when:
     * - User is an admin, OR
     * - User is a verified alumni with a confirmed registration for this event
     */
    public function show(Event $event)
    {
        // Load photos newest-first, with the uploader's name for display
        $photos = Gallery::where('event_id', $event->id)
                    ->with('user')
                    ->latest()
                    ->get();

        $canUpload = false;

        if (Auth::check()) {
            $user = Auth::user();

            if ($user->isAdmin()) {
                // Admins can always upload to any event
                $canUpload = true;
            } elseif ($user->is_verified) {
                // Verified alumni: must have a confirmed registration for this event
                $canUpload = EventRegistration::where('event_id', $event->id)
                    ->where('user_id', $user->id)
                    ->where('status', 'confirmed')
                    ->exists();
            }
        }

        return view('gallery.show', compact('event', 'photos', 'canUpload'));
    }

    /**
     * Handle photo uploads for an event gallery.
     * Accepts up to 10 images per request (JPG, PNG, WebP, max 4 MB each).
     * Each photo can have an optional caption.
     *
     * Aborts with 404 if event is not published.
     * Aborts with 403 if user does not have upload permission.
     */
    public function store(Request $request, Event $event)
    {
        // Block uploads to unpublished events
        abort_if(!$event->is_published, 404);

        $user = Auth::user();

        // Re-check upload permission server-side (cannot rely on UI alone)
        $canUpload = $user->isAdmin() || (
            $user->is_verified &&
            EventRegistration::where('event_id', $event->id)
                ->where('user_id', $user->id)
                ->where('status', 'confirmed')
                ->exists()
        );

        abort_if(!$canUpload, 403);

        $request->validate([
            'photos'     => 'required|array|min:1|max:10',
            'photos.*'   => 'image|mimes:jpg,jpeg,png,webp|max:4096',
            'captions'   => 'nullable|array',
            'captions.*' => 'nullable|string|max:200',
        ]);

        $uploaded = 0;

        // Save each photo to storage and create a Gallery record
        foreach ($request->file('photos') as $index => $photo) {
            $path = $photo->store('gallery', 'public');

            Gallery::create([
                'event_id'   => $event->id,
                'user_id'    => Auth::id(),
                'image_path' => $path,
                'caption'    => $request->captions[$index] ?? null,
            ]);

            $uploaded++;
        }

        return redirect()->route('gallery.show', $event)
            ->with('success', "Successfully uploaded {$uploaded} photo(s)!");
    }

    /**
     * Delete a single gallery photo.
     * Only the uploader or an admin can delete a photo.
     * Also removes the physical file from storage.
     */
    public function destroy(Gallery $gallery)
    {
        $user = Auth::user();

        // Prevent deletion by users who are neither the owner nor an admin
        abort_if(
            $gallery->user_id !== $user->id && !$user->isAdmin(),
            403
        );

        // Delete the file from disk before removing the database record
        Storage::disk('public')->delete($gallery->image_path);
        $gallery->delete();

        return back()->with('success', 'Photo deleted.');
    }
}