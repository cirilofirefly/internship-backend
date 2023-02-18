<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CoordinatorController;
use App\Http\Controllers\Api\RequirementController;
use App\Http\Controllers\Api\DailyTimeRecordController
;
use App\Http\Controllers\Api\DetailedReportController;
use App\Http\Controllers\Api\SupervisorController;
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
    ->controller(CoordinatorController::class)
    ->prefix('coordinator')
    ->group(function () {
        Route::get('get-offices', 'getOffices');
        Route::get('get-approved-interns', 'getApprovedInterns');
        Route::post('assign-intern', 'assignIntern');
});

Route::middleware('auth:sanctum')
    ->controller(SupervisorController::class)
    ->prefix('supervisor')
    ->group(function () {
        Route::get('get-assigned-interns', 'getAssignedInterns');
});

Route::middleware('auth:sanctum')
    ->controller(RequirementController::class)
    ->prefix('intern')
    ->group(function () {
        Route::get('get-requirements', 'getRequirements');
        Route::get('get-requirements-as-coordinator', 'getRequirementsAsCoordinator');
        Route::post('upload-requirement', 'uploadRequirement');
        Route::delete('delete-requirement/{id}', 'deleteRequirement');
        Route::get('download-file/{id}', 'downloadFile');
});

Route::middleware('auth:sanctum')
    ->controller(DailyTimeRecordController::class)
    ->prefix('intern')
    ->group(function () {

        Route::get('get-daily-time-records', 'getDailyTimeRecords');
        Route::post('save-daily-time-record', 'saveDailyTimeRecord');
        Route::post('submit-daily-time-record', 'submitDailyTimeRecord');
        Route::put('update-daily-time-record/{id}', 'updateDailyTimeRecord');
        Route::delete('delete-daily-time-record/{id}', 'deleteDailyTimeRecord');

});

Route::middleware('auth:sanctum')
    ->controller(DetailedReportController::class)
    ->prefix('intern')
    ->group(function () {
        Route::get('get-offices', 'getOffices');
        Route::post('submit-detailed-report', 'submitDetailedReport');
        Route::get('get-detailed-reports', 'getDetailedReports');
        Route::post('save-detailed-report', 'saveDetailedReport');

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