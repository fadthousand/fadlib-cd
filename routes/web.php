<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EpubDownloadController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $request) {
    $user = $request->user();

    if ($user === null) {
        return redirect()->route('login');
    }

    return $user->isStaffOrAdmin()
        ? redirect()->route('dashboard')
        : redirect()->route('books.index');
});

Route::middleware('auth')->group(function (): void {
    // Admin & Staff Operational Routes
    Route::middleware('role:admin,staff')->group(function (): void {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Book Management (Create, Edit, Delete)
        Route::get('/books/create', [BookController::class, 'create'])->name('books.create');
        Route::post('/books', [BookController::class, 'store'])->name('books.store');
        Route::get('/books/{book}/edit', [BookController::class, 'edit'])->name('books.edit');
        Route::put('/books/{book}', [BookController::class, 'update'])->name('books.update');
        Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('books.destroy');

        // Member Management (Full CRUD + Detail)
        Route::resource('members', MemberController::class);

        // Borrowing Management (Back-Office Multi-Book Circulation & Return)
        Route::get('/borrowings', [BorrowingController::class, 'index'])->name('borrowings.index');
        Route::get('/borrowings/create', [BorrowingController::class, 'create'])->name('borrowings.create');
        Route::post('/borrowings', [BorrowingController::class, 'store'])->name('borrowings.store');
        Route::get('/borrowings/{borrowing}', [BorrowingController::class, 'show'])->name('borrowings.show');
        Route::patch('/borrowings/{borrowing}/return', [BorrowingController::class, 'markReturned'])->name('borrowings.return');
    });

    // Admin-Only Routes (User Management & Category Write Operations)
    Route::middleware('role:admin')->group(function (): void {
        Route::resource('users', UserController::class)->except(['show']);

        Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    });

    // Shared Authenticated Routes (Admin, Staff, & Member)
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/books', [BookController::class, 'index'])->name('books.index');
    Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');
    Route::get('/books/{book}/download', [EpubDownloadController::class, 'download'])->name('books.download');

    // Member Self-Service Shelf
    Route::middleware('role:member')->group(function (): void {
        Route::get('/my-shelf', [BorrowingController::class, 'myShelf'])->name('shelf.index');
        Route::post('/books/{book}/checkout', [BorrowingController::class, 'memberCheckout'])->name('books.checkout');
        Route::patch('/my-shelf/{borrowing}/return', [BorrowingController::class, 'memberReturn'])->name('shelf.return');
    });

    // Breeze Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
