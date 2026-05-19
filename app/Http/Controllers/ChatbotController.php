<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatbotController extends Controller
{
    /**
     * Process chatbot request
     */
    public function ask(Request $request)
    {
        // Validate user input
        $request->validate([
            'message' => 'required|string|max:500',
        ]);

        $userMessage = $request->message;

        // AI system prompt
        $systemPrompt = "You are a friendly and knowledgeable assistant for an Alumni Platform
System built for Ramon Magsaysay Memorial College.

YOUR ROLE:
Help alumni navigate and use the platform effectively.
Be warm, concise, and helpful.
Keep responses under 100 words unless more detail is needed.

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
  Verified alumni can create posts in categories:
  Career Update, Achievement, Opportunity, Reunion, or General.

- All alumni can comment on posts and flag inappropriate content.

- AI Chatbot: That's you! Available to logged-in alumni on all pages.

- Admin Panel: Admins manage everything at /admin.

HOW VERIFICATION WORKS:
Alumni become verified automatically when they complete their profile
with their course, graduation year, and student ID.

Verified alumni get a blue checkmark and can create posts.

RESPONSE RULES:
- Be friendly and encouraging
- Give direct, actionable answers
- Never make up features that don't exist
- If asked about unrelated topics, politely explain that
  you only help with alumni platform questions
- If unsure, suggest contacting the school admin";

        try {

            // OpenRouter API request
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.gemini.key'),
                'HTTP-Referer' => 'http://localhost',
                'X-Title' => 'Alumni Platform',
            ])->post('https://openrouter.ai/api/v1/chat/completions', [

                'model' => config('services.gemini.model'),

                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $systemPrompt
                    ],
                    [
                        'role' => 'user',
                        'content' => $userMessage
                    ]
                ],

                'max_tokens' => 300,
                'temperature' => 0.7,
            ]);

            // Success response
            if ($response->successful()) {

                $data = $response->json();

                $reply = $data['choices'][0]['message']['content']
                    ?? 'Sorry, I could not generate a response.';

            } else {

                // API error
                $reply = 'API Error: ' . $response->body();
            }

        } catch (\Exception $e) {

            // Exception handling
            $reply = 'Error: ' . $e->getMessage();
        }

        // Return chatbot reply
        return response()->json([
            'reply' => $reply
        ]);
    }
}