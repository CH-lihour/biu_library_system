<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BookCopyController;
use App\Http\Controllers\BorrowTransactionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MemberPlanController;
use App\Http\Controllers\PublisherController;
use Illuminate\Support\Facades\Route;

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

    Route::middleware('role:admin,librarian')->group(function(){
        // === Books ===
        Route::resource('books', BookController::class);

        // === Book Copies ===
        Route::resource('book-copies', BookCopyController::class);

        // === Authors ===
        Route::resource('authors', AuthorController::class);

        // === Publishers ===
        Route::resource('publishers', PublisherController::class);

        // === Categories ===
        Route::resource('categories', CategoryController::class);

        // === Members ===
        Route::resource('members', MemberController::class);

        // === Member Plans ===
        Route::resource('member-plans', MemberPlanController::class);
    });

    Route::middleware('role:admin,librarian,member')->group(function(){

        // === Borrow Transactions ===
        Route::get('/borrow-books', [BorrowTransactionController::class, 'index'])->name('borrows.index');
        Route::get('/borrow-books/create', [BorrowTransactionController::class, 'create'])->name('borrows.create');
        Route::post('/borrow-books', [BorrowTransactionController::class, 'store'])->name('borrows.store');
        Route::post('/borrow-books/get-book-by-barcode', [BorrowTransactionController::class, 'getBookByBarcode'])->name('borrows.getBookByBarcode');
        Route::post('/borrow-books/{borrow}/return', [BorrowTransactionController::class, 'return'])->name('borrows.return');
    });
});
