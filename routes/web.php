<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

// user controllers
use App\Http\Controllers\User\InvoiceController;
use App\Http\Controllers\User\ProductController;
use App\Http\Controllers\User\SettingController;
use App\Http\Controllers\User\CustomerController;
use App\Http\Controllers\User\SubscriptionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Authentication Routes
Auth::routes();

// Landing Page
Route::get('/', [App\Http\Controllers\LandingController::class, 'index'])->name('landing');

// Public Invoice View (No Auth Required)
Route::get('/invoice/{unique_id}', [InvoiceController::class, 'publicView'])
    ->name('invoice.public');
Route::get('/invoice/{unique_id}/pdf', [InvoiceController::class, 'downloadPdf'])
    ->name('invoice.pdf');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Dashboard - Available for both admin and user
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Super Admin Routes (Platform Management)
    |--------------------------------------------------------------------------
    */
    Route::group(['middleware' => ['role:admin'], 'prefix' => 'admin', 'as' => 'admin.'], function () {
        // User Management
        Route::get('users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
        Route::get('users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'show'])->name('users.show');
        Route::post('users/{user}/toggle-status', [\App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::post('users/{user}/update-subscription', [\App\Http\Controllers\Admin\UserController::class, 'updateSubscription'])->name('users.update-subscription');
        Route::post('users/{user}/extend-subscription', [\App\Http\Controllers\Admin\UserController::class, 'extendSubscription'])->name('users.extend-subscription');

        // Subscription Management
        Route::get('subscriptions', [\App\Http\Controllers\Admin\SubscriptionController::class, 'index'])->name('subscriptions.index');
        Route::get('subscriptions/{subscription}', [\App\Http\Controllers\Admin\SubscriptionController::class, 'show'])->name('subscriptions.show');
        Route::post('subscriptions/{subscription}/approve', [\App\Http\Controllers\Admin\SubscriptionController::class, 'approve'])->name('subscriptions.approve');
        Route::post('subscriptions/{subscription}/reject', [\App\Http\Controllers\Admin\SubscriptionController::class, 'reject'])->name('subscriptions.reject');

        // Payment Management
        Route::get('payments', [\App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('payments.index');

        // Reports
        // Route::get('reports', [AdminReportController::class, 'index'])->name('reports.index');

        // Settings
        Route::get('settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings');
        Route::put('settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
        Route::post('settings/platform', [\App\Http\Controllers\Admin\SettingController::class, 'updatePlatformConfig'])->name('settings.platform.update');
    });

    /*
    |--------------------------------------------------------------------------
    | User/Tenant Routes (Shop Management)
    |--------------------------------------------------------------------------
    */
    Route::group(['middleware' => ['role:user', 'check.subscription']], function () {
        // Invoices Management - Index
        Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        
        // Invoice Create/Store with limit check - MUST BE BEFORE {invoice} routes
        Route::middleware('check.invoice.limit')->group(function () {
            Route::get('invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
            Route::post('invoices', [InvoiceController::class, 'store'])->name('invoices.store');
        });
        
        // Invoice View/Edit/Delete
        Route::get('invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
        Route::get('invoices/{invoice}/edit', [InvoiceController::class, 'edit'])->name('invoices.edit');
        Route::put('invoices/{invoice}', [InvoiceController::class, 'update'])->name('invoices.update');
        Route::delete('invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');

        // Invoice Actions
        Route::get('invoices/{invoice}/preview', [InvoiceController::class, 'preview'])->name('invoices.preview');
        Route::post('invoices/{invoice}/send-whatsapp', [InvoiceController::class, 'sendWhatsApp'])->name('invoices.send-whatsapp');
        Route::post('invoices/{invoice}/mark-paid', [InvoiceController::class, 'markPaid'])->name('invoices.mark-paid');
        Route::post('invoices/{invoice}/mark-unpaid', [InvoiceController::class, 'markUnpaid'])->name('invoices.mark-unpaid');
        Route::post('invoices/{invoice}/cancel', [InvoiceController::class, 'cancel'])->name('invoices.cancel');

        // Customers Management
        Route::resource('customers', CustomerController::class);
        Route::post('customers/{customer}/toggle-status', [CustomerController::class, 'toggleStatus'])->name('customers.toggle-status');

        // Products Management
        Route::resource('products', ProductController::class);
        Route::post('products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggle-status');

        // Shop Settings
        Route::get('settings', [SettingController::class, 'index'])->name('settings');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
        Route::post('settings/upload-logo', [SettingController::class, 'uploadLogo'])->name('settings.upload-logo');

        // Subscription Management
        Route::get('subscription', [SubscriptionController::class, 'index'])->name('subscription.index');
        Route::post('subscription/upgrade', [SubscriptionController::class, 'upgrade'])->name('subscription.upgrade');
        Route::post('subscription/payment-callback', [SubscriptionController::class, 'paymentCallback'])->name('subscription.callback');
    });
});
