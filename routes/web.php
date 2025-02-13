<?php

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
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

setlocale(LC_TIME, 'id_ID');
Carbon::setLocale('id_ID');

Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('login', [AuthController::class, 'authenticate']);

Route::middleware([Authenticate::class])->group(function () {
    Route::redirect('/', '/dashboard');
    Route::get('logout', [AuthController::class, 'logout']);

    Route::get('dashboard', [DashboardController::class, 'index']);

    Route::controller(SettingsController::class)->prefix('settings')->group(function () {
        Route::get('', 'edit');
        Route::post('save', 'save');
    });

    Route::controller(UserController::class)->prefix('user')->group(function () {
        Route::get('', 'index');
        Route::get('export', 'export');
        Route::match(['get', 'post'], 'edit/{id}', 'edit');
        Route::match(['get', 'post'], 'delete/{id}', 'delete');
        Route::match(['get', 'post'], 'profile', 'profile');
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
        Route::get('detail', 'detail');
    });

    Route::controller(CashAccountController::class)->prefix('cash-account')->group(function () {
        Route::get('', 'index');
        Route::get('export', 'export');
        Route::match(['get', 'post'], 'edit/{id}', 'edit');
        Route::get('delete/{id}', 'delete');
    });

    Route::get('refresh-csrf', function () {
        return csrf_token();
    });

    Route::controller(AjaxController::class)->prefix('ajax')->group(function () {
        Route::post('add-cash-transaction-category', 'addCashTransactionCategory');
    });
});

Route::redirect('/', '/login');
