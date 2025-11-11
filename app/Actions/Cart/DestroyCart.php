<?php

namespace App\Actions\Cart;

class DestroyCart
{
	public function execute(): void
	{
		session()->forget('cart');
	}
}
