<?php

use App\Http\Controllers\WebAttendanceController;
use App\Http\Controllers\WebEmployeeController;
use App\Http\Controllers\WebLogController;
use App\Http\Controllers\WebMessageController;
use App\Http\Controllers\WebNotificationController;
use App\Http\Controllers\WebSalaryController;
use App\Http\Controllers\WebShiftController;
use App\Http\Controllers\WebStockController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'web'])->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/admin', [\App\Http\Controllers\DashboardController::class, 'adminPortal'])->middleware(['auth', 'verified', 'role:admin'])->name('admin.portal');

Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Settings
    Route::get('/settings', function () { return view('settings.index'); })->name('settings.edit');
    Route::put('/settings', function (\Illuminate\Http\Request $request) {
        $user = $request->user();
        $request->validate([
            'settings' => 'required|array',
            'settings.language' => 'sometimes|string|in:en,es,fr,de,ar',
            'settings.email_notifications' => 'sometimes|boolean',
            'settings.shift_reminders' => 'sometimes|boolean',
            'settings.theme' => 'sometimes|string|in:light,dark,system',
        ]);
        $currentSettings = $user->settings ?? [];
        $user->update(['settings' => array_merge($currentSettings, $request->settings)]);
        return redirect()->route('settings.edit')->with('success', 'Settings updated successfully.');
    })->name('settings.update');

    // Attendance (Clock In/Out)
    Route::post('/attendance/clock-in', [WebAttendanceController::class, 'clockIn'])->name('attendance.clock-in');
    Route::post('/attendance/clock-out', [WebAttendanceController::class, 'clockOut'])->name('attendance.clock-out');
    Route::get('/attendance', [WebAttendanceController::class, 'index'])->name('attendance.index');

    // Shifts
    Route::get('/shifts', [WebShiftController::class, 'index'])->name('shifts.index');
    Route::get('/shifts/schedule', [WebShiftController::class, 'schedule'])->name('shifts.schedule');

    // Stock (viewable by all)
    Route::get('/stock', [WebStockController::class, 'index'])->name('stock.index');

    // Messages
    Route::get('/messages', [WebMessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/create', [WebMessageController::class, 'create'])->name('messages.create');
    Route::post('/messages', [WebMessageController::class, 'store'])->name('messages.store');
    Route::get('/messages/{message}', [WebMessageController::class, 'show'])->name('messages.show');
    Route::delete('/messages/{message}', [WebMessageController::class, 'destroy'])->name('messages.destroy');

    // Notifications
    Route::get('/notifications', [WebNotificationController::class, 'index'])->name('notifications.index');
    Route::put('/notifications/{notification}/read', [WebNotificationController::class, 'markRead'])->name('notifications.mark-read');
    Route::put('/notifications/read-all', [WebNotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');

    // Manager & Admin routes
    Route::middleware('role:manager,admin')->group(function () {
        Route::get('/employees', [WebEmployeeController::class, 'index'])->name('employees.index');
        Route::get('/employees/{user}', [WebEmployeeController::class, 'show'])->name('employees.show');

        // Shift management
        Route::get('/shifts/create', [WebShiftController::class, 'create'])->name('shifts.create');
        Route::post('/shifts', [WebShiftController::class, 'store'])->name('shifts.store');
        Route::get('/shifts/{shift}/edit', [WebShiftController::class, 'edit'])->name('shifts.edit');
        Route::put('/shifts/{shift}', [WebShiftController::class, 'update'])->name('shifts.update');
        Route::get('/shifts/{shift}/assign', [WebShiftController::class, 'assignForm'])->name('shifts.assign-form');
        Route::post('/shifts/{shift}/assign', [WebShiftController::class, 'assign'])->name('shifts.assign');
        Route::delete('/shifts/assignments/{assignment}', [WebShiftController::class, 'unassign'])->name('shifts.unassign');
    });

    // Admin only routes
    Route::middleware('role:admin')->group(function () {
        // Employee management (full)
        Route::get('/employees/create', [WebEmployeeController::class, 'create'])->name('employees.create');
        Route::post('/employees', [WebEmployeeController::class, 'store'])->name('employees.store');
        Route::get('/employees/{user}/edit', [WebEmployeeController::class, 'edit'])->name('employees.edit');
        Route::put('/employees/{user}', [WebEmployeeController::class, 'update'])->name('employees.update');
        Route::delete('/employees/{user}', [WebEmployeeController::class, 'destroy'])->name('employees.destroy');
        Route::put('/employees/{user}/toggle-status', [WebEmployeeController::class, 'toggleStatus'])->name('employees.toggle-status');

        // Shift management (full delete)
        Route::delete('/shifts/{shift}', [WebShiftController::class, 'destroy'])->name('shifts.destroy');

        // Stock management
        Route::get('/stock/create', [WebStockController::class, 'create'])->name('stock.create');
        Route::post('/stock', [WebStockController::class, 'store'])->name('stock.store');
        Route::get('/stock/{item}/edit', [WebStockController::class, 'edit'])->name('stock.edit');
        Route::put('/stock/{item}', [WebStockController::class, 'update'])->name('stock.update');
        Route::delete('/stock/{item}', [WebStockController::class, 'destroy'])->name('stock.destroy');

        // Salary management
        Route::get('/salaries', [WebSalaryController::class, 'index'])->name('salaries.index');
        Route::get('/salaries/create', [WebSalaryController::class, 'create'])->name('salaries.create');
        Route::post('/salaries', [WebSalaryController::class, 'store'])->name('salaries.store');
        Route::patch('/salaries/{salary}/pay', [WebSalaryController::class, 'pay'])->name('salaries.pay');

        // Logs
        Route::get('/logs/activity', [WebLogController::class, 'index'])->name('activity-logs.index');
        Route::get('/logs/devices', [WebLogController::class, 'deviceLogs'])->name('device-logs.index');
    });
});

require __DIR__.'/auth.php';
