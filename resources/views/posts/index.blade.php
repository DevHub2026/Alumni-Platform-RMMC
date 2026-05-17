@extends('layouts.app')

@section('content')

<div class="max-w-3xl mx-auto">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Alumni Posts</h1>
            <p class="text-gray-500 text-sm mt-1">
                Share updates, opportunities, and achievements with the community.
            </p>
        </div>
        @auth
    @if(Auth::user()->role === 'admin')
        <a href="{{ url('/admin') }}"
           class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl
                  text-sm font-medium hover:bg-indigo-700 transition">
            ⚙️ Manage Posts
        </a>
    @elseif(Auth::user()->is_verified)
        <a href="{{ route('posts.create') }}"
           class="bg-blue-700 text-white px-5 py-2.5 rounded-xl
                  text-sm font-medium hover:bg-blue-800 transition">
            + New Post
        </a>
    @else
        <a href="{{ route('profile.edit') }}"
           class="bg-yellow-500 text-white px-5 py-2.5 rounded-xl
                  text-sm font-medium hover:bg-yellow-600 transition">
            Complete Profile to Post
        </a>
    @endif
@endauth
    </div>

    <!-- Category Filter -->
    <div class="flex gap-2 flex-wrap mb-6">
        <a href="{{ route('posts.index') }}"
           class="px-3 py-1.5 rounded-full text-xs font-medium transition
                  {{ !request('category')
                     ? 'bg-blue-700 text-white'
                     : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            All
        </a>
        @foreach($categories as $key => $label)
            <a href="{{ route('posts.index', ['category' => $key]) }}"
               class="px-3 py-1.5 rounded-full text-xs font-medium transition
                      {{ request('category') === $key
                         ? 'bg-blue-700 text-white'
                         : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <!-- Posts Feed -->
    @forelse($posts as $post)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm
                    hover:border-blue-200 transition mb-4 p-6">

            <!-- Post Header -->
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-700
                                font-bold flex items-center justify-center text-sm flex-shrink-0">
                        {{ strtoupper(substr($post->user->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <p class="text-sm font-semibold text-gray-800">
                                {{ $post->user->name }}
                            </p>
                            @if($post->user->is_verified)
                                <span class="text-blue-500 text-xs" title="Verified Alumni">✔</span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-400">
                            {{ $post->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
                <span class="text-xs font-medium px-2.5 py-1 rounded-full
                             {{ $post->category_color }}">
                    {{ $post->category_label }}
                </span>
            </div>

            <!-- Post Content -->
            <a href="{{ route('posts.show', $post) }}" class="block group">
                <h2 class="font-semibold text-gray-800 mb-2
                           group-hover:text-blue-700 transition">
                    {{ $post->title }}
                </h2>
                <p class="text-gray-600 text-sm leading-relaxed">
                    {{ Str::limit($post->body, 200) }}
                </p>
            </a>

            <!-- Post Footer -->
            <div class="flex items-center gap-4 mt-4 pt-4 border-t border-gray-50">
                <a href="{{ route('posts.show', $post) }}"
                   class="text-xs text-gray-400 hover:text-blue-600 transition">
                    💬 {{ $post->comments_count }}
                    {{ Str::plural('comment', $post->comments_count) }}
                </a>

                @auth
                    @if($post->user_id !== Auth::id())
                        <button onclick="document.getElementById('flag-{{ $post->id }}').classList.toggle('hidden')"
                                class="text-xs text-gray-400 hover:text-red-500 transition ml-auto">
                            🚩 Report
                        </button>
                    @else
                        <div class="ml-auto flex gap-3">
                            <a href="{{ route('posts.edit', $post) }}"
                               class="text-xs text-gray-400 hover:text-blue-600">Edit</a>
                            <form method="POST" action="{{ route('posts.destroy', $post) }}"
                                  onsubmit="return confirm('Delete this post?')">
                                @csrf @method('DELETE')
                                <button class="text-xs text-gray-400 hover:text-red-500">
                                    Delete
                                </button>
                            </form>
                        </div>
                    @endif
                @endauth
            </div>

            <!-- Flag form (hidden by default) -->
            @auth
                @if($post->user_id !== Auth::id())
                <div id="flag-{{ $post->id }}" class="hidden mt-3">
                    <form method="POST" action="{{ route('posts.flag', $post) }}"
                          class="bg-red-50 border border-red-100 rounded-xl p-4">
                        @csrf
                        <p class="text-xs font-semibold text-red-600 mb-2">
                            Report this post
                        </p>
                        <select name="reason"
                                class="w-full border border-red-200 rounded-lg px-3
                                       py-2 text-xs mb-2 focus:outline-none">
                            @foreach(\App\Models\PostFlag::REASONS as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="details"
                               placeholder="Additional details (optional)"
                               class="w-full border border-red-200 rounded-lg px-3
                                      py-2 text-xs mb-2 focus:outline-none">
                        <button type="submit"
                                class="bg-red-500 text-white px-4 py-1.5 rounded-lg
                                       text-xs hover:bg-red-600">
                            Submit Report
                        </button>
                    </form>
                </div>
                @endif
            @endauth

        </div>
    @empty
        <div class="text-center py-16">
            <p class="text-5xl mb-3">📝</p>
            <p class="text-gray-500 font-medium">No posts yet.</p>
            @auth
                @if(Auth::user()->is_verified)
                    <a href="{{ route('posts.create') }}"
                       class="inline-block mt-4 bg-blue-700 text-white px-5 py-2
                              rounded-xl text-sm hover:bg-blue-800">
                        Be the first to post
                    </a>
                @endif
            @endauth
        </div>
    @endforelse

    <div class="mt-6">{{ $posts->withQueryString()->links() }}</div>

</div>
@endsection