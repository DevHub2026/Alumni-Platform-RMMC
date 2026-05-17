@extends('layouts.app')

@section('content')

    <div class="max-w-3xl mx-auto">

        <a href="{{ route('announcements.index') }}"
           class="inline-flex items-center gap-1 text-sm text-blue-600
                  hover:underline mb-6">
            ← Back to Announcements
        </a>

        <article class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

            <!-- Cover -->
            @if($announcement->cover_image)
                <img src="{{ Storage::url($announcement->cover_image) }}"
                     class="w-full h-64 object-cover" alt="">
            @else
                <div class="w-full h-40 bg-gradient-to-r from-blue-600 to-blue-400
                            flex items-center justify-center text-6xl">
                    📢
                </div>
            @endif

            <div class="p-8">

                <!-- Meta -->
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-700
                                font-bold flex items-center justify-center text-sm">
                        {{ strtoupper(substr($announcement->user->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-700">
                            {{ $announcement->user->name }}
                        </p>
                        <p class="text-xs text-gray-400">
                            {{ $announcement->created_at->format('F d, Y • g:i A') }}
                        </p>
                    </div>
                </div>

                <h1 class="text-2xl font-bold text-gray-800 mb-6 leading-snug">
                    {{ $announcement->title }}
                </h1>

                <!-- Divider -->
                <div class="border-t border-gray-100 mb-6"></div>

                <!-- Content -->
                <div class="text-gray-700 text-sm leading-relaxed whitespace-pre-line">
                    {!! $announcement->content !!}
                </div>

            </div>
        </article>

        <!-- Back button bottom -->
        <div class="mt-6 text-center">
            <a href="{{ route('announcements.index') }}"
               class="inline-block border border-gray-200 text-gray-600 px-6 py-2
                      rounded-lg text-sm hover:bg-gray-50 transition">
                ← View All Announcements
            </a>
        </div>

    </div>

@endsection