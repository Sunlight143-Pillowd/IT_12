<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PcBuildController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StockInController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\WarrantyController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/desktops', [HomeController::class, 'desktops'])->name('store.desktops');
Route::get('/laptops', [HomeController::class, 'laptops'])->name('store.laptops');
Route::get('/accessories', [HomeController::class, 'accessories'])->name('store.accessories');
Route::get('/categories', [HomeController::class, 'categories'])->name('store.categories');
Route::get('/categories/{categorySlug}', [HomeController::class, 'category'])->name('store.category');
Route::get('/special-offers', [HomeController::class, 'specialOffers'])->name('store.special-offers');
Route::get('/top-selling', [HomeController::class, 'topSelling'])->name('store.top-selling');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::get('/checkout', [CartController::class, 'checkout'])->name('checkout.index');
Route::post('/checkout', [CartController::class, 'placeOrder'])->name('checkout.place-order');
Route::post('/cart/order', [CartController::class, 'placeOrder'])->name('cart.order');
Route::post('/cart/{product}', [CartController::class, 'store'])->name('cart.items.store');
Route::put('/cart/{product}', [CartController::class, 'update'])->name('cart.items.update');
Route::delete('/cart/{product}', [CartController::class, 'destroy'])->name('cart.items.destroy');
Route::get('/build-pc', [PcBuildController::class, 'customerIndex'])->name('buildpc.customer');
Route::post('/build-pc', [PcBuildController::class, 'storeCustomerBuild'])->name('buildpc.customer.store');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/orders/{storeOrder}/accept', [DashboardController::class, 'acceptOrder'])->middleware('permission:sales.create')->name('dashboard.orders.accept');
    Route::post('/dashboard/orders/{storeOrder}/shipping-fee', [DashboardController::class, 'updateShippingFee'])->middleware('permission:sales.create')->name('dashboard.orders.shipping-fee');
    Route::patch('/dashboard/orders/{storeOrder}', [DashboardController::class, 'updateAcceptedOrder'])->middleware('permission:sales.create')->name('dashboard.orders.update');
    Route::patch('/dashboard/customer-builds/{pcBuild}', [PcBuildController::class, 'updateCustomerBuild'])->middleware('permission:builds.manage')->name('dashboard.customer-builds.update');
    Route::get('/dashboard/inventory', [InventoryController::class, 'index'])->middleware('permission:inventory.manage')->name('inventory.index');
    Route::post('/dashboard/inventory', [InventoryController::class, 'store'])->middleware('permission:inventory.manage')->name('inventory.store');
    Route::post('/dashboard/inventory/categories', [CategoryController::class, 'store'])->middleware('permission:inventory.manage')->name('inventory.categories.store');
    Route::patch('/dashboard/inventory/categories/{category}', [CategoryController::class, 'update'])->middleware('permission:inventory.manage')->name('inventory.categories.update');
    Route::post('/dashboard/inventory/categories/order', [CategoryController::class, 'reorder'])->middleware('permission:inventory.manage')->name('inventory.categories.reorder');
    Route::delete('/dashboard/inventory/categories/{category}', [CategoryController::class, 'destroy'])->middleware('permission:inventory.manage')->name('inventory.categories.destroy');
    Route::post('/dashboard/inventory/categories/{categorySlug}/image', [CategoryController::class, 'uploadImage'])->middleware('permission:inventory.manage')->name('inventory.categories.image');
    Route::post('/dashboard/homepage-images/{imageKey}', [CategoryController::class, 'uploadHomepageImage'])->middleware('permission:inventory.manage')->name('homepage-images.upload');
    Route::get('/dashboard/inventory/products/{product}/edit', [InventoryController::class, 'edit'])->middleware('permission:inventory.manage')->name('inventory.products.edit');
    Route::post('/dashboard/inventory/products/{product}/image', [InventoryController::class, 'uploadFeaturedImage'])->middleware('permission:inventory.manage')->name('inventory.products.image');
    Route::patch('/dashboard/inventory/products/{product}', [InventoryController::class, 'updateProduct'])->middleware('permission:inventory.manage')->name('inventory.products.update');

    Route::get('/dashboard/stock-in', [StockInController::class, 'index'])->middleware('permission:stock_in.manage')->name('stock-in.index');
    Route::post('/dashboard/stock-in', [StockInController::class, 'store'])->middleware('permission:stock_in.manage')->name('stock-in.store');
    Route::get('/dashboard/stock-in/{stockIn}/edit', [StockInController::class, 'edit'])->middleware('permission:stock_in.manage')->name('stock-in.edit');
    Route::patch('/dashboard/stock-in/{stockIn}', [StockInController::class, 'update'])->middleware('permission:stock_in.manage')->name('stock-in.update');

    Route::get('/dashboard/pos', [PosController::class, 'index'])->middleware('permission:sales.create')->name('pos.index');
    Route::get('/dashboard/pos/receipts/{sale}', [PosController::class, 'receipt'])->name('pos.receipt');
    Route::post('/dashboard/pos/checkout', [PosController::class, 'checkout'])->middleware('permission:sales.create')->name('pos.checkout');

    Route::get('/dashboard/quotations', [QuotationController::class, 'index'])->middleware('permission:quotations.manage')->name('quotation.index');
    Route::get('/dashboard/quotations/{quotation}', [QuotationController::class, 'show'])->middleware('permission:quotations.manage')->name('quotation.show');
    Route::post('/dashboard/quotations', [QuotationController::class, 'store'])->middleware('permission:quotations.manage')->name('quotation.store');

    Route::get('/dashboard/build-pc', [PcBuildController::class, 'index'])->middleware('permission:builds.manage')->name('buildpc.index');
    Route::post('/dashboard/build-pc', [PcBuildController::class, 'store'])->middleware('permission:builds.manage')->name('buildpc.store');
    Route::get('/dashboard/build-pc/{pcBuild}', [PcBuildController::class, 'show'])->name('buildpc.show');
    Route::get('/dashboard/build-pc/{pcBuild}/edit', [PcBuildController::class, 'edit'])->name('buildpc.edit');
    Route::patch('/dashboard/build-pc/{pcBuild}', [PcBuildController::class, 'update'])->middleware('permission:builds.manage')->name('buildpc.update');
    Route::get('/dashboard/build-pc/{pcBuild}/print', [PcBuildController::class, 'print'])->name('buildpc.print');
    Route::delete('/dashboard/build-pc/{pcBuild}', [PcBuildController::class, 'destroy'])->middleware('permission:builds.manage')->name('buildpc.destroy');
    Route::post('/dashboard/build-pc/{pcBuild}/cancel', [PcBuildController::class, 'cancel'])->middleware('permission:builds.manage')->name('buildpc.cancel');
    Route::post('/dashboard/build-pc/{pcBuild}/sell', [PcBuildController::class, 'sell'])->middleware('permission:builds.manage')->name('buildpc.sell');

    Route::get('/dashboard/customers', [CustomerController::class, 'index'])->middleware('permission:customers.manage')->name('customers.index');
    Route::post('/dashboard/customers', [CustomerController::class, 'store'])->middleware('permission:customers.manage')->name('customers.store');
    Route::patch('/dashboard/customers/{customer}', [CustomerController::class, 'update'])->middleware('permission:customers.manage')->name('customers.update');
    Route::post('/dashboard/customers/{customer}/credits', [CustomerController::class, 'storeCredit'])->middleware('permission:credit.manage')->name('customers.credits.store');
    Route::post('/dashboard/credits/{customerCredit}/payments', [CustomerController::class, 'recordPayment'])->middleware('permission:credit.manage')->name('credits.payments.store');
    Route::post('/dashboard/customers/{customer}/feedback', [CustomerController::class, 'storeFeedback'])->middleware('permission:customers.manage')->name('customers.feedback.store');

    Route::get('/dashboard/suppliers', [SupplierController::class, 'index'])->middleware('permission:suppliers.manage')->name('suppliers.index');
    Route::post('/dashboard/suppliers', [SupplierController::class, 'store'])->middleware('permission:suppliers.manage')->name('suppliers.store');
    Route::patch('/dashboard/suppliers/{supplier}', [SupplierController::class, 'update'])->middleware('permission:suppliers.manage')->name('suppliers.update');
    Route::delete('/dashboard/suppliers/{supplier}', [SupplierController::class, 'destroy'])->middleware('permission:suppliers.manage')->name('suppliers.destroy');

    Route::get('/dashboard/warranty', [WarrantyController::class, 'index'])->middleware('permission:warranty.verify')->name('warranty.index');
    Route::post('/dashboard/warranty/cases', [WarrantyController::class, 'storeCase'])->middleware('permission:warranty.verify')->name('warranty.cases.store');
    Route::patch('/dashboard/warranty/cases/{serviceCase}', [WarrantyController::class, 'updateCase'])->middleware('permission:transactions.manage')->name('warranty.cases.update');

    Route::get('/dashboard/employees', [EmployeeController::class, 'index'])->middleware('permission:employees.manage')->name('employees.index');
    Route::post('/dashboard/employees', [EmployeeController::class, 'store'])->middleware('permission:employees.manage')->name('employees.store');
    Route::patch('/dashboard/employees/{user}', [EmployeeController::class, 'update'])->middleware('permission:employees.manage')->name('employees.update');
    Route::get('/dashboard/roles', [RoleController::class, 'index'])->middleware('permission:employees.manage')->name('roles.index');
    Route::post('/dashboard/roles', [RoleController::class, 'store'])->middleware('permission:employees.manage')->name('roles.store');
    Route::patch('/dashboard/roles/{role}', [RoleController::class, 'update'])->middleware('permission:employees.manage')->name('roles.update');

    Route::get('/dashboard/expenses', [ExpenseController::class, 'index'])->middleware('permission:expenses.manage')->name('expenses.index');
    Route::post('/dashboard/expenses', [ExpenseController::class, 'store'])->middleware('permission:expenses.manage')->name('expenses.store');
    Route::get('/dashboard/reports', [ReportController::class, 'index'])->middleware('permission:reports.view')->name('reports.index');
});

Route::middleware('auth')->group(function () {
    Route::get('/build-pc/{pcBuild}/edit', [PcBuildController::class, 'customerEdit'])->name('buildpc.customer.edit');
    Route::patch('/build-pc/{pcBuild}', [PcBuildController::class, 'update'])->name('buildpc.customer.update');
    Route::delete('/build-pc/{pcBuild}', [PcBuildController::class, 'destroy'])->name('buildpc.customer.destroy');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
