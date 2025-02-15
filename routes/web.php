<?php

/**
 * Hak Cipta (C) 2025 Fahmi Fauzi Rahman
 * Seluruh Hak Cipta Dilindungi Undang-Undang.
 *
 * File ini bersifat rahasia dan merupakan hak milik eksklusif.
 * Dilarang keras menjual kembali aplikasi ini kepada pihak lain
 * dalam bentuk apapun.
 *
 * Penulis  : Fahmi Fauzi Rahman
 * Kontak   : fahmifauzirahman@gmail.com
 * Youtube  : https://www.youtube.com/@hobi_coding
 * Facebook : https://www.facebook.com/fahmifauzirahman
 * Instagram: https://www.instagram.com/fahmi.fauzi.rahman
 * Tiktok   : https://www.tiktok.com/@ffr__85
 * Lisensi  : Hak Milik (Proprietary)
 */

use App\Http\Controllers\AjaxController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CashAccountController;
use App\Http\Controllers\CashTransactionCategoryController;
use App\Http\Controllers\CashTransactionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\GuestOnly;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;

setlocale(LC_TIME, 'id_ID');
Carbon::setLocale('id_ID');

Route::middleware([GuestOnly::class])->group(function () {
    Route::redirect('/', '/login');
    Route::get('login', [AuthController::class, 'login'])->name('login');
    Route::post('login', [AuthController::class, 'authenticate']);
});

Route::middleware([Authenticate::class])->group(function () {
    Route::redirect('/', '/dashboard');
    Route::get('logout', [AuthController::class, 'logout']);
    Route::get('dashboard', [DashboardController::class, 'index']);

    Route::controller(UserController::class)->prefix('user')->group(function () {
        Route::get('', 'index');
        Route::get('export', 'export');
        Route::match(['get', 'post'], 'edit/{id}', 'edit');
        Route::match(['get', 'post'], 'delete/{id}', 'delete');
        Route::match(['get', 'post'], 'profile', 'profile');
        Route::get('data', 'datatable')->name('user.data');
    });

    Route::controller(CashTransactionCategoryController::class)->prefix('cash-transaction-category')->group(function () {
        Route::get('', 'index');
        Route::get('export', 'export');
        Route::match(['get', 'post'], 'edit/{id}', 'edit');
        Route::get('delete/{id}', 'delete');
    });

    Route::controller(CashTransactionController::class)->prefix('cash-transaction')->group(function () {
        Route::get('', 'index');
        Route::get('export', 'export');
        Route::match(['get', 'post'], 'edit/{id}', 'edit');
        Route::match(['get', 'post'], 'transfer', 'transfer');
        Route::get('delete/{id}', 'delete');
    });

    Route::controller(ReportController::class)->prefix('report')->group(function () {
        Route::get('', 'index');
        Route::get('cash-flow', 'cashFlow');
        Route::get('income-expense', 'incomeExpense');
        Route::get('detail', 'detail');
    });

    Route::controller(CashAccountController::class)->prefix('cash-account')->group(function () {
        Route::get('', 'index');
        Route::get('export', 'export');
        Route::match(['get', 'post'], 'edit/{id}', 'edit');
        Route::get('delete/{id}', 'delete');
    });

    Route::controller(AjaxController::class)->prefix('ajax')->group(function () {
        Route::post('add-cash-transaction-category', 'addCashTransactionCategory');
    });
});

Route::get('refresh-csrf', function () {
    return csrf_token();
});
