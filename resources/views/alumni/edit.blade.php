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

            <form method="POST" action="{{ route('profile.update') }}"
            enctype="multipart/form-data" class="p-8">
                @csrf
                @method('PUT')


                <!-- Profile Photo -->
<p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">
    Profile Photo
</p>
<div class="flex items-center gap-5 mb-6 p-4 bg-gray-50 rounded-xl border border-gray-200">
    <!-- Current photo preview -->
    <div class="flex-shrink-0">
        @if($profile->profile_photo)
            <img src="{{ Storage::url($profile->profile_photo) }}"
                 class="w-20 h-20 rounded-full object-cover border-2 border-blue-200"
                 alt="Profile Photo">
        @else
            <div class="w-20 h-20 rounded-full bg-blue-100 text-blue-700
                        flex items-center justify-center text-2xl font-bold border-2
                        border-blue-200">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
        @endif
    </div>

    <div class="flex-1">
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Upload New Photo
        </label>
        <input type="file" name="profile_photo"
               accept="image/jpg,image/jpeg,image/png,image/webp"
               class="w-full text-sm text-gray-500
                      file:mr-4 file:py-2 file:px-4 file:rounded-lg
                      file:border-0 file:text-sm file:font-medium
                      file:bg-blue-50 file:text-blue-700
                      hover:file:bg-blue-100 cursor-pointer">
        <p class="text-xs text-gray-400 mt-1">
            JPG, PNG or WebP. Max 2MB.
        </p>
    </div>
</div>

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
                            Portfolio / Website URL
                        </label>
                        <input type="url" name="portfolio_url"
                               value="{{ old('portfolio_url', $profile->portfolio_url) }}"
                               placeholder="https://yourportfolio.com"
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
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Skills
                            <span class="text-gray-400 font-normal">(separate with commas)</span>
                        </label>
                        <input type="text" name="skills"
                               value="{{ old('skills', $profile->skills) }}"
                               placeholder="e.g. Web Development, Project Management, Design"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5
                                      text-sm focus:outline-none focus:ring-2
                                      focus:ring-blue-500 bg-gray-50">
                        <p class="text-xs text-gray-400 mt-1">
                            Add up to 10 skills separated by commas.
                        </p>
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