# Implementation Notes — 2026-04-10

## 1. Shipping Costs (Flat Rate Per Order)

### New files
- `database/migrations/2026_04_10_120317_add_flat_rate_shipping_to_products_table.php` — Adds `flat_rate_shipping` boolean (default true) to products table

### Modified files
- `app/Models/Product.php` — Added `flat_rate_shipping` to `$fillable` and `$casts`
- `config/invoice.php` — Added `shipping_flat_rate` (20.00) and `shipping_free_threshold` (100.00)
- `app/Livewire/Cart/Button.php` — Guard against `flat_rate_shipping = false`, set per-item `shipping_price` to 0, flat-rate totals calculation
- `app/Livewire/Cart/Cart.php` — Rewrote `calculateTotals()` and `updateTotal()` with flat-rate logic
- `app/Livewire/Cart/MiniCart.php` — Updated `updateTotal()` with flat-rate logic
- `app/Livewire/Checkout/Summary.php` — Same flat-rate logic in `calculateTotals()`
- `app/Services/PayrexxService.php` — Replaced per-item shipping with single order-level shipping line
- `resources/views/livewire/cart/cart.blade.php` — Removed per-item shipping price, added "Versand" line in totals
- `resources/views/livewire/checkout/summary.blade.php` — Shows shipping method name per item (no price), added "Versand" line in totals
- `resources/views/pdf/invoice.blade.php` — Removed per-item shipping rows, added Netto/Versand/MwSt/Total in totals section
- `resources/views/pages/product/show.blade.php` — Shows "Bitte kontaktieren Sie uns (E-Mail: shop@hobel.ch)" when `flat_rate_shipping = false`
- `app/Filament/Admin/Resources/Products/Schemas/ProductForm.php` — Added "Pauschalversand" toggle in settings section

### Shipping logic
- CHF 20 flat rate if any cart item uses "Versand (Schweiz)"
- Free shipping if product subtotal >= CHF 100
- Free if all items use "Abholung"
- Configurable in `config/invoice.php`

---

## 2. Shipping Methods

### New files
- `app/Console/Commands/SetupShippingMethods.php`

### Command
```bash
php artisan app:setup-shipping-methods
```

### What it does
- Renames "Abholung (Schreinerei)" (id=1) → "Abholung" (price CHF 0)
- Keeps "Versand (Schweiz)" (id=5, price CHF 20)
- Removes unused shipping methods (ids 2, 3, 4) and their pivot records
- Attaches both methods to all products

---

## 3. Import New Images

### New files
- `app/Console/Commands/ImportImages.php`

### Command
```bash
php artisan app:import-images
```

### What it does
- Scans `/storage/app/image-import/` for upscaled images
- Strips `-gigapixel-low_res-width-NNNNpx` suffix from filenames
- Matches to existing files in `/storage/app/public/products/`
- Overwrites with the upscaled version (all 294 images match)
- Updates Image model records (size, width, height)
- Clears Glide cache (`storage/app/.glide-cache`)

---

## 4. Inventory

### New files
- `app/Console/Commands/SetStock.php`

### Command
```bash
php artisan app:set-stock        # Sets stock to 50 (default)
php artisan app:set-stock 100    # Sets stock to custom amount
```

### Modified files
- `app/Models/Order.php` — `createFromSession()` now wrapped in `DB::transaction()`, deducts stock via `Product::decrement()` after creating each order item
- `resources/views/livewire/cart/button.blade.php` — Shows "Produkt derzeit nicht verfügbar" and hides quantity/cart button when `maxStock < 1`

---

## 5. Filter Attributes (Frontend)

### Modified files
- `app/Http/Controllers/CategoryController.php` — Accepts `?tag={slug}` query parameter, resolves tag, filters products server-side, passes `$activeTag` to view
- `resources/views/pages/category/index.blade.php` — Filter buttons are now `<a>` links with proper `href` attributes (SEO/shareable), Alpine.js `history.pushState` for instant client-side toggling without page reload

### URL format
```
/{category}?tag={tag-slug}
```

---

## Deployment steps

```bash
# 1. Run migration
php artisan migrate

# 2. Set up shipping methods
php artisan app:setup-shipping-methods

# 3. Set stock for all products
php artisan app:set-stock

# 4. Import upscaled images
php artisan app:import-images

# 5. Clear caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```
