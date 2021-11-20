<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('/orders', [\App\Http\Controllers\OrderController::class, 'index']);
Route::get('/orders/csv', [\App\Http\Controllers\OrderController::class, 'download']);
Route::post('/orders', [\App\Http\Controllers\OrderController::class, 'store']);
Route::post('/orders/update', [\App\Http\Controllers\OrderController::class, 'update']);
Route::post('/orders/delete', [\App\Http\Controllers\OrderController::class, 'delete']);



