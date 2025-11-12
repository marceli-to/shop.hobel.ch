<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ImageController;

Route::get('/', function () {
	return view('pages.landing');
})->name('home');

// Products
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/product/{product:slug}', [ProductController::class, 'show'])->name('product.show');

// Cart
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');

// Image manipulation with Glide
Route::get('/img/{path}', [ImageController::class, 'show'])->where('path', '.*');

// Redirect login to Filament admin login
Route::get('/login', function () {
	return redirect('/admin/login');
})->name('login');
