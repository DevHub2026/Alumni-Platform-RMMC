<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Announcement Model
 * 
 * Represents news and announcements posted by admins to the alumni community.
 * Only published announcements are visible to alumni on the platform.
 */
class Announcement extends Model
{
    // Mass-assignable columns
    protected $fillable = [
        'user_id',       // Admin who created the announcement
        'title',         // Headline
        'content',       // Full announcement text/HTML
        'cover_image',   // Thumbnail image for the announcement
        'is_published'   // Visibility flag (published = visible to alumni)
    ];

    // Type casting for better data handling
    protected $casts = [
        'is_published' => 'boolean',  // Stored as 0/1, accessed as true/false
    ];

    // Announcement belongs to one user (admin who created it)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
