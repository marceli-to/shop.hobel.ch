<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Actions\Cart\Get as GetCartAction;

class CartIcon extends Component
{
	public int $cartItemCount = 0;

	public function mount(): void
	{
		$this->updateCartItemCount();
	}

	#[On('cart-updated')]
	public function updateCartItemCount(): void
	{
		$cart = (new GetCartAction())->execute();
		$this->cartItemCount = count($cart['items'] ?? []);
	}

	public function toggleMiniCart(): void
	{
		$this->dispatch('toggle-mini-cart');
	}

	public function render()
	{
		return view('livewire.cart-icon');
	}
}
