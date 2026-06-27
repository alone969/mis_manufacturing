<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceLogController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->middleware('throttle:5,1');
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::post('/send-login-otp', [AuthController::class, 'sendLoginOtp'])->middleware('throttle:5,1');
Route::post('/login-otp', [AuthController::class, 'loginWithOtp'])->middleware('throttle:10,1');

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Profile & Settings
    Route::get('/user', [AuthController::class, 'user']);
    Route::get('/account', [AuthController::class, 'account']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::put('/password', [AuthController::class, 'changePassword']);
    Route::put('/settings', [AuthController::class, 'updateSettings']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/send-verification-otp', [AuthController::class, 'sendVerificationOtp']);
    Route::post('/verify-email', [AuthController::class, 'verifyEmail']);

    // Global Search
    Route::get('/search', [\App\Http\Controllers\SearchController::class, 'index']);

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Attendance (all authenticated users)
    Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn']);
    Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut']);
    Route::get('/attendance', [AttendanceController::class, 'index']);

    // Shifts (all authenticated users can view)
    Route::get('/shifts', [ShiftController::class, 'index']);
    Route::get('/shifts/schedule', [ShiftController::class, 'schedule']);

    // Stock (all authenticated users can view)
    Route::get('/stock', [StockController::class, 'index']);

    // Salary (employees see own)
    Route::get('/salaries', [SalaryController::class, 'index']);

    // Messages (all authenticated users)
    Route::get('/messages', [MessageController::class, 'index']);
    Route::get('/messages/unread-count', [MessageController::class, 'unreadCount']);
    Route::post('/messages', [MessageController::class, 'store']);
    Route::get('/messages/{message}', [MessageController::class, 'show']);
    Route::delete('/messages/{message}', [MessageController::class, 'destroy']);

    // Notifications (all authenticated users)
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::put('/notifications/{notification}/read', [NotificationController::class, 'markRead']);
    Route::put('/notifications/read-all', [NotificationController::class, 'markAllRead']);

    // Device Logs (own)
    Route::get('/device-logs', [DeviceLogController::class, 'index']);

    // Manager & Admin routes
    Route::middleware('role:manager,admin')->group(function () {
        // Shift Management
        Route::post('/shifts', [ShiftController::class, 'store']);
        Route::put('/shifts/{shift}', [ShiftController::class, 'update']);
        Route::post('/shifts/assign', [ShiftController::class, 'assign']);
        Route::delete('/shifts/assignments/{assignment}', [ShiftController::class, 'unassign']);

        // View team members
        Route::get('/employees', [UserController::class, 'index']);
    });

    // Admin only routes
    Route::middleware('role:admin')->group(function () {
        // User Management
        Route::post('/employees', [UserController::class, 'store']);
        Route::get('/employees/{user}', [UserController::class, 'show']);
        Route::put('/employees/{user}', [UserController::class, 'update']);
        Route::delete('/employees/{user}', [UserController::class, 'destroy']);
        Route::put('/employees/{user}/toggle-status', [UserController::class, 'toggleStatus']);

        // Shift Management (full)
        Route::delete('/shifts/{shift}', [ShiftController::class, 'destroy']);

        // Stock Management
        Route::post('/stock', [StockController::class, 'store']);
        Route::put('/stock/{item}', [StockController::class, 'update']);
        Route::delete('/stock/{item}', [StockController::class, 'destroy']);

        // Salary Management
        Route::post('/salaries/process', [SalaryController::class, 'process']);
        Route::patch('/salaries/{salary}/pay', [SalaryController::class, 'pay']);

        // Logs
        Route::get('/activity-logs', [ActivityLogController::class, 'index']);
        Route::get('/device-logs/all', [DeviceLogController::class, 'all']);
    });
});
