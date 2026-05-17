@extends('layouts.app')

@section('content')

    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-700 to-blue-500 rounded-2xl px-8 py-10 mb-8 text-white">
        <h1 class="text-3xl font-bold mb-1">Upcoming Events</h1>
        <p class="text-indigo-100 text-sm">
            Join your alumni community — register for events and stay connected.
        </p>
    </div>

    @if($events->isEmpty())
        <div class="text-center py-20">
            <p class="text-6xl mb-4">🎉</p>
            <p class="text-gray-500 font-medium">No events scheduled yet.</p>
            <p class="text-gray-400 text-sm mt-1">Check back soon!</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            @foreach($events as $event)
                @php
                    $taken    = $event->registrations()->count();
                    $isFull   = $event->slots > 0 && $taken >= $event->slots;
                    $percent  = $event->slots > 0
                                ? min(100, round(($taken / $event->slots) * 100))
                                : 0;
                @endphp

                <a href="{{ route('events.show', $event) }}"
                   class="group bg-white rounded-xl border border-gray-100 shadow-sm
                          hover:border-blue-300 hover:shadow-md transition overflow-hidden block">

                    <!-- Cover -->
                    <div class="relative h-40 bg-indigo-50 flex items-center
                                justify-center text-4xl overflow-hidden">
                        @if($event->cover_image)
                            <img src="{{ Storage::url($event->cover_image) }}"
                                 class="w-full h-full object-cover
                                        group-hover:scale-105 transition duration-300"
                                 alt="">
                        @else
                            🎉
                        @endif

                        @if($isFull)
                            <span class="absolute top-3 right-3 bg-red-500 text-white
                                         text-xs font-semibold px-2 py-1 rounded-full">
                                Full
                            </span>
                        @else
                            <span class="absolute top-3 right-3 bg-green-500 text-white
                                         text-xs font-semibold px-2 py-1 rounded-full">
                                Open
                            </span>
                        @endif
                    </div>

                    <div class="p-4">
                        <!-- Date badge -->
                        <div class="flex items-center gap-2 mb-2">
                            <span class="bg-blue-50 text-blue-700 text-xs font-semibold
                                         px-2 py-1 rounded-md">
                                {{ $event->event_date->format('M d, Y') }}
                            </span>
                            <span class="text-gray-400 text-xs">
                                {{ $event->event_date->format('g:i A') }}
                            </span>
                        </div>

                        <h3 class="font-semibold text-gray-800 text-sm mb-1
                                   group-hover:text-blue-700 transition line-clamp-2">
                            {{ $event->title }}
                        </h3>

                        <p class="text-gray-400 text-xs mb-3">
                            📍 {{ $event->location }}
                        </p>

                        <!-- Slot progress -->
                        @if($event->slots > 0)
                            <div>
                                <div class="flex justify-between text-xs text-gray-400 mb-1">
                                    <span>{{ $taken }} registered</span>
                                    <span>{{ $event->slots }} slots</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-1.5">
                                    <div class="h-1.5 rounded-full transition-all
                                                {{ $isFull ? 'bg-red-400' : 'bg-blue-500' }}"
                                         style="width: {{ $percent }}%">
                                    </div>
                                </div>
                            </div>
                        @else
                            <p class="text-xs text-green-600 font-medium">
                                ✅ Unlimited slots
                            </p>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-8">{{ $events->links() }}</div>
    @endif

@endsection