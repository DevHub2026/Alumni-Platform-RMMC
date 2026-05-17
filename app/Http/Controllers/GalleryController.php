<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Gallery;

/**
 * GalleryController
 * 
 * Manages event photo galleries.
 * Users can upload photos to events after they happen.
 * Photos are organized by event for easy browsing of event memories.
 */
class GalleryController extends Controller
{
    /**
     * Display all events that have photos in their galleries.
     * Shows events with at least 1 photo, sorted by newest first.
     * Also displays photo count for each event.
     */
    public function index()
    {
        // Fetch published events that have gallery photos
        // withCount('galleries') adds a 'galleries_count' property
        $events = Event::where('is_published', true)
                    ->whereHas('galleries')        // Only events with at least 1 photo
                    ->withCount('galleries')       // Count photos per event
                    ->latest()                     // Newest events first
                    ->get();

        return view('gallery.index', compact('events'));
    }

    /**
     * Display all photos for a specific event.
     * Shows photos sorted by newest first (most recent uploads first).
     */
    public function show(Event $event)
    {
        // Fetch all photos for this event, newest first
        $photos = Gallery::where('event_id', $event->id)
                    ->latest()
                    ->get();

        return view('gallery.show', compact('event', 'photos'));
    }
}