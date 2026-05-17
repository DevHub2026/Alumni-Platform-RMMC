<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Event;
use App\Models\User;

/**
 * HomeController
 * 
 * Handles the public homepage. Shows:
 * - Latest 3 published announcements
 * - Upcoming 3 events
 * - Total count of alumni
 * 
 * This is the first page visitors see when they land on the platform.
 */
class HomeController extends Controller
{
    /**
     * Display the homepage with platform overview.
     * Only shows published content to the public.
     */
    public function index()
    {
        // Fetch the 3 most recent published announcements
        $latestAnnouncements = Announcement::where('is_published', true)
                                ->latest()
                                ->take(3)
                                ->get();

        // Fetch upcoming 3 events (event_date >= today), sorted by date
        $upcomingEvents = Event::where('is_published', true)
                            ->where('event_date', '>=', now())
                            ->orderBy('event_date')
                            ->take(3)
                            ->get();

        // Count total alumni users on platform
        $alumniCount = User::where('role', 'alumni')->count();

        // Pass data to home view
        return view('home', compact(
            'latestAnnouncements',
            'upcomingEvents',
            'alumniCount'
        ));
    }
}