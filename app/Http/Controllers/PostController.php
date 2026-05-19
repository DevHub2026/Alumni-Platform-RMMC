<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostFlag;
use App\Models\PostComment;
use App\Models\PostReaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\PostCommentNotification;


class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::where('status', 'visible')
                    ->with(['user', 'user.alumniProfile'])
                    ->withCount('comments')
                    ->latest();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $posts      = $query->paginate(10);
        $categories = Post::CATEGORIES;

        return view('posts.index', compact('posts', 'categories'));
    }

    public function create()
    {
        if (!Auth::user()->is_verified) {
            return redirect()->route('posts.index')
                ->with('error', 'Complete your profile first to unlock posting.');
        }

        $categories = Post::CATEGORIES;
        return view('posts.create', compact('categories'));
    }

    public function store(Request $request)
{
    if (!Auth::user()->is_verified) {
        return redirect()->route('posts.index')
            ->with('error', 'Complete your profile first to unlock posting.');
    }

    $request->validate([
        'title'      => 'required|string|min:5|max:150',
        'category'   => 'required|in:' . implode(',', array_keys(Post::CATEGORIES)),
        'body'       => 'required|string|min:10|max:3000',
        'image_path' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
    ]);

    $data = [
        'user_id'  => Auth::id(),
        'title'    => $request->title,
        'category' => $request->category,
        'body'     => $request->body,
        'status'   => 'visible',
    ];

    if ($request->hasFile('image_path')) {
        $data['image_path'] = $request->file('image_path')
                                       ->store('post-images', 'public');
    }

    Post::create($data);

    return redirect()->route('posts.index')
        ->with('success', 'Post published successfully!');
}

    public function show(Post $post)
    {
        abort_if($post->status !== 'visible', 404);

        $post->load([
            'user',
            'user.alumniProfile',
            'comments' => function ($q) {
                $q->with('user')->latest();
            },
        ]);

        return view('posts.show', compact('post'));
    }

    public function edit(Post $post)
    {
        abort_if($post->user_id !== Auth::id(), 403);
        $categories = Post::CATEGORIES;
        return view('posts.edit', compact('post', 'categories'));
    }

    public function update(Request $request, Post $post)
{
    abort_if($post->user_id !== Auth::id(), 403);

    $request->validate([
        'title'      => 'required|string|min:5|max:150',
        'category'   => 'required|in:' . implode(',', array_keys(Post::CATEGORIES)),
        'body'       => 'required|string|min:10|max:3000',
        'image_path' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
    ]);

    $data = $request->only('title', 'category', 'body');

    if ($request->hasFile('image_path')) {
        if ($post->image_path) {
            \Storage::disk('public')->delete($post->image_path);
        }
        $data['image_path'] = $request->file('image_path')
                                       ->store('post-images', 'public');
    }

    $post->update($data);

    return redirect()->route('posts.show', $post)
        ->with('success', 'Post updated!');
}
    public function destroy(Post $post)
    {
        abort_if($post->user_id !== Auth::id(), 403);
        $post->delete();

        return redirect()->route('posts.index')
            ->with('success', 'Post deleted.');
    }

    public function flag(Request $request, Post $post)
    {
        if ($post->user_id === Auth::id()) {
            return back()->with('error', 'You cannot flag your own post.');
        }

        $request->validate([
            'reason'  => 'required|in:' . implode(',', array_keys(PostFlag::REASONS)),
            'details' => 'nullable|string|max:200',
        ]);

        $already = PostFlag::where('post_id', $post->id)
                           ->where('user_id', Auth::id())
                           ->exists();

        if ($already) {
            return back()->with('error', 'You have already flagged this post.');
        }

        PostFlag::create([
            'post_id' => $post->id,
            'user_id' => Auth::id(),
            'reason'  => $request->reason,
            'details' => $request->details,
        ]);

        if ($post->flags()->count() >= 3) {
            $post->update(['is_flagged' => true]);
        }

        return back()->with('success', 'Post reported. Thank you.');
    }

    public function comment(Request $request, Post $post)
{
    $request->validate([
        'body' => 'required|string|min:2|max:500',
    ]);

    $comment = PostComment::create([
        'post_id' => $post->id,
        'user_id' => Auth::id(),
        'body'    => $request->body,
    ]);

    // Notify post owner if it's not their own comment
    if ($post->user_id !== Auth::id()) {
        $post->user->notify(new PostCommentNotification($comment));
    }

    return back()->with('success', 'Comment added!');
}

    public function deleteComment(PostComment $comment)
    {
        abort_if($comment->user_id !== Auth::id(), 403);
        $comment->delete();

        return back()->with('success', 'Comment deleted.');
    }

    public function react(Request $request, Post $post)
    {
        $request->validate([
            'type' => 'required|in:like,celebrate,support',
        ]);

        $existing = PostReaction::where('post_id', $post->id)
                                ->where('user_id', Auth::id())
                                ->first();

        if ($existing) {
            if ($existing->type === $request->type) {
                // Same reaction — remove it (toggle off)
                $existing->delete();
                $reacted = false;
            } else {
                // Different reaction — update it
                $existing->update(['type' => $request->type]);
                $reacted = true;
            }
        } else {
            PostReaction::create([
                'post_id' => $post->id,
                'user_id' => Auth::id(),
                'type'    => $request->type,
            ]);
            $reacted = true;
        }

        return response()->json([
            'reacted'  => $reacted,
            'counts'   => [
                'like'      => $post->reactions()->where('type', 'like')->count(),
                'celebrate' => $post->reactions()->where('type', 'celebrate')->count(),
                'support'   => $post->reactions()->where('type', 'support')->count(),
                'total'     => $post->reactions()->count(),
            ],
        ]);
    }
}