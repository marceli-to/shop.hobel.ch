Route::get('/bestellung/uebersicht', [OrderController::class, 'index'])->name('order.overview');
Route::middleware(['ensure.cart.not.empty'])->group(function () {
  Route::get('/bestellung/rechnungsadresse', [OrderController::class, 'invoice'])->name('order.invoice-address')->middleware('ensure.correct.order.step:1');
  Route::post('/bestellung/rechnungsadresse/speichern', [OrderController::class, 'storeInvoice'])->name('order.invoice-address-store')->middleware('ensure.correct.order.step:1');
  Route::get('/bestellung/lieferadresse', [OrderController::class, 'shipping'])->name('order.shipping-address')->middleware('ensure.correct.order.step:2');
  Route::post('/bestellung/lieferadresse/speichern', [OrderController::class, 'storeShipping'])->name('order.shipping-address-store')->middleware('ensure.correct.order.step:2');
  Route::get('/bestellung/zahlung', [OrderController::class, 'payment'])->name('order.payment')->middleware('ensure.correct.order.step:3');
  Route::post('/bestellung/zahlungsmethode/speichern', [OrderController::class, 'storePaymentMethod'])->name('order.payment-method-store')->middleware('ensure.correct.order.step:3');
  Route::get('/bestellung/zusammenfassung', [OrderController::class, 'summary'])->name('order.summary')->middleware('ensure.correct.order.step:4');
  Route::post('/bestellung/abschliessen', [OrderController::class, 'finalize'])->name('order.finalize')->middleware('ensure.correct.order.step:5');
  Route::get('/bestellung/zahlung-erfolgreich', [OrderController::class, 'paymentSuccess'])->name('order.payment.success')->middleware('ensure.correct.order.step:5');
  Route::get('/bestellung/zahlung-storniert', [OrderController::class, 'paymentCancel'])->name('order.payment.cancel')->middleware('ensure.correct.order.step:5');
});