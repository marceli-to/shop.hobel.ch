<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
	return view('welcome');
});

// Redirect login to Filament admin login
Route::get('/login', function () {
	return redirect('/admin/login');
})->name('login');
