<?php

namespace App\Actions\Cart;

class GetCart
{
	public function execute(): array
	{
		return session()->get('cart', [
			'items' => [],
			'quantity' => 0,
			'total' => 0,
		]);
	}
}
