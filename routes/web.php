<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\PdfController;

// Landing page
Route::view('/', 'pages.landing')->name('home');

// Product(s) page
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/product/{product:slug}', [ProductController::class, 'show'])->name('product.show');

// Cart page
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');

// Image manipulation
Route::get('/img/{path}', [ImageController::class, 'show'])->where('path', '.*');

// PDF generation
Route::get('/pdf/invoice/{order:uuid}', [PdfController::class, 'generateInvoice'])->name('pdf.invoice');