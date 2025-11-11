# Laravel 12 Shop Prototype

Modern e-commerce prototype built with Laravel 12, Filament 4, Livewire 3, Alpine.js, and Tailwind CSS 4.

## Stack

- **Backend**: Laravel 12, PHP 8.2+
- **Admin Panel**: Filament 4
- **Frontend**: Livewire 3.6, Alpine.js 3.14, Tailwind CSS 4 (beta)
- **Database**: MySQL
- **Build Tool**: Vite 5

## What's Been Implemented

### ✅ Core Setup
- Laravel 12 project structure
- Composer dependencies installed (Filament 4, Livewire, Spatie Sluggable)
- NPM dependencies installed (Tailwind CSS 4, Alpine.js, Vite)
- Vite configured with Tailwind CSS 4
- Alpine.js integrated

### ✅ Database & Models
**Migrations:**
- `products` table - id, uuid, name, slug, description, price, stock, image, published_at
- `orders` table - customer info, invoice/shipping addresses, total, payment info
- `order_items` table - product snapshot with pricing

**Models:**
- `Product` - with Spatie Sluggable, UUID auto-generation, published scope
- `Order` - with order number (FS-000001), address formatting, UUID
- `OrderItem` - with subtotal calculation

### ✅ Admin Panel (Filament 4)
- Admin panel installed at `/admin`
- Product Resource - full CRUD
- Order Resource - view-only

## What Still Needs to Be Done

### 1. Cart System (Action Classes)
Create in `app/Actions/Cart/`:
- `GetCart.php` - Retrieve cart from session
- `StoreCart.php` - Save cart to session
- `UpdateCart.php` - Update cart items/totals
- `DestroyCart.php` - Clear cart

### 2. Livewire Components
Create in `app/Livewire/`:
- `CartButton.php` - Add to cart with quantity
- `CartIcon.php` - Header cart icon with count
- `MiniCart.php` - Dropdown cart preview
- `Cart.php` - Full cart page with line items

### 3. Controllers
Create in `app/Http/Controllers/`:
- `ProductController.php` - index(), show()
- `OrderController.php` - checkout steps

### 4. Middleware
Create in `app/Http/Middleware/`:
- `EnsureCartIsNotEmpty.php` - Protect checkout routes
- `EnsureCorrectOrderStep.php` - Sequential checkout validation

### 5. Routes
Update `routes/web.php`:
```php
// Products
GET / - Products listing
GET /product/{product:slug} - Product detail

// Cart
GET /cart - Cart page

// Checkout (protected by middleware)
GET /checkout/address
POST /checkout/address
GET /checkout/summary
POST /checkout/complete
GET /order/{order:uuid} - Order confirmation
```

### 6. Views
Create Blade templates in `resources/views/`:
- `layouts/app.blade.php` - Main layout with Tailwind CSS 4
- `products/index.blade.php` - Products grid
- `products/show.blade.php` - Product detail
- `cart/index.blade.php` - Cart page
- `checkout/address.blade.php` - Address form
- `checkout/summary.blade.php` - Order summary
- `order/confirmation.blade.php` - Order confirmation

### 7. Livewire Views
Create in `resources/views/livewire/`:
- `cart-button.blade.php`
- `cart-icon.blade.php`
- `mini-cart.blade.php`
- `cart.blade.php`

### 8. Seeders
Create `database/seeders/ProductSeeder.php` with sample products

## Quick Start

### Setup
```bash
# Install dependencies
composer install
npm install

# Configure environment
cp .env.example .env
php artisan key:generate

# Run migrations
php artisan migrate:fresh

# Seed database (once seeder is created)
php artisan db:seed

# Create admin user
php artisan make:filament-user
```

### Development
```bash
# Start backend
php artisan serve

# Start frontend (in separate terminal)
npm run dev
```

### Access Points
- **Frontend**: http://localhost:8000
- **Admin Panel**: http://localhost:8000/admin

## Database Schema

### Products
- Simple product catalog
- No variations or categories (prototype)
- Published/unpublished state

### Orders
- Customer information
- Separate invoice and shipping addresses
- Order items as snapshots (preserve pricing)
- UUID-based public URLs

## Key Features

### Session-Based Cart
- Cart stored in session (no login required)
- Real-time updates via Livewire events
- Quantity management
- Total calculation

### Simplified Checkout
3-step process:
1. Address (invoice + shipping)
2. Order summary
3. Confirmation (payment integration deferred)

### Admin Panel
- Manage products (CRUD)
- View orders
- Built with Filament 4

## Architecture Patterns

### Action Classes
Business logic encapsulated in single-purpose classes:
```php
(new GetCart())->execute();
(new UpdateCart())->execute(['items' => ...]);
```

### Livewire Events
Components communicate via events:
```php
$this->dispatch('cart-updated');
#[On('cart-updated')] public function updateCart() { }
```

### Route Model Binding
- Products: `/product/{product:slug}`
- Orders: `/order/{order:uuid}`

## Notes

- **Image handling**: Deferred (Intervention Image not Laravel 12 compatible yet)
- **Payment integration**: Placeholder for future implementation
- **Search**: Not included in prototype
- **Email notifications**: Not included in prototype
- **Authentication**: Admin only (customer checkout without login)

## Next Steps

1. Implement cart Action classes
2. Build Livewire components
3. Create controllers and routes
4. Build frontend views with Tailwind CSS 4
5. Create product seeder
6. Manual testing of full flow

---

*Built as a modernized, simplified prototype for rapid development and testing.*
