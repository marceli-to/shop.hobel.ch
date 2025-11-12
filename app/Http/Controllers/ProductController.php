<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;

class ProductController extends Controller
{
	/**
	 * Display a listing of published products.
	 */
	public function index(): View
	{
		$products = Product::whereNotNull('published_at')
			->where('published_at', '<=', now())
			->orderBy('created_at', 'desc')
			->paginate(12);

		return view('pages.products.index', compact('products'));
	}

	/**
	 * Display the specified product.
	 */
	public function show(Product $product): View
	{
		// Only show published products
		if (!$product->published_at || $product->published_at > now()) {
			abort(404);
		}

		return view('pages.products.show', compact('product'));
	}
}
