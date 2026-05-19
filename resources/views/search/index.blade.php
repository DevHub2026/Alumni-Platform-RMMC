@extends('layouts.app')

@section('content')

<div class="max-w-4xl mx-auto">

    <!-- Search Bar -->
    <div class="mb-8">
        <form method="GET" action="{{ route('search.index') }}">
            <div class="flex gap-2">
                <div class="relative flex-1">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                        🔍
                    </span>
                    <input type="text"
                           name="q"
                           value="{{ $query }}"
                           placeholder="Search alumni, posts, events, announcements..."
                           autofocus
                           class="w-full pl-10 pr-4 py-3 border border-gray-200
                                  rounded-xl text-sm bg-white focus:outline-none
                                  focus:ring-2 focus:ring-blue-500 shadow-sm">
                </div>
                <button type="submit"
                        class="bg-blue-700 text-white px-6 py-3 rounded-xl
                               text-sm font-medium hover:bg-blue-800 transition">
                    Search
                </button>
            </div>
        </form>
    </div>

    @if($query && strlen($query) >= 2)

        <!-- Results summary -->
        <div class="mb-6 flex items-center justify-between">
            <p class="text-sm text-gray-500">
                @if($total > 0)
                    Found <span class="font-semibold text-gray-800">{{ $total }}</span>
                    results for
                    <span class="font-semibold text-blue-700">"{{ $query }}"</span>
                @else
                    No results found for
                    <span class="font-semibold text-blue-700">"{{ $query }}"</span>
                @endif
            </p>
        </div>

        @if($total === 0)
            <div class="text-center py-16">
                <p class="text-5xl mb-4">🔍</p>
                <p class="text-gray-500 font-medium">No results found.</p>
                <p class="text-gray-400 text-sm mt-1">
                    Try different keywords or check your spelling.
                </p>
            </div>
        @endif

        <!-- Alumni Results -->
        @if($alumni->count() > 0)
        <div class="mb-8">
            <h2 class="text-sm font-semibold text-gray-500 uppercase
                       tracking-wide mb-3 flex items-center gap-2">
                👥 Alumni
                <span class="bg-blue-100 text-blue-700 text-xs px-2 py-0.5
                             rounded-full font-normal">
                    {{ $alumni->count() }}
                </span>
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($alumni as $alumnus)
                    <div class="bg-white rounded-xl border border-gray-100
                                shadow-sm p-4 flex items-center gap-3
                                hover:border-blue-200 transition">
                        @if($alumnus->alumniProfile?->profile_photo)
                            <img src="{{ Storage::url($alumnus->alumniProfile->profile_photo) }}"
                                 class="w-12 h-12 rounded-full object-cover flex-shrink-0"
                                 alt="{{ $alumnus->name }}">
                        @else
                            <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-700
                                        font-bold flex items-center justify-center
                                        flex-shrink-0">
                                {{ strtoupper(substr($alumnus->name, 0, 1)) }}
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-1.5">
                                <p class="font-semibold text-gray-800 text-sm truncate">
                                    {{ $alumnus->name }}
                                </p>
                                @if($alumnus->is_verified)
                                    <span class="text-blue-500 text-xs">✔</span>
                                @endif
                            </div>
                            @if($alumnus->alumniProfile)
                                <p class="text-xs text-gray-500 truncate">
                                    {{ $alumnus->alumniProfile->course }}
                                    @if($alumnus->alumniProfile->graduation_year)
                                        · Class of {{ $alumnus->alumniProfile->graduation_year }}
                                    @endif
                                </p>
                                @if($alumnus->alumniProfile->current_job)
                                    <p class="text-xs text-gray-400 truncate">
                                        💼 {{ $alumnus->alumniProfile->current_job }}
                                        @if($alumnus->alumniProfile->company)
                                            at {{ $alumnus->alumniProfile->company }}
                                        @endif
                                    </p>
                                @endif
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Posts Results -->
        @if($posts->count() > 0)
        <div class="mb-8">
            <h2 class="text-sm font-semibold text-gray-500 uppercase
                       tracking-wide mb-3 flex items-center gap-2">
                📝 Posts
                <span class="bg-blue-100 text-blue-700 text-xs px-2 py-0.5
                             rounded-full font-normal">
                    {{ $posts->count() }}
                </span>
            </h2>
            <div class="space-y-3">
                @foreach($posts as $post)
                    <a href="{{ route('posts.show', $post) }}"
                       class="block bg-white rounded-xl border border-gray-100
                              shadow-sm p-4 hover:border-blue-200 transition">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-7 h-7 rounded-full bg-blue-100 text-blue-700
                                        font-bold flex items-center justify-center
                                        text-xs flex-shrink-0">
                                {{ strtoupper(substr($post->user->name, 0, 1)) }}
                            </div>
                            <p class="text-xs text-gray-500">
                                {{ $post->user->name }} ·
                                {{ $post->created_at->diffForHumans() }}
                            </p>
                            <span class="text-xs px-2 py-0.5 rounded-full ml-auto
                                         {{ $post->category_color }}">
                                {{ $post->category_label }}
                            </span>
                        </div>
                        <p class="font-semibold text-gray-800 text-sm mb-1">
                            {{ $post->title }}
                        </p>
                        <p class="text-gray-500 text-xs">
                            {{ Str::limit(strip_tags($post->body), 120) }}
                        </p>
                    </a>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Events Results -->
        @if($events->count() > 0)
        <div class="mb-8">
            <h2 class="text-sm font-semibold text-gray-500 uppercase
                       tracking-wide mb-3 flex items-center gap-2">
                🎉 Events
                <span class="bg-blue-100 text-blue-700 text-xs px-2 py-0.5
                             rounded-full font-normal">
                    {{ $events->count() }}
                </span>
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($events as $event)
                    <a href="{{ route('events.show', $event) }}"
                       class="block bg-white rounded-xl border border-gray-100
                              shadow-sm p-4 hover:border-blue-200 transition">
                        <p class="text-xs font-semibold text-blue-600 mb-1">
                            {{ $event->event_date->format('M d, Y · g:i A') }}
                        </p>
                        <p class="font-semibold text-gray-800 text-sm mb-1">
                            {{ $event->title }}
                        </p>
                        <p class="text-xs text-gray-400">
                            📍 {{ $event->location }}
                        </p>
                    </a>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Announcements Results -->
        @if($announcements->count() > 0)
        <div class="mb-8">
            <h2 class="text-sm font-semibold text-gray-500 uppercase
                       tracking-wide mb-3 flex items-center gap-2">
                📢 Announcements
                <span class="bg-blue-100 text-blue-700 text-xs px-2 py-0.5
                             rounded-full font-normal">
                    {{ $announcements->count() }}
                </span>
            </h2>
            <div class="space-y-3">
                @foreach($announcements as $announcement)
                    <a href="{{ route('announcements.show', $announcement) }}"
                       class="block bg-white rounded-xl border border-gray-100
                              shadow-sm p-4 hover:border-blue-200 transition">
                        <p class="font-semibold text-gray-800 text-sm mb-1">
                            {{ $announcement->title }}
                        </p>
                        <p class="text-gray-500 text-xs">
                            {{ Str::limit(strip_tags($announcement->content), 120) }}
                        </p>
                        <p class="text-xs text-gray-400 mt-2">
                            {{ $announcement->created_at->format('M d, Y') }}
                        </p>
                    </a>
                @endforeach
            </div>
        </div>
        @endif

    @elseif($query && strlen($query) < 2)
        <div class="text-center py-16">
            <p class="text-gray-400 text-sm">
                Please enter at least 2 characters to search.
            </p>
        </div>
    @else
        <!-- Empty state - no query yet -->
        <div class="text-center py-16">
            <p class="text-5xl mb-4">🔍</p>
            <p class="text-gray-500 font-medium">Search the Alumni Platform</p>
            <p class="text-gray-400 text-sm mt-1">
                Find alumni, posts, events, and announcements.
            </p>
        </div>
    @endif

</div>
@endsection