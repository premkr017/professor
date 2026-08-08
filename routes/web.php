<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecurringController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SavingsGoalController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

// Login
Route::get('/', [AuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/login', [AuthController::class, 'login'])->name('admin.login.submit');

// Registration
Route::get('/admin/register', [AuthController::class, 'showRegistrationForm'])->name('admin.register');
Route::post('/admin/register', [AuthController::class, 'register'])->name('admin.register.submit');

// Protected routes (requires authentication)
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Accounts / Wallets
    Route::resource('accounts', AccountController::class);

    // Categories
    Route::resource('categories', CategoryController::class);

    // Transactions
    Route::resource('transactions', TransactionController::class);

    // Budgets
    Route::resource('budgets', BudgetController::class);

    // Savings Goals
    Route::resource('savings-goals', SavingsGoalController::class);

    // Loans / Debts
    Route::resource('loans', LoanController::class);

    // Recurring Transactions
    Route::resource('recurrings', RecurringController::class);

    // Reports & Analytics
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'changePassword'])->name('profile.password');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::patch('/settings', [SettingsController::class, 'update'])->name('settings.update');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');
});
