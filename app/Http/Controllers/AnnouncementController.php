<?php

namespace App\Http\Controllers;

use App\Models\Announcement;

/**
 * AnnouncementController
 * 
 * Manages viewing announcements on the public platform.
 * Only published announcements are visible to users.
 * Admins create announcements through Filament admin panel.
 */
class AnnouncementController extends Controller
{
    /**
     * List all published announcements, paginated (9 per page).
     * Latest announcements appear first.
     */
    public function index()
    {
        $announcements = Announcement::where('is_published', true)
                            ->latest()
                            ->paginate(9);

        return view('announcements.index', compact('announcements'));
    }

    /**
     * Display a single announcement detail page.
     * Abort with 404 if announcement is not published (not visible to users).
     */
    public function show(Announcement $announcement)
    {
        // Security: Check if announcement is published before allowing access
        abort_if(!$announcement->is_published, 404);

        return view('announcements.show', compact('announcement'));
    }
}