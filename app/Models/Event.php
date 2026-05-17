<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    /**
     * Event Model
     * 
     * Represents alumni events (reunions, seminars, networking, etc.).
     * Events are created by admins and can be registered by alumni users.
     * 
     * Key Features:
     * - Limited slots (if slots > 0, event has a cap; if 0, unlimited registration)
     * - Publish status (is_published = false means not visible to alumni)
     * - Gallery support (photos can be uploaded after event)
     * - Registration tracking (who registered and their status)
     */
    
    // Mass-assignable columns
    protected $fillable = [
        'user_id',        // Creator/admin who posted the event
        'title',          // Event name
        'description',    // Event details and agenda
        'location',       // Where the event takes place
        'event_date',     // When the event happens
        'slots',          // Available slots (0 = unlimited)
        'cover_image',    // Event poster/thumbnail image
        'is_published'    // Visibility flag (published = visible to alumni)
    ];

    // Type casting for better data handling
    protected $casts = [
        'event_date'    => 'datetime',  // Stored as string, accessed as DateTime object
        'is_published'  => 'boolean',   // Stored as 0/1, accessed as true/false
    ];

    // Event has many registrations (alumni sign-ups)
    public function registrations()
    {
        return $this->hasMany(EventRegistration::class);
    }

    // Event can have many gallery photos (uploaded after event)
    public function galleries()
    {
        return $this->hasMany(Gallery::class);
    }

    // Event belongs to one user (admin who created it)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

