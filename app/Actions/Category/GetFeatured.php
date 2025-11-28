<?php

namespace App\Actions\Category;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class GetFeatured
{
  /**
   * Get all featured categories that have published products.
   *
   * @return Collection
   */
  public function execute(): Collection
  {
    return Category::featured()
      ->withPublishedProducts()
      ->with('image')
      ->orderBy('order')
      ->get();
  }
}
