# shop.hobel.ch

E-commerce shop for the Swiss market (CHF, German). Sells configurable, made-to-measure tables and wood products.

## Stack

- PHP 8.4 · Laravel 12
- Filament 4 (admin) · Livewire 3
- Tailwind CSS 4 · Vite 5 · Alpine.js 3
- Payrexx (payments) · Resend (email) · League Glide (images) · Spatie PDF via Browsershot/Sidecar

## Setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

## Development

```bash
php artisan serve   # app server
npm run dev         # Vite dev server
npm run build       # production assets
```

## Common commands

```bash
php artisan test        # run tests (PHPUnit 11)
./vendor/bin/pint       # format PHP
php artisan optimize    # clear/cache config, routes, views
```

The admin panel lives at `/admin`. See [CLAUDE.md](CLAUDE.md) for architecture details.
