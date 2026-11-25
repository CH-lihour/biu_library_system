<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BookCopyController;
use App\Http\Controllers\BorrowTransactionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\MemberApprovalController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MemberPlanController;
use App\Http\Controllers\Portal\PortalController;
use App\Http\Controllers\PublisherController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Services\AuthService;
use Illuminate\Support\Facades\Route;

Route::get('/', fn (AuthService $auth) => auth()->check() ? redirect($auth->home(auth()->user())) : redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('store');

    Route::get('/forgot-password', [ForgotPasswordController::class, 'index'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'store'])->middleware('throttle:6,1')->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'index'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'store'])->middleware('throttle:6,1')->name('password.update');

    // === Member self-registration ===
    Route::get('/register', [RegisterController::class, 'index'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->middleware('throttle:6,1')->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    // === Admin panel ===
    // Every named route in this group is an action that permissions can open.
    Route::middleware('admin.access')->group(function () {
        // === Dashboard ===
        Route::get('/dashboard', function () {
            return view('index');
        })->name('dashboard');

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
        Route::get('/members/{member}/approval', [MemberApprovalController::class, 'show'])->name('members.approval');
        Route::post('/members/{member}/approval', [MemberApprovalController::class, 'approve'])->name('members.approve');
        Route::post('/members/{member}/reject', [MemberApprovalController::class, 'reject'])->name('members.reject');

        // === Member Plans ===
        Route::resource('member-plans', MemberPlanController::class);

        // === Borrow Transactions ===
        Route::get('/borrow-books', [BorrowTransactionController::class, 'index'])->name('borrows.index');
        Route::get('/borrow-books/create', [BorrowTransactionController::class, 'create'])->name('borrows.create');
        Route::post('/borrow-books', [BorrowTransactionController::class, 'store'])->name('borrows.store');
        Route::post('/borrow-books/get-book-by-barcode', [BorrowTransactionController::class, 'getBookByBarcode'])->name('borrows.getBookByBarcode');
        Route::post('/borrow-books/{borrow}/return', [BorrowTransactionController::class, 'return'])->name('borrows.return');

        // === Reservations ===
        Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
        Route::post('/reservations/{reservation}/ready', [ReservationController::class, 'ready'])->name('reservations.ready');
        Route::post('/reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('reservations.cancel');

        // === Administration ===
        Route::resource('users', UserController::class)->except('show');
        Route::resource('roles', RoleController::class)->except('show');
    });

    // === Member front-end ===
    Route::middleware('member')->prefix('my')->name('portal.')->controller(PortalController::class)->group(function () {
        Route::get('/', 'home')->name('home');
        Route::get('/catalog', 'catalog')->name('catalog');
        Route::get('/catalog/{book}', 'book')->whereNumber('book')->name('book');
        Route::post('/catalog/{book}/reserve', 'reserve')->whereNumber('book')->name('reserve');
        Route::get('/loans', 'loans')->name('loans');
        Route::get('/reservations', 'reservations')->name('reservations');
        Route::post('/reservations/{reservation}/cancel', 'cancelReservation')->whereNumber('reservation')->name('reservations.cancel');
        Route::get('/profile', 'profile')->name('profile');
        Route::put('/profile', 'updateProfile')->name('profile.update');
    });
});
