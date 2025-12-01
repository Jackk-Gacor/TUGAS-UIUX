<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    return view('home');
})->name('home');


Route::get('/menu', [MenuController::class, 'index'])->name('menu');
Route::post('/checkout', [OrderController::class, 'store'])->name('checkout');
Route::get('/checkout/{order}', [OrderController::class, 'show'])->name('checkout.show');
Route::post('/checkout/{order}/pay', [OrderController::class, 'pay'])->name('checkout.pay');

// Admin dashboard sederhana untuk pembukuan keuangan & pesanan
Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
Route::get('/admin/orders/{order}/edit', [AdminController::class, 'edit'])->name('admin.orders.edit');
Route::put('/admin/orders/{order}', [AdminController::class, 'update'])->name('admin.orders.update');
Route::delete('/admin/orders/{order}', [AdminController::class, 'destroy'])->name('admin.orders.destroy');
Route::post('/admin/orders/bulk-delete', [AdminController::class, 'bulkDestroy'])->name('admin.orders.bulk-destroy');
