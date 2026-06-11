<?php

namespace Tests\Unit\Services;

use App\Enums\TableShape;
use App\Models\Edge;
use App\Models\Product;
use App\Models\Surface;
use App\Models\WoodType;
use App\Services\ConfigurablePriceCalculator;
use Tests\TestCase;

class ConfigurablePriceCalculatorTest extends TestCase
{
	private ConfigurablePriceCalculator $calculator;

	protected function setUp(): void
	{
		parent::setUp();
		$this->calculator = new ConfigurablePriceCalculator();
	}

	/**
	 * Baseline configurable product (unsaved — the calculator only reads
	 * attributes, so no persistence is required).
	 */
	private function product(array $overrides = []): Product
	{
		return Product::factory()->configurable()->make($overrides);
	}

	private function woodType(array $overrides = []): WoodType
	{
		return WoodType::factory()->make(array_merge([
			'price' => 1000,
			'sorting_factor' => 1,
		], $overrides));
	}

	private function surface(array $overrides = []): Surface
	{
		return Surface::factory()->make(array_merge([
			'price' => 50,
			'minimum_amount' => 0,
		], $overrides));
	}

	private function edge(array $overrides = []): Edge
	{
		return Edge::factory()->make(array_merge([
			'price' => 20,
		], $overrides));
	}

	/**
	 * Run the calculator with the baseline 200 × 100 cm rectangular table
	 * (area 2.0 m², perimeter 6.0 m) unless options are overridden.
	 */
	private function calculate(
		?Product $product = null,
		float $length = 200,
		?float $width = 100,
		?WoodType $woodType = null,
		?Surface $surface = null,
		?Edge $edge = null,
	): array {
		return $this->calculator->calculate(
			$product ?? $this->product(),
			$length,
			$width,
			$woodType ?? $this->woodType(),
			$surface ?? $this->surface(),
			$edge ?? $this->edge(),
		);
	}

	public function test_geometry_for_rectangular_table(): void
	{
		$breakdown = $this->calculate()['breakdown'];

		$this->assertEqualsWithDelta(2.0, $breakdown['area_m2'], 0.0001);
		$this->assertEqualsWithDelta(6.0, $breakdown['perimeter_m'], 0.0001);
	}

	public function test_geometry_for_round_table_uses_length_as_diameter(): void
	{
		$result = $this->calculate(
			product: $this->product(['shape' => TableShape::Round->value]),
			length: 100,
			width: null,
		);

		// Diameter 1.0 m → area π·0.25, circumference π·1.
		$this->assertEqualsWithDelta(M_PI * 0.25, $result['breakdown']['area_m2'], 0.0001);
		$this->assertEqualsWithDelta(M_PI, $result['breakdown']['perimeter_m'], 0.0001);
	}

	public function test_baseline_price_sums_all_components(): void
	{
		// base 100 + material 160 + surface_processing 20 + edge 120 + surface 100 = 500
		$this->assertEqualsWithDelta(500.0, $this->calculate()['price'], 0.0001);
	}

	public function test_material_cost_uses_wood_price_thickness_and_sorting_factor(): void
	{
		$breakdown = $this->calculate()['breakdown'];

		// 1000 × 2.0 m² × 0.040 m × 1.0 = 80 raw; × material_factor 2 = 160
		$this->assertEqualsWithDelta(80.0, $breakdown['material_raw'], 0.0001);
		$this->assertEqualsWithDelta(160.0, $breakdown['material'], 0.0001);
	}

	public function test_sorting_factor_scales_material_cost(): void
	{
		$breakdown = $this->calculate(woodType: $this->woodType(['sorting_factor' => 1.5]))['breakdown'];

		// 1000 × 2.0 × 0.040 × 1.5 = 120
		$this->assertEqualsWithDelta(120.0, $breakdown['material_raw'], 0.0001);
	}

	public function test_surface_processing_price_applied_per_area(): void
	{
		// area 2.0 m² × surface_processing_price 10 = 20
		$this->assertEqualsWithDelta(20.0, $this->calculate()['breakdown']['surface_processing'], 0.0001);
	}

	public function test_surface_cost_uses_area_times_price_when_above_minimum(): void
	{
		// area 2.0 × price 50 = 100, above minimum_amount 0
		$this->assertEqualsWithDelta(100.0, $this->calculate()['breakdown']['surface'], 0.0001);
	}

	public function test_surface_cost_floors_at_minimum_amount(): void
	{
		$surface = $this->surface(['price' => 50, 'minimum_amount' => 200]);

		// max(200, 2.0 × 50 = 100) = 200
		$this->assertEqualsWithDelta(200.0, $this->calculate(surface: $surface)['breakdown']['surface'], 0.0001);
	}

	public function test_edge_cost_applied_per_perimeter(): void
	{
		// perimeter 6.0 m × edge price 20 = 120
		$this->assertEqualsWithDelta(120.0, $this->calculate()['breakdown']['edge'], 0.0001);
	}

	public function test_large_format_surcharge_triggers_above_threshold(): void
	{
		$product = $this->product([
			'large_format_threshold' => 1.0,
			'large_format_surcharge' => 100,
		]);

		// (area 2.0 − 1.0) × 100 = 100
		$this->assertEqualsWithDelta(100.0, $this->calculate(product: $product)['breakdown']['large_format'], 0.0001);
	}

	public function test_no_large_format_surcharge_below_threshold(): void
	{
		$product = $this->product([
			'large_format_threshold' => 5.0,
			'large_format_surcharge' => 100,
		]);

		$this->assertEqualsWithDelta(0.0, $this->calculate(product: $product)['breakdown']['large_format'], 0.0001);
	}

	public function test_oversize_surcharge_triggers_above_threshold(): void
	{
		$product = $this->product([
			'oversize_threshold' => 1.5,
			'oversize_surcharge' => 200,
		]);

		// (area 2.0 − 1.5) × 200 = 100
		$this->assertEqualsWithDelta(100.0, $this->calculate(product: $product)['breakdown']['oversize'], 0.0001);
	}

	public function test_form_surcharge_applied_for_non_rectangular_shapes(): void
	{
		$product = $this->product([
			'shape' => TableShape::Round->value,
			'form_surcharge' => 250,
		]);

		$result = $this->calculate(product: $product, length: 120, width: null);

		$this->assertEqualsWithDelta(250.0, $result['breakdown']['form_surcharge'], 0.0001);
	}

	public function test_no_form_surcharge_for_rectangular_shape(): void
	{
		$product = $this->product(['form_surcharge' => 250]);

		$this->assertEqualsWithDelta(0.0, $this->calculate(product: $product)['breakdown']['form_surcharge'], 0.0001);
	}

	public function test_minimum_price_floor_is_enforced(): void
	{
		// Baseline raw total is 500; a minimum of 1000 must win.
		$product = $this->product(['minimum_price' => 1000]);

		$result = $this->calculate(product: $product);

		$this->assertEqualsWithDelta(500.0, $result['breakdown']['raw_total'], 0.0001);
		$this->assertEqualsWithDelta(1000.0, $result['price'], 0.0001);
	}

	public function test_price_is_rounded_to_configured_step(): void
	{
		// base 130 lifts raw to 530; rounding step 50 → 550 (round half up).
		$product = $this->product(['base_price' => 130]);

		$result = $this->calculate(product: $product);

		$this->assertEqualsWithDelta(530.0, $result['breakdown']['raw_total'], 0.0001);
		$this->assertEqualsWithDelta(550.0, $result['price'], 0.0001);
	}
}
