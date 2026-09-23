<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PcBuildController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuotationController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/desktops', [HomeController::class, 'desktops'])->name('store.desktops');
Route::get('/laptops', [HomeController::class, 'laptops'])->name('store.laptops');
Route::get('/accessories', [HomeController::class, 'accessories'])->name('store.accessories');
Route::get('/categories', [HomeController::class, 'categories'])->name('store.categories');
Route::get('/special-offers', [HomeController::class, 'specialOffers'])->name('store.special-offers');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::post('/dashboard/inventory', [InventoryController::class, 'store'])->name('inventory.store');
    Route::post('/dashboard/inventory/categories', [CategoryController::class, 'store'])->name('inventory.categories.store');
    Route::patch('/dashboard/inventory/categories/{category}', [CategoryController::class, 'update'])->name('inventory.categories.update');
    Route::post('/dashboard/inventory/update', [InventoryController::class, 'update'])->name('inventory.update');

    Route::get('/dashboard/pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('/dashboard/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');

    Route::get('/dashboard/quotations', [QuotationController::class, 'index'])->name('quotation.index');
    Route::post('/dashboard/quotations', [QuotationController::class, 'store'])->name('quotation.store');

    Route::get('/dashboard/build-pc', [PcBuildController::class, 'index'])->name('buildpc.index');
    Route::post('/dashboard/build-pc', [PcBuildController::class, 'store'])->name('buildpc.store');
    Route::get('/dashboard/build-pc/{pcBuild}', [PcBuildController::class, 'show'])->name('buildpc.show');
    Route::get('/dashboard/build-pc/{pcBuild}/print', [PcBuildController::class, 'print'])->name('buildpc.print');
    Route::post('/dashboard/build-pc/{pcBuild}/cancel', [PcBuildController::class, 'cancel'])->name('buildpc.cancel');
    Route::post('/dashboard/build-pc/{pcBuild}/sell', [PcBuildController::class, 'sell'])->name('buildpc.sell');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
