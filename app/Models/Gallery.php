<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Gallery Model
 * 
 * Stores photos/images uploaded to events after they happen.
 * Each photo is associated with an event and the user who uploaded it.
 * Used to create a photo album/memories for past events.
 */
class Gallery extends Model
{
    // Mass-assignable columns
    protected $fillable = [
        'event_id',     // Which event these photos belong to
        'user_id',      // Who uploaded this photo
        'image_path',   // File path/URL to the image
        'caption'       // Optional description of the photo
    ];

    // Gallery belongs to one event
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    // Gallery belongs to one user (uploader)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
