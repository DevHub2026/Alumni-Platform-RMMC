# API Reference

## Overview

This application is **not an API-first** project. There is no `routes/api.php`, no Sanctum/Passport, and no versioning.

The public interface is **server-rendered Blade**. A small set of endpoints return **JSON** for AJAX interactions from Alpine.js/fetch.

All JSON endpoints require an **authenticated session** (cookie-based) unless noted otherwise.

---

## JSON Endpoints

### POST `/chatbot/ask`

**Route name:** `chatbot.ask`  
**Middleware:** `auth`  
**Controller:** `ChatbotController@ask`

#### Request

| Field | Type | Rules |
|-------|------|-------|
| `message` | string | required, max 500 |

Headers: `X-CSRF-TOKEN`, session cookie.

#### Response `200`

```json
{
  "reply": "Assistant response text..."
}
```

#### Errors

- Validation errors: Laravel standard 422
- API failure: `reply` may contain `"API Error: ..."` or `"Error: ..."` (exception message)

#### Backend

Proxies to OpenRouter `https://openrouter.ai/api/v1/chat/completions` using `config('services.gemini.key')` and `config('services.gemini.model')`.

---

### POST `/posts/{post}/react`

**Route name:** `posts.react`  
**Middleware:** `auth`  
**Controller:** `PostController@react`

#### Request

| Field | Type | Rules |
|-------|------|-------|
| `type` | string | required, `in:like,celebrate,support` |

#### Response `200`

```json
{
  "reacted": true,
  "counts": {
    "like": 3,
    "celebrate": 1,
    "support": 0,
    "total": 4
  }
}
```

`reacted: false` when user toggled off same reaction type.

#### Behavior

- One reaction per user per post (DB unique constraint)
- Same type again → deletes reaction (toggle off)
- Different type → updates existing row

---

### GET `/notifications/unread`

**Route name:** `notifications.unread`  
**Middleware:** `auth`  
**Controller:** `NotificationController@unread`

#### Response `200`

```json
{
  "notifications": [
    {
      "id": "uuid",
      "data": {
        "message": "Jane commented on your post.",
        "post_id": 1,
        "post_title": "My achievement",
        "commenter": "Jane"
      },
      "read_at": null,
      "created_at_human": "2 minutes ago"
    }
  ],
  "unread": 3
}
```

Returns latest **5** notifications.

---

### POST `/notifications/mark-read`

**Route name:** `notifications.markRead`  
**Middleware:** `auth`  
**Controller:** `NotificationController@markRead`

#### Request

No body required. CSRF required.

#### Response `200`

```json
{
  "status": "ok"
}
```

Marks **all** unread notifications as read for current user.

---

## HTML Endpoints (Non-JSON)

All other routes return `text/html` Blade views or redirects with session flash messages.

Form submissions use standard `application/x-www-form-urlencoded` or `multipart/form-data` (file uploads).

---

## Authentication for JSON Calls

From browser JavaScript:

```javascript
fetch('/notifications/unread', {
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    }
});
```

Axios is preconfigured in `resources/js/bootstrap.js` to send CSRF token for same-origin requests.

---

## Future API Considerations

If a mobile app or SPA is needed:

1. Add `routes/api.php` with Sanctum token auth
2. Create API Resources for consistent JSON shapes
3. Move chatbot/reactions/notifications to versioned controllers
4. Do not expose raw exception messages in production JSON

See [SECURITY_AND_SCALABILITY_ANALYSIS.md](./SECURITY_AND_SCALABILITY_ANALYSIS.md).
