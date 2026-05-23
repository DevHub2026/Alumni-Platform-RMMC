# Full Demonstration Script — Alumni Platform

**Read this document aloud while performing the live demo.**  
**Total time:** approximately 15–20 minutes  
**URL:** `http://127.0.0.1:8000` (adjust if different)

**Test accounts (prepare before demo):**

| Role | Suggested email | Password | Notes |
|------|-----------------|----------|-------|
| Admin | `admin@school.edu` | `password` | `role = admin` |
| Alumni A | `maria@test.com` | `password` | Verified, for comments/reactions |
| Alumni B | `juan@test.com` | `password` | Unverified until profile step |

---

## PART 0 — Before You Touch the Mouse (60–90 seconds)

> Good [morning/afternoon], everyone. Thank you for your time.
>
> I will now present the **Alumni Platform** — a web application developed for **Ramon Magsaysay Memorial College**. Its purpose is to help graduates stay connected to the school and to one another through profiles, official announcements, events, community posts, and photo galleries.
>
> This is a **live demonstration** of the working system. You will see both the **public alumni website** and the **administrative panel** exactly as real users would use them.
>
> In the next fifteen to twenty minutes, I will show four main areas: first, what a **guest** can browse without an account; second, the **alumni experience** from login through verification and social engagement; third, **events and galleries**; and fourth, the **admin panel** for institutional control and moderation. I will also demonstrate our **AI chatbot** for logged-in alumni.
>
> The system runs on **Laravel** with a shared database between the public site and the admin tools at `/admin`. I have prepared test accounts so you can see the difference between a new alumni member and a verified one.
>
> I will start on the homepage. Please follow the navigation bar at the top — we will use Home, Announcements, Events, Directory, Gallery, and Posts throughout this demo.
>
> Let’s begin.

**[ACTION: Open browser → `http://127.0.0.1:8000/` — logged out / guest]**

---

## PART 1 — Guest Public Experience (2–3 minutes)

### 1.1 Homepage

> This is the public landing page. Anyone can visit without logging in, which helps new graduates discover the platform before they register.
>
> At the top you see our main navigation and, when logged in later, profile and notification tools. In the hero section we welcome alumni and invite them to join or log in.
>
> These three statistics summarize platform activity: how many alumni are registered, how many upcoming events we have, and recent announcements. They update from the live database.

**[ACTION: Scroll slowly through the page]**

> Below that we preview the **latest announcements** and **upcoming events**. Alumni can click through to read the full details. This gives visitors immediate value even before they create an account.

**[ACTION: Optionally click “View all” on announcements — then return Home]**

---

### 1.2 Announcements

**[ACTION: Click **Announcements** in the navbar]**

> Announcements are **official school news**. Only administrators create them in the back office — alumni cannot publish here. That keeps institutional communication trustworthy.
>
> Only **published** announcements appear on this page. If content is not published, the public never sees it — the system returns a “not found” response rather than exposing drafts.

**[ACTION: Open one announcement]**

> Here is the full announcement with title, body, and date. Simple, readable, and mobile-friendly through our responsive layout.

**[ACTION: Back to navbar]**

---

### 1.3 Events

**[ACTION: Click **Events**]**

> The events section lists **upcoming** and **past** events separately, so alumni can register for what is ahead and still browse history from reunions or seminars.
>
> Each event shows key information: title, date, and location. **Registration requires a logged-in account** — I will demonstrate that shortly.

**[ACTION: Optionally open one event — point at Register button]**

> Guests can read event details, but the register action will prompt login. That is intentional.

**[ACTION: Back — click **Directory**]**

---

### 1.4 Alumni directory

> The **alumni directory** is searchable. You can find graduates by name, course, company, job title, or graduation year.
>
> This supports networking — finding batchmates or professionals from the same program.

**[ACTION: Type a short search term if you have seeded data — show results]**

**[ACTION: Click **Posts**]**

> The **posts feed** is our community board. Even guests can **read** visible posts. Categories include career updates, achievements, opportunities, reunions, and general discussion.
>
> Creating posts requires a verified alumni account — we will see that in a moment.

**[ACTION: Optionally click a category filter]**

> That completes the guest view: browse, discover, and understand what the platform offers before signing up.

---

## PART 2 — Alumni Registration & Profile (3–4 minutes)

### 2.1 Register or log in as new alumni

**[ACTION: Click **Register** — OR click **Log in** if Alumni B already exists]**

> New graduates can **register** with name, email, and password. The system automatically assigns the **alumni** role — not administrator.
>
> A full alumni profile is not created at registration; it is completed on first profile edit, which keeps signup fast.

**[ACTION: If registering live: fill form → submit. If using Alumni B: Log in with `juan@test.com`]**

> After login, the user is authenticated with a secure session. You may briefly see a dashboard page — our main alumni workflow uses the custom navigation and profile area instead.

**[ACTION: Note avatar/name in top-right when logged in]**

---

### 2.2 Unverified alumni — cannot post yet

**[ACTION: Click **Posts**]**

> Notice that this user does **not** yet see “New Post.” Instead the interface guides them: **“Complete Profile to Post.”** The rule is visible in the UI, not hidden in documentation.

**[ACTION: Click that button — lands on profile edit]**

> This is an important design choice: we encourage complete profiles before community posting.

---

### 2.3 Complete profile and auto-verification

**[ACTION: On profile edit — fill required fields]**

> The alumni fills academic and professional information: **student ID**, **course**, **graduation year**, and optionally phone, job, company, bio, skills, portfolio link, and profile photo.
>
> When **course**, **graduation year**, and **student ID** are all saved, the system **automatically verifies** the alumni. Administrators can also verify manually, but automatic verification scales for large batches.

**[ACTION: Submit / Save profile]**

> Success message confirms the update. The user is now a **verified alumni**.

**[ACTION: Go to **Posts** again]**

> Now the **“+ New Post”** button appears. Verification unlocks posting while keeping the directory useful for everyone.

---

### 2.4 Create a post

**[ACTION: Click **+ New Post**]**

> Verified alumni choose a **category**, enter a **title** and **body**, and may attach an **image** — for example a certificate photo or event picture.

**[ACTION: Fill form — e.g. category “Achievement”, title and body — submit]**

> The post is saved with **visible** status and appears on the public feed with a category badge. Images are stored securely on the server and served through public storage.

**[ACTION: Show post on **Posts** index]**

> Alumni B has now contributed to the community feed. Next I will show interaction from a second user.

---

## PART 3 — Comments, Reactions, Notifications (3–4 minutes)

### 3.1 Second alumni engages

**[ACTION: **Logout** → Login as Alumni A — `maria@test.com`]**

> I am now logged in as a **second alumni**, already verified. All logged-in alumni can **comment** and **react** on posts — not only the author.

**[ACTION: Open the post Alumni B created]**

---

### 3.2 Comment

**[ACTION: Scroll to comments — type a short comment — submit]**

> I am adding a comment. The system validates the text length and saves it immediately.
>
> The **post owner** receives an **in-app notification** — stored in our database. Email alerts are on our roadmap, but the bell icon already gives real-time awareness.

---

### 3.3 Reaction

**[ACTION: Click **Like**, **Celebrate**, or **Support**]**

> Reactions are lightweight engagement — like, celebrate, or support. The page updates through a small AJAX request without reloading the entire screen.
>
> Each user may have only **one reaction per post**; clicking the same reaction again removes it; choosing a different type updates it.

**[ACTION: Point at reaction counts if visible]**

---

### 3.4 Notification for post owner

**[ACTION: **Logout** → Login again as Alumni B — post owner]**

> Now I am back as the **author** of the post.

**[ACTION: Click the bell icon 🔔 in the navbar]**

> Here is the notification: who commented, and a link back to the post. The system polls for updates periodically while the user is on the site.
>
> Opening the full notifications page marks items as read. This closes the loop between posting and community feedback.

**[ACTION: Optional — click **View all** notifications]**

---

### 3.5 Flagging (optional — if time allows)

**[ACTION: Logout → Login as Alumni A — open another user’s post — submit flag with reason]**

> If content is inappropriate, any alumni can **flag** a post — but not their own. Each user may flag a given post only once.
>
> When **three or more** alumni flag the same post, it is marked for **admin review**. The post is not automatically deleted; staff make the final decision. That balances community safety with fair process.

---

## PART 4 — Events & Registration (2–3 minutes)

**[ACTION: Stay logged in as verified alumni — click **Events**]**

> Events are central to alumni life — reunions, seminars, networking days.

**[ACTION: Open an upcoming published event]**

> The detail page shows description, location, date, how many have registered, and whether **I am already registered**.

**[ACTION: Click **Register**]**

> I register with one click. The server checks: the event is not in the past, capacity is available if slots are limited, and I am not already registered.
>
> Registration is stored as **confirmed**, which also matters for gallery permissions later.

**[ACTION: Show success message and registered state on page]**

> If the event had a fixed number of seats and was full, the system would reject registration with a clear error — enforced on the server, not only in the browser.

**[ACTION: Optional — click Unregister — explain it removes the registration]**

> Unregistering frees a slot for others and removes gallery upload rights for that event.

---

## PART 5 — Gallery (2 minutes)

**[ACTION: Click **Gallery** in navbar]**

> The gallery lists events that have photos. It is organized by event so memories stay contextual — not one unstructured photo dump.

**[ACTION: Open the event you registered for — or any event with photos]**

> Inside the gallery, photos show with optional captions and uploader names.

**[ACTION: If upload is allowed — select one to three images — submit]**

> **Upload rules:** administrators can always upload. Verified alumni can upload only if they have a **confirmed registration** for that event — so photos come from people who were actually there.
>
> Files are validated for type and size, then stored on the server.

**[ACTION: Show new photos in the grid]**

> The uploader may delete their own photos; administrators can remove any photo. That is accountability without blocking community contribution.

---

## PART 6 — Search & Chatbot (2 minutes)

### 6.1 Global search

**[ACTION: Click 🔍 **Search** — enter at least two characters]**

> **Global search** finds alumni, posts, events, and announcements in one place. It is useful when you remember a name or keyword but not which section it belongs to.
>
> In this version we use database search; a dedicated search engine is planned for larger scale.

---

### 6.2 AI chatbot

**[ACTION: Click floating chat button — bottom-right]**

> Logged-in alumni also have an **AI assistant**. It answers questions about how to use the platform — profiles, verification, events, posts, and navigation.
>
> The AI runs on the **server**; API keys are never exposed to the browser. The assistant is instructed only about features that actually exist in this system.

**[ACTION: Type: “How do I become verified?” or “How do I register for an event?” — send]**

> Here is the response. This reduces support load on school staff for common “how do I…” questions.
>
> *If the API fails during demo:* “The chatbot requires an API key in the environment; the integration is implemented and works when configured.”

**[ACTION: Close chatbot]**

---

## PART 7 — Admin Panel (3–4 minutes)

### 7.1 Enter admin

**[ACTION: **Logout** → Login as **Admin** — OR switch to pre-opened admin tab]**

> Now I switch to the **institutional side**. Administrators use the same login system but with the **admin** role, which unlocks the Filament panel.

**[ACTION: Click **⚙️ Admin Panel** or go to `/admin`]**

> Non-admin users cannot access this area — access is enforced in the application, not only by hiding a link.

---

### 7.2 Dashboard

**[ACTION: View dashboard]**

> The dashboard gives leadership a quick overview:
>
> - Total alumni and how many are **verified**  
> - Total posts, including how many are **flagged**  
> - Upcoming published events and total **registrations**  
> - Published **announcements**  
> - And a chart of **new alumni registrations** over the last six months  
>
> This supports planning and monitoring without running manual reports.

---

### 7.3 Publish announcement (optional quick create)

**[ACTION: Content → **Announcements** → Create — fill title and body — enable Published — Save]**

> Here staff create official announcements. Toggling **published** makes them visible on the public site immediately.

---

### 7.4 Manage events (optional)

**[ACTION: Content → **Events** — show list or create]**

> Admins set title, description, location, date, optional **slot limit**, cover image, and publish status. Zero slots means unlimited attendance.

**[ACTION: Optional — Content → **Event Registrations** — show who registered]**

---

### 7.5 User management

**[ACTION: Users & Profiles → **Users** — open an alumni record]**

> Administrators manage accounts: name, email, role, **verified** status, and **suspension**.
>
> **Suspend** requires a reason shown to the user at login — for policy violations without deleting their history. **Unsuspend** restores access.

**[ACTION: Optional — demonstrate Suspend modal without saving, or show suspended flag]**

---

### 7.6 Post moderation

**[ACTION: Community → **Posts**]**

> All community posts appear here. Flagged posts are easy to spot. Staff can **approve** — keep visible and clear flags — **hide** from the public feed, or **remove** permanently.
>
> Status values are: visible, hidden, or removed. The public feed only shows **visible** posts.

**[ACTION: Optional — perform Approve or Hide on a test post]**

---

### 7.7 Flag review

**[ACTION: Moderation → **Flagged Posts** — if any flags exist]**

> Each flag shows who reported, the reason, and which post. Admins can **approve the post** or **remove** it and clear the flag record.
>
> This completes the moderation workflow from community report to institutional decision.

**[ACTION: Optional — Content → **Galleries** — show admin can manage all photos]**

---

## PART 8 — Closing (45–60 seconds)

**[ACTION: Return to public homepage or admin dashboard — face audience]**

> To summarize what you have seen:
>
> **One**, the platform welcomes guests and alumni with clear navigation and published institutional content.  
> **Two**, alumni register, complete profiles, earn verification, and participate through posts, comments, reactions, and notifications.  
> **Three**, events and galleries connect real-world participation with digital memories.  
> **Four**, administrators govern users, publish official content, and moderate the community through a dedicated panel.
>
> The system is built on **Laravel** with a maintainable structure, documented in our technical `/docs` and `/architecture` folders, and ready for deployment on a school server with MySQL, secure HTTPS, and file storage.
>
> Our roadmap includes email notifications, expanded automated testing, policy-based authorization, and improved search — but the core alumni engagement loop is complete and working today.
>
> Thank you. I am happy to answer your questions or repeat any part of the demonstration.

**[ACTION: Pause for Q&A — use PANEL_DEFENSE_NOTES.md if needed]**

---

## Quick Reference — Action Checklist

Use this while practicing; do not read aloud.

| Step | Action |
|------|--------|
| ☐ | Guest: Home → Announcements → Events → Directory → Posts |
| ☐ | Register/Login Alumni B (unverified) |
| ☐ | Posts → Complete Profile → Save → verify |
| ☐ | Create post |
| ☐ | Logout → Login Alumni A → comment + react |
| ☐ | Login Alumni B → bell notification |
| ☐ | Events → Register |
| ☐ | Gallery → Upload (if allowed) |
| ☐ | Search + Chatbot |
| ☐ | Login Admin → Dashboard → Users → Posts → Flags |
| ☐ | Closing summary |

---

## Troubleshooting (silent)

| Issue | Fix |
|-------|-----|
| Images broken | `php artisan storage:link` |
| No Admin Panel | `role = admin` on user |
| No New Post | Fill course, year, student_id |
| Chatbot error | Set `GEMINI_API_KEY` in `.env` |
| 419 error on react/notify | Refresh page |

---

## Related Files

- [SYSTEM_DEMONSTRATION_FLOW.md](./SYSTEM_DEMONSTRATION_FLOW.md) — click-by-click reference  
- [PRESENTATION_SCRIPT.md](./PRESENTATION_SCRIPT.md) — formal presentation without live clicks  
- [PANEL_DEFENSE_NOTES.md](./PANEL_DEFENSE_NOTES.md) — Q&A after demo
