<?php

namespace App\Actions\Cart;

class Destroy
{
	public function execute(): void
	{
		session()->forget('cart');
	}
}
