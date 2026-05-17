@extends('layouts.app')

@section('content')

    <div class="max-w-3xl mx-auto">

        <!-- Profile Hero -->
        <div class="bg-gradient-to-r from-blue-700 to-indigo-600 rounded-2xl p-8 mb-6 text-white">
            <div class="flex items-center gap-6">
                <div class="w-20 h-20 rounded-full bg-white bg-opacity-20 border-2
                            border-white border-opacity-40 flex items-center justify-center
                            text-3xl font-bold text-white flex-shrink-0">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="flex-1">
                    <h1 class="text-2xl font-bold">{{ $user->name }}</h1>
                    <p class="text-blue-200 text-sm">{{ $user->email }}</p>
                    @if($profile && $profile->course)
                        <div class="flex items-center gap-2 mt-2 flex-wrap">
                            <span class="bg-white bg-opacity-20 text-white text-xs
                                         px-3 py-1 rounded-full">
                                🎓 {{ $profile->course }}
                            </span>
                            @if($profile->graduation_year)
                                <span class="bg-white bg-opacity-20 text-white text-xs
                                             px-3 py-1 rounded-full">
                                    Class of {{ $profile->graduation_year }}
                                </span>
                            @endif
                        </div>
                    @endif
                </div>
                <a href="{{ route('profile.edit') }}"
                   class="bg-white text-blue-700 font-semibold px-5 py-2 rounded-xl
                          text-sm hover:bg-blue-50 transition flex-shrink-0">
                    Edit Profile
                </a>
            </div>
        </div>

        @if($profile)

            <!-- Current Work -->
            @if($profile->current_job || $profile->company)
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-4">
                    <h2 class="text-xs font-semibold text-gray-400 uppercase
                               tracking-wide mb-4">Current Work</h2>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center
                                    justify-center text-2xl">
                            💼
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">
                                {{ $profile->current_job ?? 'Not specified' }}
                            </p>
                            <p class="text-gray-500 text-sm">
                                {{ $profile->company ?? '' }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Details Grid -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-4">
                <h2 class="text-xs font-semibold text-gray-400 uppercase
                           tracking-wide mb-4">Profile Details</h2>
                <div class="grid grid-cols-2 gap-4">

                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-xs text-gray-400 mb-1">Student ID</p>
                        <p class="text-sm font-medium text-gray-700">
                            {{ $profile->student_id ?? '—' }}
                        </p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-xs text-gray-400 mb-1">Phone</p>
                        <p class="text-sm font-medium text-gray-700">
                            {{ $profile->phone ?? '—' }}
                        </p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4 col-span-2">
                        <p class="text-xs text-gray-400 mb-1">Address</p>
                        <p class="text-sm font-medium text-gray-700">
                            {{ $profile->address ?? '—' }}
                        </p>
                    </div>

                    @if($profile->bio)
                        <div class="bg-gray-50 rounded-xl p-4 col-span-2">
                            <p class="text-xs text-gray-400 mb-1">Bio</p>
                            <p class="text-sm text-gray-700 leading-relaxed">
                                {{ $profile->bio }}
                            </p>
                        </div>
                    @endif

                </div>
            </div>

            <!-- LinkedIn -->
            @if($profile->linkedin_url)
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <a href="{{ $profile->linkedin_url }}" target="_blank"
                       class="flex items-center gap-3 text-blue-700 hover:text-blue-800 transition">
                        <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center
                                    justify-center text-xl">
                            🔗
                        </div>
                        <div>
                            <p class="text-sm font-semibold">LinkedIn Profile</p>
                            <p class="text-xs text-gray-400 truncate max-w-xs">
                                {{ $profile->linkedin_url }}
                            </p>
                        </div>
                        <span class="ml-auto text-gray-300">→</span>
                    </a>
                </div>
            @endif

        @else
            <!-- Empty state -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-10 text-center">
                <p class="text-5xl mb-4">📝</p>
                <p class="text-yellow-800 font-semibold mb-1">
                    Your profile is empty
                </p>
                <p class="text-yellow-600 text-sm mb-5">
                    Fill in your details so other alumni can find and connect with you.
                </p>
                <a href="{{ route('profile.edit') }}"
                   class="inline-block bg-blue-700 text-white px-6 py-2.5 rounded-xl
                          text-sm font-medium hover:bg-blue-800 transition">
                    Complete Your Profile
                </a>
            </div>
        @endif

    </div>

@endsection