<?php

namespace App\Actions\Category;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class GetProducts
{
  /**
   * Get all published products for a category.
   *
   * @param Category $category
   * @return Collection
   */
  public function execute(Category $category): Collection
  {
    return $category->products()
      ->where('published', true)
      ->whereNull('parent_id') // Only parent products
      ->with(['previewImage', 'tags', 'children'])
      ->orderBy('name')
      ->get();
  }
}
