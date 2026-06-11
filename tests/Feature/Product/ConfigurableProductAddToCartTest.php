<?php

namespace Tests\Feature\Product;

use App\Livewire\Product\ConfigurableProduct;
use App\Models\Edge;
use App\Models\Product;
use App\Models\ShippingMethod;
use App\Models\Surface;
use App\Models\WoodType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ConfigurableProductAddToCartTest extends TestCase
{
	use RefreshDatabase;

	private WoodType $woodType;
	private Surface $surface;
	private Edge $edge;
	private ShippingMethod $shippingMethod;

	/**
	 * A 200 × 100 cm rectangular configurable table with default wood, surface
	 * and edge options attached — identical inputs to the calculator's baseline,
	 * so the expected price is 500.
	 */
	private function makeProduct(): Product
	{
		$product = Product::factory()->configurable()->create([
			'min_length' => 200,
			'min_width' => 100,
		]);

		$this->woodType = WoodType::factory()->create(['price' => 1000, 'sorting_factor' => 1]);
		$this->surface = Surface::factory()->create(['price' => 50, 'minimum_amount' => 0]);
		$this->edge = Edge::factory()->create(['price' => 20]);
		$this->shippingMethod = ShippingMethod::factory()->create(['is_shipping' => true]);

		$product->woodTypes()->attach($this->woodType->id, ['is_default' => true]);
		$product->surfaces()->attach($this->surface->id, ['is_default' => true]);
		$product->edges()->attach($this->edge->id, ['is_default' => true]);
		$product->shippingMethods()->attach($this->shippingMethod->id, ['price' => 0]);

		return $product->refresh();
	}

	public function test_mount_preselects_defaults_and_computes_the_price(): void
	{
		$product = $this->makeProduct();

		Livewire::test(ConfigurableProduct::class, ['product' => $product])
			->assertSet('length', 200.0)
			->assertSet('width', 100.0)
			->assertSet('woodTypeId', $this->woodType->id)
			->assertSet('surfaceId', $this->surface->id)
			->assertSet('edgeId', $this->edge->id)
			->assertSet('price', 500.0);
	}

	public function test_add_to_cart_stores_a_configured_item_in_the_session(): void
	{
		$product = $this->makeProduct();

		Livewire::test(ConfigurableProduct::class, ['product' => $product])
			->call('addToCart')
			->assertDispatched('open-mini-cart');

		$cart = session('cart');
		$this->assertCount(1, $cart['items']);

		$item = $cart['items'][0];
		$this->assertSame($product->uuid, $item['uuid']);
		$this->assertEqualsWithDelta(500.0, $item['price'], 0.0001);
		$this->assertSame(1, $item['quantity']);
		$this->assertNull($item['label']);
		$this->assertTrue($item['is_shipping']);
		$this->assertSame($this->shippingMethod->id, $item['selected_shipping']);
	}

	public function test_add_to_cart_builds_the_configuration_summary_and_key(): void
	{
		$product = $this->makeProduct();

		Livewire::test(ConfigurableProduct::class, ['product' => $product])
			->call('addToCart');

		$item = session('cart')['items'][0];

		// Summary: "{wood}, 200 × 100 cm, {surface}, {edge}"
		$this->assertStringContainsString('200 × 100 cm', $item['configuration']);
		$this->assertStringContainsString($this->woodType->name, $item['configuration']);
		$this->assertStringContainsString($this->surface->name, $item['configuration']);
		$this->assertStringContainsString($this->edge->name, $item['configuration']);

		$expectedKey = sprintf(
			'%s|L200|W100|wt%s|s%s|e%s',
			$product->uuid,
			$this->woodType->id,
			$this->surface->id,
			$this->edge->id,
		);
		$this->assertSame($expectedKey, $item['cart_key']);
	}

	public function test_adding_the_same_configuration_twice_merges_quantity(): void
	{
		$product = $this->makeProduct();

		Livewire::test(ConfigurableProduct::class, ['product' => $product])
			->call('addToCart')
			->call('addToCart');

		$cart = session('cart');
		$this->assertCount(1, $cart['items']);
		$this->assertSame(2, $cart['items'][0]['quantity']);
	}
}
