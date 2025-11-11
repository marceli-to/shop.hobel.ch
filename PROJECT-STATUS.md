# Project Status - Laravel 12 Shop Prototype

**Last Updated:** 2025-11-11

## Current State

This is a modernized e-commerce prototype built with:
- Laravel 12
- Filament 4
- Livewire 3.6
- Alpine.js 3.14
- Tailwind CSS 4 (beta)

## ✅ Completed (7/12 tasks)

### 1. Project Setup ✅
- Laravel 12 installed and configured
- All dependencies installed (Composer + NPM)
- Vite configured with Tailwind CSS 4
- Alpine.js integrated in `resources/js/app.js`
- `.editorconfig` configured for **1 tab indentation** (enforced across all files)

### 2. Database Schema ✅
**Migrations created and run:**
- `products` - id, uuid, name, slug, description, price, stock, image, published_at
- `orders` - customer info, invoice/shipping addresses, total, payment_method, paid_at
- `order_items` - order_id, product snapshot (name, description, price, quantity)
- `users` - id, name, email, password (for admin access)

### 3. Eloquent Models ✅
All models use **tabs for indentation**:
- `Product` - with Spatie Sluggable, UUID generation, published scope, route key = slug
- `Order` - with relationships to OrderItem, UUID, order number accessor (FS-000001)
- `OrderItem` - with order relationship, subtotal accessor
- `User` - implements FilamentUser for admin access

### 4. Filament 4 Admin Panel ✅
**Located at:** `/admin`
**Admin user created:**
- Email: `m@marceli.to`
- Password: `9aq31rr23`

**Resources:**
- Product Resource - Full CRUD (create, edit, list)
- Order Resource - View only (list, view)

**Files (all using tabs):**
- `app/Providers/Filament/AdminPanelProvider.php` - Panel configured with `.login()` and `.default()`
- `app/Filament/Admin/Resources/Products/` - ProductResource + Pages + Forms + Tables
- `app/Filament/Admin/Resources/Orders/` - OrderResource + Pages + Infolists + Tables

### 5. Cart Action Classes ✅
**Located in:** `app/Actions/Cart/`
All files use **tab indentation**:
- `GetCart.php` - Retrieves cart from session (default: items[], quantity=0, total=0)
- `StoreCart.php` - Saves cart to session
- `UpdateCart.php` - Merges updates into cart
- `DestroyCart.php` - Clears cart from session

**Pattern:** `(new ActionName())->execute($params)`

### 6. Livewire Components ✅
**Located in:** `app/Livewire/`
All files use **tab indentation**:

**CartIcon.php**
- Shows cart item count in header
- Listens to `cart-updated` event
- Property: `$cartItemCount`

**CartButton.php**
- Add to cart with quantity selector
- Handles product stock limits
- Updates cart totals
- Dispatches `cart-updated` event
- Properties: `$productUuid`, `$quantity`, `$cart`

**MiniCart.php**
- Dropdown cart preview
- Toggle visibility
- Listens to `cart-updated` event
- Properties: `$cart`, `$showCart`

**Cart.php**
- Full cart page component
- Remove items functionality
- Update quantities
- Recalculates totals
- Methods: `removeItem()`, `updateQuantity()`, `updateTotal()`

**Views exist at:** `resources/views/livewire/` (old HTML, needs Tailwind CSS 4 styling)

### 7. Routes & Authentication ✅
**Routes configured in:** `routes/web.php`
- `GET /` - Welcome page (uses `welcome.blade.php`)
- `GET /login` - Redirects to `/admin/login`
- Filament handles all `/admin/*` routes

**Authentication working:**
- Admin login at `/admin/login` ✅
- User model implements FilamentUser ✅

---

## 🔨 In Progress (1/12)

### Livewire Views
**Status:** Component logic complete, but views need Tailwind CSS 4 styling
**Files exist at:**
- `resources/views/livewire/cart-button.blade.php` - OLD
- `resources/views/livewire/cart-icon.blade.php` - OLD
- `resources/views/livewire/mini-cart.blade.php` - Created but empty
- `resources/views/livewire/cart.blade.php` - OLD

**TODO:** Rebuild these views with Tailwind CSS 4 utilities

---

## ⏳ Not Started (4/12)

### 8. Controllers
**Need to create:**
- `app/Http/Controllers/ProductController.php`
  - `index()` - List published products
  - `show(Product $product)` - Show single product detail

- `app/Http/Controllers/OrderController.php`
  - Checkout flow methods (address, summary, finalize, confirmation)
  - Store order on completion (no payment integration yet)

### 9. Middleware
**Need to port from old codebase:**
- `app/Http/Middleware/EnsureCartIsNotEmpty.php`
  - Protects checkout routes
  - Redirects to cart if empty

- `app/Http/Middleware/EnsureCorrectOrderStep.php`
  - Validates sequential checkout flow
  - Prevents skipping steps
  - Takes `$requiredStep` parameter

**Register in:** `bootstrap/app.php` or Kernel

### 10. Web Routes
**Need to define in:** `routes/web.php`

```php
// Products
GET / or /products - ProductController@index
GET /product/{product:slug} - ProductController@show

// Cart
GET /cart - Cart Livewire component full page

// Checkout (protected by middleware)
GET /checkout/address - OrderController@address
POST /checkout/address - OrderController@storeAddress
GET /checkout/summary - OrderController@summary
POST /checkout/complete - OrderController@complete
GET /order/{order:uuid} - OrderController@confirmation
```

**Middleware groups:**
- `ensure.cart.not.empty` - Apply to all /checkout/* routes
- `ensure.correct.order.step:X` - Apply with step number

### 11. Blade Views & Layouts
**Need to create:**

**Layouts:**
- `resources/views/layouts/app.blade.php`
  - Main layout with navigation
  - Include CartIcon component in header
  - Vite assets (@vite)
  - Alpine.js and Livewire scripts

**Product views:**
- `resources/views/products/index.blade.php` - Grid/list of products
- `resources/views/products/show.blade.php` - Product detail with CartButton

**Cart views:**
- `resources/views/cart/index.blade.php` - Full cart page

**Checkout views:**
- `resources/views/checkout/address.blade.php` - Address form (invoice + shipping)
- `resources/views/checkout/summary.blade.php` - Order summary
- `resources/views/order/confirmation.blade.php` - Thank you page

**Livewire views (rebuild with Tailwind CSS 4):**
- Update all 4 livewire views mentioned above

**Use:** Tailwind CSS 4 utilities (configured in `resources/css/app.css` with `@import "tailwindcss"`)

### 12. Product Seeder
**Create:** `database/seeders/ProductSeeder.php`

Generate 10-15 sample products with:
- Random names
- Descriptions
- Prices (CHF 10-500)
- Stock (5-50)
- Published_at set to now
- UUID auto-generated by model

**Run with:** `php artisan db:seed --class=ProductSeeder`

---

## Important Configuration Notes

### Code Style
- **ALL PHP files use 1 TAB for indentation** (enforced by `.editorconfig`)
- This includes: Models, Actions, Livewire, Migrations, Routes, Providers, Filament Resources
- Converted using: `perl -i -pe 's/^( {4})+/"\t" x (length($&)\/4)/e' "$file"`

### Tailwind CSS 4
- Import statement: `@import "tailwindcss"` in `resources/css/app.css`
- Vite plugin: `@tailwindcss/vite` in `vite.config.js`
- No tailwind.config.js needed (CSS-first configuration)

### Cart Architecture
- **Session-based** (no database persistence)
- Structure: `['items' => [], 'quantity' => 0, 'total' => 0]`
- Items contain: uuid, name, description, price, quantity, image
- Event-driven: Components dispatch/listen to `cart-updated`

### Order System
- Order number format: `FS-000001` (FS- prefix + 6-digit padded ID)
- Routes use UUID for public URLs: `/order/{order:uuid}`
- Products use slug for SEO: `/product/{product:slug}`

### Simplified for Prototype
- ❌ No product variations
- ❌ No product categories
- ❌ No CMS pages
- ❌ No payment integration (placeholder for later)
- ❌ No image optimization (deferred)
- ❌ No search functionality
- ❌ No email notifications
- ❌ No customer authentication (checkout as guest)

---

## Next Session TODO

Start with **Task 8 - Controllers**:

1. Create ProductController with index() and show()
2. Create OrderController with checkout methods
3. Port middleware (EnsureCartIsNotEmpty, EnsureCorrectOrderStep)
4. Define all web routes with middleware protection
5. Build main app layout with navigation + CartIcon
6. Create product listing and detail views
7. Build checkout flow views
8. Style Livewire component views with Tailwind CSS 4
9. Create product seeder
10. Test full flow: browse products → add to cart → checkout → order confirmation

---

## Quick Commands

```bash
# Development
php artisan serve
npm run dev

# Database
php artisan migrate:fresh
php artisan db:seed

# Clear cache
php artisan optimize:clear

# Access points
- Frontend: http://localhost:8000 or https://shop.hobel.ch.test
- Admin: https://shop.hobel.ch.test/admin (m@marceli.to / 9aq31rr23)
```

---

## Files That Need Tab Indentation (if created)
When creating new PHP files, ensure you use **1 tab** for indentation (EditorConfig will enforce this automatically in most editors).
