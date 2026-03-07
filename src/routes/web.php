<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\BreakController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;

// ----------------------------------------------------
// ゲスト（未ログイン）
// ----------------------------------------------------
Route::middleware('guest')->group(function () {
    // スタッフ
    Route::get('/login', fn() => view('auth.login', [
        'postRoute' => url('/login'),
    ]))->name('login');

    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

    Route::get('/register', fn() => view('auth.register'))->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);

    // 管理者
    Route::get('/admin/login', fn() => view('auth.login', [
        'postRoute' => url('/admin/login'),
    ]))->name('admin.login');

    Route::post('/admin/login', [AuthenticatedSessionController::class, 'store']);
});

// ----------------------------------------------------
// メール認証済み
// ----------------------------------------------------
Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

Route::middleware('auth')->group(function () {
        Route::get('/attendance', [AttendanceController::class, 'record'])
            ->name('attendance.record');
    });
    Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn'])
        ->name('attendance.clock-in');

    Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut'])
        ->name('attendance.clock-out');

    Route::post('/attendance/break-start', [BreakController::class, 'start'])
        ->name('attendance.break-start');

    Route::post('/attendance/break-end', [BreakController::class, 'end'])
        ->name('attendance.break-end');
});
