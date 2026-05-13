<?php

namespace App\Services;

use App\Enums\TableShape;
use App\Models\Edge;
use App\Models\Product;
use App\Models\Surface;
use App\Models\WoodType;

class ConfigurablePriceCalculator
{
    public function calculate(
        Product $product,
        float $lengthCm,
        ?float $widthCm,
        WoodType $woodType,
        Surface $surface,
        Edge $edge,
    ): array {
        $shape = $product->shape ?? TableShape::Rectangular;

        [$areaM2, $perimeterM] = $this->geometry($shape, $lengthCm, $widthCm);

        $rawThickness = (float) config('shop.configurable.raw_thickness_m');
        $roundingStep = (float) config('shop.configurable.price_rounding_step');

        $materialRaw = (float) $woodType->price
            * $areaM2
            * $rawThickness
            * (float) $woodType->sorting_factor;

        $surfaceCost = max(
            (float) $surface->minimum_amount,
            $areaM2 * (float) $surface->price,
        );

        $edgeCost = $perimeterM * (float) $edge->price;

        $largeFormatExcess = max(0.0, $areaM2 - (float) $product->large_format_threshold)
            * (float) $product->large_format_surcharge;

        $oversizeExcess = max(0.0, $areaM2 - (float) $product->oversize_threshold)
            * (float) $product->oversize_surcharge;

        $formSurcharge = $shape === TableShape::Rectangular
            ? 0.0
            : (float) $product->form_surcharge;

        $raw =
            (float) $product->base_price
            + $materialRaw * (float) $product->material_factor
            + $areaM2 * (float) $product->surface_processing_price
            + $edgeCost
            + $surfaceCost
            + $largeFormatExcess
            + $oversizeExcess
            + $formSurcharge;

        $price = $this->roundTo(max((float) $product->minimum_price, $raw), $roundingStep);

        return [
            'price' => $price,
            'breakdown' => [
                'area_m2' => $areaM2,
                'perimeter_m' => $perimeterM,
                'material_raw' => $materialRaw,
                'material' => $materialRaw * (float) $product->material_factor,
                'surface_processing' => $areaM2 * (float) $product->surface_processing_price,
                'edge' => $edgeCost,
                'surface' => $surfaceCost,
                'large_format' => $largeFormatExcess,
                'oversize' => $oversizeExcess,
                'form_surcharge' => $formSurcharge,
                'base_price' => (float) $product->base_price,
                'minimum_price' => (float) $product->minimum_price,
                'raw_total' => $raw,
            ],
        ];
    }

    /**
     * @return array{0: float, 1: float} [area_m², perimeter_m]
     */
    private function geometry(TableShape $shape, float $lengthCm, ?float $widthCm): array
    {
        $lengthM = $lengthCm / 100;
        $widthM = ($widthCm ?? 0) / 100;

        return match ($shape) {
            TableShape::Rectangular => [
                $lengthM * $widthM,
                2 * ($lengthM + $widthM),
            ],
            TableShape::Round => [
                M_PI * ($lengthM / 2) ** 2,
                M_PI * $lengthM,
            ],
            TableShape::Oval => $this->ovalGeometry($lengthM, $widthM),
        };
    }

    /**
     * @return array{0: float, 1: float}
     */
    private function ovalGeometry(float $lengthM, float $widthM): array
    {
        $a = $lengthM / 2;
        $b = $widthM / 2;
        $area = M_PI * $a * $b;
        $perimeter = M_PI * (3 * ($a + $b) - sqrt((3 * $a + $b) * ($a + 3 * $b)));

        return [$area, $perimeter];
    }

    private function roundTo(float $value, float $step): float
    {
        if ($step <= 0) {
            return round($value, 2);
        }

        return round($value / $step) * $step;
    }
}
