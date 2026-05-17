@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">

    <a href="{{ route('posts.index') }}"
       class="text-sm text-blue-600 hover:underline mb-6 inline-block">
        ← Back to Posts
    </a>

    <!-- Post -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 mb-6">

        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-700
                            font-bold flex items-center justify-center flex-shrink-0">
                    {{ strtoupper(substr($post->user->name, 0, 1)) }}
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <p class="font-semibold text-gray-800">{{ $post->user->name }}</p>
                        @if($post->user->is_verified)
                            <span class="text-blue-500 text-xs" title="Verified Alumni">✔</span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-400">
                        {{ $post->created_at->format('F d, Y • g:i A') }}
                    </p>
                </div>
            </div>
            <span class="text-xs font-medium px-3 py-1 rounded-full
                         {{ $post->category_color }}">
                {{ $post->category_label }}
            </span>
        </div>

        <h1 class="text-xl font-bold text-gray-800 mb-4">{{ $post->title }}</h1>
        <div class="text-gray-700 text-sm leading-relaxed whitespace-pre-line">
            {{ $post->body }}
        </div>

        @auth
            @if($post->user_id === Auth::id())
                <div class="flex gap-3 mt-6 pt-4 border-t border-gray-100">
                    <a href="{{ route('posts.edit', $post) }}"
                       class="text-sm text-blue-600 hover:underline">Edit Post</a>
                    <form method="POST" action="{{ route('posts.destroy', $post) }}"
                          onsubmit="return confirm('Delete this post?')">
                        @csrf @method('DELETE')
                        <button class="text-sm text-red-500 hover:underline">
                            Delete Post
                        </button>
                    </form>
                </div>
            @endif
        @endauth
    </div>

    <!-- Comments -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-4">
        <h2 class="font-bold text-gray-800 mb-4">
            💬 {{ $post->comments->count() }}
            {{ Str::plural('Comment', $post->comments->count()) }}
        </h2>

        @forelse($post->comments as $comment)
            <div class="flex gap-3 mb-4 pb-4 border-b border-gray-50 last:border-0">
                <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700
                            font-bold flex items-center justify-center text-xs flex-shrink-0">
                    {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold text-gray-700">
                            {{ $comment->user->name }}
                        </p>
                        <div class="flex items-center gap-3">
                            <p class="text-xs text-gray-400">
                                {{ $comment->created_at->diffForHumans() }}
                            </p>
                            @auth
                                @if($comment->user_id === Auth::id())
                                    <form method="POST"
                                          action="{{ route('posts.comment.delete', $comment) }}">
                                        @csrf @method('DELETE')
                                        <button class="text-xs text-red-400 hover:text-red-600">
                                            Delete
                                        </button>
                                    </form>
                                @endif
                            @endauth
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 mt-1">{{ $comment->body }}</p>
                </div>
            </div>
        @empty
            <p class="text-gray-400 text-sm">
                No comments yet. Be the first to comment!
            </p>
        @endforelse
    </div>

    <!-- Add Comment -->
    @auth
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="font-semibold text-gray-800 mb-4 text-sm">Add a Comment</h3>
            <form method="POST" action="{{ route('posts.comment', $post) }}">
                @csrf
                <textarea name="body" rows="3"
                          placeholder="Write a comment..."
                          class="w-full border border-gray-200 rounded-xl px-4 py-2.5
                                 text-sm bg-gray-50 focus:outline-none focus:ring-2
                                 focus:ring-blue-500 resize-none mb-3"></textarea>
                <button type="submit"
                        class="bg-blue-700 text-white px-5 py-2 rounded-xl
                               text-sm hover:bg-blue-800 transition">
                    Post Comment
                </button>
            </form>
        </div>
    @else
        <div class="bg-gray-50 rounded-2xl border border-gray-100 p-6 text-center">
            <p class="text-sm text-gray-500">
                <a href="{{ route('login') }}" class="text-blue-600 hover:underline">
                    Log in
                </a> to leave a comment.
            </p>
        </div>
    @endauth

</div>
@endsection