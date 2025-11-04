<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/', function () {
    return view('home');
});
Route::get('/', function () {
    return view('home');
})->name('home'); // Opsional: beri nama route home juga

Route::get('/menu', [MenuController::class, 'index'])->name('menu'); 