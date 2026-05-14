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

        $firstChild = $product->children->first();
        $this->selectedUuid = $firstChild->uuid;
        $this->selectedLabel = $firstChild->label ?? '';
        $this->selectedPrice = (float) $firstChild->price;

        $this->productUuid = $firstChild->uuid;
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
