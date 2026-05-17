<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * EventRegistration Model
 * 
 * Join table between User and Event.
 * Tracks which alumni registered for which events and their registration status.
 * Status can be: 'confirmed', 'cancelled', 'attended', etc.
 */
class EventRegistration extends Model
{
    // Mass-assignable columns
    protected $fillable = [
        'event_id',   // Which event
        'user_id',    // Which alumni registered
        'status'      // Registration status (confirmed, cancelled, attended, etc.)
    ];

    // Registration belongs to one event
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    // Registration belongs to one user (alumni)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
