<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'user_id', 'category', 'title',
        'body', 'status', 'is_flagged'
    ];

    protected $casts = [
        'is_flagged' => 'boolean',
    ];

    // Category labels for display
    public const CATEGORIES = [
        'career_update' => 'Career Update',
        'achievement'   => 'Achievement',
        'opportunity'   => 'Opportunity',
        'reunion'       => 'Reunion',
        'general'       => 'General',
    ];

    // Category badge colors (Tailwind)
    public const CATEGORY_COLORS = [
        'career_update' => 'bg-blue-100 text-blue-700',
        'achievement'   => 'bg-yellow-100 text-yellow-700',
        'opportunity'   => 'bg-green-100 text-green-700',
        'reunion'       => 'bg-purple-100 text-purple-700',
        'general'       => 'bg-gray-100 text-gray-600',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function flags()
    {
        return $this->hasMany(PostFlag::class);
    }

    public function comments()
    {
        return $this->hasMany(PostComment::class)->latest();
    }

    // Scope: only visible posts
    public function scopeVisible($query)
    {
        return $query->where('status', 'visible');
    }

    // Scope: flagged posts for admin
    public function scopeFlagged($query)
    {
        return $query->where('is_flagged', true);
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? 'General';
    }

    public function getCategoryColorAttribute(): string
    {
        return self::CATEGORY_COLORS[$this->category] ?? 'bg-gray-100 text-gray-600';
    }
}