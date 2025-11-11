<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Actions\Cart\GetCart;

class MiniCart extends Component
{
	public array $cart;
	public bool $showCart = false;

	public function mount(): void
	{
		$this->cart = (new GetCart())->execute();
	}

	#[On('cart-updated')]
	public function updateCart(): void
	{
		$this->cart = (new GetCart())->execute();
		$this->showCart = $this->cart['quantity'] > 0;
	}

	public function toggleCart(): void
	{
		$this->showCart = !$this->showCart;
	}

	public function render()
	{
		return view('livewire.mini-cart');
	}
}
