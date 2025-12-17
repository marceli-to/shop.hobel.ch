<?php

namespace App\Actions\Order;

use App\Actions\Cart\Destroy as DestroyCartAction;

class Finalize
{
	public function execute(): void
	{
		// TODO: Create invoice PDF
		// TODO: Send information mail to admin
		// TODO: Send confirmation mail to customer

		// Clear cart and checkout session data
		(new DestroyCartAction())->execute();

		// Clear payment-specific session data
		session()->forget(['payment_reference', 'payment_gateway_id']);
	}
}
