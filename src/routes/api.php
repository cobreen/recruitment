<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\passportAuthController;
use App\Http\Controllers\TweetController;

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

Route::post('/register',[passportAuthController::class,'registerUser']);
Route::post('/login',[passportAuthController::class,'loginUser']);
//add this middleware to ensure that every request is authenticated
Route::middleware('auth:api')->group(function(){
    Route::get('user', [passportAuthController::class,'authenticatedUserDetails']);
    Route::post('user',[passportAuthController::class,'updateUser']);
    Route::post('tweet',[TweetController::class,'createTweet']);
});
Route::get('user/{id}/tweets',[TweetController::class,'userTweets']);
Route::get('tweets/random',[TweetController::class,'randomTweet']);