@extends('layouts.app')

@section('content')

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Alumni Directory</h1>
        <p class="text-gray-500 text-sm">{{ $alumni->total() }} alumni registered</p>
    </div>

    <!-- Search Bar -->
    <form method="GET" action="{{ route('alumni.index') }}" class="mb-6">
        <div class="flex gap-2">
            <input type="text" name="search"
                   value="{{ request('search') }}"
                   placeholder="Search by name, course, company, graduation year..."
                   class="flex-1 border border-gray-300 rounded-lg px-4 py-2
                          text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit"
                    class="bg-blue-700 text-white px-5 py-2 rounded-lg
                           text-sm hover:bg-blue-800">
                Search
            </button>
            @if(request('search'))
                <a href="{{ route('alumni.index') }}"
                   class="border border-gray-300 text-gray-600 px-4 py-2 rounded-lg
                          text-sm hover:bg-gray-50">
                    Clear
                </a>
            @endif
        </div>
    </form>

    <!-- Alumni Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @forelse($alumni as $alumnus)
            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3 mb-3">
                    <div class="flex-shrink-0">
                        @if($alumnus->alumniProfile && $alumnus->alumniProfile->profile_photo)
                            <img src="{{ Storage::url($alumnus->alumniProfile->profile_photo) }}"
                                 class="w-12 h-12 rounded-full object-cover"
                                 alt="{{ $alumnus->name }}">
                        @else
                            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center
                                        justify-center font-bold text-blue-700 text-lg">
                                {{ strtoupper(substr($alumnus->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800 text-sm">
                            {{ $alumnus->name }}
                        </p>
                        @if($alumnus->alumniProfile)
                            <p class="text-xs text-gray-500">
                                Class of {{ $alumnus->alumniProfile->graduation_year }}
                            </p>
                        @endif
                    </div>
                </div>

                @if($alumnus->alumniProfile)
                    <div class="text-sm text-gray-600 space-y-1">
                        @if($alumnus->alumniProfile->course)
                            <p>🎓 {{ $alumnus->alumniProfile->course }}</p>
                        @endif
                        @if($alumnus->alumniProfile->current_job)
                            <p>💼 {{ $alumnus->alumniProfile->current_job }}
                               @if($alumnus->alumniProfile->company)
                                   at {{ $alumnus->alumniProfile->company }}
                               @endif
                            </p>
                        @endif
                    </div>
                    @if($alumnus->alumniProfile->skills)
                        <div class="flex flex-wrap gap-1 mt-2">
                            @foreach(array_slice(explode(',', $alumnus->alumniProfile->skills), 0, 3) as $skill)
                                @if(trim($skill))
                                    <span class="bg-blue-50 text-blue-600 text-xs px-2 py-0.5
                                                 rounded-full">
                                        {{ trim($skill) }}
                                    </span>
                                @endif
                            @endforeach
                        </div>
                    @endif
                @else
                    <p class="text-xs text-gray-400 italic">Profile not completed</p>
                @endif
            </div>
        @empty
            <div class="col-span-3 text-center py-12 text-gray-400">
                <p class="text-lg">No alumni found.</p>
                @if(request('search'))
                    <p class="text-sm mt-1">Try a different search term.</p>
                @endif
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $alumni->withQueryString()->links() }}
    </div>

@endsection