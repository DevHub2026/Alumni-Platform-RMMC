<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Gallery;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    // Show all events that have gallery photos
    public function index()
    {
        $events = Event::where('is_published', true)
                    ->whereHas('galleries')
                    ->withCount('galleries')
                    ->with('galleries')
                    ->latest()
                    ->get();

        return view('gallery.index', compact('events'));
    }

    // Show all photos for a specific event
    public function show(Event $event)
    {
        $photos = Gallery::where('event_id', $event->id)
                    ->with('user')
                    ->latest()
                    ->get();

        // Check if logged-in user can upload
        $canUpload = false;

        if (Auth::check()) {
            $user = Auth::user();

            // Admin can always upload
            if ($user->isAdmin()) {
                $canUpload = true;
            }
            // Verified alumni who registered for this event
            elseif ($user->is_verified) {
                $canUpload = EventRegistration::where('event_id', $event->id)
                    ->where('user_id', $user->id)
                    ->where('status', 'confirmed')
                    ->exists();
            }
        }

        return view('gallery.show', compact('event', 'photos', 'canUpload'));
    }

    // Store uploaded photos
    public function store(Request $request, Event $event)
    {
        abort_if(!$event->is_published, 404);

        $user = Auth::user();

        // Check upload permission
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

    // Delete a photo — own photos or admin
    public function destroy(Gallery $gallery)
    {
        $user = Auth::user();

        // Only owner or admin can delete
        abort_if(
            $gallery->user_id !== $user->id && !$user->isAdmin(),
            403
        );

        Storage::disk('public')->delete($gallery->image_path);
        $gallery->delete();

        return back()->with('success', 'Photo deleted.');
    }
}