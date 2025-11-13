<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
	protected $fillable = [
		'uuid',
		'customer_name',
		'customer_email',
		'customer_phone',
		'invoice_company',
		'invoice_street',
		'invoice_street_number',
		'invoice_zip',
		'invoice_city',
		'invoice_country',
		'use_invoice_address',
		'shipping_company',
		'shipping_street',
		'shipping_street_number',
		'shipping_zip',
		'shipping_city',
		'shipping_country',
		'total',
		'payment_method',
		'paid_at',
	];

	protected $casts = [
		'total' => 'decimal:2',
		'use_invoice_address' => 'boolean',
		'paid_at' => 'datetime',
	];

	protected $appends = [
		'order_number',
		'invoice_address',
		'shipping_address',
	];

	/**
	 * Get the order items for the order.
	 */
	public function items(): HasMany
	{
		return $this->hasMany(OrderItem::class);
	}

	/**
	 * Get the route key for the model.
	 */
	public function getRouteKeyName(): string
	{
		return 'uuid';
	}

	/**
	 * Get the order number attribute.
	 */
	public function getOrderNumberAttribute(): string
	{
		return 'HO-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
	}

	/**
	 * Get the formatted invoice address.
	 */
	public function getInvoiceAddressAttribute(): string
	{
		$address = $this->invoice_street . ' ' . $this->invoice_street_number . ', ';
		$address .= $this->invoice_zip . ' ' . $this->invoice_city;

		if ($this->invoice_company) {
			$address = $this->invoice_company . ', ' . $address;
		}

		return $address;
	}

	/**
	 * Get the formatted shipping address.
	 */
	public function getShippingAddressAttribute(): ?string
	{
		if ($this->use_invoice_address) {
			return $this->invoice_address;
		}

		$address = $this->shipping_street . ' ' . $this->shipping_street_number . ', ';
		$address .= $this->shipping_zip . ' ' . $this->shipping_city;

		if ($this->shipping_company) {
			$address = $this->shipping_company . ', ' . $address;
		}

		return $address;
	}

	/**
	 * Check if order is paid.
	 */
	public function isPaid(): bool
	{
		return !is_null($this->paid_at);
	}

	/**
	 * Create an order from cart data.
	 *
	 * @param array $orderData Order details (customer info, addresses, etc.)
	 * @param array $cartItems Cart items from session
	 * @return self
	 */
	public static function createFromCart(array $orderData, array $cartItems): self
	{
		$order = self::create($orderData);

		foreach ($cartItems as $item) {
			$order->items()->create([
				'product_name' => $item['name'],
				'product_description' => $item['description'] ?? null,
				'product_price' => $item['price'],
				'quantity' => $item['quantity'],
				'configuration' => $item['configuration'] ?? [],
			]);
		}

		return $order->load('items');
	}

	/**
	 * Boot the model.
	 */
	protected static function boot()
	{
		parent::boot();

		static::creating(function ($order) {
			if (empty($order->uuid)) {
				$order->uuid = (string) Str::uuid();
			}
		});
	}
}
