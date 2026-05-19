@extends('layouts.app')

@section('content')

    <div class="max-w-3xl mx-auto">

        <!-- Profile Hero -->
        <div class="bg-gradient-to-r from-blue-700 to-indigo-600 rounded-2xl p-8 mb-6 text-white">
            <div class="flex items-center gap-6">
                <div class="flex-shrink-0">
                    @if($profile && $profile->profile_photo)
                        <img src="{{ Storage::url($profile->profile_photo) }}"
                             class="w-20 h-20 rounded-full object-cover border-2
                                    border-white border-opacity-40"
                             alt="{{ $user->name }}">
                    @else
                        <div class="w-20 h-20 rounded-full bg-white bg-opacity-20 border-2
                                    border-white border-opacity-40 flex items-center justify-center
                                    text-3xl font-bold text-white">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
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

            <!-- Skills -->
            @if($profile->skills)
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-4">
                    <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">
                        Skills
                    </h2>
                    <div class="flex flex-wrap gap-2">
                        @foreach(explode(',', $profile->skills) as $skill)
                            @if(trim($skill))
                                <span class="bg-blue-50 text-blue-700 text-xs font-medium
                                             px-3 py-1.5 rounded-full border border-blue-100">
                                    {{ trim($skill) }}
                                </span>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Portfolio + LinkedIn -->
            @if($profile->linkedin_url || $profile->portfolio_url)
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-4">
                    <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">
                        Links
                    </h2>
                    <div class="space-y-2">
                        @if($profile->linkedin_url)
                            <a href="{{ $profile->linkedin_url }}" target="_blank"
                               class="flex items-center gap-3 text-blue-700
                                      hover:text-blue-800 transition group">
                                <div class="w-9 h-9 bg-blue-50 rounded-xl flex items-center
                                            justify-center text-lg group-hover:bg-blue-100
                                            transition flex-shrink-0">
                                    🔗
                                </div>
                                <div>
                                    <p class="text-sm font-semibold">LinkedIn</p>
                                    <p class="text-xs text-gray-400 truncate max-w-xs">
                                        {{ $profile->linkedin_url }}
                                    </p>
                                </div>
                            </a>
                        @endif

                        @if($profile->portfolio_url)
                            <a href="{{ $profile->portfolio_url }}" target="_blank"
                               class="flex items-center gap-3 text-purple-700
                                      hover:text-purple-800 transition group">
                                <div class="w-9 h-9 bg-purple-50 rounded-xl flex items-center
                                            justify-center text-lg group-hover:bg-purple-100
                                            transition flex-shrink-0">
                                    🌐
                                </div>
                                <div>
                                    <p class="text-sm font-semibold">Portfolio</p>
                                    <p class="text-xs text-gray-400 truncate max-w-xs">
                                        {{ $profile->portfolio_url }}
                                    </p>
                                </div>
                            </a>
                        @endif
                    </div>
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