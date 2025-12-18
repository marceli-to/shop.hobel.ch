<?php
namespace App\Http\Controllers;
use App\Actions\Category\GetProducts as GetProductsAction;
use App\Models\Category;
use Illuminate\View\View;

class CategoryController extends Controller
{
  /**
   * Display a category with its products.
   *
   * @param Category $category
   * @return View
   */
  public function get(Category $category): View
  {
    $products = (new GetProductsAction())->execute($category);
    
    // Get only tags that are actually used by products in this category
    $usedTagIds = $products->flatMap->tags->pluck('id')->unique();
    $tags = $category->tags()
      ->whereIn('id', $usedTagIds)
      ->orderBy('order')
      ->get();

    return view('pages.category.index', [
      'category' => $category,
      'products' => $products,
      'tags' => $tags,
    ]);
  }
}
