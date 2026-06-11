<?php

namespace App\Livewire\Product;

use Livewire\Component;
use App\Models\Product;
use App\Livewire\Product\Concerns\HandlesCart;

class VariationsProduct extends Component
{
    use HandlesCart;

    public Product $product;
    public string $selectedUuid;
    public string $selectedLabel;
    public float $selectedPrice;

    public function mount(Product $product): void
    {
        $this->product = $product;

        $cheapestChild = $product->children->sortBy('price')->first();
        $this->selectedUuid = $cheapestChild->uuid;
        $this->selectedLabel = $cheapestChild->label ?? '';
        $this->selectedPrice = (float) $cheapestChild->price;

        $this->productUuid = $cheapestChild->uuid;
    }

    public function selectVariation(string $uuid): void
    {
        $child = $this->product->children->firstWhere('uuid', $uuid);
        if (!$child) {
            return;
        }

        $this->selectedUuid = $child->uuid;
        $this->selectedLabel = $child->label ?? '';
        $this->selectedPrice = (float) $child->price;

        $this->productUuid = $child->uuid;
        $this->quantity = 1;
        $this->loadCartProduct();
        $this->syncWithCart();
    }

    public function render()
    {
        return view('livewire.product.variations-product');
    }
}
