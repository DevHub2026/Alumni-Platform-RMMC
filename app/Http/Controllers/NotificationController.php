<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()
                            ->notifications()
                            ->latest()
                            ->paginate(20);

        Auth::user()->unreadNotifications->markAsRead();

        return view('notifications.index', compact('notifications'));
    }

    public function unread()
    {
        $user = Auth::user();
        return response()->json([
            'notifications' => $user->notifications()->latest()->take(5)->get()->map(function($n) {
                return [
                    'id'           => $n->id,
                    'data'         => $n->data,
                    'read_at'      => $n->read_at,
                    'created_at_human' => $n->created_at->diffForHumans(),
                ];
            }),
            'unread' => $user->unreadNotifications()->count(),
        ]);
    }

    public function markRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return response()->json(['status' => 'ok']);
    }
}