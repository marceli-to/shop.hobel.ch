<?php

namespace App\Livewire\Cart;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Product;
use App\Actions\Cart\Get as GetCartAction;
use App\Actions\Cart\Update as UpdateCartAction;

class Button extends Component
{
	public string $productUuid;
	public ?string $cartKey = null;
	public int $quantity = 1;
	public ?int $maxStock = null;
	public bool $inCart = false;
	public bool $showButton = true;

	public function mount(string $productUuid, ?string $cartKey = null, bool $showButton = true): void
	{
		$this->productUuid = $productUuid;
		$this->cartKey = $cartKey ?? $productUuid;
		$this->showButton = $showButton;
		$this->loadProduct();
		$this->syncWithCart();
	}

	public function switchProduct(string $uuid): void
	{
		$this->productUuid = $uuid;
		$this->cartKey = $uuid;
		$this->quantity = 1;
		$this->loadProduct();
		$this->syncWithCart();
	}

	#[On('cart-updated')]
	public function syncWithCart(): void
	{
		$cart = (new GetCartAction())->execute();
		$item = collect($cart['items'])->firstWhere('cart_key', $this->cartKey);

		if ($item) {
			$this->quantity = $item['quantity'];
			$this->inCart = true;
		} else {
			$this->quantity = 1;
			$this->inCart = false;
		}
	}

	private function loadProduct(): void
	{
		$product = Product::where('uuid', $this->productUuid)->first();
		if ($product) {
			$this->maxStock = $product->stock;
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

		$cart = (new GetCartAction())->execute();
		$cartItems = collect($cart['items']);
		$existingItem = $cartItems->firstWhere('cart_key', $this->cartKey);

		if ($existingItem) {
			// Update quantity
			$cart['items'] = $cartItems->map(function ($item) use ($product) {
				if ($item['cart_key'] === $this->cartKey) {
					$item['quantity'] = min($this->quantity, $product->stock);
				}
				return $item;
			})->toArray();
		} else {
			// For child products, get shipping methods from parent
			$shippingProduct = $product->parent_id ? $product->parent : $product;
			$shippingMethods = $shippingProduct->shippingMethods->map(function ($method) {
				return [
					'id' => $method->id,
					'name' => $method->name,
					'price' => $method->pivot->price ?? $method->price,
					'is_shipping' => $method->is_shipping,
				];
			})->toArray();

			// For child products, get image from parent
			$imageProduct = $product->parent_id ? $product->parent : $product;
			$image = $imageProduct->images->first()?->file_path;

			// Display name: parent name + child label (if child)
			$displayName = $product->parent_id && $product->label
				? $product->parent->name
				: $product->name;

			// Add new item
			$cart['items'][] = [
				'cart_key' => $this->cartKey,
				'uuid' => $product->uuid,
				'name' => $displayName,
				'label' => $product->label, // For child products
				'description' => $product->description,
				'price' => $product->price,
				'base_price' => $product->price,
				'quantity' => min($this->quantity, $product->stock),
				'image' => $image,
				'configuration' => null,
				'shipping_methods' => $shippingMethods,
				'selected_shipping' => $shippingMethods[0]['id'] ?? null,
				'shipping_name' => $shippingMethods[0]['name'] ?? null,
				'shipping_price' => 0,
				'is_shipping' => $shippingMethods[0]['is_shipping'] ?? false,
			];
		}

		$this->updateTotal($cart);
		$this->dispatch('open-mini-cart');
	}

	private function updateCart(): void
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
			if ($item['cart_key'] === $this->cartKey) {
				$item['quantity'] = min($this->quantity, $product->stock);
			}
			return $item;
		})->toArray();

		$this->updateTotal($cart);
	}

	private function updateTotal(array $cart): void
	{
		$taxRate = config('invoice.tax_rate') / 100;
		$flatRate = config('invoice.shipping_flat_rate');
		$freeThreshold = config('invoice.shipping_free_threshold');
		$items = collect($cart['items']);

		$subtotal = $items->sum(fn($item) => $item['price'] * $item['quantity']);

		$shippingItems = $items->filter(fn($item) => $item['is_shipping'] ?? false);
		$shippingSubtotal = $shippingItems->sum(fn($item) => $item['price'] * $item['quantity']);
		$shipping = ($shippingItems->isNotEmpty() && $shippingSubtotal < $freeThreshold) ? $flatRate : 0;

		$cart['subtotal'] = $subtotal;
		$cart['shipping'] = $shipping;
		$cart['tax'] = ($subtotal + $shipping) * $taxRate;
		$cart['total'] = $subtotal + $shipping + $cart['tax'];
		$cart['quantity'] = $items->sum('quantity');

		// Initialize order_step if not set (cart has items, so step 1 is complete)
		if (!isset($cart['order_step'])) {
			$cart['order_step'] = 1;
		}

		(new UpdateCartAction())->execute($cart);
		$this->dispatch('cart-updated');
	}

	public function render()
	{
		return view('livewire.cart.button');
	}
}
