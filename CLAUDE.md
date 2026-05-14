# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Modern Laravel 12 e-commerce prototype (shop.hobel.ch) for the German/Swiss market (CHF, German). Built with Filament 4 admin, Livewire 3 components, Tailwind CSS 4, and Payrexx for payments.

## Development Commands

### Setup
```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

### Development
```bash
# Start development server
php artisan serve

# Frontend dev (Vite)
npm run dev

# Build for production
npm run build
```

### Testing
```bash
# Run all tests (PHPUnit 11)
php artisan test

php artisan test --testsuite=Feature
php artisan test --testsuite=Unit
php artisan test tests/Feature/ExampleTest.php
```

### Code Quality
```bash
# Format with Laravel Pint
./vendor/bin/pint
./vendor/bin/pint app/Models/Product.php
```

### Database
```bash
php artisan migrate
php artisan migrate:rollback
php artisan migrate:fresh --seed
php artisan make:migration create_table_name
```

### Cache Management
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize
```

## Architecture

### Directory Structure

- **app/Actions/** — Single-purpose action classes organized by domain (`Cart`, `Category`, `Order`, `Product`)
  - Pattern: `(new ActionName())->execute($params)`
  - Encapsulate business logic outside controllers

- **app/Livewire/** — Interactive components
  - `Cart/` — `Cart`, `Button`, `Icon`, `MiniCart`
  - `Checkout/` — `InvoiceAddress`, `DeliveryAddress`, `Payment`, `Summary`, `Confirmation`
  - `Product/` — `SimpleProduct`, `VariationsProduct`, `ConfigurableProduct`

- **app/Filament/Admin/Resources/** — Filament 4 admin resources
  - `Products`, `Categories`, `Orders`, `ShippingMethods`, `WoodTypes`, `Surfaces`, `Edges`
  - Admin panel at `/admin`

- **app/Models/** — Eloquent models
  - `Product` — Has many self-referencing variations via `parent_id`; belongs to many `Category`, `Tag`, `WoodType`, `Surface`, `Edge`, `ShippingMethod`; uses `HasSlug` + soft deletes
  - `Category` — Belongs to many `Product`; sluggable
  - `Order` — Has many `OrderItem`
  - Other: `Edge`, `Image`, `OrderItem`, `ProductAttribute`, `ShippingMethod`, `Surface`, `Tag`, `User`, `WoodType`

- **app/Http/Middleware/** — Checkout flow guards
  - `EnsureCartIsNotEmpty` — Protects checkout routes
  - `EnsureCorrectOrderStep` — Validates sequential step progression
  - `EnsureOrderIsPaid` — Validates payment completion

- **app/Http/Controllers/** — `LandingController`, `CategoryController`, `ProductController`, `CartController`, `PaymentController`, `ImageController`, `PdfController`

- **app/Services/** — `PayrexxService` (payment gateway), `ConfigurablePriceCalculator` (configurable product pricing)

- **app/Notifications/Order/** — `ConfirmationNotification` (customer), `InformationNotification` (shop owner)

- **app/Jobs/** — `ProcessOrderJob` (queued order processing)

- **app/Enums/** — `ProductType` (simple, configurable, variations), `TableShape`

### Key Patterns

**Session-Based Cart System**
- Cart state lives in session, mediated by Action classes
- Cart structure: items, invoice_address, shipping_address, payment_method, order_step, is_paid
- Operations: `Cart/Get`, `Cart/Store`, `Cart/Update`, `Cart/Destroy`

**Multi-Step Checkout Flow (German routes)**
1. `/bestellung/warenkorb` — Basket
2. `/bestellung/rechnungsadresse` — Invoice address (step 1)
3. `/bestellung/lieferadresse` — Delivery address (step 2)
4. `/bestellung/zahlung` — Payment method (step 3)
5. `/bestellung/zusammenfassung` — Summary (step 4)
6. Payrexx redirect → `/bestellung/bestaetigung` — Confirmation
- Protected by `ensure.cart.not.empty` and `ensure.correct.order.step:{n}` middleware

**Product System**
- `ProductType` enum: `Simple`, `Configurable`, `Variations`
- Variations modeled as self-referencing rows via `parent_id` (no separate variations table)
- Configurable products compose price from selected `WoodType`, `Surface`, `Edge`, and `ProductAttribute` options
- `Spatie\Sluggable\HasSlug` + custom `HasGermanSlug` trait for SEO-friendly URLs (route key `slug`)

**Payment Integration (Payrexx)**
- `PayrexxService` creates gateway sessions; `PaymentController` handles `success`, `cancel`, and `webhook`
- On successful payment, `Order/Finalize` action persists the order and triggers notifications

**Image Management (Glide)**
- League Glide 3 handles on-the-fly image manipulation/caching
- Served via `ImageController` at `/img/{path}`

**PDF Generation**
- Spatie Laravel PDF using Browsershot (Puppeteer) running on AWS Lambda via Hammerstone Sidecar (`wnx/sidecar-browsershot`)
- Invoice route: `/pdf/invoice/{order:uuid}` via `Order/GenerateInvoicePdf`

### Database Schema

**Key Tables:**
- `products` — Catalog, with `type`, `parent_id` (self-referencing for variations), configurable fields
- `categories` + `category_product` — Many-to-many categorization
- `tags` + `product_tag`
- `wood_types`, `surfaces`, `edges` + pivots (`product_wood_type`, `product_surface`, `edge_product`) — Configurable product options
- `product_attributes` — Additional configurable attributes
- `shipping_methods` + `product_shipping_method`
- `orders`, `order_items` — Order persistence with snapshotted prices and shipping
- `images` — Polymorphic image attachments
- `users` — Admin users (Filament)

**Important Fields:**
- Products: `uuid`, `slug`, `type` (enum), `parent_id`, configurable pricing fields
- Orders: `uuid`, `order_number`, payment reference, tax/shipping fields
- Soft deletes on `Product`, `Category`, `Tag`, `Order`

### Third-Party Integrations

- **Filament v4** — Admin panel
- **Livewire v3.6** — Reactive components
- **Payrexx PHP SDK** (`payrexx/payrexx`) — Payment gateway
- **Resend** (`resend/resend-laravel`) — Transactional email
- **League Glide v3** — Image manipulation
- **Spatie Laravel PDF** + **Hammerstone Sidecar** + **wnx/sidecar-browsershot** — Headless-Chrome PDFs via AWS Lambda
- **Spatie Sluggable** — SEO-friendly URLs
- **Puppeteer** (npm) — Used by sidecar-browsershot

### Frontend Stack

- **Vite 5** — Asset bundler
- **Tailwind CSS 4 (beta)** via `@tailwindcss/vite`; `@tailwindcss/typography` plugin
- **Alpine.js 3** + `@alpinejs/collapse`
- Blade templates in `resources/views/` (`components/`, `livewire/`, `mail/`, `pages/`, `pdf/`)

## Important Notes

- Swiss market shop — currency CHF, language German
- Order numbers generated via `Order/GenerateOrderNumber`
- Shipping country validation uses `config('countries.delivery')` (see `config/countries.php`, `config/shop.php`)
- Payrexx config in `config/payrexx.php`
- Sidecar (AWS Lambda) config in `config/sidecar.php` and `config/sidecar-browsershot.php` — required for PDF generation
- Session-based cart — no persistent cart for guests
- Admin panel customized in `app/Providers/Filament/AdminPanelProvider.php`

## Stack Summary

- PHP **8.4**, Laravel **12**
- Filament **4**, Livewire **3.6**
- Tailwind CSS **4 beta**, Vite **5**, Alpine.js **3**
- Payrexx (not Stripe), Resend (not SMTP/Mailgun), League Glide (not Intervention Image), Spatie PDF via Browsershot/Sidecar (not DomPDF)
- PHPUnit **11**, Laravel Pint
