<?php

namespace App\Actions\Order;

use App\Models\Order;

class GenerateOrderNumber
{
	public function execute(): string
	{
		do {
			$number = 'HOBEL-' . $this->generateCode(6);
		} while (Order::where('order_number', $number)->exists());

		return $number;
	}

	private function generateCode(int $length): string
	{
		// Excludes 0, O, 1, I, L to avoid confusion
		$chars = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';
		$code = '';

		for ($i = 0; $i < $length; $i++) {
			$code .= $chars[random_int(0, strlen($chars) - 1)];
		}

		return $code;
	}
}
