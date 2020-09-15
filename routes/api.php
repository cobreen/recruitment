<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\YmlController;
use App\Http\Controllers\Api\QueueStateController;

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

Route::group(["prefix" => "yml", "middleware" => ["apiKeyValidated"]], function() {
    Route::post("/make", [YmlController::class, "SaveAndEnqueue"]);
    Route::get("/give", [YmlController::class, "GiveResult"]);
});

Route::group(["middleware" => ["apiKeyValidated"], "prefix" => "queue"], function () {
    Route::get("/{token}/get", [QueueStateController::class, "getAll"]);
    Route::get("/{token}/{product_id}/get", [QueueStateController::class, "getProduct"]);
    Route::get("/{token}/{product_id}/{attribute_name}/get", [QueueStateController::class, "getProductAttribute"]);

    Route::post("/{token}/add", [QueueStateController::class, "addProduct"]);

    Route::post("/{token}/{product_id}/{attribute_name}/{value}", [QueueStateController::class, "updateProductAttribute"]);

    Route::post("/{token}/{product_id}/drop", [QueueStateController::class, "dropProduct"]);

});