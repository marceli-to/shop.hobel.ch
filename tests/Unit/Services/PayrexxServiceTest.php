<?php

namespace Tests\Unit\Services;

use App\Services\PayrexxService;
use Mockery;
use Tests\TestCase;

/**
 * Exercises isPaymentSuccessful()'s parsing of a gateway payload. getGateway()
 * (the SDK/HTTP boundary) is mocked out via a partial mock, so no Payrexx
 * client is constructed and no network call is made.
 */
class PayrexxServiceTest extends TestCase
{
	private function serviceReturningGateway(?array $gateway): PayrexxService
	{
		$service = Mockery::mock(PayrexxService::class)->makePartial();
		$service->shouldReceive('getGateway')->andReturn($gateway);

		return $service;
	}

	public function test_payment_is_unsuccessful_when_gateway_cannot_be_fetched(): void
	{
		$service = $this->serviceReturningGateway(null);

		$this->assertFalse($service->isPaymentSuccessful(123));
	}

	public function test_payment_is_successful_when_an_invoice_is_confirmed(): void
	{
		$service = $this->serviceReturningGateway([
			'status' => 'waiting',
			'invoices' => [
				['status' => 'waiting'],
				['status' => 'confirmed'],
			],
		]);

		$this->assertTrue($service->isPaymentSuccessful(123));
	}

	public function test_payment_is_successful_when_the_gateway_status_is_confirmed(): void
	{
		$service = $this->serviceReturningGateway([
			'status' => 'confirmed',
			'invoices' => [],
		]);

		$this->assertTrue($service->isPaymentSuccessful(123));
	}

	public function test_payment_is_unsuccessful_when_neither_gateway_nor_invoices_are_confirmed(): void
	{
		$service = $this->serviceReturningGateway([
			'status' => 'waiting',
			'invoices' => [
				['status' => 'waiting'],
			],
		]);

		$this->assertFalse($service->isPaymentSuccessful(123));
	}

	public function test_payment_is_unsuccessful_when_there_are_no_invoices_and_status_is_pending(): void
	{
		$service = $this->serviceReturningGateway([
			'status' => 'waiting',
			'invoices' => [],
		]);

		$this->assertFalse($service->isPaymentSuccessful(123));
	}
}
