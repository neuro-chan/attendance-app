<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceCorrectionController;
use App\Http\Controllers\BreakController;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

// ----------------------------------------------------
// 未ログイン
// ----------------------------------------------------
Route::middleware('guest')->group(function () {
    Route::get('/login', fn () => view('auth.login', [
        'postRoute' => url('/login'),
    ]))->name('login');

    Route::get('/register', fn () => view('auth.register'))->name('register');

    Route::get('/admin/login', fn () => view('auth.login', [
        'postRoute' => url('/admin/login'),
    ]))->name('admin.login');

    Route::post('/admin/login', [AuthenticatedSessionController::class, 'store'])->name('admin.login.store');
});

// ----------------------------------------------------
// 認証済み
// ----------------------------------------------------
Route::middleware('auth')->group(function () {

    // 同一パス要件
    Route::get('/stamp_correction_request/list', [AttendanceCorrectionController::class, 'index'])
        ->name('request.index');

    // ----------------------------------------------------
    // 管理者（権限必須）
    // ----------------------------------------------------
    Route::middleware(EnsureUserIsAdmin::class)->name('admin.')->group(function () {

        Route::get('/stamp_correction_request/approve/{id}', [Admin\CorrectionApproveController::class, 'approve'])
            ->name('correction.approve');

        Route::post('/stamp_correction_request/approve/{id}', [Admin\CorrectionApproveController::class, 'approveStore'])
            ->name('correction.approve.store');

        // 管理者画面
        Route::prefix('admin')->group(function () {
            Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

            Route::get('/attendance/list', [Admin\AttendanceController::class, 'index'])->name('attendance.index');
            Route::get('/attendance/{id}', [Admin\AttendanceController::class, 'show'])->name('attendance.show');
            Route::post('/attendance/{id}', [Admin\AttendanceController::class, 'update'])->name('attendance.update');

            Route::get('/staff/list', [Admin\StaffController::class, 'index'])->name('staff.index');
            Route::get('/attendance/staff/{id}', [Admin\StaffController::class, 'show'])->name('staff.attendance');
            Route::get('/attendance/staff/{id}/export', [Admin\StaffController::class, 'exportCsv'])->name('staff.export');
        });
    });

    // ----------------------------------------------------
    // スタッフ（メール認証必須）
    // ----------------------------------------------------
    Route::middleware('verified')->group(function () {
        Route::get('/attendance', [AttendanceController::class, 'record'])->name('attendance.record');
        Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn'])->name('attendance.clock-in');
        Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut'])->name('attendance.clock-out');

        Route::post('/attendance/break-start', [BreakController::class, 'start'])->name('attendance.break-start');
        Route::post('/attendance/break-end', [BreakController::class, 'end'])->name('attendance.break-end');

        Route::get('/attendance/list', [AttendanceController::class, 'index'])->name('staff.index');
        Route::get('/attendance/detail/{id}', [AttendanceController::class, 'show'])->name('staff.show');
        Route::post('/attendance/detail/{id}', [AttendanceCorrectionController::class, 'store'])->name('correction.store');
    });
});
