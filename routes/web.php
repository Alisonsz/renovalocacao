<?php

use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\SiteSettingController as AdminSiteSettingController;
use App\Http\Controllers\Admin\TestimonialController as AdminTestimonialController;
use App\Http\Controllers\Admin\TrackingCodeController as AdminTrackingCodeController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\GoogleMerchantController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/produtos/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/reservar/{slug}', [BookingController::class, 'create'])->name('booking.create');
Route::post('/reservar', [BookingController::class, 'store'])->name('booking.store');
Route::get('/reserva-confirmada', [BookingController::class, 'success'])->name('booking.success');

// Google Merchant XML feed
Route::get('/google-merchant.xml', [GoogleMerchantController::class, 'feed'])->name('google-merchant');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::redirect('dashboard', '/admin')->name('dashboard');

    // Admin panel
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::resource('categories', AdminCategoryController::class)
            ->except(['show']);

        Route::resource('products', AdminProductController::class)
            ->except(['show']);
        Route::post('products/{product}/set-primary-image', [AdminProductController::class, 'setPrimaryImage'])
            ->name('products.set-primary-image');

        Route::resource('testimonials', AdminTestimonialController::class)
            ->except(['show']);

        Route::resource('bookings', AdminBookingController::class)
            ->only(['index', 'update', 'destroy']);

        Route::get('settings', [AdminSiteSettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [AdminSiteSettingController::class, 'update'])->name('settings.update');

        Route::resource('tracking-codes', AdminTrackingCodeController::class)
            ->except(['show']);
    });
});

require __DIR__.'/settings.php';
