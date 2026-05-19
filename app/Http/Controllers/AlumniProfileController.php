<?php

namespace App\Http\Controllers;

use App\Models\AlumniProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


/**
 * AlumniProfileController
 * 
 * Manages alumni profile viewing and editing.
 * 
 * Public Endpoints:
 * - index: Browse all alumni directory with search
 * 
 * Protected Endpoints (require authentication):
 * - show: View your own profile
 * - edit: Show edit form for your profile
 * - update: Save profile changes
 * 
 * Features:
 * - Search alumni by name, course, job, company, graduation year
 * - Pagination (12 per page)
 * - Profile photo upload
 * - LinkedIn URL integration
 */
class AlumniProfileController extends Controller
{
    /**
     * Display alumni directory with optional search filtering.
     * Shows 12 alumni per page.
     * 
     * Search can filter by:
     * - Name
     * - Course of study
     * - Current job title
     * - Company name
     * - Graduation year
     */
    public function index(Request $request)
    {
        // Start query: get all users with 'alumni' role and load their profiles
        $query = User::where('role', 'alumni')
                     ->with('alumniProfile');

        // If search term provided, filter by name or profile fields
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                // Search in user name
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('alumniProfile', function($q) use ($search) {
                      // Search in alumni profile fields
                      $q->where('course', 'like', "%{$search}%")
                        ->orWhere('current_job', 'like', "%{$search}%")
                        ->orWhere('company', 'like', "%{$search}%")
                        ->orWhere('graduation_year', 'like', "%{$search}%");
                  });
            });
        }

        // Paginate: 12 alumni per page
        $alumni = $query->paginate(12);

        return view('alumni.index', compact('alumni'));
    }

    /**
     * Display the currently logged-in user's own profile.
     */
    public function show()
    {
        $user = Auth::user();
        $profile = $user->alumniProfile;
        return view('alumni.profile', compact('user', 'profile'));
    }

    /**
     * Show the edit form for the current user's profile.
     * If profile doesn't exist, create an empty instance for form binding.
     */
    public function edit()
    {
        $user = Auth::user();
        // Create empty profile if user doesn't have one yet (first edit)
        $profile = $user->alumniProfile ?? new AlumniProfile();
        return view('alumni.edit', compact('user', 'profile'));
    }

    /**
     * Update the currently logged-in user's profile.
     * 
     * Validates all input fields:
     * - student_id: up to 50 chars
     * - course: up to 100 chars
     * - graduation_year: between 1990 and current year
     * - phone: up to 20 chars
     * - address: up to 255 chars
     * - current_job: up to 100 chars
     * - company: up to 100 chars
     * - linkedin_url: must be valid URL
     * - bio: up to 1000 chars
     * 
     * Uses updateOrCreate:
     * - If profile exists: updates it
     * - If profile doesn't exist: creates new one
     * All in one operation!
     */
    public function update(Request $request)
{
    $request->validate([
        'student_id'      => 'nullable|string|max:50',
        'course'          => 'nullable|string|max:100',
        'graduation_year' => 'nullable|integer|min:1990|max:' . date('Y'),
        'phone'           => 'nullable|string|max:20',
        'address'         => 'nullable|string|max:255',
        'current_job'     => 'nullable|string|max:100',
        'company'         => 'nullable|string|max:100',
        'linkedin_url'    => 'nullable|url|max:255',
        'portfolio_url'   => 'nullable|url|max:255',
        'bio'             => 'nullable|string|max:1000',
        'skills'          => 'nullable|string|max:500',
        'profile_photo'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);

    $user = Auth::user();
    $data = $request->only([
        'student_id', 'course', 'graduation_year',
        'phone', 'address', 'current_job', 'company',
        'linkedin_url', 'portfolio_url', 'bio', 'skills'
    ]);

    // Handle profile photo upload
    if ($request->hasFile('profile_photo')) {
        // Delete old photo if exists
        $existing = AlumniProfile::where('user_id', $user->id)->first();
        if ($existing && $existing->profile_photo) {
            \Storage::disk('public')->delete($existing->profile_photo);
        }

        $data['profile_photo'] = $request->file('profile_photo')
                                          ->store('profile-photos', 'public');
    }

    $profile = AlumniProfile::updateOrCreate(
        ['user_id' => $user->id],
        $data
    );

    // Auto-verify alumni when key profile fields are complete
    if ($profile->course && $profile->graduation_year && $profile->student_id) {
        $user->update(['is_verified' => true]);
    }

    return redirect()->route('profile.show')
                     ->with('success', 'Profile updated successfully!');
}
}