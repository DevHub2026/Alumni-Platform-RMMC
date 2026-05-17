<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * AlumniProfile Model
 * 
 * Stores detailed alumni information for each user.
 * Each user has exactly one alumni profile (created when they register).
 * Contains: education history, employment info, contact details, and social links.
 */
class AlumniProfile extends Model
{
    // Mass-assignable columns for alumni profile
    protected $fillable = [
        'user_id',           // Foreign key to User
        'student_id',        // University/school student ID
        'course',            // Field of study (e.g., "Computer Science")
        'graduation_year',   // Year graduated
        'phone',             // Contact phone number
        'address',           // Home/work address
        'current_job',       // Current job title
        'company',           // Current employer
        'linkedin_url',      // LinkedIn profile link
        'profile_photo',     // Path to profile picture
        'bio'                // Short biography/about me
    ];

    // Profile belongs to one user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
