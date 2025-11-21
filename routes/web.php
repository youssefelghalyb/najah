<?php

use App\Http\Controllers\BundleController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QrCodeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::get('/{uuid}', [QrCodeController::class, 'redirect'])
    ->name('qr.redirect')
    ->where('uuid', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');

// Authenticated QR Code Management Routes
Route::prefix('qr-codes')->name('qr-codes.')->group(function () {
    // Standard CRUD
    Route::get('/', [QrCodeController::class, 'index'])->name('index');
    Route::get('/create', [QrCodeController::class, 'create'])->name('create');
    Route::post('/', [QrCodeController::class, 'store'])->name('store');
    Route::get('/{qrCode}', [QrCodeController::class, 'show'])->name('show');
    Route::get('/{qrCode}/edit', [QrCodeController::class, 'edit'])->name('edit');
    Route::put('/{qrCode}', [QrCodeController::class, 'update'])->name('update');
    Route::delete('/{qrCode}', [QrCodeController::class, 'destroy'])->name('destroy');
    
    // Bulk Operations
    Route::get('/bulk/create', [QrCodeController::class, 'bulkCreate'])->name('bulk-create');
    Route::post('/bulk/store', [QrCodeController::class, 'bulkStore'])->name('bulk-store');
    Route::get('/bulk/preview', [QrCodeController::class, 'bulkPreview'])->name('bulk-preview');
    
    // Export & Download
    Route::post('/export', [QrCodeController::class, 'export'])->name('export');
    Route::get('/{qrCode}/download', [QrCodeController::class, 'download'])->name('download');
    
    // Bulk Actions
    Route::post('/bulk/delete', [QrCodeController::class, 'bulkDestroy'])->name('bulk-destroy');
    
    // Status Toggle
    Route::post('/{qrCode}/toggle-status', [QrCodeController::class, 'toggleStatus'])->name('toggle-status');
});



Route::middleware(['auth'])->group(function () {
    
    // Products Routes
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/create', [ProductController::class, 'create'])->name('create');
        Route::post('/', [ProductController::class, 'store'])->name('store');
        Route::get('/{product}', [ProductController::class, 'show'])->name('show');
        Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
        Route::put('/{product}', [ProductController::class, 'update'])->name('update');
        Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
        
        // Additional Product Actions
        Route::post('/{product}/update-stock', [ProductController::class, 'updateStock'])->name('update-stock');
        Route::post('/{product}/delete-gallery-image', [ProductController::class, 'deleteGalleryImage'])->name('delete-gallery-image');
        Route::post('/{product}/duplicate', [ProductController::class, 'duplicate'])->name('duplicate');
        
        // Bulk Actions
        Route::post('/bulk/delete', [ProductController::class, 'bulkDelete'])->name('bulk-delete');
        Route::post('/bulk/update-status', [ProductController::class, 'bulkUpdateStatus'])->name('bulk-update-status');
    });
    
    // Bundles Routes
    Route::prefix('bundles')->name('bundles.')->group(function () {
        Route::get('/', [BundleController::class, 'index'])->name('index');
        Route::get('/create', [BundleController::class, 'create'])->name('create');
        Route::post('/', [BundleController::class, 'store'])->name('store');
        Route::get('/{bundle}', [BundleController::class, 'show'])->name('show');
        Route::get('/{bundle}/edit', [BundleController::class, 'edit'])->name('edit');
        Route::put('/{bundle}', [BundleController::class, 'update'])->name('update');
        Route::delete('/{bundle}', [BundleController::class, 'destroy'])->name('destroy');
        
        // Additional Bundle Actions
        Route::post('/{bundle}/delete-gallery-image', [BundleController::class, 'deleteGalleryImage'])->name('delete-gallery-image');
        Route::post('/{bundle}/duplicate', [BundleController::class, 'duplicate'])->name('duplicate');
        Route::post('/{bundle}/recalculate-pricing', [BundleController::class, 'recalculatePricing'])->name('recalculate-pricing');
        
        // Bulk Actions
        Route::post('/bulk/delete', [BundleController::class, 'bulkDelete'])->name('bulk-delete');
        Route::post('/bulk/update-status', [BundleController::class, 'bulkUpdateStatus'])->name('bulk-update-status');
    });
});


Route::middleware(['auth'])->group(function () {
    // Orders CRUD
    Route::resource('orders', OrderController::class);
    
    // QR Code Assignment
    Route::get('orders/{order}/assign-qr-codes', [OrderController::class, 'assignQRCodesPage'])->name('orders.assign-qr-codes');
    Route::post('orders/{order}/assign-qr-codes', [OrderController::class, 'assignQRCodes'])->name('orders.assign-qr-codes.store');
    Route::delete('orders/{order}/unassign-qr-code/{orderItemId}', [OrderController::class, 'unassignQRCode'])->name('orders.unassign-qr-code');
    
    // Status Management
    Route::post('orders/{order}/change-status', [OrderController::class, 'changeStatus'])->name('orders.change-status');
    Route::post('orders/{order}/update-payment-status', [OrderController::class, 'updatePaymentStatus'])->name('orders.update-payment-status');
});





require __DIR__.'/auth.php';
