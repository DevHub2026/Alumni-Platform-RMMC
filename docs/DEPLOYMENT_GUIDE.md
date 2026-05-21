# Deployment Guide

## Target Environments

Production deployment typical stack:

- Linux server (Ubuntu 22.04+)
- Nginx or Apache
- PHP 8.3-FPM
- MySQL 8+ or PostgreSQL (SQLite not recommended for production)
- Redis (optional: cache, sessions, queues)
- Supervisor (queue workers)

## Pre-Deployment Checklist

| Item | Action |
|------|--------|
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_KEY` | Set and secret |
| Database | MySQL/PostgreSQL provisioned |
| Mail | SMTP or transactional provider |
| Storage | `storage:link` + persistent disk or S3 |
| HTTPS | TLS certificate |
| Chatbot API key | `GEMINI_API_KEY` in server env |

## Build & Release Steps

### 1. Clone repository on server

```bash
git clone <repository-url> /var/www/alumni-platform
cd /var/www/alumni-platform
```

### 2. Install dependencies

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
```

### 3. Environment file

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` for production values.

### 4. Database

```bash
php artisan migrate --force
```

### 5. Optimize Laravel

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan filament:optimize
```

### 6. Storage

```bash
php artisan storage:link
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

### 7. Queue worker (recommended if notifications queued in future)

Supervisor example:

```ini
[program:alumni-queue]
command=php /var/www/alumni-platform/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
```

Current app: notifications are sync; queue optional.

## Nginx Configuration (Example)

```nginx
server {
    listen 80;
    server_name alumni.example.edu;
    root /var/www/alumni-platform/public;

    index index.php;
    client_max_body_size 20M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

## Production `.env` Recommendations

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://alumni.example.edu

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=alumni_platform
DB_USERNAME=alumni_user
DB_PASSWORD=<secure>

SESSION_DRIVER=redis
CACHE_STORE=redis
QUEUE_CONNECTION=redis

FILESYSTEM_DISK=local
# Or configure S3 disk for media

MAIL_MAILER=smtp
MAIL_HOST=smtp.example.edu
MAIL_FROM_ADDRESS=noreply@example.edu

GEMINI_API_KEY=<secret>
GEMINI_MODEL=google/gemini-2.0-flash-001
```

## File Storage at Scale

For multi-server deploys, move `public` disk to **S3**:

1. Configure `AWS_*` in `.env`
2. Set `FILESYSTEM_DISK=s3` or use `public` disk driver `s3`
3. Run `php artisan storage:link` not needed for S3 public URLs

## Scheduled Tasks

Add to crontab:

```cron
* * * * * cd /var/www/alumni-platform && php artisan schedule:run >> /dev/null 2>&1
```

No scheduled commands defined in app yet—placeholder for future cleanups.

## Zero-Downtime Deploy

1. Enable maintenance mode: `php artisan down`
2. Pull code, run composer/npm/migrate
3. Clear caches and re-cache
4. `php artisan up`

## Post-Deploy Verification

| Check | URL |
|-------|-----|
| Health | `/up` |
| Homepage | `/` |
| Login | `/login` |
| Admin | `/admin` |
| Upload test | Profile photo / post image |
| Storage URL | `/storage/...` |

## Backup Strategy

| Asset | Method |
|-------|--------|
| Database | Daily mysqldump / managed backups |
| `storage/app/public` | Filesystem sync or S3 versioning |
| `.env` | Secure secrets manager |

## Related Docs

- [DEVELOPMENT_SETUP.md](./DEVELOPMENT_SETUP.md)
- [SECURITY_AND_SCALABILITY_ANALYSIS.md](./SECURITY_AND_SCALABILITY_ANALYSIS.md)
