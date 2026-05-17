@extends('layouts.app')

@section('content')

    <!-- HERO SECTION -->
    <div class="bg-blue-700 rounded-2xl text-white px-10 py-16 mb-10 text-center">
        <h1 class="text-4xl font-bold mb-3">Welcome to the Alumni Platform</h1>
        <p class="text-blue-100 text-lg mb-6">
            Stay connected with your school community, discover events,
            and reconnect with fellow alumni.
        </p>
        @guest
    <div class="flex justify-center gap-4 mt-6">
        <a href="{{ route('register') }}"
           class="bg-white text-blue-700 font-semibold px-6 py-3 rounded-lg hover:bg-blue-50">
            Join Now
        </a>
        <a href="{{ route('login') }}"
           class="border-2 border-white text-white font-semibold px-6 py-3 rounded-lg hover:bg-blue-600">
            Log In
        </a>
    </div>
@endguest
    </div>

   <!-- STATS ROW -->
<div class="grid grid-cols-3 gap-6 mb-10">
    <div class="bg-white rounded-xl p-6 text-center shadow-sm border border-gray-100">
        <p class="text-3xl font-bold text-blue-700">{{ $alumniCount }}</p>
        <p class="text-gray-500 text-sm mt-1">Registered Alumni</p>
    </div>
    <div class="bg-white rounded-xl p-6 text-center shadow-sm border border-gray-100">
        <p class="text-3xl font-bold text-blue-700">{{ $upcomingEvents->count() }}</p>
        <p class="text-gray-500 text-sm mt-1">Upcoming Events</p>
    </div>
    <div class="bg-white rounded-xl p-6 text-center shadow-sm border border-gray-100">
        <p class="text-3xl font-bold text-blue-700">{{ $latestAnnouncements->count() }}</p>
        <p class="text-gray-500 text-sm mt-1">Latest Announcements</p>
    </div>
</div>

    <!-- LATEST ANNOUNCEMENTS -->
    <div class="mb-10">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-800">Latest Announcements</h2>
            <a href="{{ route('announcements.index') }}"
               class="text-sm text-blue-700 hover:underline">View all →</a>
        </div>

        @forelse($latestAnnouncements as $announcement)
            <a href="{{ route('announcements.show', $announcement) }}"
               class="block bg-white rounded-xl p-5 mb-3 shadow-sm border border-gray-100
                      hover:border-blue-300 transition">
                <h3 class="font-semibold text-gray-800">{{ $announcement->title }}</h3>
                <p class="text-gray-500 text-sm mt-1">
                    {{ Str::limit(strip_tags($announcement->content), 100) }}
                </p>
                <p class="text-xs text-gray-400 mt-2">
                    {{ $announcement->created_at->diffForHumans() }}
                </p>
            </a>
        @empty
            <p class="text-gray-400 text-sm">No announcements yet.</p>
        @endforelse
    </div>

    <!-- UPCOMING EVENTS -->
    <div>
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-800">Upcoming Events</h2>
            <a href="{{ route('events.index') }}"
               class="text-sm text-blue-700 hover:underline">View all →</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @forelse($upcomingEvents as $event)
                <a href="{{ route('events.show', $event) }}"
                   class="block bg-white rounded-xl p-5 shadow-sm border border-gray-100
                          hover:border-blue-300 transition">
                    <p class="text-xs font-semibold text-blue-600 uppercase mb-1">
                        {{ $event->event_date->format('M d, Y') }}
                    </p>
                    <h3 class="font-semibold text-gray-800">{{ $event->title }}</h3>
                    <p class="text-gray-500 text-sm mt-1">📍 {{ $event->location }}</p>
                </a>
            @empty
                <p class="text-gray-400 text-sm col-span-3">No upcoming events.</p>
            @endforelse
        </div>
    </div>

@endsection