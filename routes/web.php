<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\InternController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

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

Route::get('store-intern-id',  [InternController::class, 'setInternUserIdAsCookie']);
Route::controller(AuthController::class)->group(function(){
    Route::get('auth/facebook/callback', 'facebookCallback');
    Route::get('auth/google/callback', 'googleCallback');
    Route::get('signin/{method}', 'signin');
});


