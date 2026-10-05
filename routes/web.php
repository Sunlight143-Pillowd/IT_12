<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PcBuildController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\StockInController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/desktops', [HomeController::class, 'desktops'])->name('store.desktops');
Route::get('/laptops', [HomeController::class, 'laptops'])->name('store.laptops');
Route::get('/accessories', [HomeController::class, 'accessories'])->name('store.accessories');
Route::get('/categories', [HomeController::class, 'categories'])->name('store.categories');
Route::get('/special-offers', [HomeController::class, 'specialOffers'])->name('store.special-offers');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/order', [CartController::class, 'placeOrder'])->name('cart.order');
Route::post('/cart/{product}', [CartController::class, 'store'])->name('cart.items.store');
Route::put('/cart/{product}', [CartController::class, 'update'])->name('cart.items.update');
Route::delete('/cart/{product}', [CartController::class, 'destroy'])->name('cart.items.destroy');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/orders/{storeOrder}/accept', [DashboardController::class, 'acceptOrder'])->name('dashboard.orders.accept');
    Route::get('/dashboard/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::post('/dashboard/inventory', [InventoryController::class, 'store'])->name('inventory.store');
    Route::post('/dashboard/inventory/categories', [CategoryController::class, 'store'])->name('inventory.categories.store');
    Route::patch('/dashboard/inventory/categories/{category}', [CategoryController::class, 'update'])->name('inventory.categories.update');
    Route::get('/dashboard/inventory/products/{product}/edit', [InventoryController::class, 'edit'])->name('inventory.products.edit');
    Route::patch('/dashboard/inventory/products/{product}', [InventoryController::class, 'updateProduct'])->name('inventory.products.update');

    Route::get('/dashboard/stock-in', [StockInController::class, 'index'])->name('stock-in.index');
    Route::post('/dashboard/stock-in', [StockInController::class, 'store'])->name('stock-in.store');

    Route::get('/dashboard/pos', [PosController::class, 'index'])->name('pos.index');
    Route::get('/dashboard/pos/receipts/{sale}', [PosController::class, 'receipt'])->name('pos.receipt');
    Route::post('/dashboard/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');

    Route::get('/dashboard/quotations', [QuotationController::class, 'index'])->name('quotation.index');
    Route::get('/dashboard/quotations/{quotation}', [QuotationController::class, 'show'])->name('quotation.show');
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
