<?php

namespace App\Notifications;

use App\Models\PostComment;
use Illuminate\Notifications\Notification;

class PostCommentNotification extends Notification
{
    public function __construct(public PostComment $comment) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => $this->comment->user->name . ' commented on your post.',
            'post_id' => $this->comment->post_id,
            'post_title' => $this->comment->post->title,
            'commenter' => $this->comment->user->name,
        ];
    }
}