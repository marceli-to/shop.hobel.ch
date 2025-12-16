<?php

namespace App\Livewire\Cart;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Actions\Cart\Get as GetCartAction;

class CartImages extends Component
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

	public function render()
	{
		return view('livewire.cart.cart-images');
	}
}
