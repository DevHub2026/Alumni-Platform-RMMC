<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * EventController
 * 
 * Manages event viewing and registration for alumni.
 * - Public endpoints: view all events, view single event
 * - Protected endpoints: register/unregister (auth required)
 * 
 * Features:
 * - Slot management (capacity checking)
 * - Duplicate registration prevention
 * - Registration status tracking
 */
class EventController extends Controller
{
    /**
     * List all published upcoming events (paginated, 9 per page).
     * Events sorted by event_date in ascending order (nearest first).
     */
    public function index()
    {
        // Fetch published events sorted by date (ascending = nearest first)
        $events = Event::where('is_published', true)
                    ->orderBy('event_date')
                    ->paginate(9);

        return view('events.index', compact('events'));
    }

    /**
     * Show a single event with registration info.
     * Abort with 404 if event is not published.
     * 
     * Shows:
     * - Event details
     * - Whether current user is registered
     * - Number of people registered
     */
    public function show(Event $event)
    {
        // Security: Block access if event is not published
        abort_if(!$event->is_published, 404);

        $isRegistered = false;                    // Default: not registered
        $registrationCount = $event->registrations()->count();

        // If user is logged in, check their registration status
        if (Auth::check()) {
            $isRegistered = EventRegistration::where('event_id', $event->id)
                                ->where('user_id', Auth::id())
                                ->exists();
        }

        return view('events.show', compact(
            'event',
            'isRegistered',
            'registrationCount'
        ));
    }

    /**
     * Register current user for an event.
     * Requires authentication.
     * 
     * Validation:
     * - Check if event has available slots
     * - Check if user is already registered
     * - Prevent duplicate registrations
     */
    public function register(Event $event)
    {
        // Get current count of registrations
        $registrationCount = $event->registrations()->count();

        // Check if slots are full (slots > 0 means limited, 0 means unlimited)
        if ($event->slots > 0 && $registrationCount >= $event->slots) {
            return back()->with('error', 'Sorry, this event is already full.');
        }

        // Check if user is already registered for this event
        $alreadyRegistered = EventRegistration::where('event_id', $event->id)
                                ->where('user_id', Auth::id())
                                ->exists();

        if ($alreadyRegistered) {
            return back()->with('error', 'You are already registered for this event.');
        }

        // Create new registration record
        EventRegistration::create([
            'event_id' => $event->id,
            'user_id'  => Auth::id(),
            'status'   => 'confirmed',  // Automatically confirm on sign-up
        ]);

        return back()->with('success', 'You have successfully registered for this event!');
    }

    /**
     * Unregister current user from an event.
     * Requires authentication.
     * Removes the registration record completely.
     */
    public function unregister(Event $event)
    {
        // Delete registration record for this user and event
        EventRegistration::where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->delete();

        return back()->with('success', 'You have unregistered from this event.');
    }
}