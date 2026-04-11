<?php

namespace App\Livewire\Product\Concerns;

use App\Models\Product;
use App\Actions\Cart\Get as GetCartAction;
use App\Actions\Cart\Update as UpdateCartAction;
use Livewire\Attributes\On;

trait HandlesCart
{
    public string $productUuid = '';
    public int $quantity = 1;
    public ?int $maxStock = null;
    public bool $inCart = false;
    public bool $canAddToCart = false;

    public function initializeHandlesCart(): void
    {
        $this->loadCartProduct();
        $this->syncWithCart();
    }

    #[On('cart-updated')]
    public function syncWithCart(): void
    {
        $cart = (new GetCartAction())->execute();
        $item = collect($cart['items'])->firstWhere('cart_key', $this->productUuid);

        if ($item) {
            $this->quantity = $item['quantity'];
            $this->inCart = true;
        } else {
            $this->quantity = 1;
            $this->inCart = false;
        }
    }

    protected function loadCartProduct(): void
    {
        $product = Product::where('uuid', $this->productUuid)->first();
        if ($product) {
            $this->maxStock = $product->stock;
            $shippingProduct = $product->parent_id ? $product->parent : $product;
            $this->canAddToCart = $shippingProduct->flat_rate_shipping && $product->stock >= 1;
        }
    }

    public function increment(): void
    {
        if ($this->maxStock && $this->quantity < $this->maxStock) {
            $this->quantity++;
            $this->updateCart();
        }
    }

    public function decrement(): void
    {
        if ($this->quantity > 1) {
            $this->quantity--;
            $this->updateCart();
        }
    }

    public function updatedQuantity($value): void
    {
        $this->quantity = max(1, min((int) $value, $this->maxStock ?? PHP_INT_MAX));
        $this->updateCart();
    }

    public function addToCart(): void
    {
        $product = Product::where('uuid', $this->productUuid)->first();

        if (!$product || $product->stock < 1) {
            return;
        }

        $shippingProduct = $product->parent_id ? $product->parent : $product;
        if (!$shippingProduct->flat_rate_shipping) {
            return;
        }

        $cart = (new GetCartAction())->execute();
        $cartItems = collect($cart['items']);
        $existingItem = $cartItems->firstWhere('cart_key', $this->productUuid);

        if ($existingItem) {
            $cart['items'] = $cartItems->map(function ($item) use ($product) {
                if ($item['cart_key'] === $this->productUuid) {
                    $item['quantity'] = min($this->quantity, $product->stock);
                }
                return $item;
            })->toArray();
        } else {
            $shippingMethods = $shippingProduct->shippingMethods->map(function ($method) {
                return [
                    'id' => $method->id,
                    'name' => $method->name,
                    'price' => $method->pivot->price ?? $method->price,
                ];
            })->toArray();

            $imageProduct = $product->parent_id ? $product->parent : $product;
            $image = $imageProduct->images->first()?->file_path;

            $displayName = $product->parent_id && $product->label
                ? $product->parent->name
                : $product->name;

            $cart['items'][] = [
                'cart_key' => $this->productUuid,
                'uuid' => $product->uuid,
                'name' => $displayName,
                'label' => $product->label,
                'description' => $product->description,
                'price' => $product->price,
                'base_price' => $product->price,
                'quantity' => min($this->quantity, $product->stock),
                'image' => $image,
                'configuration' => [],
                'shipping_methods' => $shippingMethods,
                'selected_shipping' => $shippingMethods[0]['id'] ?? null,
                'shipping_name' => $shippingMethods[0]['name'] ?? null,
                'shipping_price' => 0,
            ];
        }

        $this->recalculateCart($cart);
        $this->dispatch('open-mini-cart');
    }

    protected function updateCart(): void
    {
        if (!$this->inCart) {
            return;
        }

        $product = Product::where('uuid', $this->productUuid)->first();

        if (!$product) {
            return;
        }

        $cart = (new GetCartAction())->execute();

        $cart['items'] = collect($cart['items'])->map(function ($item) use ($product) {
            if ($item['cart_key'] === $this->productUuid) {
                $item['quantity'] = min($this->quantity, $product->stock);
            }
            return $item;
        })->toArray();

        $this->recalculateCart($cart);
    }

    protected function recalculateCart(array $cart): void
    {
        $taxRate = config('invoice.tax_rate') / 100;
        $flatRate = config('invoice.shipping_flat_rate');
        $freeThreshold = config('invoice.shipping_free_threshold');
        $items = collect($cart['items']);

        $subtotal = $items->sum(fn($item) => $item['price'] * $item['quantity']);

        $hasShipping = $items->contains(fn($item) => str_contains($item['shipping_name'] ?? '', 'Versand'));
        $shipping = ($hasShipping && $subtotal < $freeThreshold) ? $flatRate : 0;

        $cart['subtotal'] = $subtotal;
        $cart['shipping'] = $shipping;
        $cart['tax'] = ($subtotal + $shipping) * $taxRate;
        $cart['total'] = $subtotal + $shipping + $cart['tax'];
        $cart['quantity'] = $items->sum('quantity');

        if (!isset($cart['order_step'])) {
            $cart['order_step'] = 1;
        }

        (new UpdateCartAction())->execute($cart);
        $this->dispatch('cart-updated');
    }
}
