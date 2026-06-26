<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceLogController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\StockController;
use Illuminate\Support\Facades\Route;

// ── Public auth routes ───────────────────────────────────────────────
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// ── Authenticated routes ─────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::get('/account', [AuthController::class, 'account']);
    Route::put('/account', [AuthController::class, 'updateProfile']);
    Route::put('/account/password', [AuthController::class, 'changePassword']);
    Route::put('/account/settings', [AuthController::class, 'updateSettings']);

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Messages (all roles)
    Route::get('/messages', [MessageController::class, 'index']);
    Route::post('/messages', [MessageController::class, 'store']);
    Route::put('/messages/{message}/read', [MessageController::class, 'markRead']);
    Route::get('/messages/unread-count', [MessageController::class, 'unreadCount']);

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::put('/notifications/{notification}/read', [NotificationController::class, 'markRead']);
    Route::put('/notifications/read-all', [NotificationController::class, 'markAllRead']);

    // Device logs (users see own devices)
    Route::get('/device-logs', [DeviceLogController::class, 'index']);

    // Users (for messaging recipient list)
    Route::get('/users', [\App\Http\Controllers\UserController::class, 'forMessaging']);

    // Stock (read-only for employees/managers)
    Route::get('/stock', [StockController::class, 'index']);

    // Shifts (all roles can view; manager/admin can manage)
    Route::get('/shifts', [ShiftController::class, 'index']);
    Route::get('/shifts/assignments/{date?}', [ShiftController::class, 'assignments']);
    Route::post('/shifts/{assignment}/clock-in', [ShiftController::class, 'clockIn']);
    Route::post('/shifts/{assignment}/clock-out', [ShiftController::class, 'clockOut']);

    // Salaries (employees see own, admin sees all)
    Route::get('/salaries', [SalaryController::class, 'index']);
});

// ── Manager & Admin routes ───────────────────────────────────────────
Route::middleware(['auth', 'role:manager,admin'])->group(function () {
    Route::post('/shifts', [ShiftController::class, 'store']);
    Route::put('/shifts/{shift}', [ShiftController::class, 'update']);
    Route::delete('/shifts/{shift}', [ShiftController::class, 'destroy']);
    Route::post('/shifts/{shift}/assign', [ShiftController::class, 'assign']);
    Route::delete('/shifts/{shift}/assignments/{assignment}', [ShiftController::class, 'unassign']);
});

// ── Admin-only routes ────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->group(function () {
    // User management
    Route::get('/admin/users', [\App\Http\Controllers\UserController::class, 'index']);
    Route::put('/admin/users/{user}/role', [\App\Http\Controllers\UserController::class, 'updateRole']);

    // Stock management (CRUD)
    Route::post('/stock', [StockController::class, 'store']);
    Route::put('/stock/{item}', [StockController::class, 'update']);
    Route::delete('/stock/{item}', [StockController::class, 'destroy']);

    // Salary management
    Route::post('/salaries', [SalaryController::class, 'store']);
    Route::put('/salaries/{salary}/pay', [SalaryController::class, 'markPaid']);
    Route::get('/salaries/summary', [SalaryController::class, 'summary']);

    // Activity log
    Route::get('/activity-logs', [ActivityLogController::class, 'index']);

    // Device logs (admin sees all)
    Route::get('/admin/device-logs', [DeviceLogController::class, 'all']);
});
