<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostFlag extends Model
{
    protected $fillable = ['post_id', 'user_id', 'reason', 'details'];

    public const REASONS = [
        'spam'          => 'Spam',
        'inappropriate' => 'Inappropriate content',
        'misinformation'=> 'Misinformation',
        'harassment'    => 'Harassment',
        'other'         => 'Other',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}