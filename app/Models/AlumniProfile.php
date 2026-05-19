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
        'user_id', 'student_id', 'course', 'graduation_year',
        'phone', 'address', 'current_job', 'company',
        'linkedin_url', 'portfolio_url', 'profile_photo',
        'bio', 'skills'
    ];

    // Profile belongs to one user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
