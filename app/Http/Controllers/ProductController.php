<?php
namespace App\Http\Controllers;
use App\Actions\Product\Find;
use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class ProductController extends Controller
{
	/**
	 * Display the specified product within a category.
	 */
	public function show(Category $category, Product $product): View
	{
		// Only show published products
		if (!$product->published) {
			abort(404);
		}

		// Load product with all images
		$product = (new Find())->execute($product);

		return view('pages.product.show', compact('category', 'product'));
	}
}
