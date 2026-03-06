<?php

use App\Http\Controllers\AttendanceController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;

// ----------------------------------------------------
// ゲスト（未ログイン）専用
// ----------------------------------------------------
Route::middleware('guest')->group(function () {
    // 一般ユーザー
    Route::get('/login', fn() => view('auth.login', [
        'postRoute' => url('/login'),
    ]))->name('login');

    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

    Route::get('/register', fn() => view('auth.register'))->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);

    // 管理者ログイン（未ログイン時のみ表示）
    Route::get('/admin/login', fn() => view('auth.login', [
        'postRoute' => url('/admin/login'),
    ]))->name('admin.login');

    Route::post('/admin/login', [AuthenticatedSessionController::class, 'store']);
});

// ----------------------------------------------------
// ログイン必須
// ----------------------------------------------------
Route::middleware('auth')->group(function () {
    // ログアウト
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // メール認証済みでないと入れないページ
    Route::middleware('verified')->group(function () {
        Route::get('/attendance', [AttendanceController::class, 'clock'])->name('attendance.record');
    });
});
