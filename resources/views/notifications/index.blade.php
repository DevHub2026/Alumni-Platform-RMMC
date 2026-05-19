@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Notifications</h1>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        @forelse($notifications as $notification)
            <a href="/posts/{{ $notification->data['post_id'] ?? '#' }}"
               class="flex items-start gap-4 px-6 py-4 hover:bg-gray-50
                      transition border-b border-gray-50 last:border-0 block
                      {{ !$notification->read_at ? 'bg-blue-50' : '' }}">

                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center
                            justify-center text-blue-600 flex-shrink-0">
                    💬
                </div>
                <div class="flex-1">
                    <p class="text-sm text-gray-700">
                        {{ $notification->data['message'] ?? 'New notification' }}
                    </p>
                    <p class="text-xs text-gray-400 mt-1">
                        {{ $notification->created_at->diffForHumans() }}
                    </p>
                </div>

                @if(!$notification->read_at)
                    <div class="w-2 h-2 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                @endif
            </a>
        @empty
            <div class="text-center py-16">
                <p class="text-4xl mb-3">🔔</p>
                <p class="text-gray-500 font-medium">No notifications yet.</p>
                <p class="text-gray-400 text-sm mt-1">
                    You'll be notified when someone comments on your posts.
                </p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $notifications->links() }}</div>

</div>
@endsection