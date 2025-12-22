<?php

namespace App\Actions\Product;

use App\Models\Product;

class Find
{
  /**
   * Find a product with all images.
   *
   * @param Product $product
   * @return Product
   */
  public function execute(Product $product): Product
  {
    return $product->load(['images', 'children', 'attributes']);
  }
}
