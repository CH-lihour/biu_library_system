<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

Route::get('/', fn () => auth()->check() ? redirect()->route('dashboard') : redirect()->route('login') );

Route::middleware('guest')->group(function(){
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('store');
});

Route::middleware('auth')->group(function(){
    Route::get('/logout', [LoginController::class, 'destroy'])->name('logout');
    
    Route::get('/dashboard', function(){
        return view('index');
    })->name('dashboard');
});
