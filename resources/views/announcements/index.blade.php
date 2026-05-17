@extends('layouts.app')

@section('content')

    <!-- Page Header -->
    <div class="bg-gradient-to-r from-blue-700 to-blue-500 rounded-2xl px-8 py-10 mb-8 text-white">
        <h1 class="text-3xl font-bold mb-1">Announcements</h1>
        <p class="text-blue-100 text-sm">Stay updated with the latest news from your alumni community.</p>
    </div>

    @if($announcements->isEmpty())
        <div class="text-center py-20">
            <p class="text-6xl mb-4">📢</p>
            <p class="text-gray-500 font-medium">No announcements yet.</p>
            <p class="text-gray-400 text-sm mt-1">Check back soon for updates.</p>
        </div>
    @else

        <!-- Featured (first announcement) -->
        @php $featured = $announcements->first(); @endphp
        <a href="{{ route('announcements.show', $featured) }}"
           class="group block bg-white rounded-2xl border border-gray-100 shadow-sm
                  hover:border-blue-300 hover:shadow-md transition mb-6 overflow-hidden">
            <div class="md:flex">
                <div class="md:w-2/5 bg-blue-50 h-52 md:h-auto flex items-center
                            justify-center text-7xl rounded-l-2xl">
                    @if($featured->cover_image)
                        <img src="{{ Storage::url($featured->cover_image) }}"
                             class="w-full h-full object-cover" alt="">
                    @else
                        📢
                    @endif
                </div>
                <div class="p-6 md:w-3/5 flex flex-col justify-center">
                    <span class="text-xs font-semibold text-blue-600 uppercase
                                 tracking-wide mb-2">Latest</span>
                    <h2 class="text-xl font-bold text-gray-800 mb-2 group-hover:text-blue-700
                               transition">
                        {{ $featured->title }}
                    </h2>
                    <p class="text-gray-500 text-sm leading-relaxed mb-4">
                        {{ Str::limit($featured->content, 160) }}
                    </p>
                    <div class="flex items-center gap-3 text-xs text-gray-400">
                        <span class="w-7 h-7 rounded-full bg-blue-100 text-blue-700
                                     font-bold flex items-center justify-center">
                            {{ strtoupper(substr($featured->user->name, 0, 1)) }}
                        </span>
                        {{ $featured->user->name }} •
                        {{ $featured->created_at->format('M d, Y') }}
                    </div>
                </div>
            </div>
        </a>

        <!-- Rest of announcements grid -->
        @if($announcements->count() > 1)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            @foreach($announcements->skip(1) as $announcement)
                <a href="{{ route('announcements.show', $announcement) }}"
                   class="group bg-white rounded-xl border border-gray-100 shadow-sm
                          hover:border-blue-300 hover:shadow-md transition overflow-hidden block">

                    <div class="h-36 bg-blue-50 flex items-center justify-center text-4xl">
                        @if($announcement->cover_image)
                            <img src="{{ Storage::url($announcement->cover_image) }}"
                                 class="w-full h-full object-cover" alt="">
                        @else
                            📢
                        @endif
                    </div>

                    <div class="p-4">
                        <h3 class="font-semibold text-gray-800 text-sm mb-2
                                   group-hover:text-blue-700 transition line-clamp-2">
                            {{ $announcement->title }}
                        </h3>
                        <p class="text-gray-400 text-xs leading-relaxed mb-3">
                            {{ Str::limit($announcement->content, 80) }}
                        </p>
                        <div class="flex items-center justify-between text-xs text-gray-400">
                            <span>{{ $announcement->user->name }}</span>
                            <span>{{ $announcement->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
        @endif

        <div class="mt-8">{{ $announcements->links() }}</div>

    @endif

@endsection