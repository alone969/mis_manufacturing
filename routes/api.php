<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');
Route::get('/user', [AuthController::class, 'user'])->middleware('auth');
Route::get('/account', [AuthController::class, 'account'])->middleware('auth');

// Admin-only routes
Route::get('/admin/users', [\App\Http\Controllers\UserController::class, 'index'])
    ->middleware(['auth', 'role:admin']);
Route::put('/admin/users/{user}/role', [\App\Http\Controllers\UserController::class, 'updateRole'])
    ->middleware(['auth', 'role:admin']);
