# SlimmePC

## 1. Overview
- Description: Laravel 12 + Breeze (Blade) web application for a Dutch computer shop (Slimme-PC). The `users` table is customized with Dutch customer fields (`klantnummer`, `house_number`, `street`, `postcode`, `city`) and a role system (`admin` / `user` / `technician`). All auth screens (login, register, forgot/reset password, confirm password, verify email) are rebuilt from the client's HTML designs (`D:\sm 2026\slimmepc2026nieuwe\step-1\`) into a reusable Blade design system: brand colors (light + dark themes), buttons with loading states, icon inputs, auth card layout, theme toggle. **A fully independent admin panel exists** at `/admin` (own router `admin.php`, own layout + components, role-guarded, seeded admin account, role-based login redirect). **The landing page (`/`) was converted from `step-1/home.html` into Blade + CMS**: all content is stored in DB (`content_blocks` + `content_meta` tables) and editable from the admin under "Website inhoud" — sections: **Header** (logo image + logo text + tagline), **Hero** (title/description/images with real uploads), **Waarom voor ons kiezen** (title, hub, 6 benefit cards, 4 stats), **Services** (8 fixed cards, edit-only + hide toggle) and **Footer** (description, social, contact, trust, copyright, payments) plus "Ontwerp instellingen" (colors, font, SEO) stored as JSON in `content_meta`. All CMS icon fields use a **searchable lucide icon picker**. The logo is **global**: landing header (desktop+mobile), footer, favicon, auth pages (`<x-logo>`), logged-in user nav and the admin sidebar all read it from the CMS. Frontend reads via the `Cms` helper with version-busting cache (**1-month TTL, invalidated instantly on admin save**) and the **entire rendered HTML is cached** (full-page cache in `PageController`) so the home is served without re-rendering. The admin panel has an elegant page-navigation loader (`#admin-loader`).
- Project type: Web
- Goal / end user: PC/shop app with admin, regular users (customers) and technicians. UI language: Dutch (nl).
- Tagline / brief identity: "SlimmePC" (Dutch for "Smart PC").

## 2. Tech Stack
- Backend: PHP ^8.2, Laravel framework ^12.0
- Frontend: Blade templates + Tailwind CSS v3 (`tailwind.config.js`, installed 3.4.19) + Alpine.js ^3 + Vite (vite.config.js, laravel-vite-plugin ^2)
- JS libraries: jQuery (^3, installed, exposed as `window.$` / `window.jQuery`) + axios (default Laravel, with X-CSRF-TOKEN header)
- Database: MySQL `slimmepc_2026` in `.env` (local Laragon, root/no password); SQLite remains the default in `.env.example` (`database/database.sqlite` exists)
- Auth: Laravel Breeze (Blade stack) — routes/controllers/views present, views redesigned
- Key libraries: laravel/tinker, @tailwindcss/forms; dev: pestphp/pest ^3, laravel/pint, laravel/sail, mockery, fakerphp, collision, laravel/pail; npm: `lucide` (icons, vendored locally)
- Deployment environment: not defined yet
- Composer scripts: `composer setup` (fresh install), `composer dev` (serve + queue + logs + vite concurrently), `composer test`
- Node: v18.18.0 (vite warns it wants >= 20.19 — build still works)

## 3. Full Folder Structure
```
slimmepc/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── AdminController.php   # dashboard() → admin.dashboard view with stats
│   │   │   │   ├── ContentController.php # CMS: index (editor per page), updateSection (save one section),
│   │   │   │   │                         #   updateDesign (design settings JSON) + image upload per block
│   │   │   │   └── KlantController.php   # Users-beheren CRUD (JSON) — served under /admin/users (admin.users.*)
│   │   │   ├── Auth/          # Breeze: AuthenticatedSession (role-based redirect), ConfirmablePassword,
│   │   │   │                  #   EmailVerificationNotification, EmailVerificationPrompt,
│   │   │   │                  #   NewPassword, Password, PasswordResetLink, RegisteredUser, VerifyEmail
│   │   │   ├── Controller.php
│   │   │   ├── PageController.php    # home() → landing.home with Cms::page('home') + flat Cms::design(); serves the full-page HTML cache for guests only (auth users always render fresh — personalized header)
│   │   │   └── ProfileController.php
│   │   ├── Middleware/
│   │   │   └── EnsureUserIsAdmin.php     # role !== 'admin' → 403
│   │   └── Requests/
│   │       ├── Auth/          # Breeze form requests (LoginRequest, RegisterRequest, ...)
│   │       ├── Admin/         # StoreKlantRequest, UpdateKlantRequest (Dutch messages)
│   │       └── ProfileUpdateRequest.php
│   ├── Models/
│   │   ├── ContentBlock.php   # CMS block: page/section/block_key/type/value/json_value (cast array)/sort_order
│   │   ├── ContentMeta.php    # CMS meta: meta_key/meta_value (design JSON + cache_version) — table `content_meta`
│   │   └── User.php           # Customized: phone, is_blocked, address fields, role, klantnummer + isAdmin()/isTechnician()/isCustomer()
│   ├── Providers/
│   │   └── AppServiceProvider.php
│   ├── Support/
│   │   └── Cms.php            # version()/page()/get()/design()/designValue()/bust() — cached reads keyed by cache_version
│   └── View/
├── bootstrap/
│   └── app.php                # registers admin.php routes + 'admin' middleware alias
├── config/
│   └── cms.php                # CMS schema: editable sections/blocks (home: hero + why) + design settings groups
├── database/
│   ├── database.sqlite
│   ├── factories/
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php   # users + password_reset_tokens + sessions
│   │   ├── 0001_01_01_000001_create_cache_table.php   # cache + cache_locks
│   │   ├── 0001_01_01_000002_create_jobs_table.php    # jobs + job_batches + failed_jobs
│   │   └── 2026_08_11_000001_create_content_blocks_table.php  # page+section+block_key unique; json_value LONGTEXT
│   │   └── 2026_08_11_000002_create_content_meta_table.php    # meta_key unique
│   └── seeders/
│       ├── DatabaseSeeder.php         # calls AdminUserSeeder + ContentBlockSeeder
│       ├── AdminUserSeeder.php        # creates slimmepc@admin.com admin account
│       └── ContentBlockSeeder.php     # 55 default blocks (Dutch, mirrored from step-1/home.html) + design JSON + cache_version
├── lang/
│   └── nl/
│       └── validation.php        # Dutch validation messages + Dutch attribute names (app locale = 'nl')
├── node_modules/
├── public/
│   └── assets/                      # ALL frontend assets live here (no Vite runtime)
│       ├── css/
│       │   ├── app.css              # compiled Tailwind output (source: resources/css/app.css → npm run css)
│       │   ├── landing.css          # compiled landing CSS (source: resources/css/landing.css → npm run css:landing)
│       │   └── landing-base.css     # original step-1 style.css (imported by landing.css)
│       ├── js/
│       │   ├── design.js            # global JS: theme, loading states, password toggle, toasts, modals, confirm, axios defaults
│       │   ├── landing.js           # landing page JS: lucide icons, mobile drawer, search overlay, accordion, process orbit, shop carousel
│       │   ├── admin/
│       │   │   ├── klanten.js       # Users CRUD (axios, no page refresh) — endpoints target /admin/users
│       │   │   ├── content.js       # CMS editor: section forms (axios, no reload on save), json row add/remove, color sync, file preview
│       │   │   ├── icon-picker.js   # CMS icon picker: searchable dropdown of all lucide icons (icon+name), kebab/pascal conversion, MutationObserver re-renders new rows
│       │   │   └── loader.js        # admin page-navigation loader: intercepts internal links/forms (sessionStorage flag adminPageNav)
│       │   └── vendor/
│       │       ├── jquery.min.js    # jQuery 4 (local copy from node_modules)
│       │       ├── axios.min.js     # axios 1.x (local copy from node_modules)
│       │       ├── alpine.min.js    # Alpine.js CDN build (local copy from node_modules)
│   │       └── lucide.min.js    # lucide v0.469.0 UMD (local copy, no CDN) — includes BRAND icons (facebook/instagram/youtube); v1.x removed them, never pull @latest
│       └── img/
│           ├── logo.webp              # Slimme-PC logo (copied from step-1/images)
│           ├── aanmelden-photo.png    # copied from step-1/images
│           └── landing/               # landing page images (copied from step-1/images): logo.webp, hero PNGs,
│                                      #   product images (53f89edd…, b4c74892…, aad70dda…, 363f8f55…, e6cc3cb7…,
│                                      #   ipad.png, 85cea032…, 45e1353e…, cd15d7f8…, IEuzcak…jpg, VmKc…webp,
│                                      #   yEbdqw…webp, 550x399.jpg) — new uploads also land here via the CMS
├── resources/
│   ├── css/
│   │   ├── app.css           # Tailwind SOURCE + design system (CSS vars light/dark, .btn-primary, .form-input, spinner, blobs...) — compiled via `npm run css` → public/assets/css/app.css
│   │   └── landing.css       # Tailwind SOURCE for landing (directives + landing-base.css import + design-var classes:
│   │                         #   .bg-brand-btn, .bg-brand-gradient-br, .gradient-text, .text-brand-heading, ...)
│   ├── js/
│   │   ├── app.js            # stock Vite entry — NOT loaded by any layout anymore
│   │   └── bootstrap.js      # stock Breeze — unused (assets loaded directly from public/assets)
│   └── views/
│       ├── auth/             # redesigned (Dutch): login, register, forgot-password, reset-password, confirm-password, verify-email
│       ├── admin/
│       │   ├── content/
│       │   │   ├── design.blade.php   # Home-page → "Ontwerp & SEO" page: SEO meta + colors + font (design form, JSON save)
│       │   │   ├── section.blade.php  # Home-page → single section editor (header/hero/why): per-field forms, location badge, json rows
│       │   │   └── partials/
│       │   │       └── json-row.blade.php  # repeatable item row for type=json blocks (index param, __INDEX__ for templates)
│       │   ├── klanten/index.blade.php # Users-beheren (AJAX table + modals) — route admin.users.index
│       │   └── dashboard.blade.php     # admin dashboard (Dutch, responsive)
│       ├── components/       # design-system blade components (see section 11)
│       │   └── admin/        # admin-only components: layout, sidebar-link, stat-card, card, modal
│       ├── landing/          # CMS-driven landing page (rebuilt from step-1/home.html)
│       │   ├── layouts/app.blade.php   # head: Bunny Fonts, landing.css, CSS vars from $design, lucide + landing.js
│       │   ├── home.blade.php          # includes the partials below
│       │   └── partials/
│       │       ├── header.blade.php    # nav (desktop + mobile drawer + search overlay) from $c['header']; order = $navBefore (Home, Over ons) → Webshop ▾ → Diensten ▾ → $navAfter (Lid worden, Tarieven, Contact); account area is AUTH-AWARE: guest → Account button (login), logged-in → user name + dropdown (native <details>, no JS) with Mijn account → /profile + Uitloggen (POST /logout); mobile drawer shows the name tile + full-width logout button
│       │       ├── hero.blade.php      # badge/title/description/buttons/trust + desktop orbit visual + mobile steps
│       │       ├── why.blade.php       # benefits hub + stats from $c['why']
│       │       ├── services.blade.php  # service cards from $c['services']
│       │       ├── shop.blade.php      # carousel of products from $c['shop']
│       │       ├── footer.blade.php    # brand block reads header.logo_text/tagline (fallback badge letter = 1st letter); Diensten/Webshop/Over ons link columns HARDCODED; social/contact/trust/copyright/payments from $c['footer']
│       │       └── floating.blade.php  # chat + WhatsApp floating buttons from $c['floating']
│       ├── layouts/          # app.blade.php, guest.blade.php, navigation.blade.php (all theme-aware)
│       ├── profile/          # edit, partials (update-profile-information, update-password, delete-user)
│       └── welcome.blade.php
├── routes/
│   ├── admin.php             # /admin prefix, auth+verified+admin middleware, admin.* names (+ content.* group)
│   ├── auth.php               # Breeze auth routes
│   ├── console.php
│   └── web.php               # GET / → PageController@home (name: home) — the user-facing dashboard no longer exists
├── storage/
├── tests/
├── vendor/
├── .editorconfig
├── .env                      # MySQL slimmepc_2026 (root, no password)
├── .env.example
├── .gitattributes
├── .gitignore
├── artisan
├── composer.json / composer.lock
├── package.json / package-lock.json   # scripts: css (app.css), css:landing (landing.css) + lucide dependency
├── phpunit.xml
├── postcss.config.js
├── README.md
├── tailwind.config.js        # darkMode: 'class', brand palette, accent palette (400 #a3e635/500 #84cc16/600 #65a30d),
│                             #   shadows (navbar/menu), content scans resources/views + public/assets/js
└── vite.config.js
```

## 4. Visual Identity / Design System
- **Brand**: Slimme-PC — logo at `public/assets/img/logo.webp` (used via `<x-logo>` component)
- **Primary colors** (from client design in `step-1/`):
  - Gradient primary button: `#075be8` → `#064bd7` (`from-primary to-primary` / `bg-brand-gradient`)
  - Heading / dark navy: `#071b46`
  - Page background (light): `#f7faff` / `#f6f9ff`
  - Brand blue scale: `brand.50–950` (Tailwind blue based, `brand.DEFAULT = #075be8`)
  - **Admin sidebar**: dark navy `#0b1638` with white/10 separators, active link = brand gradient + glow shadow
- **Light/dark theme**: class-based dark mode (`darkMode: 'class'` in tailwind.config.js). CSS custom properties in `resources/css/app.css` under `:root` (light) and `.dark` (dark):
  - `--c-page` (page bg), `--c-card`, `--c-card-border`, `--c-heading`, `--c-body`, `--c-muted`, `--c-input-bg`, `--c-input-border`, `--c-shadow`, `--c-blob-1/2/3` (background blobs)
  - Theme preference stored in `localStorage` key `slimmepc-theme`; respects system preference as default. Toggle via `<x-theme-toggle>` (sun/moon icons)
- **Font**: Figtree (400–800) via Bunny Fonts — `font-sans` in tailwind.config.js. Only the admin-selected family is requested (`landing/layouts/app.blade.php` builds the Bunny URL per `font_family`, with `display=swap`) — was loading all 5 families at once
- **Icons**: lucide v0.469.0 vendored (`public/assets/js/vendor/lucide.min.js`); brand icons (facebook/instagram/youtube) exist in this version — v1.31.0 had removed them
- **Landing stats row**: 4 stats in ONE row on desktop (`lg:grid-cols-4`), 2×2 on small screens — borders/icons/colors match `step-1/home.html` exactly (icons `bg-blue-50 text-blue-600`, last card lime, values `text-blue-700`)
- **Design style**: glassy auth card (`rounded-[28px]`, `backdrop-blur-xl`, blue-tinted shadow `0 25px 80px rgba(37,99,235,.13)`), soft blurred blobs in the background, gradient primary buttons with hover lift + glow, rounded-xl inputs with left icons and focus ring (`focus:ring-4 focus:ring-blue-100`). Admin panel: fixed 288px sidebar (drawer on mobile with overlay), sticky glass topbar, stat cards with hover lift, dashed quick-action tiles.
- **Design source files**: `D:\sm 2026\slimmepc2026nieuwe\step-1\` (login.html, register.html, home.html, style.css, script.js, images/) — reference for future screens

## 5. Preferred Workflow
- Not defined yet (auto-detected from code, not from user)
- Code style tool: Laravel Pint (`laravel/pint` in require-dev)
- Tests: Pest (`pestphp/pest` ^3) + Pest Laravel plugin; run via `composer test`
- Conventions: standard Laravel 12 structure, PSR-4 `App\` namespace
- UI copy language: Dutch (nl), matching the client design

## 6. Rules & Gotchas
- Never: commit `.env` (real keys) — only `.env.example`
- Never: commit secrets or API keys
- Always: run `composer test` after changes; run `npm run css` after changing Tailwind classes/components in blade/JS (compiles resources/css/app.css → public/assets/css/app.css)
- Assets: ALL runtime JS/CSS lives in `public/assets/` and is linked via `asset()` — no `@vite` in layouts. JS edits need NO build step (edit file, refresh browser). Tailwind class changes need `npm run css`
- Vendor libs (jquery/axios/alpine) are local copies in `public/assets/js/vendor/` — no CDN dependency
- Always: keep the `users` table fields (already customized) intact — they are the core of this app's domain
- Existing custom user fields (do not remove without reason): `phone`, `is_blocked`, `house_number`, `street`, `postcode`, `city`, `role` (enum: admin/user/technician), `klantnummer`
- **Blade gotcha**: never pass raw HTML via `:icon='<svg>...'` / `:action='<span>...'` component attributes — Blade compiles them as a PHP expression and breaks (ParseError). Icons/actions must go through named slots (`<x-slot name="icon">`, `<x-slot name="action">`)
- Dark mode: always provide `dark:` variants or use the CSS variables (var(--c-*)) for new UI
- Loading states: any form gets `data-loading` on the `<form>` (or the submit button) — design.js shows a spinner and disables the button; links use `data-loading` attribute
- Theme toggle relies on `localStorage` `slimmepc-theme` — don't rename without updating design.js
- Admin security: every admin route MUST use `middleware(['auth', 'verified', 'admin'])` (alias registered in bootstrap/app.php). Never add an admin route outside the `admin.` group
- Admin layout lives in `resources/views/components/admin/layout.blade.php` (used as `<x-admin.layout>`) — NOT in `views/admin/layouts/` (anonymous components only resolve from `views/components/`)
- Landing CSS: Tailwind class changes in `resources/views/landing/**` require `npm run css:landing` (→ `public/assets/css/landing.css`); plain CSS/JS changes need no build
- CMS gotchas: `config/cms.php` drives the admin editor AND `updateSection` validation — a block not listed there cannot be saved; json blocks post as `blocks[key][]` and are normalized in `ContentController::normalizeJson`; image uploads come in as `blocks.{key}_file` (single fields) or `blocks.{key}.{i}.{field}_file` (json rows) and are stored to `public/assets/img/landing` with `hashName()`; design forms post `design[group][key]` and `_hex` twin inputs are stripped on save. `'fixed' => true` on a json block locks the rows (no add/remove; a boolean field renders as a toggle in the row header). **Icon fields (`type: 'icon'`)** are stored as kebab-case strings and rendered by `admin/icon-picker.js`; it needs `lucide.min.js` loaded first in the admin layout (the vendored v0.469.0 registers icons under PascalCase keys — icon-picker.js converts kebab↔pascal for lookup/display)
- CMS saves must call `Cms::bust()` — never forget it, otherwise the landing page caches (**1-month TTL**) hide the change. `bust()` writes an always-unique version (`now()->format('Uv')` — microseconds) so even back-to-back saves in the same second invalidate. The full-page HTML cache (`cms.page.html.home.{version}`) and all `Cms::page/design` keys include the version → one bust refreshes everything on the next request
- Performance: below-the-fold images use `loading="lazy" decoding="async"`; the hero image stays eager with `fetchpriority="high"` (LCP) — never lazy-load above-the-fold images
- **Landing cache is guest-only**: the full-page HTML cache must never store a logged-in user's response (it would leak their name to all guests). `PageController@home` checks `Auth::check()` before reading AND before writing the cache
- **No user-facing `/dashboard` anymore**: the route + view were deleted (2026-08-12). All post-auth redirects point to `route('home')`; do not reintroduce a dashboard without updating every redirect + the header "Mijn account" links (they point to `profile.edit`)
- Admin test account: `slimmepc@admin.com` / `slimmepc@@#@10`

## 7. Sections & Pages / Main Features
| # | Section | Path/Page | Description & Details |
|---|---------|-----------|------------------------|
| 1 | Welcome / Landing | `/` | **CMS-driven landing page** rebuilt from `step-1/home.html` (3982 lines) into Blade partials: header (nav + mobile drawer + search overlay), hero (badge, 3-line gradient title, description, buttons, trust points + animated desktop orbit visual + mobile process steps), why (benefit hub + stats 4-in-a-row on desktop), services, shop (product carousel + dots), footer, floating chat/WhatsApp buttons. All content from DB via `Cms::page('home')`; design (colors/font/SEO) from `Cms::design()` → CSS vars. Editable in admin. No CDN: lucide vendored locally. **Full-page HTML cache** (1 month, invalidated instantly on admin save) **served to guests only** — logged-in users bypass it so their header shows their name + logout (dropdown via native `<details>`, no JS). Header bar is 72px tall (was 96px), logo 52px. **Desktop nav order** (per client): Home · Over ons → Webshop ▾ → Diensten ▾ → Lid worden · Tarieven · Contact (`nav_links` split into `$navBefore`/`$navAfter` around the two hardcoded dropdowns in `landing/partials/header.blade.php`). Fully responsive on small screens: `html, body { overflow-x: clip }` safety net, hero title uses `clamp(1.7rem,8vw,3rem)`, badges wrap instead of overflowing, shop CTA row stacks, product cards 100% width on mobile |
| 2 | Login | `/login` | Redesigned from `step-1/login.html`: auth card, logo, email + password with icons, password toggle, remember me, forgot link, gradient submit with loading |
| 3 | Register | `/register` | Redesigned from `step-1/register.html`: voornaam & achternaam, telefoon, e-mail, straat + huisnummer (2-col), postcode + stad (2-col), wachtwoord + herhaal (with toggles), terms checkbox, compact card. All address/contact fields saved to `users` table. Validation messages in Dutch via `lang/nl/validation.php` |
| 4 | Forgot password | `/forgot-password` | Redesigned: email input, "Stuur wachtwoord reset link" button, back-to-login link |
| 5 | Reset password | `/reset-password/{token}` | Redesigned: email + new password + confirm with toggles |
| 6 | Confirm password | `/confirm-password` | Redesigned: single password field + toggle |
| 7 | Verify email | `/verify-email` | Redesigned: resend button + logout link, green auto-dismiss alert |
| 8 | ~~Dashboard~~ (removed) | — | **Deleted entirely** (2026-08-12): `resources/views/dashboard.blade.php` + the `/dashboard` route are gone. After login/register/email-verify/password-confirm the user lands on the **home page** (`route('home')`). Logged-in users manage their account at `/profile` (link from the header dropdown "Mijn account" + mobile tile) |
| 9 | Profile | `/profile` | Auth only. Edit/update/delete profile via `ProfileController` (still Breeze styling). This is now the only logged-in user page besides the landing — "Mijn account" dropdown item + mobile name tile link here |
| 10 | Admin dashboard | `/admin` | Auth + verified + admin role only. Independent admin panel: dark sidebar (logo, nav: Dashboard + **Home-page** dropdown (split CMS) + **Users-beheren** dropdown (renamed from Klanten) + placeholders). Sidebar redesigned with **Heroicons SVG** (no emojis), elegant dropdown styling (left `border-white/30` border), **dark `#0b1638` background with light/white text** (`text-blue-100`/`blue-50` labels, sub-links and icons; active sidebar link = **white pill with `#1d4ed8` blue text**), and a 2-column "Binnenkort" section list that mirrors the old sidebar. Sticky topbar (title, theme toggle, user dropdown), stat cards (Klanten/Techniciens/Bestellingen/Reparaties), welcome banner, recent orders empty state, quick actions, system status. Fully responsive (sidebar → mobile drawer + overlay) |
| 11 | Users-beheren | `/admin/users` | Admin CRUD for customers/technicians/admins (controller still named `KlantController`). AJAX table (search, role filter, per-page, pagination), stats chips (users/technicians/admins), create/edit modal (auto klantnummer KL-YY-XXXX + auto password generation), details modal, delete confirm modal, inline role select + block/unblock toggle. All Dutch, no page refresh (axios + jQuery, `public/assets/js/admin/klanten.js` — endpoints target `/admin/users`). Route names `admin.users.*` (renamed from `admin.klanten.*`) |
| 12 | Home-page (CMS) | `/admin/content/design` (Ontwerp & SEO) + `/admin/content/home/section/{header\|hero\|why\|services\|footer}` | Admin CMS editor **split into separate pages** (no more single accordion page). The sidebar "Home-page" dropdown links to: **Ontwerp & SEO** (`design.blade.php` — SEO meta, 6 color pickers + hex sync, font select, saved as JSON in `content_meta`), **Header** (`section.blade.php` — logo image upload + logo text + tagline), **Hero** (badge, 3 title lines, description, 2 images with real file upload + live preview), **Waarom voor ons kiezen** (badge, title prefix/highlight, description, hub icon/title/subtitle, **Voordelen** 6 json cards, **Statistieken** 4 json cards), **Services** (8 fixed service cards: per-card image upload, icon picker, hide toggle) and **Footer** (bedrijfstekst, social, contact, trust, copyright, payment methods). Each page has a header card explaining what it controls + where it appears on the site (location badge). JSON repeaters render 2 cards per row when the block defines `'columns' => 2`; `'fixed' => true` locks the rows (hide toggle in the row header, no add/delete). **All icon fields use a searchable lucide icon picker** (button showing icon+name → dropdown grid of ~1743 icons, live search). Each page has its own save button (axios POST → JSON, **no page refresh — shows "Opgeslagen!" only**). The old top quick-nav tabs (Home-page secties / Live website bekijken) were **removed**. Saving calls `Cms::bust()` → instant frontend update |

## 8. Complete Routes

### web.php
| Method | URI | Name/Handler | Middleware/Auth |
|--------|-----|--------------|------------------|
| GET | `/` | `PageController@home` → `landing.home` (CMS content + design) — **name `home`** | none (guests served from full-page HTML cache; logged-in users always render fresh) |
| GET | `/profile` | `ProfileController@edit` | auth |
| PATCH | `/profile` | `ProfileController@update` | auth |
| DELETE | `/profile` | `ProfileController@destroy` | auth |

### admin.php (registered in bootstrap/app.php `withRouting(web: [...])`)
| Method | URI | Name/Handler | Middleware/Auth |
|--------|-----|--------------|------------------|
| GET | `/admin` | `Admin\AdminController@dashboard` → `admin.dashboard` | auth, verified, admin |
| GET | `/admin/dashboard` | `Admin\AdminController@dashboard` → `admin.dashboard.alias` | auth, verified, admin |
| GET | `/admin/content` | redirects to `admin.content.design.edit` | auth, verified, admin |
| GET | `/admin/content/design` | `Admin\ContentController@editDesign` → `admin.content.design.edit` (Ontwerp & SEO page) | auth, verified, admin |
| POST | `/admin/content/design` | `Admin\ContentController@updateDesign` → `admin.content.design` (JSON, saves design groups) | auth, verified, admin |
| GET | `/admin/content/{page}/section/{section}` | `Admin\ContentController@editSection` → `admin.content.section.edit` (separate section page; `{page}`/`{section}` alpha-only) | auth, verified, admin |
| POST | `/admin/content/{page}/section/{section}` | `Admin\ContentController@updateSection` → `admin.content.section` (JSON, saves one section + image uploads) | auth, verified, admin |
| GET | `/admin/users` | `Admin\KlantController@index` → `admin.users.index` | auth, verified, admin |
| POST | `/admin/users` | `Admin\KlantController@store` → `admin.users.store` (JSON, 201) | auth, verified, admin |
| GET | `/admin/users/data` | `Admin\KlantController@data` → `admin.users.data` (JSON: items + pagination + counts) | auth, verified, admin |
| GET | `/admin/users/{klant}` | `Admin\KlantController@show` → `admin.users.show` (JSON) | auth, verified, admin |
| PUT | `/admin/users/{klant}` | `Admin\KlantController@update` → `admin.users.update` (JSON) | admin.users.update |
| DELETE | `/admin/users/{klant}` | `Admin\KlantController@destroy` → `admin.users.destroy` (JSON; blocks self-delete 422) | auth, verified, admin |
| POST | `/admin/users/{klant}/role` | `Admin\KlantController@updateRole` → `admin.users.role` (JSON; blocks self-role-change 422) | auth, verified, admin |
| POST | `/admin/users/{klant}/toggle-block` | `Admin\KlantController@toggleBlock` → `admin.users.toggle-block` (JSON; blocks self + admins 422) | auth, verified, admin |

### auth.php
| Method | URI | Name/Handler | Middleware/Auth |
|--------|-----|--------------|------------------|
| GET | `/register` | `RegisteredUserController@create` | guest |
| POST | `/register` | `RegisteredUserController@store` | guest |
| GET | `/login` | `AuthenticatedSessionController@create` | guest |
| POST | `/login` | `AuthenticatedSessionController@store` | guest |
| GET | `/forgot-password` | `PasswordResetLinkController@create` | guest |
| POST | `/forgot-password` | `PasswordResetLinkController@store` | guest |
| GET | `/reset-password/{token}` | `NewPasswordController@create` | guest |
| POST | `/reset-password` | `NewPasswordController@store` | guest |
| GET | `/verify-email` | `EmailVerificationPromptController` | auth |
| GET | `/verify-email/{id}/{hash}` | `VerifyEmailController` | auth, signed, throttle:6,1 |
| POST | `/email/verification-notification` | `EmailVerificationNotificationController@store` | auth, throttle:6,1 |
| GET | `/confirm-password` | `ConfirmablePasswordController@show` | auth |
| POST | `/confirm-password` | `ConfirmablePasswordController@store` | auth |
| PUT | `/password` | `PasswordController@update` | auth |
| POST | `/logout` | `AuthenticatedSessionController@destroy` | auth |

## 9. Database Schema

### Table: `users`
| Column | Type | Description |
|--------|------|--------------|
| id | bigint (PK, auto) | Primary key |
| name | string | User's full name |
| phone | string, nullable | Phone number |
| is_blocked | boolean, default false | Whether the user is blocked |
| house_number | string, nullable | Address: house number |
| street | string, nullable | Address: street |
| postcode | string, nullable | Address: postal code |
| city | string, nullable | Address: city |
| role | enum('admin','user','technician'), default 'user' | Access level |
| email | string, unique | Login email |
| klantnummer | string, unique, nullable | Dutch customer number |
| email_verified_at | timestamp, nullable | Email verification timestamp |
| password | string | Hashed password |
| remember_token | string (nullable) | "Remember me" token |
| created_at / updated_at | timestamp | Timestamps |

### Table: `password_reset_tokens`
| Column | Type | Description |
|--------|------|--------------|
| email | string (PK) | Email address |
| token | string | Reset token |
| created_at | timestamp, nullable | When created |

### Table: `sessions`
| Column | Type | Description |
|--------|------|--------------|
| id | string (PK) | Session id |
| user_id | foreignId, nullable, indexed | Owning user |
| ip_address | string(45), nullable | Client IP |
| user_agent | text, nullable | Browser/device |
| payload | longText | Session data |
| last_activity | integer, indexed | Timestamp |

### Table: `cache` / `cache_locks`
(Standard Laravel — created by 0001_01_01_000001.)

### Table: `content_blocks`
| Column | Type | Description |
|--------|------|--------------|
| id | bigint (PK, auto) | Primary key |
| page | string | Page key (currently `home`) |
| section | string | Section key (`hero`, `why`, `header`, `services`, `shop`, `footer`, `floating`, `process`) |
| block_key | string | Block key within the section (`title_line1`, `description`, `products`, …) |
| type | string | text / textarea / image / json |
| value | text, nullable | String value for non-json blocks |
| json_value | longtext, nullable | JSON array for json blocks (products, benefits, nav links…) |
| sort_order | integer, default 0 | Order |
| created_at / updated_at | timestamp | Timestamps |
| **unique index** | (page, section, block_key) | One row per block |

### Table: `content_meta`
| Column | Type | Description |
|--------|------|--------------|
| id | bigint (PK, auto) | Primary key |
| meta_key | string, unique | `design` (design settings JSON) / `cache_version` (cache busting version — microtime `Uv`, unique per save → instant invalidation) |
| meta_value | longtext, nullable | JSON / string value |
| created_at / updated_at | timestamp | Timestamps |

### Table: `jobs` / `job_batches` / `failed_jobs`
(Standard Laravel — created by 0001_01_01_000002.)

## 10. Models/Entities & Relationships
```
User ──→ (sessions via user_id, owned)
ContentBlock / ContentMeta ──→ standalone CMS tables (no FKs)
```
| Model | Table | Relationships |
|-------|-------|----------------|
| User | users | Has many sessions (via foreign key), standard auth relations |
| ContentBlock | content_blocks | None (standalone). Casts `json_value` → array |
| ContentMeta | content_meta | None (standalone). `protected $table = 'content_meta'` (fixes Laravel pluralization) |

### Support helper: `App\Support\Cms`
| Method | Purpose |
|--------|---------|
| `version()` | Current cache version (`cache_version` meta), cached 1 month |
| `page($page)` | All blocks of a page as `[section][block_key] => value` (json decoded), cached **1 month** keyed by version |
| `get($page,$section,$key,$default)` | Single block value |
| `design()` | Design settings as flat `[key => value]` (handles both flat seeder data and grouped admin-form data), cached **1 month** keyed by version |
| `designValue($key,$default)` | Single design value |
| `bust()` | Writes an **always-unique** version (`now()->format('Uv')` — microseconds) → every cache keyed by version (incl. the full-page HTML cache) is invalidated on the next request |

### CMS data (seedered, 55 blocks + design)
- Hero: badge, title_line1/2/gradient, description, buttons (2), trust (3), hero_image, hero_image_mobile, hero_image_alt
- Why: badge, title_prefix, title_highlight, description, hub (icon/title/subtitle), benefits (6 cards), stats (4)
- Services: badge, titles, description, services (4 cards with icons/images)
- Shop: badge, titles, description, benefits (3), cta, note, products (4, with badge/badge_color/specs/price/in_stock), trust (3)
- Header: logo_text/tagline/logo_image, nav_links (5), webshop_dropdown (4), services_dropdown (4), search_placeholder, counts, account_label
- Footer: brand_about, social (4), contact (4), trust (4), copyright, payments (4) — brand name/tagline and the Diensten/Webshop/Over ons link columns are **no longer seeded** (brand is header-driven; link columns are hardcoded in the design)
- Floating: chat + WhatsApp tooltips/URLs
- Design JSON: meta_title/meta_description, brand_primary #2563eb, brand_primary_dark #1d4ed8, brand_accent #84cc16, brand_heading #020617, gradient_from #1d4ed8, gradient_to #3b82f6, font_family Figtree

### `config/cms.php` (drives admin forms + validation)
- `pages.home.sections`: editable sections — `header` (logo_image upload, logo_text, tagline), `hero` (badge, 3 title lines, description, 2 images), `why` (badge, title_prefix, title_highlight, description, hub_icon/title/subtitle, `benefits` json, `stats` json with **`'columns' => 2`**), `services` (**`'fixed' => true`** json block — 8 service cards, per-row image upload + hide toggle, no add/delete), `footer` (brand_about, social/contact/trust/payments json + copyright text; link columns are hardcoded in the design, not editable). json blocks support optional `'columns'` (2-col grid) and `'fixed'` (rows locked, boolean toggle in the row header, no add/remove); **icon fields use `type: 'icon'`** → rendered as the lucide icon picker. single fields: text / textarea / image / icon
- `design`: 3 groups — site (meta_title, meta_description), colors (6 color pickers), typography (font_family select with 5 Bunny fonts)

## 11. Controllers / Core Business Logic
| Controller/Module | Main Methods | Purpose |
|---------------------|---------------|---------|
| Admin\AdminController | dashboard | Admin dashboard view with stats (customer/technician counts from DB, orders/repairs placeholders) |
| Admin\ContentController | editDesign, editSection, updateSection, updateDesign | CMS editor (split pages). `editDesign` passes design groups + current design to `design.blade.php` (Ontwerp & SEO page). `editSection` validates `{page}`/`{section}` against `config/cms.php`, passes section schema + content to `section.blade.php`. `updateSection` validates against `config/cms.php`, upserts blocks (json blocks normalized + boolean cast), handles image uploads (`blocks.{key}_file` → `public/assets/img/landing` with hashName), then `Cms::bust()`. `updateDesign` stores design groups as JSON in `content_meta` (rejects `_hex` twin inputs) + busts cache |
| Admin\KlantController | index, data, store, show, update, destroy, updateRole, toggleBlock | Users-beheren CRUD (routes under `admin.users.*`, URL `/admin/users`; controller class name unchanged). `data` = search/role-filtered paginated JSON (+ role counts). `store` auto-generates klantnummer (KL-YY-XXXX) + password (`Str::password(10)`), returns `generated_password`. Guards: no self-delete, no self-role-change, no self-block, no blocking admins (all 422 JSON) |
| PageController | home | Landing page: **full-page HTML cache** — serves `cms.page.html.home.{version}` directly when cached (zero DB/Blade work); on miss renders `Cms::page('home')` + flat `Cms::design()` → `landing.home` and stores the HTML for 1 month. **Auth-aware**: `Auth::check()` bypasses the cache entirely (fresh render for the personalized header) and the result is never written into the shared guest cache |
| ProfileController | edit, update, destroy | Update/delete the authenticated user's profile |
| Auth\AuthenticatedSessionController | create, store, destroy | Login/logout — `store` redirects admins to `admin.dashboard`, **everyone else to `home`** (the old `/dashboard` destination was removed) |
| Auth\RegisteredUserController | create, store | Register a new user — stores name, phone, email, street, house_number, postcode, city + hashed password (Dutch validation via lang/nl) |
| Auth\PasswordResetLinkController | create, store | Send password reset link |
| Auth\NewPasswordController | create, store | Reset password with token |
| Auth\PasswordController | update | Change password (authenticated) |
| Auth\ConfirmablePasswordController | show, store | Re-confirm password |
| Auth\EmailVerificationPromptController / Notification / VerifyEmail | — | Email verification flow |

### Middleware
| Middleware | Alias | Purpose |
|------------|-------|---------|
| EnsureUserIsAdmin | `admin` (registered in bootstrap/app.php) | Aborts 403 unless `auth()->user()->role === 'admin'` |

### Blade design-system components (`resources/views/components/`)
| Component | Usage / Purpose |
|-----------|-----------------|
| `x-logo` | Slimme-PC logo image (`assets/img/logo.webp`), `:size="100"` |
| `x-auth-card` | Glassy auth card wrapper with blurred background blobs, `:max-width="'440px'"` |
| `x-theme-toggle` | Sun/moon light-dark toggle button (works via `data-theme-toggle`) |
| `x-input-label` | Form label, `:compact="true"` for 13px register style |
| `x-text-input` | Input with optional `icon` named slot, `toggle` (password eye), `:compact` |
| `x-input-error` | Red error box with message list |
| `x-auth-session-status` | Green success alert, auto-dismisses after 4s |
| `x-primary-button` | Gradient submit button with loading state (`data-loading`), `:compact`, `data-loading-text` |
| `x-nav-link` / `x-responsive-nav-link` | Nav links, theme-aware |
| `x-dropdown-link` / `x-dropdown` | Dropdown, dark-mode aware |

### Admin Blade components (`resources/views/components/admin/`)
| Component | Usage / Purpose |
|-----------|-----------------|
| `x-admin.layout` | Full admin layout: dark sidebar (logo + nav + logout), sticky glass topbar (title, theme toggle, user dropdown), content slot, footer. Mobile: sidebar becomes drawer with overlay. Pass `title` prop. Loads all assets from `public/assets` directly |
| `x-admin.sidebar-link` | Sidebar nav link: `:active` (white pill `bg-white text-[#1d4ed8]` when active, white text otherwise), `icon` slot, optional `badge` slot |
| `x-admin.stat-card` | Stat tile: `label`, `value`, optional `trend` + `trendUp`, `icon` slot |
| `x-admin.card` | Card with optional `title` + `action` named slot |
| `x-admin.modal` | Modal shell: `id`, `title`, `subtitle`, `size` (sm/md/lg/xl), `footer` slot. Opened via `window.SlimmePC.modal.open('id')` |

### Form request validation (`app/Http/Requests/Admin/`)
| Request | Purpose |
|---------|---------|
| `StoreKlantRequest` | Create: name/email required, unique email + klantnummer, role in [user,technician,admin], password nullable min:8. Dutch messages |
| `UpdateKlantRequest` | Update: same as store but unique rules ignore current klant |

### Frontend JS (`public/assets/js/`)
| File | Purpose |
|------|---------|
| `design.js` | Global SlimmePC lib: theme (localStorage + system pref), loading states (spinner + disabled), password toggle, auto-dismiss alerts, `SlimmePC.modal` (open/close/overlay/Escape), `SlimmePC.toast` (success/error), `SlimmePC.confirm` (dynamic confirm dialog), axios defaults (CSRF + X-Requested-With) |
| `admin/klanten.js` | Users-beheren: load/render table (search debounced, role filter, per-page, pagination), create/edit modal (PUT/POST, field errors inline), details modal, delete confirm, block/unblock, inline role change with confirm. Endpoints target `/admin/users`. Auto-inits only when `#klantTableBody` exists |
| `admin/content.js` | CMS editor: submit each section form via axios (FormData, **no page refresh — the old `window.location.reload()` was removed**), add/remove json rows (replaces `__INDEX__`), color picker ↔ hex sync, live image preview via FileReader, per-form success/error status text |
| `admin/icon-picker.js` | CMS icon picker: builds the dropdown grid from `Object.keys(lucide.icons)` (~1743 icons, displayed/stored in kebab-case via pascal↔kebab conversion with roundtrip safety), renders SVGs from the icon spec directly (no global `createIcons` re-scan), case-insensitive live search, kebab→pascal lookup for rendering, `MutationObserver` renders previews in dynamically added JSON rows. Requires `lucide.min.js` (loaded first in the admin layout) |
| `landing.js` | Landing page: lucide icons, mobile drawer + overlay, search overlay, accordions, Escape/resize handling, process orbit pause on hover, shop carousel (responsive perPage + dots) |
| `admin/loader.js` | Page-navigation loader for the whole admin: intercepts internal link clicks + native form submits (skips AJAX forms via `defaultPrevented`), stores `sessionStorage.adminPageNav`; the arriving page's inline script reads the flag, shows `#admin-loader` and fades it out on `load` (6s safety) |

### Landing CSS pipeline
- Source: `resources/css/landing.css` (@tailwind directives + `landing-base.css` import + design-var classes) → build: `npm run css:landing` → `public/assets/css/landing.css`. **Run it after any Tailwind class change in `resources/views/landing/**` or the var-driven classes in the source**
- Design vars (`--brand-primary`, `--brand-primary-dark`, `--brand-accent`, `--brand-heading`, `--brand-gradient-from/to`) are injected per-request in `landing/layouts/app.blade.php` from `$design`; `body` font-family from `font_family`. Only the selected family is requested from Bunny Fonts (single-family URL + `display=swap`) — was all 5 families (~heaviest asset) before

## 12. Data Flow
```
[Admin CMS (Website inhoud)] → POST section/design → ContentController → content_blocks / content_meta
        ↓ (Cms::bust: cache_version = microtime, always unique → old keys ignored instantly)
[Browser GET /] → [PageController@home]
        ├─ GUEST + HIT   "cms.page.html.home.{version}" → return stored HTML (no DB, no Blade render)
        ├─ GUEST + MISS  → [Cms::page('home') + Cms::design()]  (cached 1 month, keyed by version)
        │               ↓ render [landing.home Blade partials (CSS vars + assets)] → store HTML 1 month
        └─ AUTH USER     → always render fresh (personalized header: name + logout), never cached
Logo (site-wide): landing header/footer, favicon, auth `<x-logo>`, logged-in nav, admin sidebar — all read Cms::page('home')['header']['logo_image'] from the CMS
```

## 13. Active Build Plans

### Plan: Admin modules (Klanten / Bestellingen / Reparaties / Producten / Instellingen)
- **Goal:** Replace the placeholder sidebar links with real CRUD modules in the admin panel.
- **Steps:**
  1. Pick a module (recommended: Klanten — customers list, create, edit, block)
  2. Controller + routes in `routes/admin.php` under the `admin.` group
  3. Views under `resources/views/admin/<module>/` reusing `x-admin.layout`, `x-admin.card`, `x-admin.stat-card`
  4. DB migrations + models + relationships (documented in section 9/10 when added)
  5. Wire real stats into `AdminController@dashboard`
- **Current status:** Users-beheren DONE (renamed from Klanten). CMS ("Home-page") DONE v3 (split into separate pages: design + section editors; JSON repeaters with 2-col layout; save without refresh). Next: Bestellingen (orders) or Reparaties, or extend the CMS to services/shop/footer sections.

### Plan: Landing page CMS (DONE v1)
- **Goal:** Convert `step-1/home.html` to Blade + DB-driven content with admin editing.
- **Steps:**
  1. `content_blocks` + `content_meta` migrations, `ContentBlock`/`ContentMeta` models, `Cms` helper with version-busting cache, `config/cms.php` schema DONE
  2. `ContentBlockSeeder` (63 blocks, Dutch, mirrored from home.html) + design JSON DONE
  3. Assets copied (css/js/images/lucide vendored) + `landing.css` Tailwind pipeline (`npm run css:landing`) DONE
  4. Blade conversion: `landing/layouts/app.blade.php` + `home.blade.php` + 8 partials DONE (verified 200, all content + images present)
  5. Admin CMS editor: page tabs + design accordion + per-section accordions DONE (limited to hero + why per client request; image fields are real file uploads with live preview)
  6. Verified end-to-end: login → save hero title → homepage reflects instantly (cache bust); restore works
- **Current status:** DONE v2. Visual details fixed vs original `step-1/home.html` (stats = one row of 4 on desktop with original borders/colors; landing.css rebuilt — stale build was missing classes; lucide upgraded to v0.469.0 with brand icons). Performance: full-page HTML cache (1 month, instant bust), single font family, lazy below-fold images, hero fetchpriority=high. Optionally expose services/shop/footer sections in the editor later.

## 14. Changelog
- [2026-08-11] — Initial `project-structure.md` created from code scan. Project recognized as fresh Laravel 12 + Breeze (Blade) app with a customized `users` table (roles + Dutch address/customer fields).
- [2026-08-11] — Design system v1 built: brand colors + light/dark themes (CSS vars + class dark mode), Figtree font, `public/assets/img` (logo), Tailwind config (brand palette, shadows, animations), app.css component classes (auth-card, btn-primary, form-input, spinner, blobs). jQuery installed.
- [2026-08-11] — JS design layer: `design.js` (theme toggle with localStorage `slimmepc-theme`, loading states on all forms/buttons/links, password toggle, auto-dismiss alerts), `bootstrap.js` (jQuery + axios + CSRF).
- [2026-08-11] — Auth screens converted from `step-1` HTML designs to Blade components: login, register, forgot-password, reset-password, confirm-password, verify-email (all Dutch). Guest layout = auth card + blobs + floating theme toggle. Dashboard + app layout + navigation themed for dark mode.
- [2026-08-11] — Fixed Blade ParseError: raw HTML via `:icon='...'` attribute is invalid — switched to `<x-slot name="icon">` in text-input component. All auth pages verified rendering (200).
- [2026-08-11] — Admin panel v1: `routes/admin.php` (registered in bootstrap/app.php), `EnsureUserIsAdmin` middleware (alias `admin`), `Admin\AdminController@dashboard`, role-based login redirect in AuthenticatedSessionController (admin → `/admin`, user → `/dashboard`), User model helpers `isAdmin()/isTechnician()/isCustomer()`, `AdminUserSeeder` (slimmepc@admin.com), admin layout + 3 components, Dutch responsive admin dashboard with live stats. Verified: admin login → /admin, user login → /dashboard, non-admin /admin → 403.
- [2026-08-11] — **Assets architecture change**: everything moved from Vite/resources to `public/assets` (user requirement). `design.js` + new `admin/klanten.js` now plain JS in `public/assets/js/` (no imports, global `$`/`axios`); jquery/axios/alpine vendored locally in `public/assets/js/vendor/`. Tailwind compiled via `npm run css` → `public/assets/css/app.css`. All 3 layouts (admin/guest/app) link assets directly with `asset()` — `@vite` removed. `tailwind.config.js` content now scans `public/assets/js/**/*.js`. Added `SlimmePC.confirm` + axios CSRF defaults to design.js.
- [2026-08-11] — **Klantenbeheer module v1** (admin): `KlantController` (index/data/store/show/update/destroy/updateRole/toggleBlock, all JSON), `StoreKlantRequest` + `UpdateKlantRequest` (Dutch messages), 8 routes under `admin.klanten.*`, view `resources/views/admin/klanten/index.blade.php` with search + role filter + per-page + stats chips + AJAX table + 3 modals (create/edit `x-admin.modal`, details, delete confirm). Auto klantnummer KL-YY-XXXX + auto-generated password on create (returned via toast). Guards: no self-delete/self-role-change/self-block, admins can't be blocked. Sidebar Klanten link now live. JS: `public/assets/js/admin/klanten.js` (axios CRUD, debounced search, pagination, inline role select + block toggle). Verified: routes registered, all views compile, assets serve 200.
- [2026-08-11] — **Dutch validation system**: created `lang/nl/validation.php` (full Dutch message set + Dutch field-name attributes) and set app locale to `nl` (config/app.php). ALL validation errors across the app now render in Dutch (e.g. "Het e-mailadres is al in gebruik.", "Het wachtwoord moet ten minste 8 tekens bevatten.").
- [2026-08-11] — **Registration extended**: `auth/register.blade.php` now collects voornaam & achternaam, telefoon, e-mailadres, straat, huisnummer, postcode, stad, wachtwoord + herhalen (2-column grids for address fields, icons + toggles, Dutch labels). `RegisteredUserController@store` validates + saves all fields to `users`. Verified end-to-end: POST /register → 302 /dashboard, all fields persisted, Dutch error messages shown.
- [2026-08-11] — **CMS foundation**: migrations `2026_08_11_000001_create_content_blocks_table` (unique page+section+block_key) + `2026_08_11_000002_create_content_meta_table`; models `ContentBlock` (json_value cast) + `ContentMeta` (`$table='content_meta'` fix); `App\Support\Cms` helper (version()/page()/get()/design()/designValue()/bust()); `config/cms.php` schema; `ContentBlockSeeder` (63 Dutch blocks + design JSON + cache_version) wired into `DatabaseSeeder`. Migrations + seed ran on MySQL `slimmepc_2026`.
- [2026-08-11] — **Landing page assets**: copied `step-1` style.css → `public/assets/css/landing-base.css`, script.js → rewritten `public/assets/js/landing.js`, images → `public/assets/img/landing/` (logo.webp + 13 product/hero images), lucide vendored → `public/assets/js/vendor/lucide.min.js` (npm lucide dependency added). New Tailwind pipeline `npm run css:landing`: `resources/css/landing.css` (directives + base import + design-var classes: .bg-brand-btn, .bg-brand-gradient-br, .gradient-text, .text-brand-heading…) → `public/assets/css/landing.css`. tailwind.config.js: accent palette + navbar/menu shadows.
- [2026-08-11] — **Landing Blade conversion**: `PageController@home` + `routes/web.php` `GET /`; `landing/layouts/app.blade.php` (Bunny Fonts Figtree/Inter/Poppins/Roboto/Merriweather, CSS vars from `$design`, lucide + landing.js) + `home.blade.php` + 8 partials (header/hero/why/services/shop/footer/floating) driven by `Cms::page('home')`. Fixed 500 (design array_merge when flat) — `Cms::design()` now normalizes flat vs grouped. Verified homepage 200 with all sections, images, CSS vars, icons.
- [2026-08-11] — **Admin CMS editor v1** ("Website inhoud", `/admin/content`): `Admin\ContentController` (index/updateSection/updateDesign) + 3 routes (`admin.content.index/design/section`); editor view with page tabs + "Ontwerp instellingen" accordion (SEO + 6 colors + font select, JSON in content_meta) + per-section accordions; `public/assets/js/admin/content.js` (axios saves, json rows, color sync); sidebar "Website inhoud" link + JS include. Fixed bugs: `route('admin.content')` → `admin.content.index`; `$schema` not captured in transaction closure (silently saved nothing). Per client request narrowed to **only hero + why** sections (badge, title, description, image for hero; title + description for why) — details/benefits/stats stay seeded. Fixed later: image fields upgraded from path-typing to **real file uploads** (`blocks.{key}_file` → stored in `public/assets/img/landing` with hashName) + live preview + keep-current-if-empty. Verified end-to-end via HTTP: login → save hero title → homepage updates instantly (cache bust), values restored.
- [2026-08-11] — **Landing design repaired**: `public/assets/css/landing.css` was a stale build (missing `from-brand-950`, `shadow-navbar`, etc.) — rebuilt via `npm run css:landing` (tailwindcss CLI actually works; earlier assumption wrong). Vendored lucide was v1.31.0 (417KB, brand icons removed) — replaced with **v0.469.0** (358,106 bytes); all 42 used icons verified present (facebook/instagram/youtube included; stored PascalCase like `ArrowRight`). Console `contentscript.js`/`ObjectMultiplex` errors traced to the MetaMask browser extension — not the app.
- [2026-08-11] — **Why section fully editable in the CMS**: `config/cms.php` now exposes badge, hub_icon/hub_title/hub_subtitle, `benefits` (6 json cards: icon/title/description) and `stats` (4 json cards: icon/value/label). Data was already seeded in `ContentBlockSeeder`. Reused the existing JSON repeater UI (`data-row-template` + `__INDEX__` replacement).
- [2026-08-11] — **Admin saves no longer reload the page**: removed `window.location.reload()` from `admin/content.js` (was executing ~900ms after every successful save on the content page); saves now show "Opgeslagen!" only. The klanten page was verified pure AJAX already (`#klantForm` has no action/method, all buttons `type="button"`, Enter intercepted with `preventDefault` at klanten.js:546).
- [2026-08-11] — **Stats cards layout**: admin editor renders JSON rows 2-per-row via a new `'columns' => 2` option on json blocks (`config/cms.php` + `admin/content/index.blade.php` conditional grid). Frontend stats section now matches `home.html` exactly: **4 stats in ONE row on desktop** (`sm:grid-cols-2 lg:grid-cols-4` + per-card borders identical to the original), icons `bg-blue-50 text-blue-600` (last card `bg-lime-50 text-lime-600`), values `text-blue-700`.
- [2026-08-11] — **Content restored to originals**: re-ran `ContentBlockSeeder` after test edits polluted many fields with trailing "1" junk (`title_prefix "…voor1"`, hub_subtitle "…Reparatie1", benefits icons "zap11", stats "2500+1" + "Lokaal & betrokken1", design meta "…Apeldoorn1", truncated hero description, different hero images). Hero/why/header/services/shop/footer/floating + design JSON all back to `step-1/home.html` values (hero images `53f89edd…`/`b4c74892…` verified on disk).
- [2026-08-11] — **Header section in the CMS + global logo**: new editable `header` section (logo_image real upload, logo_text, tagline — first accordion in the editor). The logo now propagates **site-wide**: landing header (desktop + mobile — already `$c['header']`), footer (image replaces the letter badge, fallback kept), favicon (`landing/layouts/app.blade.php` line 11), auth pages via `<x-logo>` (now reads `Cms::page('home')['header']['logo_image']`), logged-in user nav (`layouts/navigation.blade.php`) and the admin sidebar (`components/admin/layout.blade.php`) — all from one CMS value.
- [2026-08-11] — **Admin page-navigation loader**: `#admin-loader` element at the top of the admin layout (3px animated gradient top bar + blurred overlay + card with the site logo + spinner + "Laden...") + new `public/assets/js/admin/loader.js`: intercepts internal link clicks and native form submits (skips AJAX forms via `defaultPrevented`), stores flag `sessionStorage.adminPageNav`; the arriving page shows the loader immediately (inline script right after `<body>`) and fades it out on `window.load` (6s safety). Dark-mode ready (CSS vars), respects `prefers-reduced-motion`, cache-busted include via filemtime.
- [2026-08-11] — **Landing performance / full-page caching**: `PageController@home` now serves the **fully rendered HTML** from cache keyed by the CMS version (`cms.page.html.home.{version}`, TTL **1 month**) — zero DB/Blade work on hit; on admin save the version changes → next request re-renders fresh (direct update for the user). All `Cms` cache layers moved from 1h to **1 month** (`now()->addMonth()`); `Cms::bust()` writes a microtime version (`now()->format('Uv')`) so repeated saves always invalidate. `landing/layouts/app.blade.php` now loads **only the selected font family** from Bunny Fonts (was all 5 at once — the heaviest asset) with `display=swap`. Applied `loading="lazy" decoding="async"` to all 14 below-fold images (8 services + 5 products + footer logo), `fetchpriority="high" decoding="async"` on the hero image (LCP stays eager), `decoding="async"` on the rest.
- [2026-08-12] — **Personalized header by login state**: `PageController@home` bypasses the full-page cache for authenticated users (fresh render, never cached — guests keep the cached HTML). Landing header account area is auth-aware: guest → "Inloggen" (`login`) + "Aanmelden" (`register`) buttons (desktop) / Inloggen tile (mobile drawer); logged-in → user name on the brand-accent button opening a native `<details>` dropdown (no JS) with "Mijn account" → `profile.edit` + "Uitloggen" (POST `logout`, CSRF); mobile drawer shows the name tile + full-width Uitloggen button. Verified: 18 auth tests pass, `view:cache` compiles, icons exist in vendored lucide (UserRound/LogIn/UserPlus/ChevronDown/LayoutDashboard/LogOut), landing.css rebuilt.
- [2026-08-12] — **User-facing Dashboard removed completely**: deleted `resources/views/dashboard.blade.php` + the `/dashboard` route; `GET /` now named `home`. All post-auth redirects (login, register, email verification prompt/notification/verify, confirm-password, `RedirectIfAuthenticated` fallback) now land on `home`. Header dropdown/mobile tile "Mijn account" → `/profile`. `layouts/navigation.blade.php` logo + nav links → home; `welcome.blade.php` link → `/`. Tests updated (`route('dashboard')` → `route('home')` in AuthenticationTest/RegistrationTest/EmailVerificationTest). Only `admin.dashboard` remains. Verified: 18 auth tests pass, no `route('dashboard')` references left in app code.
- [2026-08-12] — **Landing responsiveness on small screens**: root causes of horizontal overflow fixed — (1) `html, body { overflow-x: clip }` safety net in `resources/css/landing.css`; (2) shop CTA row (`Bekijk All!` + "Meer dan 500 producten online!") was a single flex line wider than the viewport → stacks on mobile (`flex-col sm:flex-row`, full-width button); (3) badges are `inline-flex` pills whose long text ("Slimme-PC Webshop", hero badge) couldn't wrap → `flex-wrap max-w-full text-center` on mobile, back to inline-flex at `sm:`; (4) hero h1 38px→`clamp(1.7rem,8vw,3rem)` (drops the odd `xl:text-[40px]` shrink); (5) shop product cards `flex: 0 0 88%` → `100%` on mobile (card fully inside the screen, next card hidden until swipe); (6) header shortened 96px→72px with 52px logo; (7) `min-w-0` on the shop grid's product column. Rebuilt via `npm run css:landing`.
- [2026-08-12] — **Admin CMS content editor redesigned**: overhauled `/admin/content` layout for visual organization — (1) added top workflow helper strip ("Zo werkt het"); (2) numbered section accordions (`01`, `02`, `03`...) with section-specific gradient icons; (3) 2-column responsive grid layout for form fields (`sm:grid-cols-2`); (4) card-styled JSON repeaters (`json-row.blade.php`) with numbered badges (`#01`, `#02`...) and trash icons; (5) updated `public/assets/js/admin/content.js` to auto-number newly appended JSON rows. All JavaScript data attributes and submit hooks preserved.
- [2026-08-12] — **CMS editor split into separate pages** (replaces the single accordion page): removed `resources/views/admin/content/index.blade.php`; added `design.blade.php` (Ontwerp & SEO — SEO meta + 6 color pickers + font select) and `section.blade.php` (one section per page: header/hero/why, with a location badge + per-field guidance). New routes in `routes/admin.php`: `GET /admin/content` → redirect to `admin.content.design.edit`; `GET/POST /admin/content/design` (`editDesign`/`updateDesign`); `GET/POST /admin/content/{page}/section/{section}` (`editSection`/`updateSection`, alpha-only params, `whereAlpha`). Sidebar "Website inhoud" renamed to **"Home-page"** and made a dropdown listing Ontwerp & SEO, Header, Hero, Waarom voor ons (each links to its page). Removed the top quick-nav tabs (Home-page secties / Live website bekijken) from both editor pages for a cleaner look.
- [2026-08-12] — **Admin sidebar fully redesigned**: replaced all emoji icons in `components/admin/layout.blade.php` with **Heroicons SVG** (inline, `text-blue-400` accent), kept the same 12-group structure (Dashboard, Home-page, Diensten, Pages, Webshop, Website-aanvragen, Contact-klanten, Bestellings, Afspraken, Abonnement, Prijs-instellingen, Users-beheren, Manual Invoices) but improved the dropdown styling — left brand-colored border (`border-blue-500/40`), indented sub-items, and active sub-link = `bg-white/10 text-white font-bold shadow-sm`. Both real modules (Home-page, Users-beheren) now open as elegant dropdowns matching the Binnenkort placeholder styling.
- [2026-08-12] — **Klanten renamed to Users** in the router: `routes/admin.php` prefix `klanten` → `users` and name `klanten.` → `users.` (URL is now `/admin/users`, route names `admin.users.*`). Sidebar dropdown label "Klanten" → "Users"; `public/assets/js/admin/klanten.js` endpoints updated from `/admin/klanten` to `/admin/users`. Controller class `KlantController` and view `admin/klanten/index.blade.php` kept as-is. Verified with `php artisan route:list` (8 `admin.users.*` routes registered).
- [2026-08-12] — **Landing header nav reordered** (`resources/views/landing/partials/header.blade.php`): the client asked for the top menu to read **Home · Over ons → Webshop ▾ → Diensten ▾ → Lid worden · Tarieven · Contact** (Webshop + Diensten dropdowns between the two groups instead of after all nav_links). Implemented by splitting `nav_links` into `$navBefore` (first 2) and `$navAfter` (rest) via `array_slice` at the top of the partial, rendering `$navBefore` → Webshop dropdown → Diensten dropdown → `$navAfter`, identically for desktop nav and the mobile drawer. Data order in `ContentBlockSeeder`/DB unchanged (Home, Over ons, Lid worden, Tarieven, Contact).
- [2026-08-12] — **Admin sidebar colors adjusted** (`components/admin/layout.blade.php` + `components/admin/sidebar-link.blade.php`): the client first asked to lighten the dark-navy `#0b1638` sidebar (tried a `#3b82f6 → #2563eb` gradient) then reverted to **keep the dark `#0b1638` background** but with **lighter, whiter text**. Final state: background stays `#0b1638`; dropdown buttons/links use white (`rgba(255,255,255,0.95)` / `text-white`), section labels + sub-links + icons use `text-blue-100`/`text-blue-50` (was `text-slate-500`/`text-blue-400`), dropdown indent border `border-white/30` (was `border-blue-500/40`), Binnenkort badges `bg-white/20 text-white`; the active sidebar link is now a **white pill with dark-blue text** (`bg-white text-[#1d4ed8]`, icon follows) instead of the dark blue gradient. Rebuilt via `npm run css` (new classes confirmed in compiled `app.css`).
- [2026-08-13] — **First push to GitHub + repo hygiene**: remote corrected to `https://github.com/yisefmahdi/slimmepc.git` (initial wrong remote `Slimme-pc/slimmepcbackend-laravel.git` removed); `git pull --rebase --allow-unrelated-histories` merged GitHub's auto README; pushed `8c12e2f` → all 184 files live on `origin/main` (branch fully in sync, working tree clean). Because the client kept seeing old "Last commit" dates per folder (normal GitHub per-folder display — it only shows when files in that folder last changed), created two cosmetic commits: `bf09f2c` (empty "Force refresh") and `795b3fe` "Touch all text files to refresh timestamps" (appended a newline to all 161 tracked text files; 22 binary images skipped to avoid corruption). Files were already all present before — the touch was purely cosmetic.
- [2026-08-14] — **Toast messages across the dashboard + editor polish**: the CMS editor (`admin/content.js`) now uses the global `window.SlimmePC.toast.success/error` (bottom-right toast defined in `design.js`, already used by klanten.js) instead of the inline `.form-status` text — saving any section (services, header, hero, why, design) now shows the same "Opgeslagen!" / error toast as the Users page. Hide toggle moved to the **last field** of each service card (`config/cms.php` field order: image, title, icon, description, link[hidden], hidden). `node --check` passed on content.js; JS assets are cache-busted via `filemtime` in the admin layout. **Small-screen fixes**: the admin header title (e.g. "Home-page - Services (Onze diensten)") no longer breaks mobile layout — it's hidden on `sm`-and-below (`hidden sm:block`) with `min-w-0 truncate` on larger screens. The "Verberg van de homepage" toggle moved from the card grid into the **row header, replacing the "Vast item" badge** (green switch + label beside it); the boolean field is skipped in the grid for fixed blocks so no duplicate input is submitted. **Auth translations fixed**: created `lang/nl/auth.php` (`failed`, `password`, `throttle`) — before, `trans('auth.failed')` etc. had no Dutch file and rendered the raw key text (`auth.failed`) under the login fields. Verified via tinker: `trans('auth.failed')` → "Deze inloggegevens komen niet overeen met onze gegevens.", throttle message interpolates `:seconds`. **Mobile layout decompressed**: reduced horizontal padding on small screens across the admin — main content/footer `px-4`→`px-3` (`components/admin/layout.blade.php`); section & design editor cards `px-6 py-6`→`px-4 py-5` (sm keeps `px-6`), json blocks `p-5`→`p-4`, action footers `-mx-6 -mb-6 px-6`→`-mx-4 -mb-5 px-4` (sm: `sm:-mx-6 sm:-mb-6 sm:px-6`), field grids `gap-6`→`gap-5`. Rebuilt `app.css` (new classes confirmed).
- [2026-08-14] — **Services section editor (edit-only + hide toggle)**: new `services` section in `config/cms.php` (badge, title_prefix/highlight/suffix, description + `services` json block with `'fixed' => true`, `columns => 2`, fields hidden/image/title/icon/description/link). `section.blade.php` gains a `services` info card and hides the "Item toevoegen" button + switches the helper text when a block is `fixed`; `json-row.blade.php` hides the "Verwijderen" button for fixed blocks (shows a "Vast item" badge instead). Per-service **Apple-style green toggle** (`peer`/`peer-checked` switch, green when ON) replaces the plain checkbox — ON = hidden from homepage. **Per-row image upload**: `image` fields now render a live preview + file input (`blocks.{key}.{i}.{field}_file`); `ContentController@normalizeJson` (now receives the Request + block key, iterates original indexes) validates the upload (image ≤5MB) and moves it to `assets/img/landing` storing just the filename; `json-row.blade.php` previews via `asset('assets/img/landing/' . value)`; `admin/content.js` `updateSavedImages` extended to update JSON-row hidden inputs/previews after save (json block divs tagged `data-block-key`). Landing `services.blade.php` skips hidden services (`@continue(!empty($service['hidden']))`). Seeder: `hidden => false` on all 8 services. Sidebar: the "Services (Binnenkort)" placeholder under Home-page is now a **live link** to the services editor. Rebuilt via `npm run css` (new toggle classes confirmed in compiled app.css) + cache/view/config cleared. **Follow-up refinements**: removed the duplicated "Verberg van de homepage" text from inside the toggle (label now only sits above the switch, no "(aan = verborgen…)" hint); removed the image filename text from the per-row preview (only the live preview + "Ondersteund: PNG, JPG, WEBP (Max 5MB)" remain); the `link` field is hidden in the editor via `'hidden' => true` (renders as a hidden input so its value round-trips and is never wiped — link will be fixed per service in a later phase).
- [2026-08-15] — **Footer CMS editor**: new `footer` section in `config/cms.php` (editable: `brand_about` textarea, `social` json (icon+url), `contact` json (icon+label+value textarea), `trust` json (icon+title+subtitle), `copyright` text, `payments` json (label); all json blocks non-fixed → add/edit/delete rows). `section.blade.php` gets a `footer` info card; the sidebar "Footer (Binnenkort)" placeholder became a **live link** to the footer editor. No frontend/design change — `footer.blade.php` untouched at this stage.
- [2026-08-15] — **Footer link columns made static**: per client request the three link columns (Diensten/Webshop/Over ons) are **hardcoded in the design** and removed from the CMS: `config/cms.php` dropped `services_col`/`shop_col`/`about_col`; `footer.blade.php` now renders the links from inline PHP arrays (same labels/URLs as before); `ContentBlockSeeder` cleaned of the dead data.
- [2026-08-15] — **Icon picker component (CMS)**: all 7 icon inputs (`hub_icon` single field; `benefits[].icon`, `stats[].icon`, `services[].icon`, `social[].icon`, `contact[].icon`, `trust[].icon`) changed from `type: text` to `type: icon` in `config/cms.php`. New `public/assets/js/admin/icon-picker.js` + `.icon-picker-*` styles in `resources/css/app.css` (rebuilt via `npm run css`): each field is now a select-like button showing the current icon + name; clicking opens a searchable dropdown grid of all ~1743 lucide icons (list built from `Object.keys(lucide.icons)`, no hardcoded list). Icon names are displayed/stored in **kebab-case** (matches existing seeded data) via a pascal→kebab conversion with a roundtrip safety net (falls back to the Pascal name if not resolvable) and rendered through a kebab→pascal lookup. `renderIcon()` builds the SVG from the icon spec `[tag, attrs, children]` directly (no global `createIcons` re-scan). Search is case-insensitive. A `MutationObserver` re-renders preview icons in dynamically added JSON rows (no changes to `content.js`). `lucide.min.js` is now loaded in the admin layout before `icon-picker.js` (both cache-busted via filemtime). **Fixed the PascalCase bug**: lucide v0.469.0 registers icons under PascalCase keys (`Facebook`, `ShieldCheck`) — the original kebab-case lookup returned undefined so previews never rendered and case-sensitive search matched nothing. **Fixed row height**: in JSON rows the icon-picker trigger now uses `px-3 py-1.5` (matches the compact text inputs beside it; the shared CSS default `px-4 py-3` only applies to single fields like hub_icon).
- [2026-08-15] — **Sidebar cleanup**: removed the "Webshop (Binnenkort)" placeholder from the Home-page dropdown in `components/admin/layout.blade.php`.
- [2026-08-15] — **Footer brand tied to the header**: `footer.blade.php` brand block now reads `header.logo_text` (name) + `header.tagline` (subtitle) instead of the old footer fields; the fallback letter badge derives from the first letter of `logo_text` (`mb_strtoupper(mb_substr(...))`). Removed `brand_name`/`brand_tagline`/`brand_badge_letter` from `ContentBlockSeeder` (dead data). Editing the header name/tagline in the admin updates the footer automatically.
- [2026-08-15] — **Tarieven (pricing) page — CMS + landing** (from `tariven.html` design):
  **Admin CMS** — new `tarieven` page in `config/cms.php` with **3 sections**: `hero` (badge, title_line1/2, description, button1/2 text+url, hero_image, hero_image_alt, `trust_points` json), `pricing` (heading, description, `categories` json: icon/label/title/description/image/notice **+ nested `prices`**), `extra` (heading-level: `accordions` json: icon/title/accent/description **+ nested `prices`**; `trust_cards` json). **Nested JSON editor**: new `type: 'nested'` field in `config/cms.php`; `json-row.blade.php` renders nested blocks; new partials `json-nested-row.blade.php` (container + `data-add-nested-row` + `data-nested-row-template` with `__NINDEX__` placeholder) + `json-nested-row-item.blade.php` (`.json-nested-row` class, `data-remove-nested-row`, icon pickers work via existing delegation + MutationObserver); `admin/content.js` gained nested add/remove handlers (`__NINDEX__` replacement, no `.json-row` clash → `updateSavedImages` unaffected); `ContentController::normalizeJson` refactored to recursive `normalizeItems($request, $prefix, $value, $fields)` so nested prices save cleanly (verified via tinker: save → "Opgeslagen.", nested price `Reparatie / vanaf / €99` round-trips, original data restored). `editSection` title + `section.blade.php` info cards now **page-aware** (`$sectionInfos[$page][$section]`, `pageLabel`). Sidebar: new **Tarieven** dropdown (Hero / Tarieven & Prijzen / Algemene & Zakelijke tarieven) + Home dropdown active checks now include `page === 'home'`.
  **Default data + caching**: `database/data/tarieven.php` (shared source: all 5 categories + 25 prices, both accordions + 11 price rows, 4 trust cards — exact design values); idempotent migration `2026_08_15_000001_seed_tarieven_content_blocks.php` uses **`firstOrCreate`** (never overwrites admin edits on auto-deploy) + `Cms::bust()`; `ContentBlockSeeder` extended to seed tarieven too. **Caching (client priority)**: every admin save already runs `Cms::bust()` → bumps `cache_version` → all versioned keys (`cms.page.*`, `cms.page.html.*`, `cms.design.*`) invalidate instantly; migration also busts. Copied 3 missing images to `public/assets/img/landing/` (`586bcf7a…` hero, `Xbox-Series-X-and-Playstation-5-ps5.webp`, `ChatGPT Image…png` → renamed `chatgpt-image-25-jul-2026.png`).
  **Landing page** (client: "connect to UI + route in top header"): `GET /tarieven` route + `PageController@tarieven` (mirrors home incl. full-page cache `cms.page.html.tarieven.{version}`, `$c = Cms::page('home')` for header/footer + `$t = Cms::page('tarieven')`); `landing/tarieven.blade.php` + partials `tarieven-hero` + `tarieven-pricing` (all panels server-rendered, JS toggles); `public/assets/js/tarieven.js` (service tabs + accordion toggle); nav link "Tarieven" in the header now highlights dynamically (path match). CSS: tarieven classes (hero/pricing-background, dot-pattern, hero/device-floating, service-tab active, content-animation, accordion transitions, price-row hover) + `shadow-soft/panel/button/image` in `tailwind.config.js`; rebuilt `landing.css`. Verified: `/tarieven` 200 with all tabs/panels/images/accordions/trust cards, home intact, migration ran, nested save round-trip OK.
- [2026-08-15] — **Tarieven landing performance (client: "extremely heavy" → "excellent, it's light")**: multiple rounds of fixes — (1) `loading="lazy"` on all 5 tab-panel images + `fetchpriority="high"` on the hero image (was eagerly loading ~13MB); (2) removed `drop-shadow-2xl` filters from the animated hero/device images (CSS filter on an `animation: infinite` element forces per-frame re-rasterization → GPU freeze); (3) accordion animation replaced twice — first `max-height` accordion → `grid-template-rows: 0fr→1fr` (still forcing full layout per frame) → finally an **instant display toggle**: `.accordion-content { display:none }` / `.accordion-item.open .accordion-content { display:block; padding-top:20px; animation: contentChange 240ms }` (no height animation at all, rebuilt `landing.css`). Verified in real headless Chrome (puppeteer@21 + Chromium 121): **132–151fps, no long tasks while opening accordions, no JS errors, all 115 icons render (0 `data-lucide` left), page weight down ~5MB**. **Root causes of the freeze found and fixed**: (a) **17 stray `php artisan serve` processes** fighting over ports 8000/8899 since 14:47 — the browser was hitting stale servers running old heavy code; killed them all, one clean server on 8000; (b) **missing lucide icon `file-euro`** (not in lucide v0.469) → `createIcons()` threw and aborted `landing.js` → replaced with `euro` in `database/data/tarieven.php` + DB (`accordions[0].icon`); (c) **polluted DB `value` column** (surfaced because `Cms::page()` falls back to `value` when `json_value` is NULL): `logo_image` was a gitignored 2.5MB design screenshot (`u1qSDe…png`, loaded 4×/page ≈ 5MB), `logo_text`/`tagline`/`hub_subtitle` had trailing junk (`SLIMME-PC1`, `IT-service1`) → fixed DB to `logo.webp`/`SLIMME-PC`/`Reparatie · Verkoop · IT-service`/`IT-service & Reparatie` + `Cms::bust()`. CACHE_STORE=database confirmed working (the earlier "run2 not faster" was Laravel boot ~350ms in `php artisan serve`, not a cache failure). Commits `78f1d20` (lazy+fetchpriority), `74b6a2f` (drop-shadow removal), `51b3da7` (grid accordion), `a4c1e22` (instant accordion + euro icon + DB logo values).
- [2026-08-15] — **Header active nav link fixed** (`resources/views/landing/partials/header.blade.php`): the DB had `nav_links[].active => true` baked into the "Home" link from the design, so Tarieven stayed "active" on every page. The partial now **resets every nav link `active` to false first**, then sets `true` only on an exact path match (including the home path `''`). Verified: `/` → Home active, `/tarieven` → Tarieven active.
- [2026-08-15] — **Admin tarieven editor layout fixed (client: "card inside card inside card is ugly" + "small screens compress to 2 cards")**: the pricing editor had 5 nested bordered cards (main card → json-block card → category card → prices container card → price-row card). Reduced visual nesting: (1) `json-nested-row.blade.php` (prices container) lost its border/blue border-color → soft `bg-slate-50/50` section; (2) `json-nested-row-item.blade.php` (price rows) lost border + shadow → plain `bg-white` rows; (3) `json-row.blade.php` (category card) lost `shadow-sm`. **Responsive fix**: every 2-col grid moved from `sm:grid-cols-2` → `md:grid-cols-2` (`section.blade.php` single fields + json-rows, `json-row` fields, `json-nested-row-item` fields) and all `sm:col-span-2` → `md:col-span-2` so full-width fields (nested block, textarea, image) match the grid breakpoint — below 768px everything is **one clear column** (the old `sm:col-span-2` on a 1-col grid was overflowing the card between 640–768px). **Padding reduced twice per client request** (left/right): card `p-4`→`p-3`, container `p-4`→`px-1.5 py-2.5`, price row `p-3.5`→`px-1.5 py-2.5` → input now starts 25px from the card edge (was 46px), field width 239px on small screens (was 215px). Rebuilt `app.css`. Verified in headless Chrome at 380px (1 column, full-width cards) + 1280px (2 columns, nested block spans the full card width 406px→373px, no overflow) and via tinker render of all 3 tarieven sections (pricing: 6 nested blocks, 40 `md:grid-cols-2` occurrences).
- [2026-08-16] — **Contact page — CMS + landing** (from `contact.html` design; client: "build it like Tarieven and wire it into the header navigation"): new `contact` page in `config/cms.php` with **4 sections** — `hero` (badge, title_line1/2, description, button1_text/url, button2_text, `whatsapp_number` (no `+`, wa.me is built at render), hero_image (image), hero_image_alt, `trust_points` json: icon+label), `gegevens` (card1_title/icon, company_name, address textarea, kvk, btw, route_label/url; card2_title/icon + `contact_methods` json: icon+label+value+url; card3_title/icon + `opening_hours` json: day+note+time+`closed` boolean → grey display), `formulier` (badge, title_line1/2, description, `benefits` json: label — **the form itself is static per client decision: "the form doesn't do anything yet"**, hardcoded fields per design with `onsubmit="return false"`), `locatie` (badge, title_line1/2, description, `map_src` textarea Google Maps embed, route_label/url, `location_items` json: icon+title+text). **Default data**: `database/data/contact.php` (shared source, exact design values: KvK 86906720, BTW NL864142560B01, tel 055 203 21 45 → `tel:`, info@slimme-pc.nl, WhatsApp wa.me/31617100945, opening hours incl. Zondag Gesloten, map + route Google URLs); idempotent migration `2026_08_16_000001_seed_contact_content_blocks.php` (`firstOrCreate` + `Cms::bust()`); `ContentBlockSeeder` extended. Copied hero image `6F69A001-617B-44CE-B7E9-75C6165A3A4F_1_105_c.jpeg` to `public/assets/img/landing/`. **Landing**: `GET /contact` route + `PageController@contact` (mirrors tarieven incl. full-page cache `cms.page.html.contact.{version}`, `$c = Cms::page('home')` + `$p = Cms::page('contact')`); `landing/contact.blade.php` + 4 partials (`contact-hero` gradient banner w/ wa.me CTA, `contact-gegevens` 3 icon cards `md:grid-cols-2 lg:grid-cols-3` w/ new `.card-soft` shadow, `contact-formulier` intro + static form w/ radio "type aanvraag" + attachment dropzone, `contact-locatie` Google embed + route button). CSS: `.card-soft` (`0 14px 45px rgba(15,23,42,.06)`) added to `resources/css/landing.css`, rebuilt. **Admin**: sidebar **Contact dropdown under Tarieven** (Hero / Contactgegevens / Contactformulier / Locatie, envelope icon) + `$sectionInfos['contact']` entries in `section.blade.php`. Header "Contact" nav link (`/contact`, already in DB) highlights automatically via path match. Verified: all 4 admin sections render, headless Chrome 380px (single column, no overflow, 3 stacked 348px cards) + 1280px (3 cards one row, no overflow, hero image loaded, 64 lucide icons), `/contact` 200 with all sections + active header link + `ring-1 ring-white/10` on both navs.

