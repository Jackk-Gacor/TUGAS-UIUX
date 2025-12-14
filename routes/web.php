<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

// ✅ HOME BENAR (tidak redirect)
Route::get('/', function () {
    return view('home');
})->name('home');

// ✅ MENU TETAP
Route::get('/menu', [MenuController::class, 'index'])
    ->name('menu');

/*
|--------------------------------------------------------------------------
| CHECKOUT ROUTES
|--------------------------------------------------------------------------
*/

// CREATE ORDER
Route::post('/checkout', [OrderController::class, 'store'])
    ->name('checkout');

// SHOW CHECKOUT PAGE
Route::get('/checkout/order/{order}', [OrderController::class, 'show'])
    ->name('checkout.show');

// PAY ORDER
Route::post('/checkout/{order}/pay', [OrderController::class, 'pay'])
    ->name('checkout.pay');

// UPLOAD QRIS
Route::post('/checkout/{order}/upload-proof', [OrderController::class, 'uploadQrisProof'])
    ->name('checkout.upload-proof');

// SUCCESS PAGE
Route::get('/checkout/success', [OrderController::class, 'successPage'])
    ->name('checkout.success');

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/admin', [AdminController::class, 'index'])
    ->name('admin.dashboard');

Route::get('/admin/pembukuan', [AdminController::class, 'pembukuan'])
    ->name('admin.pembukuan');

Route::get('/admin/orders/{order}/edit', [AdminController::class, 'edit'])
    ->name('admin.orders.edit');

Route::put('/admin/orders/{order}', [AdminController::class, 'update'])
    ->name('admin.orders.update');

Route::delete('/admin/orders/{order}', [AdminController::class, 'destroy'])
    ->name('admin.orders.destroy');

Route::post('/admin/orders/bulk-delete', [AdminController::class, 'bulkDestroy'])
    ->name('admin.orders.bulk-destroy');
