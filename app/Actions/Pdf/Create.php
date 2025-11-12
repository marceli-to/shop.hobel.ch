<?php

namespace App\Actions\Pdf;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPDF;

class Create
{
	/**
	 * Execute the action to generate an invoice PDF.
	 */
	public function execute(Order $order): DomPDF
	{
		$data = $this->prepareInvoiceData($order);

		return Pdf::loadView('pdf.invoice', compact('data'));
	}

	/**
	 * Map Order model to the expected data structure for the PDF template.
	 */
	private function prepareInvoiceData(Order $order): object
	{
		return (object) [
			'order_number' => $order->order_number,
			'invoice_name' => $order->customer_name,
			'invoice_address' => $order->invoice_street . ' ' . $order->invoice_street_number,
			'invoice_location' => $order->invoice_zip . ' ' . $order->invoice_city,
			'country' => $order->invoice_country,
			'salutation' => null,
			'shipping_full_name' => $order->customer_name,
			'shipping_company' => $order->shipping_company,
			'shipping_address' => $order->shipping_street . ' ' . $order->shipping_street_number,
			'shipping_location' => $order->shipping_zip . ' ' . $order->shipping_city,
			'shipping_country' => $order->shipping_country,
			'use_invoice_address' => $order->use_invoice_address,
			'payment_info' => $this->getPaymentInfo($order->payment_method),
			'total' => number_format($order->total, 2, '.', ''),
			'orderProducts' => $this->mapOrderItems($order),
		];
	}

	/**
	 * Map order items to the expected format for the PDF template.
	 */
	private function mapOrderItems(Order $order): array
	{
		return $order->items->map(function ($item) {
			return (object) [
				'title' => $item->product_name,
				'description' => $item->product_description ?? '',
				'quantity' => $item->quantity,
				'price' => $item->product_price,
				'shipping' => 0.00,
			];
		})->toArray();
	}

	/**
	 * Get readable payment info based on payment method.
	 */
	private function getPaymentInfo(string $paymentMethod): string
	{
		return match($paymentMethod) {
			'card' => 'Zahlung per Kreditkarte',
			'invoice' => 'Zahlung per Rechnung',
			'twint' => 'Zahlung per TWINT',
			default => 'Zahlung per ' . ucfirst($paymentMethod),
		};
	}
}
