<?php

use App\Http\Controllers\API\ApiAuth;
use App\Http\Controllers\API\Activities;
use App\Http\Controllers\API\CloudDrive;
use App\Http\Controllers\API\ExtraSessionController;
use App\Http\Controllers\API\ExtraClassApiController;
use App\Http\Controllers\API\ProbationersList;
use App\Http\Controllers\API\Squads;
use App\Http\Controllers\API\Probationers;
use App\Http\Controllers\API\Attendance;
use App\Http\Controllers\API\ProbationersDailyActivityController;
use App\Http\Controllers\API\Healthprofiles;
use App\Http\Controllers\API\FitnessController;
use App\Http\Controllers\API\ReportController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\PersonalNoteAPI;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Passport - Login
Route::post('/login', [ApiAuth::class, 'login']);

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});




Route::middleware('auth:api')->group(function () {
    // Get notifications for the users
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/sentnotifications', [NotificationController::class, 'sent_notifications']);
    Route::post('/createnotification', [NotificationController::class, 'store']);
    Route::post('/updatenotification', [NotificationController::class, 'update']);
    Route::post('/deletenotification', [NotificationController::class, 'destroy']);
    Route::post('/notificationmarkread', [NotificationController::class, 'notification_mark_read']);

    // To know the probationer data for that session
    Route::post('/squaddata', [Squads::class, 'verifysquaddata']);
});

Route::middleware('auth:api')->group(function () {
    // Activities
    Route::post('/activities', [Activities::class, 'ActivityList']);
    Route::post('/insertactivitydata', [Squads::class, 'ActivityData']);

    // Timetable
    Route::post('/gettimetables', [Activities::class, 'get_timetables']);
    // Today's sessions for Di
    Route::post('/gettodaystimetables', [Activities::class, 'get_todays_timetables']);


    // Attendance - get probationer attendance
    Route::post('/probationerattendance', [ProbationersDailyActivityController::class, 'Probattendance']);

    Route::post('/probationermonthlysession', [ProbationersDailyActivityController::class, 'probationermonthlysession']);

    Route::post('/rescheduledsessions', [ProbationersDailyActivityController::class, 'rescheduledSessions']);

    // Probationers profile and Squads
    Route::post('/probationerslist', [Squads::class, 'ProbationerList']);
    Route::post('/probationerprofile', [Probationers::class, 'ProbationerDetails']);
    Route::post('/insertprobationer', [Probationers::class, 'ProbationerData']);
    Route::post('/probationerimage', [Probationers::class, 'image']);  //Probationer Image POST call
    Route::post('/drillinspectorimage', [Probationers::class, 'diimage']);  //Probationer Image POST call
    Route::post('/getprobationerimage', [Probationers::class, 'getimage']);  //drill inspector Image POST call
    Route::post('/getdrillinspectorimage', [Probationers::class, 'digetimage']);  //Probationer Image POST call
    Route::post('/probationercadreupdate', [Probationers::class, 'probationercadre']);

    Route::post('/squads', [Squads::class, 'SquadList']);
    Route::post('/sessions', [Squads::class, 'sessiondata']);
    Route::post('/squaddata', [Squads::class, 'verifysquaddata']);      // To know the probationer data for that session

    // Hospitalization
    Route::post('/healthprofiles', [Healthprofiles::class, 'Probhealthprofile']);
    Route::post('/medicalexamination', [Healthprofiles::class, 'medicalexam']);


    // Fitness Evaluation
    Route::post('/fitnessevaluvation', [Healthprofiles::class, 'fitness']);
    Route::post('/get-fitness-data', [Healthprofiles::class, 'fitness_data']);
    Route::post('/viewfitnessevaluvation', [Healthprofiles::class, 'fitnessview']);




    Route::post('/generalassesment', [FitnessController::class, 'general_assesment']);
    Route::post('/generalassesmentview', [FitnessController::class, 'general_assesment_view']);
    Route::post('/generalassesment-listview', [FitnessController::class, 'general_assesment_list_view']);

    // Extra Sessions / Missed classes
    // Route::post('/extrasessions', [ExtraSessionController::class, 'index']);
    Route::post('/missedsessions-dates', [ExtraSessionController::class, 'missedsessions']);
    Route::post('/missed-class-attendance', [ExtraSessionController::class, 'attendance']);
    // Route::post('/getextrasessionattendance', [ExtraSessionController::class, 'get_attendance']);
    Route::post('/get-missed-class-attendance', [ExtraSessionController::class, 'get_attendance']);
    // Route::post('/extrasessiondata', [ExtraSessionController::class, 'sessiondata']);
    Route::post('/get-missed-classes', [ExtraSessionController::class, 'get_missed_classes']);
    // Route::post('/extrasquaddata', [ExtraSessionController::class, 'extraverifysquaddata']);
    Route::post('/missed-class-attendance-data', [ExtraSessionController::class, 'missed_class_attendance_data']);
    Route::post('/missedclass-timetables', [ExtraSessionController::class, 'missed_class_timetables']);

    Route::post('/missedclass-probationerdata', [ExtraSessionController::class, 'missed_class_probationers']);
    Route::post('/probationer-total-missedclasses', [ExtraSessionController::class, 'probationer_missed_classes']);

    // Extra classes
    Route::post('/submit-extraclass-attendance', [ExtraClassApiController::class, 'submit_attendance']);
    Route::post('/get-extraclass-attendance', [ExtraClassApiController::class, 'get_attendance']);
    // Route::post('/extraclass-data', [ExtraClassApiController::class, 'sessiondata']);
    Route::post('/get-extraclasses', [ExtraClassApiController::class, 'get_extra_classes']);
    // Route::post('/extraclass-squaddata', [ExtraClassApiController::class, 'verifysquaddata']);
    Route::post('/extraclass-attendance-data', [ExtraClassApiController::class, 'extraclass_attendance_data']);
    Route::post('/extraclass-timetables', [ExtraClassApiController::class, 'timetables']);
    Route::post('/extraclass-probationers', [ExtraClassApiController::class, 'extraclass_probationers']);

    // Report
    Route::post('/probationersinglereport', [ReportController::class, 'getprobationersinglereport']);
    Route::post('/probationermonthlyreport', [ReportController::class, 'getprobationermonthlyreport']);


});

Route::middleware('auth:api')->group(function () {
    Route::post('/prescriptions', [Healthprofiles::class, 'prescriptions']);
});

Route::middleware('auth:api')->group(function () {
    Route::post('/prescriptionpdf', [Healthprofiles::class, 'prescription_pdf']);
});

Route::middleware('auth:api')->group(function () {
    Route::post('/labreports', [Healthprofiles::class, 'labreports']);
});

Route::middleware('auth:api')->group(function () {
    Route::post('/sickreports', [Healthprofiles::class, 'sickreports']);
});

Route::middleware('auth:api')->group(function () {
    Route::post('/viewsickreports', [Healthprofiles::class, 'viewsickreports']);
});



Route::middleware('auth:api')->group(function () {
    Route::post('/get-user-mytarget-url', [ReportController::class, 'get_mytarget_set_url']);
    Route::post('/mytarget-view-mobile', [ReportController::class, 'get_mytarget_view_url']);

    Route::post('/get-statistics-url', [ReportController::class, 'get_statistics_url']);
    Route::post('/get-squad-statistics-url', [ReportController::class, 'get_squad_statistics_url']);

    /**
     * Cloud Drive Storage APIs
     */

    Route::post('/cloud-drive/get-assets', [CloudDrive::class, 'get_assets']);
    Route::post('/cloud-drive/create-folder', [CloudDrive::class, 'create_folder']);
    Route::post('/cloud-drive/upload-file', [CloudDrive::class, 'upload_file']);
    Route::post('/cloud-drive/edit-folder', [CloudDrive::class, 'edit_folder']);
    Route::post('/cloud-drive/delete-folder', [CloudDrive::class, 'destroy_folder']);
    Route::post('/cloud-drive/edit-file', [CloudDrive::class, 'edit_file']);
    Route::post('/cloud-drive/delete-file', [CloudDrive::class, 'destroy_file']);

    /**
     * Cloud Drive Storage APIs
     */
    Route::post('/personal-notes', [PersonalNoteAPI::class, 'index']);
    Route::post('/personal-notes/create', [PersonalNoteAPI::class, 'create']);
    Route::post('/personal-notes/update', [PersonalNoteAPI::class, 'update']);
    Route::post('/personal-notes/delete', [PersonalNoteAPI::class, 'destroy']);


    /*
    Password Change
    */

    Route::post('/password_change', [ApiAuth::class, 'password_change']);

});
