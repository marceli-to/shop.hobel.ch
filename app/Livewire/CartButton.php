<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Product;
use App\Actions\Cart\GetCart;
use App\Actions\Cart\UpdateCart;

class CartButton extends Component
{
	public string $productUuid;
	public int $quantity = 1;
	public ?int $maxStock = null;
	public bool $inCart = false;

	public function mount(string $productUuid): void
	{
		$this->productUuid = $productUuid;
		$this->loadProduct();
		$this->syncWithCart();
	}

	#[On('cart-updated')]
	public function syncWithCart(): void
	{
		$cart = (new GetCart())->execute();
		$item = collect($cart['items'])->firstWhere('uuid', $this->productUuid);

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

	public function addToCart(): void
	{
		$product = Product::where('uuid', $this->productUuid)->first();

		if (!$product || $product->stock < 1) {
			return;
		}

		$cart = (new GetCart())->execute();
		$cartItems = collect($cart['items']);
		$existingItem = $cartItems->firstWhere('uuid', $product->uuid);

		if ($existingItem) {
			// Update quantity
			$cart['items'] = $cartItems->map(function ($item) use ($product) {
				if ($item['uuid'] === $product->uuid) {
					$item['quantity'] = min($this->quantity, $product->stock);
				}
				return $item;
			})->toArray();
		} else {
			// Add new item
			$cart['items'][] = [
				'uuid' => $product->uuid,
				'name' => $product->name,
				'description' => $product->description,
				'price' => $product->price,
				'quantity' => min($this->quantity, $product->stock),
				'image' => $product->image,
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

		$cart = (new GetCart())->execute();
		$cart['items'] = collect($cart['items'])->map(function ($item) use ($product) {
			if ($item['uuid'] === $product->uuid) {
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

		(new UpdateCart())->execute($cart);
		$this->dispatch('cart-updated');
	}

	public function render()
	{
		return view('livewire.cart-button');
	}
}
