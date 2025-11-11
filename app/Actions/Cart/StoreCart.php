<?php

namespace App\Actions\Cart;

class StoreCart
{
	public function execute(array $cart): void
	{
		session()->put('cart', $cart);
	}
}
