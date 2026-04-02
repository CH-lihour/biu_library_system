<?php

use App\Http\Controllers\BookController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

Route::get('/', fn () => auth()->check() ? redirect()->route('dashboard') : redirect()->route('login') );

Route::middleware('guest')->group(function(){
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('store');
});

Route::middleware('auth')->group(function(){
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    // === Dashboard ===
    Route::get('/dashboard', function(){
        return view('index');
    })->name('dashboard');

    // === Books ===
    Route::middleware('role:admin,librarian')->group(function(){
        Route::resource('books', BookController::class);
    });

});
