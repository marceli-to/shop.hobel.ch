<?php

namespace App\Livewire\Cart;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Actions\Cart\Get as GetCartAction;
use App\Actions\Cart\Update as UpdateCartAction;
use App\Actions\Cart\Destroy as DestroyCartAction;

class Cart extends Component
{
	public array $cart;

	public function mount(): void
	{
		$this->cart = (new GetCartAction())->execute();
	}

	#[On('cart-updated')]
	public function updateCart(): void
	{
		$this->cart = (new GetCartAction())->execute();
	}

	public function removeItem(string $cartKey): void
	{
		$this->cart = (new GetCartAction())->execute();

		$this->cart['items'] = collect($this->cart['items'])
			->reject(fn($item) => ($item['cart_key'] ?? $item['uuid']) === $cartKey)
			->values()
			->toArray();

		$this->cart['quantity'] = collect($this->cart['items'])->sum('quantity');

		if ($this->cart['quantity'] <= 0) {
			(new DestroyCartAction())->execute();
			$this->cart = [
				'items' => [],
				'quantity' => 0,
				'total' => 0,
			];
		} else {
			$this->updateTotal();
		}

		$this->dispatch('cart-updated');
	}

	public function updateQuantity(string $cartKey, int $quantity): void
	{
		if ($quantity <= 0) {
			$this->removeItem($cartKey);
			return;
		}

		$this->cart = (new GetCartAction())->execute();

		$this->cart['items'] = collect($this->cart['items'])
			->map(function ($item) use ($cartKey, $quantity) {
				if (($item['cart_key'] ?? $item['uuid']) === $cartKey) {
					$item['quantity'] = $quantity;
				}
				return $item;
			})
			->toArray();

		$this->updateTotal();
		$this->dispatch('cart-updated');
	}

	private function updateTotal(): void
	{
		$this->cart['total'] = collect($this->cart['items'])->sum(fn($item) => $item['price'] * $item['quantity']);
		$this->cart['quantity'] = collect($this->cart['items'])->sum('quantity');
		(new UpdateCartAction())->execute($this->cart);
	}

	public function render()
	{
		return view('livewire.cart.cart');
	}
}
