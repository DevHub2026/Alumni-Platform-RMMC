# Deployment Architecture

Based on `composer.json` scripts, `.env.example`, `config/filesystems.php`, and Laravel 13 defaults.

---

## 1. Local Development (Presentation)

```mermaid
flowchart TB
    Dev[Developer] --> Serve[php artisan serve]
    Dev --> Vite[npm run dev]
    Serve --> App[Laravel]
    App --> SQLite[(SQLite)]
    App --> LocalStore[storage/app/public]
```

---

## 2. Local Development (Technical)

```mermaid
flowchart TB
    subgraph dev_machine [Developer Machine]
        CMD1[composer install]
        CMD2[cp .env.example .env]
        CMD3[php artisan key:generate]
        CMD4[php artisan migrate]
        CMD5[php artisan storage:link]
        CMD6[npm install && npm run dev]
        CMD7[composer dev optional]
    end

    subgraph processes [Running Processes]
        SRV[artisan serve :8000]
        VITE[Vite HMR :5173]
        QUEUE[queue:listen optional]
        PAIL[pail logs optional]
    end

    CMD7 --> SRV
    CMD7 --> VITE
    CMD7 --> QUEUE
    CMD7 --> PAIL

    SRV --> Laravel[Laravel App]
    Laravel --> DB[(database/database.sqlite)]
    Laravel --> FS[storage/app/public]
    VITE --> Assets[resources/css app.css js app.js]
```

**Composer `dev` script:** concurrently runs serve, queue, pail, vite.

---

## 3. Laravel Server Structure (Presentation)

```mermaid
flowchart TB
    Web[public/index.php] --> Boot[bootstrap/app.php]
    Boot --> App[Laravel Application]
    App --> Routes[routes]
    App --> Config[config]
```

---

## 4. Laravel Server Structure (Technical)

```mermaid
flowchart TB
    subgraph public_dir [public/ Document Root]
        IDX[index.php]
        BUILD[build/ Vite manifest]
        STOR[storage/ symlink]
        FIL[js/filament/ assets]
    end

    subgraph app_code [Application Code]
        APP[app/]
        RTE[routes/]
        CFG[config/]
        RES[resources/views]
        DBM[database/migrations]
    end

    subgraph runtime [Runtime Writable]
        STG[storage/logs]
        STG2[storage/framework]
        STG3[storage/app/public]
        BOOTC[bootstrap/cache]
    end

    IDX --> Boot[bootstrap/app.php]
    Boot --> APP
    Boot --> RTE
    Boot --> CFG
    RES --> Blade
    APP --> DBM
```

---

## 5. Database Connection (Presentation)

```mermaid
flowchart LR
    Laravel -->|Eloquent PDO| DB[(Database)]
```

---

## 6. Database Connection (Technical)

```mermaid
flowchart TB
    subgraph env [Environment]
        DEV[DB_CONNECTION sqlite]
        PROD[DB_CONNECTION mysql]
    end

    subgraph laravel_db [Laravel Database Layer]
        CFG[config/database.php]
        ELQ[Eloquent Models]
        MIG[artisan migrate]
    end

    DEV --> CFG
    PROD --> CFG
    CFG --> ELQ
    MIG --> Tables[users posts events etc]
    ELQ --> Tables

    subgraph infra_tables [Laravel Infra Tables]
        SESS[sessions]
        CACHE[cache]
        JOBS[jobs]
    end
```

**Testing:** PHPUnit uses `:memory:` SQLite per `phpunit.xml`.

---

## 7. Storage Structure (Presentation)

```mermaid
flowchart TB
    Upload[User Upload] --> Disk[public disk]
    Disk --> Link[storage link]
    Link --> URL[/storage/ URL]
```

---

## 8. Storage Structure (Technical)

```mermaid
flowchart TB
    subgraph disks [Filesystem Disks]
        DEF[default local private]
        PUB[public disk]
    end

    subgraph paths [Upload Paths on public disk]
        PP[profile-photos/]
        PI[post-images/]
        GL[gallery/]
    end

    subgraph controllers [Writers]
        APC[AlumniProfileController]
        POC[PostController]
        GAC[GalleryController]
        FIL[Filament FileUpload fields]
    end

    APC --> PP
    POC --> PI
    GAC --> GL
    FIL --> paths

    PUB --> STG[storage/app/public]
    STG --> SYM[public/storage symlink]
    SYM --> HTTP[APP_URL/storage/...]
```

**Default `.env.example`:** `FILESYSTEM_DISK=local` (uploads explicitly use `public` disk in code).

---

## 9. Production Deployment (Presentation)

```mermaid
flowchart TB
    Users[Internet Users] --> HTTPS[Nginx HTTPS]
    HTTPS --> PHP[PHP-FPM Laravel]
    PHP --> MySQL[(MySQL)]
    PHP --> Files[Storage]
```

---

## 10. Production Deployment (Technical)

```mermaid
flowchart TB
    subgraph internet [Internet]
        U[Users]
    end

    subgraph edge [Edge Optional]
        CDN[CDN for static assets]
        LB[Load Balancer]
    end

    subgraph app_server [Application Server]
        NGX[Nginx]
        FPM[PHP 8.3 FPM]
        LAR[Laravel optimized]
    end

    subgraph data_tier [Data Tier]
        MY[(MySQL)]
        RD[(Redis sessions cache queue)]
        S3[(S3 optional media)]
    end

    subgraph deploy_steps [Deploy Steps]
        D1[composer install no-dev]
        D2[npm run build]
        D3[migrate force]
        D4[config route view cache]
        D5[storage link]
    end

    U --> CDN --> LB --> NGX --> FPM --> LAR
    LAR --> MY
    LAR --> RD
    LAR --> S3
    deploy_steps --> LAR
```

---

## 11. Environment Layers (Reference)

| Layer | Development | Production |
|-------|-------------|------------|
| App URL | localhost:8000 | https://domain |
| Database | SQLite file | MySQL |
| Sessions | database | redis recommended |
| Queue | database sync | redis + supervisor |
| Mail | log | smtp |
| Debug | true | false |
| Chatbot | GEMINI_API_KEY | secrets manager |

---

## 12. Health and Ops

```mermaid
flowchart LR
    Monitor[Monitoring] --> Health[GET /up]
    Monitor --> Logs[storage/logs]
    Monitor --> Queue[failed_jobs table]
```

See `/docs/DEPLOYMENT_GUIDE.md` for command checklist.
