@extends('layouts.app')

@section('content')

    <h1 class="text-2xl font-bold text-gray-800 mb-6">Gallery</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        @forelse($events as $event)
            <a href="{{ route('gallery.show', $event) }}"
               class="bg-white rounded-xl shadow-sm border border-gray-100
                      hover:border-blue-300 transition block overflow-hidden group">

                @php
                    $cover = $event->galleries->first();
                @endphp

                @if($cover)
                    <div class="overflow-hidden">
                        <img src="{{ Storage::url($cover->image_path) }}"
                             class="w-full h-40 object-cover group-hover:scale-105
                                    transition duration-300" alt="">
                    </div>
                @else
                    <div class="w-full h-40 bg-blue-50 flex items-center
                                justify-center text-4xl">
                        🖼️
                    </div>
                @endif

                <div class="p-4">
                    <h2 class="font-semibold text-gray-800 text-sm">
                        {{ $event->title }}
                    </h2>
                    <p class="text-xs text-gray-400 mt-1">
                        {{ $event->galleries_count }} photos •
                        {{ $event->event_date->format('M d, Y') }}
                    </p>
                </div>
            </a>
        @empty
            <div class="col-span-3 text-center py-16 text-gray-400">
                <p class="text-5xl mb-3">🖼️</p>
                <p>No gallery photos yet.</p>
            </div>
        @endforelse
    </div>

@endsection