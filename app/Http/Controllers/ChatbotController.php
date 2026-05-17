<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * ChatbotController
 * 
 * Provides AI-powered chatbot support for alumni.
 * Uses Google's Gemini API to answer questions about the platform.
 * Only authenticated users (alumni) can use the chatbot.
 * 
 * Chatbot Knowledge Base:
 * - How to update profile
 * - How to register for events
 * - How to browse alumni directory
 * - General platform information
 * - Alumni community questions
 */
class ChatbotController extends Controller
{
    /**
     * Process a user question and return AI-generated response.
     * 
     * Security:
     * - Requires authentication (middleware: 'auth')
     * - Validates message length (max 500 chars)
     * 
     * API Integration:
     * - Uses Google Gemini 2.0 Flash API
     * - Requires GEMINI_API_KEY in .env
     * 
     * Error Handling:
     * - Network errors caught gracefully
     * - Returns user-friendly error messages
     */
    public function ask(Request $request)
    {
        // Validate user input
        $request->validate([
            'message' => 'required|string|max:500',
        ]);

        $userMessage = $request->message;

        // Define chatbot personality and scope
        // This "system prompt" tells Gemini what role to play
        $systemPrompt = "You are a friendly and knowledgeable assistant for an Alumni Platform
    System built for Ramon Magsaysay Memorial College.

    YOUR ROLE:
    Help alumni navigate and use the platform effectively. Be warm, concise,
    and helpful. Keep responses under 100 words unless more detail is needed.

    PLATFORM FEATURES YOU KNOW ABOUT:
    - Alumni Profiles: Alumni can view and edit their profile at /profile.
      They need to complete their profile (course, graduation year, student ID)
      to become verified and unlock posting.
    - Alumni Directory: Browse and search all alumni at /alumni.
      Search by name, course, company, or graduation year.
    - Announcements: School news and updates at /announcements.
      Posted by admins only.
    - Events: Upcoming alumni events at /events.
      Logged-in alumni can register for events with available slots.
    - Gallery: Event photo galleries at /gallery.
      Browse photos organized by event.
    - Posts: Alumni community posts at /posts.
      Verified alumni can create posts in categories: Career Update,
      Achievement, Opportunity, Reunion, or General.
      All alumni can comment on posts and flag inappropriate content.
    - AI Chatbot: That's you! Available to logged-in alumni on all pages.
    - Admin Panel: Admins manage everything at /admin (restricted access).

    HOW VERIFICATION WORKS:
    Alumni become verified automatically when they complete their profile
    with their course, graduation year, and student ID. Verified alumni
    get a blue checkmark and can create posts.

    RESPONSE RULES:
    - Be friendly and encouraging
    - Give direct, actionable answers
    - If asked about something unrelated to the platform, politely say
      you can only help with alumni platform questions
    - Never make up features that don't exist
    - If unsure, suggest the user contact their school admin";

        try {
            // Call Google Gemini API with the user message
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post(
                'https://generativelanguage.googleapis.com/v1beta/models/' . config('services.gemini.model') . ':generateContent?key='
                . config('services.gemini.key'),
                [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    // Combine system prompt with user message
                                    'text' => $systemPrompt
                                              . "\n\nUser: " . $userMessage
                                ]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'maxOutputTokens' => 300,    // Keep responses concise
                        'temperature'     => 0.7,    // Balance creativity & consistency
                    ]
                ]
            );

            // Parse and extract the AI response
            if ($response->successful()) {
                $data  = $response->json();
                $reply = $data['candidates'][0]['content']['parts'][0]['text']
                         ?? 'Sorry, I could not generate a response.';
            } else {
                // API call failed
                $reply = 'Sorry, I am having trouble connecting right now. Please try again later.';
            }

        } catch (\Exception $e) {
            // Catch any unexpected errors (network issues, timeout, etc.)
            $reply = 'Sorry, something went wrong. Please try again.';
        }

        // Return response as JSON for JavaScript to display
        return response()->json(['reply' => $reply]);
    }
}