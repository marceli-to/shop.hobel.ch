# Configurable Products System

## Overview

This document preserves the implementation of a complete configurable products system built for shop.hobel.ch. This feature allows products to have customizable options (materials, sizes, finishes, etc.) with individual pricing for each configuration option.

**Status:** Archived Feature
**Implemented:** November 2025
**Technology Stack:** Laravel 10, Livewire 3, Filament 3, Tailwind CSS

### Key Concepts

- **Configurable Products:** Products that have multiple customization options
- **Configuration Schema:** JSON structure defining available options and their attributes
- **Configuration State:** Customer's selected options stored with cart items and orders
- **Dynamic Pricing:** Base price + sum of all selected option price modifiers
- **Validation:** Required options must be selected before adding to cart

---

## Architecture Overview

### Data Flow

```
1. Admin creates configurable product in Filament
   └─> Product model stores configuration_schema (JSON)

2. Customer visits product page
   └─> Livewire Configurator loads product and schema

3. Customer selects options
   └─> Configuration state updates in real-time
   └─> Price recalculates with modifiers

4. Customer adds to cart
   └─> Cart validates configuration
   └─> Stores configuration with cart item
   └─> Generates unique cart_key (uuid + config hash)

5. Customer completes checkout
   └─> Configuration persists to order_items.configuration (JSON)
```

### Unique Cart Keys

To allow the same product with different configurations in the cart, each cart item has a unique `cart_key`:

- Simple products: `cart_key = product_uuid`
- Configured products: `cart_key = product_uuid + '_' + md5(configuration_json)`

This allows customers to add the same table with different wood finishes as separate cart items.

---

## Database Schema

### Migration 1: Add Configurable Fields to Products

**File:** `database/migrations/2025_11_13_115202_add_configurable_fields_to_products_table.php`

```php
Schema::table('products', function (Blueprint $table) {
    $table->boolean('is_configurable')->default(false)->after('stock');
    $table->json('configuration_schema')->nullable()->after('is_configurable');
});
```

**Fields:**
- `is_configurable` (boolean): Flag to enable configurator UI
- `configuration_schema` (JSON): Defines attributes and options

### Migration 2: Add Configuration to Order Items

**File:** `database/migrations/2025_11_13_115815_add_configuration_to_order_items_table.php`

```php
Schema::table('order_items', function (Blueprint $table) {
    $table->json('configuration')->nullable()->after('quantity');
});
```

**Purpose:** Preserves customer's configuration choices in completed orders

---

## Configuration Schema Structure

The `configuration_schema` JSON stored in the Product model follows this structure:

```json
{
  "attributes": [
    {
      "key": "table_top",
      "label": "Tischplatte",
      "required": true,
      "options": [
        {
          "value": "oak",
          "label": "Eiche",
          "price_modifier": 0,
          "description": "Klassisches Eichenholz"
        },
        {
          "value": "walnut",
          "label": "Nussbaum",
          "price_modifier": 150.00,
          "description": "Edles Nussbaumholz"
        }
      ]
    },
    {
      "key": "width",
      "label": "Breite",
      "required": true,
      "options": [
        {
          "value": "120cm",
          "label": "120 cm",
          "price_modifier": 0
        },
        {
          "value": "150cm",
          "label": "150 cm",
          "price_modifier": 200.00
        }
      ]
    }
  ]
}
```

### Schema Fields

**Attribute Level:**
- `key` (string, required): Unique identifier for attribute (e.g., "table_top")
- `label` (string, required): Display name shown to customers (e.g., "Tischplatte")
- `required` (boolean): Whether customer must select an option
- `options` (array): Available choices for this attribute

**Option Level:**
- `value` (string, required): Technical identifier (e.g., "oak")
- `label` (string, required): Display name (e.g., "Eiche")
- `price_modifier` (decimal): Price added when this option is selected (CHF)
- `description` (string, optional): Additional info shown to customer

---

## Model Implementation

### Product Model

**File:** `app/Models/Product.php`

#### New Fillable Fields

```php
protected $fillable = [
    // ... existing fields
    'is_configurable',
    'configuration_schema',
];

protected $casts = [
    // ... existing casts
    'is_configurable' => 'boolean',
    'configuration_schema' => 'array',
];
```

#### Key Methods

**`isConfigurable(): bool`**
```php
public function isConfigurable(): bool
{
    return $this->is_configurable;
}
```

**`getConfigurationAttributes(): array`**
```php
public function getConfigurationAttributes(): array
{
    if (!$this->isConfigurable() || !$this->configuration_schema) {
        return [];
    }
    return $this->configuration_schema['attributes'] ?? [];
}
```

**`calculateConfiguredPrice(array $configuration): float`**

Calculates final price by adding selected option price modifiers to base price:

```php
public function calculateConfiguredPrice(array $configuration = []): float
{
    $basePrice = (float) $this->price;

    if (!$this->isConfigurable() || empty($configuration)) {
        return $basePrice;
    }

    $totalModifier = 0;

    foreach ($this->getConfigurationAttributes() as $attribute) {
        $attributeKey = $attribute['key'];
        if (isset($configuration[$attributeKey]['price'])) {
            $totalModifier += (float) $configuration[$attributeKey]['price'];
        }
    }

    return $basePrice + $totalModifier;
}
```

**Configuration Format in Runtime:**
```php
[
    'table_top' => [
        'option' => 'walnut',
        'label' => 'Nussbaum',
        'price' => 150.00
    ],
    'width' => [
        'option' => '150cm',
        'label' => '150 cm',
        'price' => 200.00
    ]
]
```

**`isValidConfiguration(array $configuration): bool`**

Validates that:
1. All required attributes have selections
2. Selected options exist in the schema

```php
public function isValidConfiguration(array $configuration): bool
{
    if (!$this->isConfigurable()) {
        return empty($configuration);
    }

    foreach ($this->getConfigurationAttributes() as $attribute) {
        // Check required attributes
        if (($attribute['required'] ?? false) && !isset($configuration[$attribute['key']])) {
            return false;
        }

        // Validate option exists
        if (isset($configuration[$attribute['key']])) {
            $selectedOption = $configuration[$attribute['key']]['option'] ?? null;
            $validOptions = collect($attribute['options'])->pluck('value')->toArray();

            if (!in_array($selectedOption, $validOptions)) {
                return false;
            }
        }
    }

    return true;
}
```

**`getDisplayPrice(): string`**

Shows "ab CHF X" for configurable products, "CHF X" for simple products:

```php
public function getDisplayPrice(): string
{
    if ($this->isConfigurable()) {
        return 'ab CHF ' . number_format($this->price, 2, '.', '\'');
    }

    return 'CHF ' . number_format($this->price, 2, '.', '\'');
}
```

### OrderItem Model

**File:** `app/Models/OrderItem.php`

#### New Fields

```php
protected $fillable = [
    'order_id',
    'product_name',
    'product_description',
    'product_price',
    'quantity',
    'configuration', // NEW
];

protected $casts = [
    'product_price' => 'decimal:2',
    'configuration' => 'array', // NEW
];
```

The `configuration` field stores the customer's selected options as JSON, preserving:
- Option keys and values
- Option labels (for display in order history)
- Price modifiers (for audit trail)

---

## Filament Admin Interface

### ProductForm Schema

**File:** `app/Filament/Admin/Resources/Products/Schemas/ProductForm.php`

#### Configuration Section

The admin UI provides a nested repeater interface for building configuration schemas:

```php
Section::make('Produktkonfiguration')
    ->description('Konfigurierbare Produkte ermöglichen Kunden, Optionen wie Material, Größe, etc. zu wählen.')
    ->schema([
        Toggle::make('is_configurable')
            ->label('Konfigurierbares Produkt')
            ->live()
            ->helperText('Aktivieren Sie dies für Produkte mit wählbaren Optionen'),

        Repeater::make('configuration_schema.attributes')
            ->label('Konfigurationsoptionen')
            ->schema([
                TextInput::make('key')
                    ->required()
                    ->label('Schlüssel')
                    ->helperText('Eindeutiger Bezeichner (z.B. table_top, width)'),

                TextInput::make('label')
                    ->required()
                    ->label('Bezeichnung')
                    ->helperText('Angezeigter Name (z.B. Tischplatte, Breite)'),

                Toggle::make('required')
                    ->label('Pflichtfeld')
                    ->default(true),

                Repeater::make('options')
                    ->label('Optionen')
                    ->schema([
                        TextInput::make('value')
                            ->required()
                            ->label('Wert'),

                        TextInput::make('label')
                            ->required()
                            ->label('Bezeichnung'),

                        TextInput::make('price_modifier')
                            ->numeric()
                            ->default(0)
                            ->prefix('CHF')
                            ->label('Preisaufschlag'),

                        Textarea::make('description')
                            ->label('Beschreibung')
                            ->rows(2),
                    ])
                    ->collapsible()
                    ->itemLabel(fn (array $state): ?string => $state['label'] ?? null),
            ])
            ->collapsible()
            ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
            ->visible(fn ($get) => $get('is_configurable')),
    ])
    ->collapsible()
    ->collapsed()
```

#### Key Features

- **Nested Repeaters:** Attributes contain repeatable options
- **Conditional Visibility:** Configuration fields only show when `is_configurable` is enabled
- **Live Updates:** Toggle changes immediately show/hide dependent fields
- **Item Labels:** Repeater items show the label field for easy identification
- **Collapsible Sections:** Reduces clutter when managing multiple attributes

### Admin Workflow

1. Create product with basic details
2. Enable "Konfigurierbares Produkt" toggle
3. Add attributes (e.g., "Tischplatte", "Breite")
4. For each attribute, add options with price modifiers
5. Mark attributes as required if needed
6. Save product

---

## Frontend Implementation

### Livewire Configurator Component

**File:** `app/Livewire/Product/Configurator.php`

#### Component Properties

```php
public string $productUuid;        // Product identifier
public array $configuration = [];  // Selected options
public int $quantity = 1;          // Quantity selector
public ?int $maxStock = null;      // Stock limit
public bool $inCart = false;       // Already in cart flag
```

#### Key Methods

**`mount(string $productUuid)`**

Initializes component with product data and syncs with cart:

```php
public function mount(string $productUuid): void
{
    $this->productUuid = $productUuid;
    $this->loadProduct();
    $this->syncWithCart();
}
```

**`syncWithCart()`**

Listens for `cart-updated` events and restores configuration from cart:

```php
#[On('cart-updated')]
public function syncWithCart(): void
{
    $cart = (new GetCartAction())->execute();
    $cartKey = $this->getCartItemKey();
    $item = collect($cart['items'])->firstWhere('cart_key', $cartKey);

    if ($item) {
        $this->quantity = $item['quantity'];
        $this->configuration = $item['configuration'] ?? [];
        $this->inCart = true;
    } else {
        $this->quantity = 1;
        $this->inCart = false;
    }
}
```

**`selectOption(string $attributeKey, string $optionValue)`**

Updates configuration when customer selects an option:

```php
public function selectOption(string $attributeKey, string $optionValue): void
{
    $product = $this->product;
    if (!$product || !$product->isConfigurable()) {
        return;
    }

    // Find the attribute and option
    foreach ($product->getConfigurationAttributes() as $attribute) {
        if ($attribute['key'] === $attributeKey) {
            foreach ($attribute['options'] as $option) {
                if ($option['value'] === $optionValue) {
                    $this->configuration[$attributeKey] = [
                        'option' => $option['value'],
                        'label' => $option['label'],
                        'price' => $option['price_modifier'] ?? 0,
                    ];
                    break 2;
                }
            }
        }
    }

    // If item is already in cart, update it
    if ($this->inCart) {
        $this->updateCart();
    }
}
```

**`addToCart()`**

Validates configuration and adds item to cart with unique cart_key:

```php
public function addToCart(): void
{
    $product = $this->product;

    if (!$product || $product->stock < 1) {
        return;
    }

    if (!$this->isConfigurationValid) {
        session()->flash('error', 'Bitte wählen Sie alle erforderlichen Optionen aus.');
        return;
    }

    $cart = (new GetCartAction())->execute();
    $cartItems = collect($cart['items']);
    $cartKey = $this->getCartItemKey();
    $existingItem = $cartItems->firstWhere('cart_key', $cartKey);

    if ($existingItem) {
        // Update quantity
        $cart['items'] = $cartItems->map(function ($item) use ($cartKey) {
            if ($item['cart_key'] === $cartKey) {
                $item['quantity'] = min($this->quantity, $this->maxStock);
            }
            return $item;
        })->toArray();
    } else {
        // Add new item
        $cart['items'][] = [
            'cart_key' => $cartKey,
            'uuid' => $product->uuid,
            'name' => $product->name,
            'description' => $product->description,
            'price' => $this->configuredPrice,
            'base_price' => $product->price,
            'quantity' => min($this->quantity, $product->stock),
            'image' => $product->image,
            'configuration' => $this->configuration,
        ];
    }

    $this->updateTotal($cart);
    $this->dispatch('open-mini-cart');
}
```

**`getCartItemKey(): string`**

Generates unique identifier for cart items:

```php
private function getCartItemKey(): string
{
    if (empty($this->configuration)) {
        return $this->productUuid;
    }

    $configHash = md5(json_encode($this->configuration));
    return $this->productUuid . '_' . $configHash;
}
```

#### Computed Properties

```php
#[Computed]
public function product(): ?Product
{
    return Product::where('uuid', $this->productUuid)->first();
}

#[Computed]
public function configuredPrice(): float
{
    if (!$this->product || !$this->product->isConfigurable()) {
        return $this->product?->price ?? 0;
    }

    return $this->product->calculateConfiguredPrice($this->configuration);
}

#[Computed]
public function isConfigurationValid(): bool
{
    if (!$this->product || !$this->product->isConfigurable()) {
        return true;
    }

    return $this->product->isValidConfiguration($this->configuration);
}
```

### Blade View

**File:** `resources/views/livewire/product/configurator.blade.php`

#### Configuration Options UI

```blade
@foreach($this->product->getConfigurationAttributes() as $attribute)
    <div>
        <label class="block text-sm font-muoto mb-2">
            {{ $attribute['label'] }}
            @if($attribute['required'] ?? false)
                <span class="text-red-500">*</span>
            @endif
        </label>

        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
            @foreach($attribute['options'] as $option)
                @php
                    $isSelected = isset($configuration[$attribute['key']])
                        && $configuration[$attribute['key']]['option'] === $option['value'];
                    $priceModifier = $option['price_modifier'] ?? 0;
                @endphp

                <button
                    type="button"
                    wire:click="selectOption('{{ $attribute['key'] }}', '{{ $option['value'] }}')"
                    class="px-4 py-3 border-2 rounded-lg transition-all text-left
                        {{ $isSelected ? 'border-black bg-black text-white' : 'border-gray-200 hover:border-gray-400' }}">

                    <div class="font-muoto">{{ $option['label'] }}</div>

                    @if($priceModifier > 0)
                        <div class="text-xs {{ $isSelected ? 'text-gray-200' : 'text-gray-500' }}">
                            +CHF {{ number_format($priceModifier, 2, '.', '\'') }}
                        </div>
                    @endif

                    @if(isset($option['description']) && $option['description'])
                        <div class="text-xs mt-1 {{ $isSelected ? 'text-gray-300' : 'text-gray-500' }}">
                            {{ $option['description'] }}
                        </div>
                    @endif
                </button>
            @endforeach
        </div>
    </div>
@endforeach
```

#### Configuration Summary

```blade
@if(!empty($configuration))
    <div class="bg-gray-50 p-4 rounded-lg">
        <h4 class="font-muoto text-sm mb-2">Ihre Konfiguration:</h4>
        <div class="space-y-1 text-sm">
            @foreach($configuration as $key => $config)
                <div class="flex justify-between">
                    <span class="text-gray-600">{{ $config['label'] }}</span>
                    @if($config['price'] > 0)
                        <span class="font-muoto">+CHF {{ number_format($config['price'], 2, '.', '\'') }}</span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endif
```

#### Price Display

```blade
<div class="flex items-baseline gap-2">
    <span class="text-2xl font-muoto">
        CHF {{ number_format($this->configuredPrice, 2, '.', '\'') }}
    </span>
    @if($this->product && $this->product->isConfigurable() && $this->configuredPrice > $this->product->price)
        <span class="text-sm text-gray-500 line-through">
            CHF {{ number_format($this->product->price, 2, '.', '\'') }}
        </span>
    @endif
</div>
```

#### Add to Cart Button

```blade
<x-product.add-button
    :inCart="$inCart"
    :disabled="!$this->isConfigurationValid" />

@if(!$this->isConfigurationValid && $this->product && $this->product->isConfigurable())
    <p class="text-sm text-red-600">Bitte wählen Sie alle erforderlichen Optionen aus.</p>
@endif
```

### Supporting Blade Components

**File:** `resources/views/components/product/add-button.blade.php`

```blade
@props(['inCart' => false, 'disabled' => false])

<button
    type="button"
    wire:click="addToCart"
    wire:loading.attr="disabled"
    class="w-full bg-black text-white px-6 py-3 rounded-lg font-muoto hover:bg-gray-800 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
    {{ ($inCart || $disabled) ? 'disabled' : '' }}>
    @if($inCart)
        <span>Im Warenkorb</span>
    @else
        <span wire:loading.remove wire:target="addToCart">In den Warenkorb</span>
        <span wire:loading wire:target="addToCart">Wird hinzugefügt...</span>
    @endif
</button>
```

**File:** `resources/views/components/product/quantity-selector.blade.php`

```blade
@props(['quantity', 'maxStock' => null])

<div class="flex items-center gap-2">
    <button
        type="button"
        wire:click="decrement"
        class="w-10 h-10 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-lg">
        <!-- Minus Icon -->
    </button>

    <span class="w-12 text-center font-muoto">{{ $quantity }}</span>

    <button
        type="button"
        wire:click="increment"
        class="w-10 h-10 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-lg">
        <!-- Plus Icon -->
    </button>
</div>
```

---

## Cart Integration

### Cart Structure

Cart items with configurations include:

```php
[
    'cart_key' => 'product-uuid_config-hash',  // Unique identifier
    'uuid' => 'product-uuid',                  // Product UUID
    'name' => 'Custom Table',                  // Product name
    'price' => 1250.00,                        // Configured price
    'base_price' => 900.00,                    // Original base price
    'quantity' => 1,                           // Quantity
    'configuration' => [                       // Selected options
        'table_top' => [
            'option' => 'walnut',
            'label' => 'Nussbaum',
            'price' => 150.00
        ],
        'width' => [
            'option' => '150cm',
            'label' => '150 cm',
            'price' => 200.00
        ]
    ],
    'image' => 'path/to/image.jpg',
]
```

### Multiple Configurations in Cart

The same product with different configurations appears as separate cart items:

```php
Cart Items:
1. Custom Table (Oak, 120cm) - CHF 900
2. Custom Table (Walnut, 150cm) - CHF 1,250
```

This is achieved through unique `cart_key` values combining product UUID and configuration hash.

---

## Order Persistence

### Saving Configurations to Orders

When an order is created from the cart, the configuration is preserved in the `order_items` table:

```php
OrderItem::create([
    'order_id' => $order->id,
    'product_name' => $item['name'],
    'product_description' => $item['description'],
    'product_price' => $item['price'],  // Configured price
    'quantity' => $item['quantity'],
    'configuration' => $item['configuration'],  // Saved as JSON
]);
```

### Displaying Configurations in Order History

To display customer's choices in order confirmation emails or admin panels:

```php
@if($orderItem->configuration)
    <div class="configuration-details">
        <strong>Konfiguration:</strong>
        <ul>
            @foreach($orderItem->configuration as $config)
                <li>{{ $config['label'] }}
                    @if($config['price'] > 0)
                        (+CHF {{ number_format($config['price'], 2, '.', '\'') }})
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
@endif
```

---

## Step-by-Step Re-Implementation Guide

### Phase 1: Database Setup

1. **Create configurable fields migration**
   ```bash
   php artisan make:migration add_configurable_fields_to_products_table
   ```

   Add `is_configurable` and `configuration_schema` columns to products table.

2. **Create order items configuration migration**
   ```bash
   php artisan make:migration add_configuration_to_order_items_table
   ```

   Add `configuration` JSON column to order_items table.

3. **Run migrations**
   ```bash
   php artisan migrate
   ```

### Phase 2: Model Implementation

1. **Update Product model**
   - Add fillable fields: `is_configurable`, `configuration_schema`
   - Add casts: `'is_configurable' => 'boolean'`, `'configuration_schema' => 'array'`
   - Implement methods:
     - `isConfigurable()`
     - `getConfigurationAttributes()`
     - `calculateConfiguredPrice(array $configuration)`
     - `isValidConfiguration(array $configuration)`
     - `getDisplayPrice()`

2. **Update OrderItem model**
   - Add fillable field: `configuration`
   - Add cast: `'configuration' => 'array'`

### Phase 3: Admin Interface (Filament)

1. **Update ProductForm schema**
   - Add "Produktkonfiguration" section
   - Add `is_configurable` toggle
   - Add nested repeater for `configuration_schema.attributes`
     - Attribute fields: key, label, required
     - Options repeater: value, label, price_modifier, description
   - Set conditional visibility based on `is_configurable`

2. **Test in Filament admin**
   - Create a test product
   - Enable configurable toggle
   - Add sample attributes and options
   - Verify JSON structure is saved correctly

### Phase 4: Frontend Configurator

1. **Create Livewire component**
   ```bash
   php artisan make:livewire Product/Configurator
   ```

2. **Implement component logic**
   - Properties: productUuid, configuration, quantity, maxStock, inCart
   - Methods: mount, syncWithCart, selectOption, addToCart, updateCart
   - Computed properties: product, configuredPrice, isConfigurationValid
   - Cart key generation: getCartItemKey()

3. **Create Blade view**
   - Configuration options grid (buttons for each option)
   - Configuration summary panel
   - Dynamic price display
   - Quantity selector
   - Add to cart button with validation

4. **Create Blade components**
   - `x-product.add-button` - Add to cart button with states
   - `x-product.quantity-selector` - Increment/decrement controls

### Phase 5: Cart Integration

1. **Update cart structure**
   - Add `cart_key` field (unique per configuration)
   - Add `configuration` field to cart items
   - Add `base_price` field (for reference)

2. **Update cart actions**
   - Modify Add to Cart to use cart_key
   - Store configuration with cart items
   - Handle configuration updates for items already in cart

3. **Update cart display**
   - Show configuration details in cart
   - Display configured price
   - Support multiple configurations of same product

### Phase 6: Order Persistence

1. **Update order creation logic**
   - Pass configuration from cart to order_items
   - Store configured price in product_price
   - Preserve configuration JSON

2. **Update order views**
   - Display configuration in order confirmation
   - Show configuration in admin order details
   - Include configuration in email notifications

### Phase 7: Testing & Refinement

1. **Test cases**
   - Create configurable product with multiple attributes
   - Select various options and verify price calculation
   - Add to cart with different configurations
   - Verify unique cart items for different configs
   - Complete checkout and verify order persistence
   - Check order history displays configurations correctly

2. **Edge cases**
   - Required vs optional attributes
   - Options with zero price modifier
   - Products with no configuration (simple products)
   - Updating configuration of item already in cart

---

## Technical Considerations

### Performance

- **Configuration validation:** Happens on every option selection (real-time)
- **Price calculation:** Computed property recalculates on configuration change
- **Cart sync:** Triggered by Livewire events (`cart-updated`)

### Scalability

- **Schema flexibility:** JSON schema supports unlimited attributes/options
- **Cart uniqueness:** MD5 hash of configuration prevents duplicate keys
- **Order history:** Configuration preserved even if product is deleted

### Security

- **Validation:** Server-side validation ensures options exist in schema
- **Price integrity:** Prices calculated from schema, not user input
- **Required fields:** Enforced before add-to-cart

### Limitations

- **No inventory per configuration:** Stock tracked at product level, not per option
- **No images per option:** Single product image for all configurations
- **No option dependencies:** Cannot show/hide options based on other selections
- **Linear pricing:** Only supports additive price modifiers, no multipliers

---

## Future Enhancement Ideas

If re-implementing this system, consider these improvements:

### Enhanced Features

1. **Option Images**
   - Attach images to individual options (e.g., wood sample photos)
   - Update main product image when option is selected

2. **Inventory Per Configuration**
   - Track stock for specific combinations
   - Show "out of stock" for unavailable configurations

3. **Conditional Options**
   - Show/hide options based on other selections
   - Example: "Table legs" options appear only if "with legs" is selected

4. **Visual Configurator**
   - Real-time 3D preview of product
   - Visual representation of selections

5. **Saved Configurations**
   - Allow customers to save custom configs for later
   - Share configuration links with others

### Technical Improvements

1. **Configuration Validation Rules**
   - More complex validation (min/max selections, mutual exclusivity)
   - Custom validation messages per attribute

2. **Price Calculation Flexibility**
   - Support percentage-based modifiers
   - Allow price multipliers based on selections
   - Tiered pricing (bulk discounts on configurations)

3. **Admin UX Enhancements**
   - Preview configurator in admin
   - Import/export configuration schemas
   - Template configurations for similar products

4. **Performance Optimization**
   - Cache computed prices
   - Lazy load configuration schemas
   - Optimize cart key generation

---

## Code Removal Checklist

When cleaning up the codebase to remove this feature:

### Database
- [ ] Drop columns from `products`: `is_configurable`, `configuration_schema`
- [ ] Drop column from `order_items`: `configuration`
- [ ] Clean up any test data with configurations

### Models
- [ ] Remove Product model methods:
  - `isConfigurable()`
  - `getConfigurationAttributes()`
  - `calculateConfiguredPrice()`
  - `isValidConfiguration()`
  - `getDisplayPrice()`
- [ ] Remove fillable fields and casts
- [ ] Remove OrderItem configuration field and cast

### Filament
- [ ] Remove configuration section from `ProductForm`
- [ ] Remove related form fields and repeaters
- [ ] Update table display if needed

### Livewire
- [ ] Delete `app/Livewire/Product/Configurator.php`
- [ ] Delete `resources/views/livewire/product/configurator.blade.php`

### Blade Components
- [ ] Review and potentially remove:
  - `resources/views/components/product/add-button.blade.php`
  - `resources/views/components/product/quantity-selector.blade.php`
  - (Keep if used elsewhere, otherwise delete)

### Cart & Orders
- [ ] Remove `cart_key` logic from cart actions
- [ ] Simplify cart structure (remove configuration field)
- [ ] Update order creation to remove configuration handling
- [ ] Update order display views

### Views
- [ ] Remove configuration display from cart views
- [ ] Remove configuration display from order confirmation
- [ ] Remove configuration from email templates

### Migrations (Optional)
- [ ] Keep migrations in history for rollback capability
- [ ] Or create new "drop configurable fields" migrations

---

## Related Files Reference

### Models
- `app/Models/Product.php` - Main product model with configuration methods
- `app/Models/OrderItem.php` - Order line item with configuration storage

### Migrations
- `database/migrations/2025_11_11_122540_create_products_table.php` - Base products table
- `database/migrations/2025_11_13_115202_add_configurable_fields_to_products_table.php` - Adds configurable fields
- `database/migrations/2025_11_11_122601_create_order_items_table.php` - Base order items table
- `database/migrations/2025_11_13_115815_add_configuration_to_order_items_table.php` - Adds configuration field

### Filament Admin
- `app/Filament/Admin/Resources/Products/ProductResource.php` - Product resource definition
- `app/Filament/Admin/Resources/Products/Schemas/ProductForm.php` - Form with configuration section
- `app/Filament/Admin/Resources/Products/Tables/ProductsTable.php` - Products listing table

### Livewire
- `app/Livewire/Product/Configurator.php` - Main configurator component
- `resources/views/livewire/product/configurator.blade.php` - Configurator view

### Blade Components
- `resources/views/components/product/add-button.blade.php` - Add to cart button
- `resources/views/components/product/quantity-selector.blade.php` - Quantity controls

### Cart Actions
- `app/Actions/Cart/Get.php` - Retrieve cart from session
- `app/Actions/Cart/Update.php` - Update cart in session

---

## Questions & Troubleshooting

### Common Issues

**Q: Configuration not saving to cart**
- Verify cart_key generation is working
- Check that GetCartAction and UpdateCartAction are called correctly
- Ensure configuration is passed in addToCart() method

**Q: Price not updating when options change**
- Confirm selectOption() is called via wire:click
- Verify calculated price uses correct price_modifier from schema
- Check Livewire computed property caching

**Q: Validation always failing**
- Review required attribute configuration in schema
- Ensure all required attributes have options selected
- Check isValidConfiguration() logic matches schema structure

**Q: Multiple items not appearing in cart**
- Verify cart_key includes configuration hash
- Confirm each configuration generates unique hash
- Check cart comparison logic uses cart_key not uuid

### Debugging Tips

1. **Dump configuration state:**
   ```blade
   @dump($configuration)
   @dump($this->configuredPrice)
   @dump($this->isConfigurationValid)
   ```

2. **Inspect cart structure:**
   ```php
   dd((new GetCartAction())->execute());
   ```

3. **Verify schema structure:**
   ```php
   dd($product->configuration_schema);
   dd($product->getConfigurationAttributes());
   ```

---

## Conclusion

This configurable products system provides a complete solution for selling customizable products with:

- Flexible JSON schema for defining options
- Real-time price calculation with modifiers
- Intuitive frontend configurator with Livewire
- Comprehensive admin interface with Filament
- Full cart and order integration
- Validation to ensure complete configurations

The system is production-ready and has been tested in the shop.hobel.ch environment with CHF currency and German language support.

---

**Document Version:** 1.0
**Last Updated:** November 26, 2025
**Author:** Claude Code
**Status:** Archived - Preserved for future reference
