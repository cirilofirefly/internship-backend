<?php

use App\Http\Controllers\Api\AuthController;
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

Route::get('/', function () {
    // return view('welcome');
    return asset('storage/profile_pictures/t0nF7j4xu1FOdEAGzpzxNWL8uhg3y76JhPN3zUlp.jpg');
});

Route::controller(AuthController::class)->group(function(){
    Route::get('auth/facebook/callback', 'facebookCallback');
    Route::get('auth/google/callback', 'googleCallback');
    Route::get('signin/{method}', 'signin');
});
        

