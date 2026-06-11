<?php

namespace Tests\Feature\Payment;

use App\Http\Middleware\VerifyCsrfToken;
use App\Jobs\ProcessOrderJob;
use App\Models\Order;
use App\Services\PayrexxService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Tests\TestCase;

class PaymentControllerTest extends TestCase
{
	use RefreshDatabase;

	private function invoiceAddress(): array
	{
		return [
			'salutation' => 'Frau',
			'firstname' => 'Anna',
			'lastname' => 'Müller',
			'street' => 'Bahnhofstrasse',
			'street_number' => '12',
			'zip' => '8001',
			'city' => 'Zürich',
			'country' => 'CH',
			'email' => 'anna@example.ch',
			'phone' => '+41 44 000 00 00',
		];
	}

	/**
	 * Seed a complete post-checkout session: a credit-card cart at step 4 with
	 * the payment reference and gateway id the success/cancel handlers read.
	 */
	private function seedCheckoutSession(string $reference = 'HOBEL-TEST', int $gatewayId = 999): void
	{
		session()->put('cart', [
			'items' => [[
				'cart_key' => 'a',
				'uuid' => 'uuid-a',
				'name' => 'Tisch',
				'label' => null,
				'price' => 500.0,
				'base_price' => 500.0,
				'quantity' => 1,
				'image' => null,
				'configuration' => 'Eiche, 200 × 100 cm',
				'shipping_methods' => [],
				'selected_shipping' => null,
				'shipping_name' => 'Standard',
				'shipping_price' => 0,
				'is_shipping' => true,
			]],
			'quantity' => 1,
			'subtotal' => 500.0,
			'shipping' => 0.0,
			'tax' => 40.5,
			'total' => 540.5,
			'order_step' => 4,
		]);
		session()->put('invoice_address', $this->invoiceAddress());
		session()->put('delivery_address', []);
		session()->put('payment_reference', $reference);
		session()->put('payment_gateway_id', $gatewayId);
	}

	private function mockPayrexx(int $gatewayId, bool $successful): void
	{
		$mock = Mockery::mock(PayrexxService::class);
		$mock->shouldReceive('isPaymentSuccessful')->with($gatewayId)->andReturn($successful);
		$this->app->instance(PayrexxService::class, $mock);
	}

	public function test_successful_payment_creates_order_clears_cart_and_redirects(): void
	{
		Queue::fake();
		$this->seedCheckoutSession('HOBEL-OK', 999);
		$this->mockPayrexx(999, successful: true);

		$this->get(route('payment.success', ['reference' => 'HOBEL-OK']))
			->assertRedirect(route('page.checkout.confirmation'));

		$this->assertSame(1, Order::count());
		$order = Order::first();
		$this->assertSame('creditcard', $order->payment_method);
		$this->assertSame('HOBEL-OK', $order->payment_reference);
		$this->assertTrue($order->isPaid());

		// Cart and payment session keys are cleared; the order id is kept.
		$this->assertNull(session('cart'));
		$this->assertNull(session('payment_reference'));
		$this->assertNull(session('payment_gateway_id'));
		$this->assertSame($order->id, session('completed_order_id'));

		Queue::assertPushed(ProcessOrderJob::class);
	}

	public function test_mismatched_reference_redirects_to_basket_without_creating_order(): void
	{
		$this->seedCheckoutSession('HOBEL-AAA', 1);

		$this->get(route('payment.success', ['reference' => 'HOBEL-BBB']))
			->assertRedirect(route('page.checkout.basket'))
			->assertSessionHas('error');

		$this->assertSame(0, Order::count());
	}

	public function test_pending_payment_shows_pending_page_and_creates_no_order(): void
	{
		$this->seedCheckoutSession('HOBEL-PEND', 555);
		$this->mockPayrexx(555, successful: false);

		$this->get(route('payment.success', ['reference' => 'HOBEL-PEND']))
			->assertOk()
			->assertViewIs('pages.checkout.pending')
			->assertViewHas('reference', 'HOBEL-PEND');

		$this->assertSame(0, Order::count());
		// Cart is untouched so the customer can retry.
		$this->assertNotNull(session('cart'));
	}

	public function test_cancel_clears_payment_session_keeps_cart_and_redirects_to_summary(): void
	{
		$this->seedCheckoutSession('HOBEL-X', 42);

		$this->get(route('payment.cancel', ['reference' => 'HOBEL-X']))
			->assertRedirect(route('page.checkout.summary'))
			->assertSessionHas('error');

		$this->assertNull(session('payment_reference'));
		$this->assertNull(session('payment_gateway_id'));
		$this->assertNotNull(session('cart'));
	}

	public function test_webhook_accepts_a_transaction_payload(): void
	{
		$this->withoutMiddleware(VerifyCsrfToken::class);

		$this->postJson(route('payment.webhook'), [
			'transaction' => ['id' => 1, 'status' => 'confirmed'],
		])
			->assertOk()
			->assertJson(['success' => true]);
	}

	public function test_webhook_rejects_an_empty_payload(): void
	{
		$this->withoutMiddleware(VerifyCsrfToken::class);

		$this->postJson(route('payment.webhook'), [])
			->assertStatus(400)
			->assertJson(['error' => 'No transaction data']);
	}
}
