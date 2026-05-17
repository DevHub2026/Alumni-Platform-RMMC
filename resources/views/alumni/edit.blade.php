@extends('layouts.app')

@section('content')

    <div class="max-w-2xl mx-auto">

        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('profile.show') }}"
               class="text-sm text-blue-600 hover:underline">← Back to Profile</a>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

            <!-- Form Header -->
            <div class="bg-gradient-to-r from-blue-700 to-indigo-600 px-8 py-6">
                <h1 class="text-xl font-bold text-white">Edit Your Profile</h1>
                <p class="text-blue-200 text-sm mt-1">
                    Keep your information up to date so alumni can connect with you.
                </p>
            </div>

            <form method="POST" action="{{ route('profile.update') }}" class="p-8">
                @csrf
                @method('PUT')

                <!-- Section: Academic Info -->
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">
                    Academic Information
                </p>
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Student ID
                        </label>
                        <input type="text" name="student_id"
                               value="{{ old('student_id', $profile->student_id) }}"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5
                                      text-sm focus:outline-none focus:ring-2
                                      focus:ring-blue-500 bg-gray-50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Course / Program
                        </label>
                        <input type="text" name="course"
                               value="{{ old('course', $profile->course) }}"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5
                                      text-sm focus:outline-none focus:ring-2
                                      focus:ring-blue-500 bg-gray-50">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Graduation Year
                        </label>
                        <input type="number" name="graduation_year"
                               value="{{ old('graduation_year', $profile->graduation_year) }}"
                               min="1990" max="{{ date('Y') }}"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5
                                      text-sm focus:outline-none focus:ring-2
                                      focus:ring-blue-500 bg-gray-50">
                    </div>
                </div>

                <!-- Section: Contact Info -->
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">
                    Contact Information
                </p>
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Phone Number
                        </label>
                        <input type="text" name="phone"
                               value="{{ old('phone', $profile->phone) }}"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5
                                      text-sm focus:outline-none focus:ring-2
                                      focus:ring-blue-500 bg-gray-50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            LinkedIn URL
                        </label>
                        <input type="url" name="linkedin_url"
                               value="{{ old('linkedin_url', $profile->linkedin_url) }}"
                               placeholder="https://linkedin.com/in/yourname"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5
                                      text-sm focus:outline-none focus:ring-2
                                      focus:ring-blue-500 bg-gray-50">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Address
                        </label>
                        <input type="text" name="address"
                               value="{{ old('address', $profile->address) }}"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5
                                      text-sm focus:outline-none focus:ring-2
                                      focus:ring-blue-500 bg-gray-50">
                    </div>
                </div>

                <!-- Section: Professional Info -->
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">
                    Professional Information
                </p>
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Current Job Title
                        </label>
                        <input type="text" name="current_job"
                               value="{{ old('current_job', $profile->current_job) }}"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5
                                      text-sm focus:outline-none focus:ring-2
                                      focus:ring-blue-500 bg-gray-50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Company / Employer
                        </label>
                        <input type="text" name="company"
                               value="{{ old('company', $profile->company) }}"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5
                                      text-sm focus:outline-none focus:ring-2
                                      focus:ring-blue-500 bg-gray-50">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Bio
                        </label>
                        <textarea name="bio" rows="4"
                                  placeholder="Tell other alumni a little about yourself..."
                                  class="w-full border border-gray-200 rounded-xl px-4 py-2.5
                                         text-sm focus:outline-none focus:ring-2
                                         focus:ring-blue-500 bg-gray-50 resize-none">{{ old('bio', $profile->bio) }}</textarea>
                    </div>
                </div>

                <!-- Validation Errors -->
                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-4">
                        @foreach($errors->all() as $error)
                            <p class="text-red-600 text-sm">• {{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <!-- Actions -->
                <div class="flex gap-3 pt-2">
                    <button type="submit"
                            class="bg-blue-700 text-white px-6 py-2.5 rounded-xl
                                   text-sm font-medium hover:bg-blue-800 transition">
                        Save Changes
                    </button>
                    <a href="{{ route('profile.show') }}"
                       class="border border-gray-200 text-gray-600 px-6 py-2.5 rounded-xl
                              text-sm font-medium hover:bg-gray-50 transition">
                        Cancel
                    </a>
                </div>

            </form>
        </div>
    </div>

@endsection