<?php

namespace App\Actions\Cart;

class UpdateCart
{
	public function execute(array $updates): array
	{
		$cart = (new GetCart())->execute();

		$cart = array_merge($cart, $updates);

		(new StoreCart())->execute($cart);

		return $cart;
	}
}
