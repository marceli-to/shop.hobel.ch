<?php

namespace App\Livewire\Product;

use Livewire\Component;
use App\Models\Product;
use App\Livewire\Product\Concerns\HandlesCart;

class SimpleProduct extends Component
{
    use HandlesCart;

    public Product $product;

    public function mount(Product $product): void
    {
        $this->product = $product;
        $this->productUuid = $product->uuid;
        $this->initializeHandlesCart();
    }

    public function render()
    {
        return view('livewire.product.simple-product');
    }
}
