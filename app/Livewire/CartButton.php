<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Actions\Cart\GetCart;
use App\Actions\Cart\StoreCart;

class CartButton extends Component
{
	public string $productUuid;
	public int $quantity = 1;
	public array $cart;

	public function mount(string $productUuid): void
	{
		$this->productUuid = $productUuid;
		$this->cart = (new GetCart())->execute();

		// Check if product is already in cart
		$product = collect($this->cart['items'])->firstWhere('uuid', $this->productUuid);
		if ($product) {
			$this->quantity = $product['quantity'];
		}
	}

	public function addToCart(): void
	{
		$product = Product::where('uuid', $this->productUuid)->first();

		if (!$product) {
			return;
		}

		$this->cart = (new GetCart())->execute();
		$cartItems = collect($this->cart['items']);
		$existingItem = $cartItems->firstWhere('uuid', $product->uuid);

		if ($existingItem) {
			// Update quantity
			$this->cart['items'] = $cartItems->map(function ($item) use ($product) {
				if ($item['uuid'] === $product->uuid) {
					$item['quantity'] = min($this->quantity, $product->stock);
				}
				return $item;
			})->toArray();
		} else {
			// Add new item
			$this->cart['items'][] = [
				'uuid' => $product->uuid,
				'name' => $product->name,
				'description' => $product->description,
				'price' => $product->price,
				'quantity' => min($this->quantity, $product->stock),
				'image' => $product->image,
			];
			$this->cart['quantity']++;
		}

		$this->updateCartTotal();
	}

	public function updateCartTotal(): void
	{
		$this->cart['total'] = collect($this->cart['items'])->sum(function ($item) {
			return $item['price'] * $item['quantity'];
		});

		(new StoreCart())->execute($this->cart);
		$this->dispatch('cart-updated');
	}

	public function render()
	{
		return view('livewire.cart-button');
	}
}
