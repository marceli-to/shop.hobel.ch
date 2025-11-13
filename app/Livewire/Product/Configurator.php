<?php

namespace App\Livewire\Product;

use App\Actions\Cart\Get as GetCartAction;
use App\Actions\Cart\Update as UpdateCartAction;
use App\Models\Product;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class Configurator extends Component
{
	public string $productUuid;
	public array $configuration = [];
	public int $quantity = 1;
	public ?int $maxStock = null;
	public bool $inCart = false;

	public function mount(string $productUuid): void
	{
		$this->productUuid = $productUuid;
		$this->loadProduct();
		$this->syncWithCart();
	}

	#[On('cart-updated')]
	public function syncWithCart(): void
	{
		$cart = (new GetCartAction())->execute();
		$cartKey = $this->getCartItemKey();
		$item = collect($cart['items'])->firstWhere('cart_key', $cartKey);

		if ($item) {
			$this->quantity = $item['quantity'];
			$this->configuration = $item['configuration'] ?? [];
			$this->inCart = true;
		} else {
			$this->quantity = 1;
			$this->inCart = false;
		}
	}

	private function loadProduct(): void
	{
		$product = Product::where('uuid', $this->productUuid)->first();
		if ($product) {
			$this->maxStock = $product->stock;
		}
	}

	#[Computed]
	public function product(): ?Product
	{
		return Product::where('uuid', $this->productUuid)->first();
	}

	#[Computed]
	public function configuredPrice(): float
	{
		if (!$this->product || !$this->product->isConfigurable()) {
			return $this->product?->price ?? 0;
		}

		return $this->product->calculateConfiguredPrice($this->configuration);
	}

	#[Computed]
	public function isConfigurationValid(): bool
	{
		if (!$this->product || !$this->product->isConfigurable()) {
			return true;
		}

		return $this->product->isValidConfiguration($this->configuration);
	}

	public function selectOption(string $attributeKey, string $optionValue): void
	{
		$product = $this->product;
		if (!$product || !$product->isConfigurable()) {
			return;
		}

		// Find the attribute and option
		foreach ($product->getConfigurationAttributes() as $attribute) {
			if ($attribute['key'] === $attributeKey) {
				foreach ($attribute['options'] as $option) {
					if ($option['value'] === $optionValue) {
						$this->configuration[$attributeKey] = [
							'option' => $option['value'],
							'label' => $option['label'],
							'price' => $option['price_modifier'] ?? 0,
						];
						break 2;
					}
				}
			}
		}

		// If item is already in cart, update it
		if ($this->inCart) {
			$this->updateCart();
		}
	}

	public function increment(): void
	{
		if ($this->maxStock && $this->quantity < $this->maxStock) {
			$this->quantity++;
			if ($this->inCart) {
				$this->updateCart();
			}
		}
	}

	public function decrement(): void
	{
		if ($this->quantity > 1) {
			$this->quantity--;
			if ($this->inCart) {
				$this->updateCart();
			}
		}
	}

	public function addToCart(): void
	{
		$product = $this->product;

		if (!$product || $product->stock < 1) {
			return;
		}

		if (!$this->isConfigurationValid) {
			session()->flash('error', 'Bitte wählen Sie alle erforderlichen Optionen aus.');
			return;
		}

		$cart = (new GetCartAction())->execute();
		$cartItems = collect($cart['items']);
		$cartKey = $this->getCartItemKey();
		$existingItem = $cartItems->firstWhere('cart_key', $cartKey);

		if ($existingItem) {
			// Update quantity
			$cart['items'] = $cartItems->map(function ($item) use ($cartKey) {
				if ($item['cart_key'] === $cartKey) {
					$item['quantity'] = min($this->quantity, $this->maxStock);
				}
				return $item;
			})->toArray();
		} else {
			// Add new item
			$cart['items'][] = [
				'cart_key' => $cartKey,
				'uuid' => $product->uuid,
				'name' => $product->name,
				'description' => $product->description,
				'price' => $this->configuredPrice,
				'base_price' => $product->price,
				'quantity' => min($this->quantity, $product->stock),
				'image' => $product->image,
				'configuration' => $this->configuration,
			];
		}

		$this->updateTotal($cart);
		$this->dispatch('open-mini-cart');
	}

	private function updateCart(): void
	{
		if (!$this->inCart) {
			return;
		}

		$product = $this->product;
		if (!$product) {
			return;
		}

		$cart = (new GetCartAction())->execute();
		$cartKey = $this->getCartItemKey();

		$cart['items'] = collect($cart['items'])->map(function ($item) use ($product, $cartKey) {
			if ($item['cart_key'] === $cartKey) {
				$item['quantity'] = min($this->quantity, $product->stock);
				$item['configuration'] = $this->configuration;
				$item['price'] = $this->configuredPrice;
			}
			return $item;
		})->toArray();

		$this->updateTotal($cart);
	}

	private function updateTotal(array $cart): void
	{
		$cart['total'] = collect($cart['items'])->sum(function ($item) {
			return $item['price'] * $item['quantity'];
		});

		$cart['quantity'] = collect($cart['items'])->sum('quantity');

		(new UpdateCartAction())->execute($cart);
		$this->dispatch('cart-updated');
	}

	/**
	 * Generate a unique cart key for this product + configuration combo.
	 */
	private function getCartItemKey(): string
	{
		if (empty($this->configuration)) {
			return $this->productUuid;
		}

		$configHash = md5(json_encode($this->configuration));
		return $this->productUuid . '_' . $configHash;
	}

	public function render()
	{
		return view('livewire.product.configurator');
	}
}
