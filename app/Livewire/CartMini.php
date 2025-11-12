<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Actions\Cart\GetCart;
use App\Actions\Cart\UpdateCart;
use App\Models\Product;

class CartMini extends Component
{
	public array $cart = [];
	public bool $show = false;

	public function mount(): void
	{
		$this->cart = (new GetCart())->execute();
	}

	#[On('toggle-mini-cart')]
	public function toggle(): void
	{
		$this->show = !$this->show;
	}

	#[On('cart-updated')]
	public function updateCart(): void
	{
		$this->cart = (new GetCart())->execute();
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

	public function removeItem(string $productUuid): void
	{
		$this->cart = (new GetCart())->execute();
		$items = collect($this->cart['items'])->filter(function ($item) use ($productUuid) {
			return $item['uuid'] !== $productUuid;
		})->values()->toArray();

		$this->cart['items'] = $items;
		$this->cart['quantity'] = count($items);
		$this->updateTotal();
	}

	private function updateTotal(): void
	{
		$this->cart['total'] = collect($this->cart['items'])->sum(function ($item) {
			return $item['price'] * $item['quantity'];
		});

		(new UpdateCart())->execute($this->cart);
		$this->dispatch('cart-updated');
	}

	public function render()
	{
		return view('livewire.cart-mini');
	}
}
