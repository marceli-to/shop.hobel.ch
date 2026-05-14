<?php

namespace App\Livewire\Product;

use App\Actions\Cart\Get as GetCartAction;
use App\Actions\Cart\Update as UpdateCartAction;
use App\Enums\TableShape;
use App\Livewire\Product\Concerns\HandlesCart;
use App\Models\Edge;
use App\Models\Product;
use App\Models\Surface;
use App\Models\WoodType;
use App\Services\ConfigurablePriceCalculator;
use Livewire\Component;

class ConfigurableProduct extends Component
{
    use HandlesCart;

    public Product $product;

    public ?float $lengthCm = null;
    public ?float $widthCm = null;
    public ?int $woodTypeId = null;
    public ?int $surfaceId = null;
    public ?int $edgeId = null;

    public float $price = 0.0;

    public function mount(Product $product): void
    {
        $this->product = $product;
        $this->productUuid = $product->uuid;

        $this->lengthCm = $product->min_length ? (float) $product->min_length : null;
        $this->widthCm = $product->min_width ? (float) $product->min_width : null;
        $this->woodTypeId = ($product->woodTypes->firstWhere('pivot.is_default', true) ?? $product->woodTypes->first())?->id;
        $this->surfaceId = ($product->surfaces->firstWhere('pivot.is_default', true) ?? $product->surfaces->first())?->id;
        $this->edgeId = ($product->edges->firstWhere('pivot.is_default', true) ?? $product->edges->first())?->id;

        $this->initializeHandlesCart();
        $this->recalculatePrice();
    }

    public function updated(string $name): void
    {
        if (in_array($name, ['lengthCm', 'widthCm', 'woodTypeId', 'surfaceId', 'edgeId'], true)) {
            $this->recalculatePrice();
        }
    }

    public function recalculatePrice(): void
    {
        $woodType = $this->woodTypeId ? WoodType::find($this->woodTypeId) : null;
        $surface = $this->surfaceId ? Surface::find($this->surfaceId) : null;
        $edge = $this->edgeId ? Edge::find($this->edgeId) : null;

        if (! $woodType || ! $surface || ! $edge || ! $this->lengthCm) {
            $this->price = 0.0;
            return;
        }

        $result = (new ConfigurablePriceCalculator())->calculate(
            $this->product,
            (float) $this->lengthCm,
            $this->widthCm !== null ? (float) $this->widthCm : null,
            $woodType,
            $surface,
            $edge,
        );

        $this->price = $result['price'];
    }

    public function isRound(): bool
    {
        return $this->product->shape === TableShape::Round;
    }

    public function addToCart(): void
    {
        if (! $this->canAddConfiguration()) {
            return;
        }

        $woodType = WoodType::find($this->woodTypeId);
        $surface = Surface::find($this->surfaceId);
        $edge = Edge::find($this->edgeId);

        $cartKey = $this->buildCartKey();

        $cart = (new GetCartAction())->execute();
        $existing = collect($cart['items'])->firstWhere('cart_key', $cartKey);

        $shippingMethods = $this->product->shippingMethods->map(fn ($method) => [
            'id' => $method->id,
            'name' => $method->name,
            'price' => $method->pivot->price ?? $method->price,
            'is_shipping' => $method->is_shipping,
        ])->toArray();

        if ($existing) {
            $cart['items'] = collect($cart['items'])->map(function ($item) use ($cartKey) {
                if ($item['cart_key'] === $cartKey) {
                    $item['quantity'] = $item['quantity'] + $this->quantity;
                }
                return $item;
            })->toArray();
        } else {
            $cart['items'][] = [
                'cart_key' => $cartKey,
                'uuid' => $this->product->uuid,
                'name' => $this->product->name,
                'label' => null,
                'description' => $this->product->description,
                'price' => $this->price,
                'base_price' => $this->price,
                'quantity' => $this->quantity,
                'image' => $this->product->images->first()?->file_path,
                'configuration' => $this->buildConfigurationLabels($woodType, $surface, $edge),
                'shipping_methods' => $shippingMethods,
                'selected_shipping' => $shippingMethods[0]['id'] ?? null,
                'shipping_name' => $shippingMethods[0]['name'] ?? null,
                'shipping_price' => 0,
                'is_shipping' => $shippingMethods[0]['is_shipping'] ?? false,
            ];
        }

        $this->recalculateCart($cart);
        $this->dispatch('open-mini-cart');
    }

    public function canAddConfiguration(): bool
    {
        if (! $this->lengthCm || ! $this->woodTypeId || ! $this->surfaceId || ! $this->edgeId) {
            return false;
        }

        if (! $this->isRound() && ! $this->widthCm) {
            return false;
        }

        return $this->price > 0;
    }

    private function buildConfigurationLabels(?WoodType $woodType, ?Surface $surface, ?Edge $edge): array
    {
        $dimensions = $this->isRound()
            ? sprintf('Ø %s cm', $this->lengthCm)
            : sprintf('%s × %s cm', $this->lengthCm, $this->widthCm);

        return array_values(array_filter([
            $this->product->shape ? ['label' => $this->product->shape->label()] : null,
            ['label' => $dimensions],
            $woodType ? ['label' => $woodType->name] : null,
            $surface ? ['label' => $surface->name] : null,
            $edge ? ['label' => $edge->name] : null,
        ]));
    }

    private function buildCartKey(): string
    {
        return sprintf(
            '%s|L%s|W%s|wt%s|s%s|e%s',
            $this->product->uuid,
            $this->lengthCm,
            $this->isRound() ? '' : $this->widthCm,
            $this->woodTypeId,
            $this->surfaceId,
            $this->edgeId,
        );
    }

    public function render()
    {
        return view('livewire.product.configurable-product');
    }
}
