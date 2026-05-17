@extends('layouts.app')

@section('content')

    <div class="max-w-3xl mx-auto">

        <a href="{{ route('events.index') }}"
           class="inline-flex items-center gap-1 text-sm text-blue-600
                  hover:underline mb-6">
            ← Back to Events
        </a>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

            <!-- Cover -->
            @if($event->cover_image)
                <img src="{{ Storage::url($event->cover_image) }}"
                     class="w-full h-64 object-cover" alt="">
            @else
                <div class="w-full h-48 bg-gradient-to-r from-indigo-600 to-blue-400
                            flex items-center justify-center text-7xl">
                    🎉
                </div>
            @endif

            <div class="p-8">

                <h1 class="text-2xl font-bold text-gray-800 mb-4 leading-snug">
                    {{ $event->title }}
                </h1>

                <!-- Info cards -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
                    <div class="bg-blue-50 rounded-xl p-3 text-center">
                        <p class="text-xs text-blue-500 mb-1">Date</p>
                        <p class="text-sm font-semibold text-blue-800">
                            {{ $event->event_date->format('M d, Y') }}
                        </p>
                    </div>
                    <div class="bg-blue-50 rounded-xl p-3 text-center">
                        <p class="text-xs text-blue-500 mb-1">Time</p>
                        <p class="text-sm font-semibold text-blue-800">
                            {{ $event->event_date->format('g:i A') }}
                        </p>
                    </div>
                    <div class="bg-blue-50 rounded-xl p-3 text-center">
                        <p class="text-xs text-blue-500 mb-1">Location</p>
                        <p class="text-sm font-semibold text-blue-800 truncate">
                            {{ $event->location }}
                        </p>
                    </div>
                    <div class="bg-blue-50 rounded-xl p-3 text-center">
                        <p class="text-xs text-blue-500 mb-1">Registered</p>
                        <p class="text-sm font-semibold text-blue-800">
                            {{ $registrationCount }}
                            @if($event->slots > 0)
                                / {{ $event->slots }}
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Slot bar -->
                @if($event->slots > 0)
                    @php
                        $percent = min(100, round(($registrationCount / $event->slots) * 100));
                        $isFull  = $registrationCount >= $event->slots;
                    @endphp
                    <div class="mb-6">
                        <div class="flex justify-between text-xs text-gray-400 mb-1">
                            <span>{{ $registrationCount }} / {{ $event->slots }} slots filled</span>
                            <span>{{ $percent }}%</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2">
                            <div class="h-2 rounded-full {{ $isFull ? 'bg-red-400' : 'bg-blue-500' }}"
                                 style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                @endif

                <!-- Description -->
                <div class="border-t border-gray-100 pt-6 mb-6">
                    <h2 class="text-sm font-semibold text-gray-600 mb-3 uppercase tracking-wide">
                        About this Event
                    </h2>
                    <p class="text-gray-700 text-sm leading-relaxed whitespace-pre-line">
                        {{ $event->description }}
                    </p>
                </div>

                <!-- Registration -->
                <div class="border-t border-gray-100 pt-6">
                    @auth
                        @if($isRegistered)
                            <div class="flex items-center justify-between bg-green-50
                                        border border-green-200 rounded-xl px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="text-2xl">✅</span>
                                    <div>
                                        <p class="text-sm font-semibold text-green-700">
                                            You're registered!
                                        </p>
                                        <p class="text-xs text-green-600">
                                            We'll see you at the event.
                                        </p>
                                    </div>
                                </div>
                                <form method="POST"
                                      action="{{ route('events.unregister', $event) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-xs text-red-400 hover:text-red-600
                                                   hover:underline">
                                        Cancel registration
                                    </button>
                                </form>
                            </div>
                        @else
                            @php
                                $isFull = isset($isFull)
                                          ? $isFull
                                          : ($event->slots > 0 &&
                                             $registrationCount >= $event->slots);
                            @endphp
                            @if($isFull)
                                <div class="bg-red-50 border border-red-200 rounded-xl
                                            px-5 py-4 text-center">
                                    <p class="text-red-600 font-semibold text-sm">
                                        😔 This event is fully booked.
                                    </p>
                                    <p class="text-red-400 text-xs mt-1">
                                        Check other upcoming events.
                                    </p>
                                </div>
                            @else
                                <div class="flex items-center justify-between bg-blue-50
                                            border border-blue-200 rounded-xl px-5 py-4">
                                    <div>
                                        <p class="text-sm font-semibold text-blue-700">
                                            Ready to join?
                                        </p>
                                        <p class="text-xs text-blue-500 mt-0.5">
                                            Secure your spot now.
                                        </p>
                                    </div>
                                    <form method="POST"
                                          action="{{ route('events.register', $event) }}">
                                        @csrf
                                        <button type="submit"
                                                class="bg-blue-700 hover:bg-blue-800 text-white
                                                       px-5 py-2 rounded-lg text-sm
                                                       font-medium transition">
                                            Register Now
                                        </button>
                                    </form>
                                </div>
                            @endif
                        @endif
                    @else
                        <div class="bg-gray-50 border border-gray-200 rounded-xl
                                    px-5 py-4 flex items-center justify-between">
                            <p class="text-sm text-gray-600">
                                Log in to register for this event.
                            </p>
                            <a href="{{ route('login') }}"
                               class="bg-blue-700 text-white px-5 py-2 rounded-lg
                                      text-sm hover:bg-blue-800 transition">
                                Log In
                            </a>
                        </div>
                    @endauth
                </div>

            </div>
        </div>

        <!-- Gallery Preview -->
        @if(isset($event->galleries) && $event->galleries->count() > 0)
            <div class="mt-8">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-bold text-gray-800">📸 Event Gallery</h2>
                    <a href="{{ route('gallery.show', $event) }}"
                       class="text-sm text-blue-600 hover:underline">
                        View all photos →
                    </a>
                </div>
                <div class="grid grid-cols-3 gap-3">
                    @foreach($event->galleries->take(6) as $photo)
                        <img src="{{ Storage::url($photo->image_path) }}"
                             class="w-full h-28 object-cover rounded-xl
                                    hover:opacity-90 transition" alt="">
                    @endforeach
                </div>
            </div>
        @endif

    </div>

@endsection