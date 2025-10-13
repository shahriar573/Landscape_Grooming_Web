<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;

// Public routes
Route::get('/', function () {
    return redirect()->route('services.index');
});

Route::resource('services', ServiceController::class)->only(['index', 'show']);

// Authentication routes
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    Route::resource('bookings', BookingController::class);
    Route::post('/bookings/{booking}/assign-staff', [BookingController::class, 'assignStaff'])
         ->name('bookings.assign-staff');
});

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Admin\AdminDashboardController::class, 'dashboard'])->name('dashboard');
    
    // Services Management
    Route::get('/services', [App\Http\Controllers\Admin\AdminDashboardController::class, 'services'])->name('services');
    Route::post('/services', [App\Http\Controllers\Admin\AdminDashboardController::class, 'storeService'])->name('services.store');
    Route::patch('/services/{service}/toggle', [App\Http\Controllers\Admin\AdminDashboardController::class, 'toggleService'])->name('services.toggle');
    Route::delete('/services/{service}', [App\Http\Controllers\Admin\AdminDashboardController::class, 'destroyService'])->name('services.destroy');
    
    // Billing Management
    Route::get('/billing', [App\Http\Controllers\Admin\AdminDashboardController::class, 'billing'])->name('billing');
    Route::patch('/billing/{booking}/mark-paid', [App\Http\Controllers\Admin\AdminDashboardController::class, 'markPaid'])->name('billing.mark-paid');
    Route::get('/billing/invoice/{booking}', [App\Http\Controllers\Admin\AdminDashboardController::class, 'generateInvoice'])->name('billing.invoice');
    
    // Users Management
    Route::get('/users', [App\Http\Controllers\Admin\AdminDashboardController::class, 'users'])->name('users');
    Route::post('/users', [App\Http\Controllers\Admin\AdminDashboardController::class, 'storeUser'])->name('users.store');
    Route::patch('/users/{user}/toggle', [App\Http\Controllers\Admin\AdminDashboardController::class, 'toggleUser'])->name('users.toggle');
    Route::delete('/users/{user}', [App\Http\Controllers\Admin\AdminDashboardController::class, 'destroyUser'])->name('users.destroy');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::patch('/users/{user}', [UserController::class, 'update'])->name('users.update');
    
    // Bookings Management
    Route::get('/bookings', [App\Http\Controllers\Admin\AdminDashboardController::class, 'bookings'])->name('bookings');
});

// Staff routes
Route::middleware(['auth', 'role:staff'])->prefix('staff')->name('staff.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Staff\StaffDashboardController::class, 'dashboard'])->name('dashboard');
    
    // Bookings Management
    Route::get('/bookings', [App\Http\Controllers\Staff\StaffDashboardController::class, 'bookings'])->name('bookings');
    Route::patch('/bookings/{booking}/status', [App\Http\Controllers\Staff\StaffDashboardController::class, 'updateBookingStatus'])->name('bookings.update-status');
    
    // Schedule
    Route::get('/schedule', [App\Http\Controllers\Staff\StaffDashboardController::class, 'schedule'])->name('schedule');
    
    // Performance
    Route::get('/performance', [App\Http\Controllers\Staff\StaffDashboardController::class, 'performance'])->name('performance');
});