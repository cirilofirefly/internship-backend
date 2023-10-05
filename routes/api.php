<?php

use App\Http\Controllers\Api\Auth\ChangePasswordController;
use App\Http\Controllers\Api\Auth\CodeCheckController;
use App\Http\Controllers\Api\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\Auth\ResetPasswordController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CoordinatorController;
use App\Http\Controllers\Api\RequirementController;
use App\Http\Controllers\Api\DailyTimeRecordController;
use App\Http\Controllers\Api\DetailedReportController;
use App\Http\Controllers\Api\Intern\DashboardController;
use App\Http\Controllers\Api\Intern\DTRFileController;
use App\Http\Controllers\Api\InternController;
use App\Http\Controllers\Api\RFIDRegistrationQueueController;
use App\Http\Controllers\Api\SupervisorController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

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

Route::get('store-intern-id',  [InternController::class, 'setInternUserIdAsCookie']);
Route::get('get-intern-id',  function (Request $request) {
    return json_decode($request->cookie('data'));
});

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

Route::post('forgot-password', ForgotPasswordController::class);
Route::post('check-reset-password-token', CodeCheckController::class);
Route::post('reset-password', ResetPasswordController::class);

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

        Route::get('get-intern-evaluation', 'getInternEvaluation');
        Route::get('get-assigned-interns', 'getAssignedInterns');
        Route::post('validate-requirements', 'validateRequirments');
        Route::post('intern-rfid-registration', 'internRfidRegistration');
    });

Route::middleware('auth:sanctum')
    ->controller(SupervisorController::class)
    ->prefix('supervisor')
    ->group(function () {
        Route::get('get-assigned-interns', 'getAssignedInterns');
        Route::get('get-intern-daily-time-records', 'getInternDailyTimeRecords');
        Route::get('get-intern-detailed-reports', 'getInternDetailedReports');;
        Route::post('validate-intern-daily-time-records', 'validateInternDailyTimeRecords');
        Route::post('validate-intern-detailed-reports', 'validateInternDetailedReports');
        Route::post('validate-requirements', 'validateRequirments');
        Route::post('save-intern-evaluation', 'saveInternEvaluation');
        Route::get('get-intern-evaluation', 'getInternEvaluation');

        
    });

Route::middleware('auth:sanctum')
    ->controller(RequirementController::class)
    ->prefix('intern')
    ->group(function () {
        Route::get('get-requirements', 'getRequirements');
        Route::post('submit-requirements', 'submitRequirements');
        Route::get('get-requirements-as-supervisor', 'getRequirementsAsSupervisor');
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


Route::middleware('auth:sanctum')
    ->controller(ChangePasswordController::class)
    ->group(function () {
        Route::post('change-password', 'changePassword');
    });

Route::controller(UserController::class)->group(function () {
    Route::get('available-coordinator', 'availableCoordinator');
});

Route::get('register-rfid/{device_token}', [RFIDRegistrationQueueController::class, 'registerRFID']);
Route::get('scan-rfid/{cardID}', [RFIDRegistrationQueueController::class, 'scanRFID']);

Route::controller(DailyTimeRecordController::class)
    ->prefix('intern')
    ->group(function () {
        Route::post('time-in-out', 'timeInOut');
    });

Route::middleware('auth:sanctum')
    ->controller(DashboardController::class)
    ->prefix('intern')
    ->group(function () {
        Route::prefix('dashboard')->group(function () {
            Route::get('get-dashboard-count', 'getDashboardCount');
            Route::get('get-designation-info', 'getDesignationInfo');
            Route::get('get-weekly-attendance', 'getWeeklyAttendance');
        });
    });


Route::middleware('auth:sanctum')
    ->controller(DashboardController::class)
    ->prefix('supervisor')
    ->group(function () {
        Route::prefix('dashboard')->group(function () {
            Route::get('get-today-intern-daily-time-records', 'getTodayInternDailyTimeRecords');
        });
    });


Route::middleware('auth:sanctum')
    ->controller(DashboardController::class)
    ->prefix('coordinator')
    ->group(function () {
        Route::prefix('dashboard')->group(function () {
            Route::get('internship-stats', 'internshipStats');
        });
    });


Route::middleware('auth:sanctum')
    ->controller(DTRFileController::class)
    ->prefix('dtr-file')
    ->group(function () {

        Route::get('all', 'getDTRFiles');
        Route::post('upload', 'uploadDTRFile');
        Route::put('update/{id}', 'updateDTRFile');
        Route::delete('delete/{id}', 'deleteDTRFile');

    });
