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
			// Add new item
			$cart['items'][] = [
				'cart_key' => $this->cartKey,
				'uuid' => $product->uuid,
				'name' => $product->name,
				'description' => $product->description,
				'price' => $product->price,
				'base_price' => $product->price,
				'quantity' => min($this->quantity, $product->stock),
				'image' => $product->image,
				'configuration' => [],
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
		$cart['total'] = collect($cart['items'])->sum(function ($item) {
			return $item['price'] * $item['quantity'];
		});

		$cart['quantity'] = collect($cart['items'])->sum('quantity');

		(new UpdateCartAction())->execute($cart);
		$this->dispatch('cart-updated');
	}

	public function render()
	{
		return view('livewire.cart.button');
	}
}
