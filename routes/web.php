<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ImageController;


use App\Http\Controllers\PdfController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;

// Landing page
Route::get('/', [LandingController::class, 'index'])->name('page.landing');

// Image manipulation
Route::get('/img/{path}', [ImageController::class, 'show'])->where('path', '.*');

// PDF generation (Test with: https://shop.hobel.ch.test/pdf/invoice/fca8c27b-c389-40d4-a6b1-38759877cbc3)
Route::get('/pdf/invoice/{order:uuid}', [PdfController::class, 'generateInvoice'])->name('pdf.invoice');

Route::get('/', [LandingController::class, 'index'])->name('page.landing');
Route::get('/{category}', [CategoryController::class, 'category'])->name('page.category');


Route::view('/tische', 'pages.tables')->name('page.tables');

/** Prototype */
// // Landing page
// Route::view('/', 'pages.landing')->name('home');
// // Product(s) page
// Route::get('/products', [ProductController::class, 'index'])->name('products.index');
// Route::get('/product/{product:slug}', [ProductController::class, 'show'])->name('product.show');
// // Cart page
// Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
/** // Prototype */

