<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Post;
use App\Models\Event;
use App\Models\Announcement;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return view('search.index', [
                'query'         => $query,
                'alumni'        => collect(),
                'posts'         => collect(),
                'events'        => collect(),
                'announcements' => collect(),
                'total'         => 0,
            ]);
        }

        // Search alumni
        $alumni = User::where('role', 'alumni')
            ->with('alumniProfile')
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhereHas('alumniProfile', function ($q) use ($query) {
                      $q->where('course', 'like', "%{$query}%")
                        ->orWhere('current_job', 'like', "%{$query}%")
                        ->orWhere('company', 'like', "%{$query}%")
                        ->orWhere('graduation_year', 'like', "%{$query}%");
                  });
            })
            ->take(6)
            ->get();

        // Search posts
        $posts = Post::where('status', 'visible')
            ->with('user')
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('body', 'like', "%{$query}%");
            })
            ->latest()
            ->take(6)
            ->get();

        // Search events
        $events = Event::where('is_published', true)
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('location', 'like', "%{$query}%");
            })
            ->latest()
            ->take(4)
            ->get();

        // Search announcements
        $announcements = Announcement::where('is_published', true)
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('content', 'like', "%{$query}%");
            })
            ->latest()
            ->take(4)
            ->get();

        $total = $alumni->count() + $posts->count()
                + $events->count() + $announcements->count();

        return view('search.index', compact(
            'query', 'alumni', 'posts',
            'events', 'announcements', 'total'
        ));
    }
}