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

Route::controller(AuthController::class)->group(function () {
    Route::post('register-intern', 'registerIntern');
    Route::post('login', 'login');
    Route::get('signin/{method}', 'signin');
    Route::get('get-remember-token', 'getRememberToken');
    Route::post('logout', 'logout')->middleware('auth:sanctum');
    Route::get('auth/google/callback', 'googleCallback');
});

Route::middleware('auth:sanctum')
    ->controller(UserController::class)
    ->prefix('coordinator')
    ->group(function () {
        Route::get('get-interns', 'getInterns');
        Route::get('get-intern/{id}', 'getIntern');
        Route::post('approve-intern', 'approveIntern');
        Route::post('decline-intern', 'declineIntern');

        Route::get('get-supervisors', 'getSupervisors');
        Route::get('get-supervisor/{id}', 'getSupervisor');
        Route::post('create-supervisor', 'createSupervisor');
        Route::put('update-supervisor', 'updateSupervisor');

        Route::post('save-user', 'saveUser');
        Route::get('get-user/{id}', 'getUser');
});

Route::middleware('auth:sanctum')
    ->controller(UserController::class)
    ->group(function () {
        Route::get('profile-info', 'getProfileInfo');
        Route::put('update-profile', 'updateProfile');
        Route::post('update-profile-picture', 'uploadProfilePicture');
        Route::post('update-e-signature', 'uploadESignature');
});

Route::controller(UserController::class)->group(function () {
    Route::get('available-coordinator', 'availableCoordinator');
});