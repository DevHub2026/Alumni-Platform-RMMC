# Development Setup

## Prerequisites

| Requirement | Version |
|-------------|---------|
| PHP | 8.3+ |
| Composer | 2.x |
| Node.js | 18+ (for Vite) |
| npm | 9+ |
| SQLite | optional (default) or MySQL 8+ |

Extensions: `openssl`, `pdo`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`

## Quick Start (Composer Script)

```bash
composer setup
```

Runs: `composer install`, copy `.env`, `key:generate`, `migrate --force`, `npm install`, `npm run build`.

## Manual Setup

### 1. Clone and install PHP dependencies

```bash
cd alumni-platform
composer install
```

### 2. Environment

```bash
cp .env.example .env
php artisan key:generate
```

### 3. Database

**SQLite (default):**

```bash
touch database/database.sqlite
```

Ensure `.env`:

```
DB_CONNECTION=sqlite
# DB_DATABASE uses database/database.sqlite by default
```

**MySQL:**

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=alumni_platform
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Migrate

```bash
php artisan migrate
```

### 5. Storage link (required for uploads)

```bash
php artisan storage:link
```

### 6. Frontend assets

```bash
npm install
npm run dev
```

### 7. Run application

**Option A — combined dev script:**

```bash
composer dev
```

Starts: `artisan serve`, `queue:listen`, `pail` (logs), `vite` via concurrently.

**Option B — manual:**

```bash
php artisan serve
npm run dev
```

Visit: `http://127.0.0.1:8000`

### 8. Create admin user

No admin seeder included. Options:

**Tinker:**

```bash
php artisan tinker
```

```php
\App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => bcrypt('password'),
    'role' => 'admin',
    'is_verified' => true,
]);
```

**Or** register as alumni, then update `role` to `admin` in database/Filament (after first admin exists).

### 9. Chatbot (optional)

Add to `.env`:

```
GEMINI_API_KEY=your_openrouter_or_provider_key
GEMINI_MODEL=google/gemini-2.0-flash-001
```

Model string must match OpenRouter model ID.

## Filament Admin

URL: `http://127.0.0.1:8000/admin`  
Login with admin user credentials.

## Environment Variables Reference

| Variable | Purpose |
|----------|---------|
| `APP_URL` | Base URL for storage URLs |
| `DB_*` | Database connection |
| `SESSION_DRIVER` | `database` recommended for dev parity |
| `QUEUE_CONNECTION` | `database` if testing queues |
| `FILESYSTEM_DISK` | `local` default; uploads use `public` disk explicitly |
| `GEMINI_API_KEY` | Chatbot |
| `GEMINI_MODEL` | Chatbot model ID |

## Code Style (Pint)

```bash
./vendor/bin/pint
```

Laravel Pint is included as dev dependency (`laravel/pint`).

## Running Tests

```bash
composer test
# or
php artisan test
```

Uses in-memory SQLite per `phpunit.xml`.

## Common Issues

| Issue | Fix |
|-------|-----|
| 403 on uploaded images | Run `php artisan storage:link` |
| Filament 404 | Ensure user `role=admin` |
| Chatbot API errors | Check `GEMINI_API_KEY`, network, model name |
| Vite manifest missing | Run `npm run build` |
| Session errors | Run migrations (sessions table) |

## IDE Helpers (Optional)

```bash
composer require --dev laravel/boost
php artisan boost:install
```

## Related Docs

- [DEPLOYMENT_GUIDE.md](./DEPLOYMENT_GUIDE.md)
- [TESTING_AND_QUALITY.md](./TESTING_AND_QUALITY.md)
- [FOLDER_STRUCTURE.md](./FOLDER_STRUCTURE.md)
