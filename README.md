# Anime Shop

A full-featured e-commerce web application for anime merchandise (figures, apparel, manga, stickers), built with Laravel 12, Livewire 3, and Filament 3.

**Live demo:** [shop.ducdev.work](https://shop.ducdev.work)

---

## Tech Stack

![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat&logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=flat&logo=laravel&logoColor=white)
![Livewire](https://img.shields.io/badge/Livewire-3.5-4E56A6?style=flat&logo=livewire&logoColor=white)
![Filament](https://img.shields.io/badge/Filament-3-F59E0B?style=flat)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat&logo=mysql&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind-3-06B6D4?style=flat&logo=tailwindcss&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-compose-2496ED?style=flat&logo=docker&logoColor=white)

| Layer | Technology |
|---|---|
| Backend | Laravel 12, PHP 8.2 |
| Reactive UI | Livewire 3.5 + Alpine.js 3 |
| Admin panel | Filament 3 |
| Styling | Tailwind CSS 3 (mobile-first) |
| Database | MySQL 8.0 |
| Storage | Local disk / S3-compatible (AWS S3, Spaces, R2) |
| Infrastructure | Docker + Nginx + PHP-FPM |

---

## Features

### Customer-facing
- **Product catalog** — browse by category, search, sort (newest / price / popular)
- **Product detail** — image, description, stock status, related products
- **Shopping cart** — session-based, no login required; real-time icon update via Livewire events
- **Checkout** — guest or authenticated, address book, voucher/discount codes, note field; protected with honeypot + reCAPTCHA + rate limiting
- **Multiple payment methods** — Cash on Delivery (COD) and bank transfer
- **Order tracking** — order confirmation page with status badge; guest access via session token
- **Verified reviews** — only customers with a completed order can leave a review
- **Wishlist** — save favourite products (requires login)
- **User account** — order history, saved addresses, profile & password management
- **Static pages** — order guide, payment info, shipping policy, returns policy
- **XML sitemap** — auto-generated for products and categories

### Admin panel (`/admin`)
- **Dashboard** — 9 stat cards (today's orders & revenue, monthly, pending, low stock, out of stock), revenue line chart, order status chart, latest orders table, recent activity feed
- **Products** — full CRUD with image upload (local or S3), bulk import via XLSX
- **Categories** — CRUD with deletion guard (cannot delete if products exist)
- **Orders** — status management (`unpaid → pending → shipped → completed / cancelled`), read-only order items
- **Vouchers** — CRUD for discount codes (percent or fixed), expiry, min order, usage limit
- **Users** — read-only customer view with order stats
- **Admin accounts** — manage admin users independently from customers

---

## Architecture

This project follows a layered architecture to keep controllers thin and business logic testable:

```
HTTP Request
    └── Controller / Livewire component  (routing & UI state only)
            └── Action / Service         (business logic)
                    └── Model / Observer  (data & side-effects)
```

Key patterns used:

| Pattern | Example | When |
|---|---|---|
| **Action** | `PlaceOrderAction`, `ImportProductsAction` | One specific business operation |
| **Service** | `CartService` | Shared logic across multiple entry points |
| **Observer** | `OrderItemObserver` | Automatic side-effects on model events |
| **Eloquent scope** | `Product::scopeInStock()` | Reusable query constraints |

**Not used:** Repository pattern — Eloquent + scopes are sufficient.

---

## Getting Started

### Prerequisites
- Docker + Docker Compose

### Setup

```bash
# 1. Clone the repo
git clone <repo-url> anime-shop
cd anime-shop

# 2. Copy environment file and fill in values
cp .env.example .env

# 3. Start containers
docker compose up -d

# 4. Install dependencies
docker compose exec app composer install
docker compose exec app npm install

# 5. Generate app key
docker compose exec app php artisan key:generate

# 6. Run migrations and seed sample data
docker compose exec app php artisan migrate --seed

# 7. Link storage
docker compose exec app php artisan storage:link

# 8. Build assets
docker compose exec app npm run dev
```

The application will be available at **http://localhost:8005**
phpMyAdmin at **http://localhost:8080**

### Create an admin account

```bash
docker compose exec app php artisan make:filament-user
```

Admin panel: **http://localhost:8005/admin**

---

## Key Commands

```bash
# Migrations
docker compose exec app php artisan migrate
docker compose exec app php artisan migrate:fresh --seed

# Code quality
docker compose exec app ./vendor/bin/pint           # PSR-12 formatting
docker compose exec app ./vendor/bin/phpstan analyse # static analysis

# Testing
docker compose exec app php artisan test

# Production build
docker compose exec app npm run build
```

---

## Environment Variables

Key variables beyond Laravel defaults:

| Variable | Purpose |
|---|---|
| `APP_ENV` | Controls storage disk: `local` → public disk, `production` → S3 |
| `RECAPTCHA_SITE_KEY` / `RECAPTCHA_SECRET_KEY` | Checkout spam protection |
| `AWS_*` | S3-compatible storage (S3, DigitalOcean Spaces, Cloudflare R2) |
| `SESSION_ENCRYPT` | Set `true` on production |
| `SESSION_SECURE_COOKIE` | Set `true` on production |

See `.env.example` for the full list.

---

## Project Structure

```
app/
├── Actions/          # Single-responsibility business operations
├── Services/         # Shared business logic (CartService)
├── Observers/        # Model event side-effects
├── Livewire/         # Reactive UI components
├── Filament/
│   ├── Resources/    # Admin CRUD pages
│   └── Widgets/      # Dashboard widgets
└── Models/           # Eloquent models with scopes & accessors

resources/views/
├── components/       # Reusable Blade components (x-button, x-product-card...)
├── livewire/         # Livewire component templates
└── {feature}/        # Page views (products, cart, checkout, orders, account...)
```

---

## Production Deploy

The project ships with a multi-stage `Dockerfile.prod`:
1. **Node stage** — builds frontend assets
2. **PHP-FPM stage** — production-optimized image (`--no-dev`, opcache, healthcheck)

`docker/entrypoint.prod.sh` runs on container start: waits for DB → migrates → links storage → caches config/routes/views → caches Filament components.

---

## License

MIT
