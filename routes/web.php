<?php

use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SalesReportController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CategoryController;

// Halaman Utama
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Autentikasi & Profil
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/photo', [ProfileController::class, 'removeProfilePhoto'])->name('profile.photo.remove');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

// Produk
Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('products.index');
    Route::get('/{product}', [ProductController::class, 'show'])->name('products.show');
    
    // Review Produk
    Route::middleware('auth')->group(function () {
        Route::post('/{product}/review', [ProductController::class, 'addReview'])
            ->name('products.review');
    });
});

// Area Admin (Dengan Middleware Role)
Route::middleware(['auth', 'roles:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Manajemen Pengguna
    Route::resource('users', UserManagementController::class);
    
    // Manajemen Produk
    Route::resource('products', AdminProductController::class);
    
    // Manajemen Kategori
    Route::resource('categories', CategoryController::class);
});
Route::middleware(['auth', 'roles:restaurant'])->prefix('admin')->name('admin.')->group(function () {
    // Manajemen Produk
    Route::resource('products', AdminProductController::class);

});

// Keranjang
Route::middleware('auth')->prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/{product:id}', [CartController::class, 'add'])->name('add');
    Route::put('/{cart}', [CartController::class, 'update'])->name('update');
    Route::delete('/{cart}', [CartController::class, 'destroy'])->name('destroy');
});

// Pesanan
Route::middleware('auth')->prefix('orders')->name('orders.')->group(function () {
    Route::get('/', [OrderController::class, 'index'])->name('index');
    Route::post('/', [OrderController::class, 'store'])->name('store');
    
    Route::prefix('/{order}')->group(function () {
        Route::get('/', [OrderController::class, 'show'])->name('show');
        
        // Pembayaran
        Route::get('/payment-confirmation', [OrderController::class, 'paymentConfirmation'])->name('payment-confirmation');
        Route::post('/confirm-payment', [OrderController::class, 'confirmPayment'])->name('confirm-payment');
        Route::post('/confirm-cod-payment', [OrderController::class, 'confirmCodPayment'])->name('confirm-cod-payment');
        
        // Status Pesanan
        Route::post('/confirm-delivery', [OrderController::class, 'confirmDelivery'])->name('confirm-delivery');
        Route::post('/update-status', [OrderController::class, 'updateOrderStatus'])->name('update-status');
        
        // Invoice
        Route::get('/invoice', [OrderController::class, 'downloadInvoice'])->name('invoice');
        
        // Tracking
        Route::get('/tracking', [OrderController::class, 'tracking'])->name('tracking');
    });
});
Route::middleware(['auth', 'restaurant'])->prefix('restaurant/orders')->name('restaurant.orders.')->group(function () {
    Route::get('/', [OrderController::class, 'restaurantOrderIndex'])
        ->name('index');

    Route::get('/{order}', [OrderController::class, 'restaurantOrderShow'])
        ->name('show');

    Route::post('/{order}/confirm-payment', [OrderController::class, 'sellerConfirmPayment'])
        ->name('confirm-payment');

    Route::post('/{order}/update-status', [OrderController::class, 'restaurantUpdateOrderStatus'])
        ->name('update-status');
    Route::prefix('sales')->name('sales.')->group(function () {
    Route::get('/reports', [SalesReportController::class, 'index'])->name('reports');
        });
});

Route::middleware(['auth', 'roles:restaurant'])->name('reports.')->group(function () {
    Route::get('/sales-reports', [SalesReportController::class, 'index'])
        ->name('index');  // Ini akan menjadi route 'reports.index'
});

Route::middleware(['auth', 'roles:restaurant'])->prefix('sales')->name('sales.')->group(function () {
    Route::get('/reports', [SalesReportController::class, 'index'])
        ->name('reports');  // Ini adalah route 'sales.reports'
});
Route::get('/sales/reports', [SalesReportController::class, 'index'])
    ->name('sales.reports')
    ->middleware(['auth']);
Route::get('/orders/{order}/download-payment-proof', [OrderController::class, 'downloadPaymentProof'])
    ->name('download.payment.proof')
    ->middleware(['auth', 'roles:restaurant']); 
// Review Order Item
Route::middleware(['auth'])->group(function () {
    Route::post('/order-items/{orderItem}/review', [ReviewController::class, 'store'])
        ->name('order-items.review');
});
Route::post('/orders/{order}/upload-payment-proof', [OrderController::class, 'uploadPaymentProof'])
    ->name('orders.upload-payment-proof')
    ->middleware('auth');
    Route::get('/sales/reports', [SalesReportController::class, 'index'])
    ->name('sales.reports');


// Fallback Route
Route::fallback(function () {
    return redirect()->route('dashboard')->with('error', 'Halaman tidak ditemukan.');
});

// Autentikasi Bawaan Laravel
require __DIR__.'/auth.php';