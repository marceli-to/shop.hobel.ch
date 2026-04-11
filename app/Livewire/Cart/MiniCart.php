<?php

namespace App\Livewire\Cart;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Actions\Cart\Get as GetCartAction;
use App\Actions\Cart\Update as UpdateCartAction;
use App\Actions\Cart\Destroy as DestroyCartAction;
use App\Models\Product;

class MiniCart extends Component
{
	public array $cart = [];
	public bool $show = false;
  public bool $isLanding = false;

	public function mount(): void
	{
		$this->cart = (new GetCartAction())->execute();
    $this->isLanding = request()->routeIs('page.landing');
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

		if ($this->cart['quantity'] <= 0) {
			(new DestroyCartAction())->execute();
			$this->dispatch('cart-updated');
			$this->close();
			return;
		}

		$this->updateTotal();
	}

	private function updateTotal(): void
	{
		$taxRate = config('invoice.tax_rate') / 100;
		$flatRate = config('invoice.shipping_flat_rate');
		$freeThreshold = config('invoice.shipping_free_threshold');
		$items = collect($this->cart['items']);

		$subtotal = $items->sum(fn($item) => $item['price'] * $item['quantity']);

		$hasShipping = $items->contains(fn($item) => str_contains($item['shipping_name'] ?? '', 'Versand'));
		$shipping = ($hasShipping && $subtotal < $freeThreshold) ? $flatRate : 0;

		$this->cart['subtotal'] = $subtotal;
		$this->cart['shipping'] = $shipping;
		$this->cart['tax'] = ($subtotal + $shipping) * $taxRate;
		$this->cart['total'] = $subtotal + $shipping + $this->cart['tax'];
		$this->cart['quantity'] = $items->sum('quantity');

		(new UpdateCartAction())->execute($this->cart);
		$this->dispatch('cart-updated');
	}

	public function render()
	{
		return view('livewire.cart.mini-cart');
	}
}
