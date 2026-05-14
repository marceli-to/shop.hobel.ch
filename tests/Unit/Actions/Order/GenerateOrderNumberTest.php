<?php

namespace Tests\Unit\Actions\Order;

use App\Actions\Order\GenerateOrderNumber;
use App\Models\Order;
use Carbon\Carbon;
use Tests\TestCase;

class GenerateOrderNumberTest extends TestCase
{
	public function test_formats_as_year_plus_padded_id(): void
	{
		$order = new Order();
		$order->id = 1;
		$order->created_at = Carbon::create(2026, 5, 14);

		$this->assertSame('2600001', (new GenerateOrderNumber())->execute($order));
	}

	public function test_pads_to_five_digits(): void
	{
		$order = new Order();
		$order->id = 42;
		$order->created_at = Carbon::create(2025, 1, 1);

		$this->assertSame('2500042', (new GenerateOrderNumber())->execute($order));
	}

	public function test_handles_large_ids_without_truncation(): void
	{
		$order = new Order();
		$order->id = 123456;
		$order->created_at = Carbon::create(2027, 6, 1);

		// 6-digit IDs overflow the 5-digit pad — documents current behaviour
		$this->assertSame('27123456', (new GenerateOrderNumber())->execute($order));
	}
}
