<?php

namespace App\Livewire\Cart;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Actions\Cart\Get as GetCartAction;
use App\Actions\Cart\Update as UpdateCartAction;
use App\Models\Product;

class MiniCart extends Component
{
	public array $cart = [];
	public bool $show = false;

	public function mount(): void
	{
		$this->cart = (new GetCartAction())->execute();
	}

	#[On('toggle-mini-cart')]
	public function toggle(): void
	{
		$this->show = !$this->show;
	}

	#[On('cart-updated')]
	public function updateCart(): void
	{
		$this->cart = (new GetCartAction())->execute();
	}

	#[On('open-mini-cart')]
	public function open(): void
	{
		$this->show = true;
	}

	public function close(): void
	{
		$this->show = false;
	}

	public function removeItem(string $cartKey): void
	{
		$this->cart = (new GetCartAction())->execute();
		$items = collect($this->cart['items'])->filter(function ($item) use ($cartKey) {
			return ($item['cart_key'] ?? $item['uuid']) !== $cartKey;
		})->values()->toArray();

		$this->cart['items'] = $items;
		$this->cart['quantity'] = collect($items)->sum('quantity');
		$this->updateTotal();
	}

	private function updateTotal(): void
	{
		$this->cart['total'] = collect($this->cart['items'])->sum(function ($item) {
			return $item['price'] * $item['quantity'];
		});

		(new UpdateCartAction())->execute($this->cart);
		$this->dispatch('cart-updated');
	}

	public function render()
	{
		return view('livewire.cart.mini-cart');
	}
}
