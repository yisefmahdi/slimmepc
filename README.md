# SlimmePC 🖥️

Laravel 12 web platform for **Slimme-PC**, a Dutch computer shop in Apeldoorn — a modern marketing landing page with a fully editable CMS, a complete customer registration flow, and an independent admin panel. The entire UI is in **Dutch** and follows the client's original design (`step-1/` design files converted to Blade).

---

## ✨ Features

### Landing page (`/`) — CMS-driven
- **Header**: sticky navigation bar with logo, desktop dropdowns (Webshop / Diensten), wishlist & cart buttons, search overlay, and a mobile drawer menu. The account button is **auth-aware**: guests see "Inloggen / Aanmelden", logged-in users see their name with a "Mijn account / Uitloggen" dropdown — no JavaScript required
- **Hero**: badge, 3-line gradient title, description, CTA buttons, trust points, animated "repair process" orbit visual on desktop + step cards on mobile
- **Why choose us**: benefit hub diagram (desktop) / cards (mobile) + 4 statistics
- **Services**: showroom cards with hover effects
- **Webshop**: swipeable product carousel (responsive 1/2/4 per page) with dots + arrows
- **Footer**: brand, link columns, contact, trust bar, payment badges
- **Floating**: AI chat + WhatsApp buttons
- All content (texts, images, products, links) is stored in the database and editable from the admin under **"Website inhoud"** — colors, fonts and SEO meta too

### Admin panel (`/admin`)
- Independent panel (own layout, dark sidebar, topbar, page-navigation loader)
- **Website inhoud**: section-by-section CMS editor with real image uploads, JSON repeaters (benefits, stats), design settings (6 brand colors, font, SEO) — saves instantly without page reload
- **Klantenbeheer**: full AJAX customer CRUD — search, role filter, pagination, create/edit/details/delete modals, inline role change, block/unblock, auto-generated customer number (`KL-YY-XXXX`) and passwords

### Auth (Breeze, redesigned)
- Login, register (Dutch address fields: straat, huisnummer, postcode, stad), password reset, email verification, confirm password — all rebuilt from the client's HTML design with Dutch validation messages
- Role-based redirect: admins → `/admin`, everyone else → the **home page**
- **No user-facing dashboard** — the `/dashboard` route and view were removed; logged-in users land on `/` and manage their account at `/profile`

### Performance
- **Full-page HTML cache** (1 month TTL) — guests get the landing page as pre-rendered HTML with zero DB/Blade work; invalidated instantly when the admin saves (version-based cache busting)
- Logged-in users always render fresh (personalized header) — never served from the shared guest cache
- Lazy-loaded below-fold images, `fetchpriority="high"` hero image, only the selected font family loaded
- No CDN dependencies at runtime: lucide icons, jQuery and axios are vendored locally in `public/assets/js/vendor/`

---

## 🛠 Tech Stack

| Layer | Technology |
|-------|------------|
| Backend | PHP ^8.2, Laravel ^12 |
| Frontend | Blade + Tailwind CSS v3 (custom `npm run css` pipeline), vanilla JS, Alpine.js (admin only) |
| Database | MySQL (`slimmepc_2026`) — SQLite works as a quick alternative |
| Auth | Laravel Breeze (Blade stack) |
| Tests | Pest ^3 (`composer test`) |
| Tooling | Laravel Pint, jQuery, axios, lucide icons (v0.469.0 vendored) |

---

## 📋 Requirements

- PHP **8.2+** (with extensions: `pdo_mysql`, `fileinfo`, `gd`, `mbstring`)
- Composer **2.x**
- Node.js **18+** + npm (only for Tailwind CSS compilation)
- MySQL **8+** (or SQLite for local testing)
- Web server: Laragon / XAMPP / Valet / `php artisan serve`

---

## 🚀 Installation

> Windows (Laragon/XAMPP) is fully supported. Commands below work on Windows PowerShell, macOS and Linux.

### ⚡ Quick start (just to make it run)

Four commands and the project is up:

```bash
# 1. Install PHP dependencies
composer install

# 2. Create your .env from the example (Windows PowerShell: Copy-Item .env.example .env)
cp .env.example .env

# 3. Generate the application key + create the database tables and seed all content
php artisan key:generate
php artisan migrate --seed

# 4. Start the server
php artisan serve
```

Open **http://localhost:8000** — the landing page is live with all its content (the compiled CSS/JS is already included in the repository, so **no `npm install` is needed to run the app**).

> **Database**: the default `.env.example` uses MySQL with database `slimmepc_2026` — create that database first (in Laragon: HeidiSQL → right-click → New → Database), or switch to SQLite (see below). After seeding, run `php artisan cache:clear` once so the cached landing page picks up the fresh content.

### 🔧 Detailed setup

#### 1. Environment configuration

```bash
# Windows (PowerShell)
Copy-Item .env.example .env

# macOS / Linux
cp .env.example .env
```

Open `.env` and set your database:

```
APP_NAME="SlimmePC"
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=slimmepc_2026
DB_USERNAME=root
DB_PASSWORD=your_password
```

Then generate the application key (required — without it the app won't run):

```bash
php artisan key:generate
```

> **SQLite alternative** (no MySQL needed): create `database/database.sqlite` (Windows: `New-Item database\database.sqlite`), then set `DB_CONNECTION=sqlite` and `DB_DATABASE="database/database.sqlite"` in `.env`.

#### 2. Migrate & seed (full content)

```bash
php artisan migrate --seed
```

This creates all tables and seeds:
- the **default content** of the landing page (63 CMS blocks + design settings, Dutch, mirrored from the original design)
- an **admin account** — the email is `slimmepc@admin.com`; the default password is defined in `database/seeders/AdminUserSeeder.php`. ⚠️ **Change the password immediately after your first login** (Profile → Update Password) — this is a public repository!

#### 3. Development assets (only when you edit CSS/JS)

The app loads its CSS directly from `public/assets` (no Vite runtime) — the compiled files are committed, so **running the app needs no Node.js**. Only install the npm toolchain if you want to modify the design:

```bash
npm install
npm run css          # app + auth + admin styles  → public/assets/css/app.css
npm run css:landing  # landing page styles        → public/assets/css/landing.css
```

> Run `npm run css` / `npm run css:landing` every time you change a Tailwind class in the Blade views or JS files, then commit the compiled output.

---

## 🧪 Testing

```bash
composer test
```

Runs the Pest suite (auth flows, email verification, registrations…).

---

## 🔍 Useful notes

- **Landing content doesn't update after seeding?** The page uses a full-page HTML cache (1 month). After a fresh `migrate --seed`, run `php artisan cache:clear` once. From then on, admin saves invalidate the cache automatically.
- **Assets architecture**: all runtime JS/CSS lives in `public/assets/` and is linked via `asset()` — there is **no `@vite`** in any layout. JavaScript edits need no build step (edit → refresh). Tailwind class changes need `npm run css` / `npm run css:landing`.
- **Don't update the vendored lucide to v1.x**: v1 removed the brand icons (facebook, instagram, youtube) used by the footer — the project pins v0.469.0 (`public/assets/js/vendor/lucide.min.js`).
- **Cache behavior**: `App\Support\Cms` reads content cached for 1 month, keyed by a `cache_version`. `Cms::bust()` (called on every admin save) writes an always-unique version, invalidating the full-page HTML cache and all CMS cache keys instantly.
- **Landing page cache is guest-only**: `PageController@home` bypasses the cache for authenticated users so their personalized header (name + logout) is always rendered fresh.

---

## 📁 Project structure (key paths)

```
app/
├── Http/Controllers/
│   ├── Admin/          # AdminController, ContentController (CMS), KlantController
│   └── PageController  # Landing page (guest HTML cache, auth bypass)
├── Models/             # User, ContentBlock, ContentMeta
└── Support/Cms.php     # CMS read helper + cache busting

resources/
├── css/app.css + landing.css    # Tailwind sources
└── views/
    ├── admin/                   # admin dashboard, CMS editor, klantenbeheer
    ├── landing/                 # landing page layouts + 8 partials (CMS-driven)
    └── auth/                    # Breeze auth screens (redesigned, Dutch)

public/assets/                   # ALL runtime assets (css, js, images, vendored libs)
routes/admin.php + web.php + auth.php
database/seeders/                # AdminUserSeeder + ContentBlockSeeder (63 blocks + design)
```

---

## 🛡 License

Open-source, built on [Laravel](https://laravel.com) (MIT). Add your own `LICENSE` file before publishing if you intend to distribute it.

---

## 📧 Contact

Slimme-PC — computer shop & repair services (UI language: Dutch 🇳🇱)
