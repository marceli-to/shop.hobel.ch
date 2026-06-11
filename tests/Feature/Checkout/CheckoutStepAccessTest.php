<?php

namespace Tests\Feature\Checkout;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutStepAccessTest extends TestCase
{
	use RefreshDatabase;

	/**
	 * A non-empty cart at the given order step.
	 */
	private function seedCart(int $orderStep = 1): void
	{
		session()->put('cart', [
			'items' => [[
				'cart_key' => 'item-1',
				'uuid' => 'product-uuid',
				'name' => 'Test Product',
				'price' => 100.0,
				'quantity' => 1,
				'is_shipping' => true,
				'shipping_methods' => [],
				'configuration' => null,
			]],
			'quantity' => 1,
			'subtotal' => 100.0,
			'shipping' => 0.0,
			'tax' => 8.1,
			'total' => 108.1,
			'order_step' => $orderStep,
		]);
	}

	public function test_empty_cart_redirects_away_from_each_protected_step(): void
	{
		foreach ([
			'page.checkout.invoice-address',
			'page.checkout.delivery-address',
			'page.checkout.payment',
			'page.checkout.summary',
		] as $route) {
			$this->get(route($route))->assertRedirect(route('page.checkout.basket'));
		}
	}

	public function test_basket_is_reachable_with_empty_cart(): void
	{
		$this->get(route('page.checkout.basket'))->assertOk();
	}

	public function test_cannot_skip_to_payment_before_completing_earlier_steps(): void
	{
		$this->seedCart(orderStep: 1);

		// currentStep 1 < requiredStep 3 → bounced back to the basket.
		$this->get(route('page.checkout.payment'))
			->assertRedirect(route('page.checkout.basket'));
	}

	public function test_cannot_skip_to_summary_before_completing_earlier_steps(): void
	{
		$this->seedCart(orderStep: 1);

		$this->get(route('page.checkout.summary'))
			->assertRedirect(route('page.checkout.basket'));
	}

	public function test_summary_redirects_to_payment_when_only_one_step_short(): void
	{
		$this->seedCart(orderStep: 3);

		// currentStep 3 < requiredStep 4 → redirected to the payment step.
		$this->get(route('page.checkout.summary'))
			->assertRedirect(route('page.checkout.payment'));
	}

	public function test_step_is_reachable_once_its_required_step_is_met(): void
	{
		// order_step 1 meets the invoice-address requirement (step 1), so the
		// guard lets the request through and the page renders.
		$this->seedCart(orderStep: 1);

		$this->get(route('page.checkout.invoice-address'))->assertOk();
	}
}
