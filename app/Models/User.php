<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * User Model
 * 
 * Represents a user in the Alumni Platform system.
 * Every user can be either an 'admin' or 'alumni' (defined in 'role' column).
 * - Admins: Can access Filament admin panel and manage platform content
 * - Alumni: Can view platform, update profile, register for events
 * 
 * Relationships:
 * - One-to-One with AlumniProfile
 * - One-to-Many with Announcements, Events, Galleries
 * - One-to-Many with EventRegistrations
 * - One-to-Many with Posts, PostFlags, PostComments
 */
class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_verified',
    ];

    /**
     * Hidden attributes
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attribute casting
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_verified' => 'boolean',
        ];
    }

    /**
     * Allow only admins to access Filament panel
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Alumni Profile Relationship
     */
    public function alumniProfile()
    {
        return $this->hasOne(AlumniProfile::class);
    }

    /**
     * Announcements Relationship
     */
    public function announcements()
    {
        return $this->hasMany(Announcement::class);
    }

    /**
     * Events Relationship
     */
    public function events()
    {
        return $this->hasMany(Event::class);
    }

    /**
     * Event Registrations Relationship
     */
    public function eventRegistrations()
    {
        return $this->hasMany(EventRegistration::class);
    }

    /**
     * Galleries Relationship
     */
    public function galleries()
    {
        return $this->hasMany(Gallery::class);
    }

    /**
     * Posts Relationship
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Post Flags Relationship
     */
    public function postFlags()
    {
        return $this->hasMany(PostFlag::class);
    }

    /**
     * Post Comments Relationship
     */
    public function postComments()
    {
        return $this->hasMany(PostComment::class);
    }
}