<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/test', function (Request $request) {
    return 'test';
});


Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::get('signin/{method}', 'signin');
    Route::get('get-remember-token', 'getRememberToken');
    Route::post('logout', 'logout')->middleware('auth:sanctum');
    Route::get('auth/google/callback', 'googleCallback');
});

Route::controller(UserController::class)->group(function () {
    Route::post('save-user', 'saveUser');
    Route::get('get-users', 'getUsers');
    Route::get('get-user/{id}', 'getUser');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('users', UserController::class);
});

Route::middleware('auth:sanctum')->group(function () {
    // Route::apiResource('me', [AuthController::class, 'me']);
});
