<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/products/list', [App\Http\Controllers\ApiProductsController::class, 'list']);
Route::group(['middleware' => ['auth:api']], function () {
    Route::post('/add-to-cart', [App\Http\Controllers\ApiCartController::class, 'add']);
    Route::get('/cart/get', [App\Http\Controllers\ApiCartController::class, 'get']);
    Route::post('/cart/delete', [App\Http\Controllers\ApiCartController::class, 'delete']);
});
