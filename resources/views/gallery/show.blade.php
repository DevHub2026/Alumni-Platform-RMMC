@extends('layouts.app')

@section('content')

    <a href="{{ route('gallery.index') }}"
       class="text-sm text-blue-600 hover:underline mb-4 inline-block">
        ← Back to Gallery
    </a>

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ $event->title }}</h1>
            <p class="text-sm text-gray-400 mt-1">
                {{ $photos->count() }} photos •
                {{ $event->event_date->format('F d, Y') }}
            </p>
        </div>
    </div>

    @forelse($photos as $photo)
        @if($loop->first)
            <div class="columns-1 md:columns-3 gap-3 space-y-3">
        @endif

        <div class="break-inside-avoid mb-3">
            <img src="{{ Storage::url($photo->image_path) }}"
                 class="w-full rounded-xl object-cover hover:opacity-90 transition"
                 alt="{{ $photo->caption }}">
            @if($photo->caption)
                <p class="text-xs text-gray-400 mt-1 px-1">{{ $photo->caption }}</p>
            @endif
        </div>

        @if($loop->last)
            </div>
        @endif
    @empty
        <div class="text-center py-16 text-gray-400">
            <p class="text-5xl mb-3">🖼️</p>
            <p>No photos in this gallery yet.</p>
        </div>
    @endforelse

@endsection