<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\PaymentController;

use App\Http\Controllers\PdfController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;

// Image manipulation
Route::get('/img/{path}', [ImageController::class, 'show'])->where('path', '.*');

// PDF generation (Test with: https://shop.hobel.ch.test/pdf/invoice/fca8c27b-c389-40d4-a6b1-38759877cbc3)
Route::get('/pdf/invoice/{order:uuid}', [PdfController::class, 'generateInvoice'])->name('pdf.invoice');

Route::get('/', [LandingController::class, 'index'])->name('page.landing');

Route::view('/bestellung/warenkorb', 'pages.checkout.basket')->name('page.checkout.basket');
Route::middleware(['ensure.cart.not.empty'])->group(function () {
  Route::view('/bestellung/rechnungsadresse', 'pages.checkout.invoice-address')->middleware('ensure.correct.order.step:1')->name('page.checkout.invoice-address');
  Route::view('/bestellung/lieferadresse', 'pages.checkout.delivery-address')->middleware('ensure.correct.order.step:2')->name('page.checkout.delivery-address');
  Route::view('/bestellung/zahlung', 'pages.checkout.payment')->middleware('ensure.correct.order.step:3')->name('page.checkout.payment');
  Route::view('/bestellung/zusammenfassung', 'pages.checkout.summary')->middleware('ensure.correct.order.step:4')->name('page.checkout.summary');
});
Route::view('/bestellung/bestaetigung', 'pages.checkout.confirmation')->middleware(['ensure.cart.not.empty', 'ensure.order.is.paid'])->name('page.checkout.confirmation');

// Checkout & Payment
Route::get('/checkout/summary', [PaymentController::class, 'summary'])->name('checkout.summary');
Route::post('/payment/initiate', [PaymentController::class, 'initiate'])->name('payment.initiate');
Route::get('/payment/success/{reference}', [PaymentController::class, 'success'])->name('payment.success');
Route::get('/payment/cancel/{reference}', [PaymentController::class, 'cancel'])->name('payment.cancel');
Route::post('/payment/webhook', [PaymentController::class, 'webhook'])->name('payment.webhook');

Route::get('/{category}', [CategoryController::class, 'get'])->name('page.category');
Route::get('/{category}/{product}', [ProductController::class, 'show'])->name('page.product')->scopeBindings();



/** Prototype */
// // Landing page
// Route::view('/', 'pages.landing')->name('home');
// // Product(s) page
// Route::get('/products', [ProductController::class, 'index'])->name('products.index');
// Route::get('/product/{product:slug}', [ProductController::class, 'show'])->name('product.show');
// // Cart page
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');


/** // Prototype */

