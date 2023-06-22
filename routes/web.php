<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ExtraSessionController;
use App\Http\Controllers\ExtraClassController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\ProbationerController;
use App\Http\Controllers\SquadController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\TimetableController;
use App\Http\Controllers\FitnessController;
use App\Http\Controllers\MedicalExamController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\MonthlyStatisticsController;
use App\Http\Controllers\GraphController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReportController;

use App\Http\Controllers\InserthospitaldataController;
use App\Http\Controllers\HealthProfileController;
use App\Http\Controllers\ReceptionistController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\DoctorMedicalExamController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PersonalNoteController;

use App\Http\Controllers\FacultyDashboardController;
use App\Http\Controllers\PbDashboardController;

use App\Http\Controllers\EventsController;



use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

/***********   Super Admin   *************/

Route::get('/', function () {
    return redirect('login');
});

Route::get('/Dashboard', function () {
    return redirect('/');
});

/** -----------------------------------------------------------------------------------
 * Role based dashboard landing page
 *
 * Roles: admin, drillinspector, receptionist, doctor, probationer
 * --------------------------------------------------------------------------------- */
Route::middleware(['auth:sanctum', 'verified'])->get('/', function () {
    // return view('home');
    if (auth()->user()->force_password_change === 1) {
        return redirect('/reset-password');
     }

    switch(Auth::user()->role) {
        case "admin":
            return view('home');
            break;
        case "superadmin":
            return view('home');
            break;
        case "drillinspector":
            echo "drillinspector";
            break;
        case "si":
            echo "si";
            break;
        case "adi":
            echo "adi";
            break;
        case "receptionist":
            return redirect("/receptionist");
            break;
        case "doctor":
            return view('doctor.home');
            break;
        case "probationer":
            return view('PbDash.home');
            break;
        case "faculty":
            return view('faculty-dashboard.home');
            break;
    }
});

/** -----------------------------------------------------------------------------------
 * Common Routes
 * --------------------------------------------------------------------------------- */
Route::middleware(['auth:sanctum', 'verified'])->group(function () {

    Route::get('/reset-password', [AuthController::class, 'index']);
    Route::post('/change-reset-password', [AuthController::class, 'change_password']);

    Route::post('/notifications', [NotificationController::class, 'store']);
    Route::post('/notification/update', [NotificationController::class, 'update']);

    Route::post('squadDropdownOptions', [SquadController::class, 'squadDropdownOptions']);   // Ajax Call for getting Select squad Dropdown Options
    Route::post('squads/view-probationers', [SquadController::class, 'view_probationer']); //Ajax call for show probationers in the squad list  with squad id
    Route::post('getprobdata', [HealthProfileController::class, 'show']);   //Ajax Call for getting basic detials of probationer

    Route::post('activitiesDropdownOptions', [SquadController::class, 'activitiesDropdownOptions']);   // Ajax Call for getting batch wise activities Dropdown Options
    Route::post('subactivitiesDropdownOptions', [SquadController::class, 'subactivitiesDropdownOptions']);   // Ajax Call for getting batch wise Sub activities Dropdown Options
    Route::post('componentsDropdownOptions', [SquadController::class, 'componentsDropdownOptions']);   // Ajax Call for getting batch wise Sub activities Dropdown Options
    Route::post('probationerDropdownOptions', [SquadController::class, 'probationerDropdownOptions']);   // Ajax Call for getting batch wise Sub activities Dropdown Options

    Route::post('view_medical_exam', [MedicalExamController::class, 'view_medical_exam']);

    Route::post('download_medical_testdata', [MedicalExamController::class,'download_medical_testdata']); // Ajax call for download the medical examination test data

    Route::post('prob_month_fitness', [FitnessController::class, 'prob_month_fitness']);
    Route::post('/fitnesswisechart', [FitnessController::class, 'fitnesswisechart']);  //fitness chart function for probationer

    //probationers List download apis

    Route::post('/probationer/ajax', [ProbationerController::class, 'ajax']);
    Route::get('/probationers/download-probationer-datasheet/{data_request}', [ProbationerController::class, 'probationer_datasheet']);
    Route::get('/probationers/sample-download-probationer-datasheet/{data_request}', [ProbationerController::class, 'probationer_sample_datasheet']);

    // Fitness Evaluation
    Route::post('/fitness/ajax', [FitnessController::class, 'ajax']);

    // Hospitalization

    Route::get('/download_medicalexamnation_test_data/{data_request}', [MedicalExamController::class,'medicalexamination_datasheet']);

    Route::get('inpatientprescription/{id}', [DoctorController::class, 'get_prob_data'])->name('inpatientprescription');
    Route::get('/prescription/{id}', [DoctorController::class, 'get_prob_data'])->name('prescription');


    Route::get('/downloads/{id}', [DoctorMedicalExamController::class, 'getDownload'])->name('download');
    Route::post('download_medical_testdata', [MedicalExamController::class,'download_medical_testdata']);
    Route::get('/download_medicalexamnation_test_data/{data_request}', [MedicalExamController::class,'medicalexamination_datasheet']);

   // Route::get('/edit_appointment/{id}', [DoctorController::class, 'get_prob_data'])->name('editprescription');

    Route::get('/reportdownload/{file_name}', [PbDashboardController::class, 'reportDownload']);
    Route::get('/userprescription/{id}', [PbDashboardController::class, 'prescription'])->name('userprescription');
    Route::get('/userprescriptions/{id}', [PbDashboardController::class, 'prescriptions'])->name('userprescriptions');

    // Statistics / Reports
    Route::get('/reports/classes-conduct-report', [ReportController::class, 'classes_conduct_report']);
    Route::get('/reports/missed-classes-report', [ReportController::class, 'missed_classes_report']);
    Route::get('/reports/extra-classes-report', [ReportController::class, 'extra_classes_report']);
    Route::get('/reports/pass-fail-report', [ReportController::class, 'pass_fail_report']);

    Route::get('/reports/download-classes-conduct-report/{data_request}', [ReportController::class, 'download_classes_conduct_report']);
    Route::get('/reports/download-missed-classes-report/{data_request}', [ReportController::class, 'download_missed_classes_report']);
    Route::get('/reports/download-missed-classes-attendance-report/{data_request}', [ReportController::class, 'download_missed_classes_attendance_report']);
    Route::get('/reports/download-extra-classes-report/{data_request}', [ReportController::class, 'download_extra_classes_report']);
    Route::get('/reports/download-extra-classes-attendance-report/{data_request}', [ReportController::class, 'download_extra_classes_attendance_report']);
    Route::get('/reports/download-pass-fail-report/{data_request}', [ReportController::class, 'download_pass_fail_report']);

    Route::post('/reports/ajax', [ReportController::class, 'ajax']);
    /*  END  */

    Route::resource('/reports', StatisticsController::class);
    Route::post('report_single_activity_view', [StatisticsController::class, 'report_single_activity_view']);  //Report View Function

    //Route::post('probationers_report_single_activity_view',[PbDashboardController::class,'probationers_report_single_activity_view']);

    Route::resource('/monthlyreports', MonthlyStatisticsController::class);
    Route::post('report_monthly_activity_view', [MonthlyStatisticsController::class, 'report_monthly_activity_view']);  //Monthly Report View Function

    Route::get('/export/{data_request}', [StatisticsController::class, 'export']);  //Export Excel Function
    Route::post('/sub_activity_count', [StatisticsController::class, 'sub_activity_count']);  //check sub_activites
    Route::post('/component_count', [StatisticsController::class, 'component_count']);  //check components
    Route::post('/monthlyexport', [MonthlyStatisticsController::class, 'monthlyexport']);  //Monthly Export Excel Function

    Route::get('/compare-probationers', [StatisticsController::class, 'compare_probationers']);
    Route::get('/statistics/download-attendance-data/{data_request}', [StatisticsController::class, 'download_statistics_datasheet']);
    Route::post('/statistics/ajax', [StatisticsController::class, 'ajax']);  // for ajax request in statistics

    // Charts
    Route::resource('/charts', GraphController::class);
    Route::get('/monthlycharts', [GraphController::class, 'monthlycharts']);
    Route::post('/probationerchart', [GraphController::class, 'probationerchart']);  //Monthly Export Excel Function
    Route::post('/probationersinglechart', [GraphController::class, 'probationersinglechart']);  //Monthly Export Excel Function
    Route::post('/probationer_monthly_avg_chart', [GraphController::class, 'probationermonthlyavgchart']);  //Monthly Export Excel Function

    Route::post('/fitnesschart', [GraphController::class, 'fitnesschart']);  //fitness chart function

    Route::post('/fitnessdownload', [GraphController::class, 'fitnessdownload']);  //fitness download function

    Route::post('/attendance/ajax', [AttendanceController::class, 'ajax']);
    Route::post('/timetables/ajax', [TimetableController::class, 'ajax']);
    Route::get('/copy_squad',[TimetableController::class, 'ajax2']);
    Route::post('copy_field',[TimetableController::class, 'ajax1']);
    Route::post('/extrasessions/ajax', [ExtraSessionController::class, 'ajax']);
    Route::post('/extraclasses/ajax', [ExtraClassController::class, 'ajax']);

    Route::post('/extraclasses/delete/{sessionId}', [ExtraClassController::class, 'delete']);
    Route::post('/missedclasses/delete/{sessionId}', [ExtraClassController::class,'missedclass_delete']);

    Route::get('/compare-fitnessanalysis', [GraphController::class, 'compare_fitnessanalysis']);
    Route::get('/fitnessanalytics', [FitnessController::class, 'index']);
    Route::post('/fitnessanalytics_prob', [FitnessController::class, 'profileview']);
    Route::get('/fitnessanalytics/fitness-datasheet/{data_request}', [FitnessController::class, 'fitness_datasheet']);

    Route::get('prob_autosuggestion', [ProbationerController::class, 'prob_autosuggestion'])->name('prob_autosuggestion');    // auto suggestions for probationer Name and roll number

    Route::get('sickreports', [MedicalExamController::class, 'sickreports']);

    Route::post('get_sick_reports', [MedicalExamController::class, 'get_sick_reports']);

    Route::get('/generalassesment', [FitnessController::class, 'general_assesment']);
    Route::get('/generalassesment/{id}', [FitnessController::class, 'general_assesment_data']);

    Route::post('insertfamily', [HealthProfileController::class, 'storedependent']);

    Route::resource('/healthprofile', HealthProfileController::class);
    Route::post('inserthealthprofiles', [HealthProfileController::class, 'store']); //Ajax call for Inserting health profiles
      Route::post('insertfamilyhistory', [HealthProfileController::class, 'store']); //Ajax call for Inserting health profiles

      Route::post('/get_inpatients_data', [DoctorMedicalExamController::class, 'get_inpatients_data']);

      Route::post('/get_patient_history', [DoctorMedicalExamController::class, 'get_patient_history']);

      Route::post('/get_patient_report', [DoctorMedicalExamController::class, 'get_patient_report']);


    Route::get('/discharge_summary/{id}', [DoctorMedicalExamController::class, 'discharge_summary']);

    Route::get('/discharge-summary',[FacultyDashboardController::class,'dischargesummarys']);
    Route::get('/probationer-discharge-summary',[FacultyDashboardController::class,'dischargesummaryss']);
    Route::get('/patient-discharge-summary',[FacultyDashboardController::class,'dischargesummarysss']);
    Route::get('/downloads/{id}', [DoctorMedicalExamController::class, 'getDownload'])->name('download');
    Route::post('/get_patient_report', [DoctorMedicalExamController::class, 'get_patient_report']);
    Route::post('/delete_dependent', [HealthProfileController::class, 'deleteprob']);

    Route::post('insert_medical_exam', [MedicalExamController::class, 'store']);
    Route::post('prob_medical_exam', [MedicalExamController::class, 'show']);

});

/** -----------------------------------------------------------------------------------
 * Notifications
 * --------------------------------------------------------------------------------- */
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/ajax', [NotificationController::class, 'ajax']);
});

//Route::post('/notifications',[NotificationController::class,'viewallnotification']);


/***********   Super Admin / Admin  *************/
// Route::middleware('can:isAdmin')->group(function () {
Route::middleware('roles:superadmin,admin')->group(function () {

    // Route::middleware(['auth:sanctum', 'verified'])->get('/', function () {
    //     return view('home');
    // })->name('home');

    // Activities
    Route::get('/activities/assign', [ActivityController::class, 'assign']);
    Route::post('/activities/ajax', [ActivityController::class, 'ajax']);
    Route::resource('/activities', ActivityController::class);
    Route::get('/activities/download-activity-datasheet/{data_request}', [ActivityController::class, 'activity_datasheet']);



    // Extra sessions
    Route::get('/timetables/missed-classes', [ExtraSessionController::class, 'extra_sessions']);
    Route::get('/timetables/create-missed-class', [ExtraSessionController::class, 'create_extra_session']);
    Route::get('/extrasessions/extrasession-datasheet/{data_request}', [ExtraSessionController::class, 'extrasession_datasheet']);

    // Extra classes
    Route::get('/timetables/extra-classes', [ExtraClassController::class, 'extra_classes']);
    Route::get('/timetables/create-extra-class', [ExtraClassController::class, 'create_extra_class']);
    Route::get('/extraclasses/extraclass-datasheet/{data_request}', [ExtraClassController::class, 'extraclass_datasheet']);

    // Timetable Routes
    Route::get('/timetables/download-timetable-datasheet/{data_request}', [TimetableController::class, 'timetable_datasheet']);
    Route::resource('/timetables', TimetableController::class);


    // Attendance Routes
    Route::get('/attendance/manual', [AttendanceController::class, 'manual_attendance']);
    Route::get('/attendance/monthly-report', [AttendanceController::class, 'monthly_report']);
    Route::get('/attendance/monthly-sessions', [AttendanceController::class, 'monthly_sessions']);
    Route::get('/attendance/missed-sessions', [AttendanceController::class, 'missed_sessions']);

    Route::post('/attendance/monthly-report-download', [AttendanceController::class, 'monthly_report_download']);
    Route::post('/attendance/monthly-sessions-download', [AttendanceController::class, 'monthly_sessions_download']);
    Route::post('/attendance/missed-sessions-download', [AttendanceController::class, 'missed_sessions_download']);

    Route::resource('/attendance', AttendanceController::class);

    Route::resource('/staffs', StaffController::class);

    Route::resource('/probationers', ProbationerController::class);

    Route::get('/probationers/delete/{id}', 'ProbationerController@destroy')
     ->name('ProbationerController.destroy');

    Route::resource('/squads', SquadController::class);

    Route::resource('/batch', BatchController::class);

    Route::resource('/addmedicinedata', InserthospitaldataController::class);


    Route::resource('/medicalexam', MedicalExamController::class);

    ///Events module
    Route::resource('/events', EventsController::class);
    Route::get('/eventslist', [EventsController::class, 'event_list']);
    Route::post('/events/store', [EventsController::class, 'store']);
    Route::post('/events/scheduler', [EventsController::class, 'scheduler']);
    Route::post('/events/updateSchedule', [EventsController::class,'updateSchedule']);
    Route::get('/addSchedule/{id}', [EventsController::class, 'addSchedule']);
    Route::get('/editSchedule/{id}', [EventsController::class, 'editSchedule']);
    Route::get('/viewscheduler/{id}', [EventsController::class, 'viewscheduler']);
    Route::get('/result', [EventsController::class, 'get_scheduled_events']);
    Route::get('/uploadresults/{id}', [EventsController::class, 'upload_results']);
    Route::get('/viewresults/{id}', [EventsController::class, 'viewresults']);

    Route::post('/events-ajax', [EventsController::class, 'ajax']);
    Route::get('/events/download-scheduled-data/{data_request}', [EventsController::class, 'download_scheduled_sample_datasheet']);




    Route::get('event', function () {
        return view('events.event');
    });

    Route::get('event-view', function () {
            return view('events.reportsview');
        });
    Route::get('uploadresult', function () {
            return view('events.uploadresult');
        });
    Route::get('eventSchedule', function () {
            return view('events.eventSchedule');
        });

    // Fitness Evaluation



    Route::post('insertfitnessevaluvation', [FitnessController::class, 'insertfitnessevaluation']);


    Route::get('stafflist', [StaffController::class, 'show']);

    Route::get('probationerlist', [ProbationerController::class, 'show']);

    Route::get('/probationerprofile/{id}', [ProbationerController::class, 'profileview']);




    Route::get('squadlist', [SquadController::class, 'show']);
    Route::post('getsquaddata', [SquadController::class, 'squaddata']);   // Ajax Call for getting the batchwise squad data

    Route::post('batchwiseprob', [ProbationerController::class, 'batchwiseprob']);   // Ajax Call for getting Select squad Dropdown Options
    Route::post('/probationers/deleteprobationers/{id}',[ProbationerController::class,'delete_probationer']);

  //  Route::post('insertfamily', [HealthProfileController::class, 'storedependent']); //Ajax call for Inserting dependents

    Route::post('/squads/deletesquad/{id}', [SquadController::class, 'delete_squad']);

    Route::post('/squads/delete', [SquadController::class, 'delete_probationer']);

    Route::post('/batch_timetable', [TimetableController::class, 'batch_timetable']);

    //Route::post('/probationers/deleteprobationers/{id}',[ProbationerController::class,'delete_probationer']);


    Route::get('/report_single_activity_view', function () {
        return redirect('reports');
    });
    Route::get('/report_monthly_activity_view', function () {
        return redirect('monthlyreports');
    });




    Route::post('/extraclasses/delete/{sessionId}', [ExtraClassController::class, 'delete']);
    Route::post('/missedclasses/delete/{sessionId}', [ExtraClassController::class,'missedclass_delete']);



    // Route::get('/hospitalization/adddata', function () {
    //     return view('hospitalization.adddata');
    // });

    // return redirect("/login");
});
/*********** End Super Admin   *************/

/** ---------------------------------------------------------------
 * Faculty
 * ------------------------------------------------------------ */
Route::middleware('can:isFaculty')->group(function () {
    Route::get('/squad-list', [FacultyDashboardController::class, 'squads']);
    Route::get('/staff-list', [FacultyDashboardController::class, 'staffs']);
    Route::get('/probationer-list', [FacultyDashboardController::class, 'probationers']);

    Route::get('/activity-list', [FacultyDashboardController::class, 'activities']);
    Route::get('/activity-view/{id}', [FacultyDashboardController::class, 'activity_show']);

    // Atendance
    Route::get('/attendance-monthly-report', [FacultyDashboardController::class, 'monthly_attendance_report']);
    Route::get('/attendance-monthly-sessions', [FacultyDashboardController::class, 'monthly_sessions']);
    Route::get('/attendance-missed-sessions', [FacultyDashboardController::class, 'missed_sessions']);

    // Timetables
    Route::get('/timetables-view', [FacultyDashboardController::class, 'timetables_view']);
    Route::get('/timetables-missed-classes', [FacultyDashboardController::class, 'timetables_extra_sessions']);

    // Hospotalization
    Route::get('/patient-list', [FacultyDashboardController::class, 'patient_list']);
    Route::get('/health-profile', [FacultyDashboardController::class, 'health_profile']);
    Route::get('/medical-records', [FacultyDashboardController::class, 'medical_records']);
    Route::get('/medical-examination', [FacultyDashboardController::class, 'medical_examination']);

    // Fitness Evaluation
    Route::get('/fitness-evaluation', [FacultyDashboardController::class, 'fitness_evaluation']);
    Route::get('/fitness-evaluation/{id}', [FacultyDashboardController::class, 'fitness_evaluation_data']);

    Route::get('/general-assessment', [FacultyDashboardController::class, 'general_assesment']);
    Route::get('/general-assessment/{id}', [FacultyDashboardController::class, 'general_assesment_data']);

    Route::post('/faculty-ajax', [FacultyDashboardController::class, 'ajax']);


});

/** ---------------------------------------------------------------
 * Super Admin | Admin | Faculty
 * ------------------------------------------------------------ */
Route::middleware('roles:superadmin,admin,faculty')->group(function () {

    // Personal Notes
    Route::post('notes/ajax', [PersonalNoteController::class, 'ajax']);
    Route::resource('/notes', PersonalNoteController::class);
});


/** ---------------------------------------------------------------
 * Probationer
 * ------------------------------------------------------------ */
Route::middleware('can:isProbationer')->group(function () {

    // Route::get('/user-dashboard', function () {
    //     return view('PbDash.dashboard');
    // })->name("user-dashboard");

    Route::resource('/pbdash',PbDashboardController::class);
    Route::get('/user-mytarget', [PbDashboardController::class, 'user_mytarget']);
    Route::get('/user-mytarget/{activity}', [PbDashboardController::class, 'mytargetSubactivity']);
    Route::get('/mytarget-view', [PbDashboardController::class, 'mytarget_view']);
    Route::post('/user-mytarget/ajax', [PbDashboardController::class, 'ajax']);

    Route::get('/user-attendance', [PbDashboardController::class, 'user_attendance']);
    Route::get('/user-timetable', [PbDashboardController::class, 'user_timetable']);
    Route::get('/view-extrasession', [PbDashboardController::class, 'extrasession_view']);

    Route::get('/user-hospitalization', [PbDashboardController::class, 'user_hospitalization']);
    Route::get('/user-healthprofiles', [PbDashboardController::class, 'user_healthprofiles']);
    Route::get('/user-fitnessanalytics', [PbDashboardController::class, 'user_fitnessanalytics']);
    // Route::get('/user-general-assesment-data', [PbDashboardController::class, 'general_assesment']);
    Route::get('/user-statistics', [PbDashboardController::class, 'user_statistics']);
    Route::get('/user-monthlyreport', [PbDashboardController::class, 'user_monthly_statistics']);
    Route::post('/user-ajax', [PbDashboardController::class, 'ajax']);

    Route::post('single_activity_reports', [PbDashboardController::class, 'single_activity_reports']);
    Route::post('monthly_activity_reports', [PbDashboardController::class, 'monthly_activity_reports']);
    Route::post('single_activity_reports_export', [PbDashboardController::class, 'single_activity_reports_export']);
    Route::post('monthly_activity_reports_export', [PbDashboardController::class, 'monthly_activity_reports_export']);

    Route::get('/updateprobationerprofile/{id}', [PbDashboardController::class, 'profileview']);

    Route::get('/single_activity_reports', function () {
        return redirect('user-statistics');
        });
    Route::get('/monthly_activity_reports', function () {
        return redirect('user-monthlyreport');
    });

    return redirect("/login");
});

/** ---------------------------------------------------------------------------
 * Probationers mobile pages Authenticated with Access Token
 * ------------------------------------------------------------------------- */
Route::get('/user-mytarget-mobile', [PbDashboardController::class, 'user_mytarget_mobile']);
Route::get('/mytarget-view-mobile', [PbDashboardController::class, 'mytarget_view_mobile']);

Route::get('/user-statistics-mobile', [PbDashboardController::class, 'user_statistics_mobile']);
Route::get('/user-squad-statistics-mobile', [PbDashboardController::class, 'user_squad_statistics_mobile']);

Route::get('/user-probationer-attendance-mobile', [PbDashboardController::class, 'user_probationer_attendance_mobile']);


/***********   Hospital Dashboard   *************/

Route::middleware('can:isReceptionist')->group(function () {


    Route::resource('/receptionist', ReceptionistController::class);

    Route::post('/getprobationerdata', [ReceptionistController::class, 'get_prob_data']);   // Ajax call for get only one probationer data

    Route::post('/gettodayappointment', [ReceptionistController::class, 'gettodayappointment']);   // Ajax call for get probationer appointment data

    Route::get('labreports', [ReceptionistController::class, 'labreports']);

    Route::post('/fileupload', [ReceptionistController::class, 'fileUploadPost']);

    Route::get('/health-profiles', function () {
        return view('receptionist.healthprofiles');
    });

    return redirect("/login");

});

Route::middleware('roles:doctor')->group(function () {
    Route::resource('/doctor', DoctorController::class);

    Route::post('insertgenralinfo', [DoctorController::class, 'show']);   //Ajax Call for getting basic detials of probationer

    Route::post('insertvitalsign', [DoctorController::class, 'insertvitalsign']);   //Ajax Call for inserting the probationer vital signs

    Route::post('insertprescription', [DoctorController::class, 'insertprescription']);   //Ajax Call for inserting the probationer vital signs
    Route::post('updatepresciption', [DoctorMedicalExamController::class, 'updatepresciption']);   //Ajax Call for edit the probationer vital signs

    Route::post('insertinpatientprescription', [DoctorController::class, 'insertinpatientprescription']);   //Ajax Call for inserting the probationer vital signs

    Route::post('admitpatient', [DoctorController::class, 'admitpatient']);   //Ajax Call for admitting the in patients

    Route::post('dischargepatient', [DoctorController::class, 'dischargepatient']);   //Ajax Call for admitting the in patients

    Route::get('inpatientlist', [DoctorController::class, 'inpatientlist']);

    Route::get('/download/{id}', [DoctorController::class, 'getDownload'])->name('download');

    Route::resource('/medicalexams', DoctorMedicalExamController::class);

    Route::post('prob_medical_exams', [DoctorMedicalExamController::class, 'show']);

    Route::post('insert_medical_exams', [DoctorMedicalExamController::class, 'store']);

    Route::resource('/adddatas', InserthospitaldataController::class);

    Route::post('adddatas', [InserthospitaldataController::class, 'doctorstore'])->name('adddata.doctorstore');

    Route::get('autocomplete', [DoctorController::class, 'autocomplete'])->name('autocomplete');

    Route::get('labreportautocomplete', [DoctorController::class, 'labreportautocomplete'])->name('labreportautocomplete');

    Route::post('get_medicines_data', [DoctorMedicalExamController::class, 'get_medicines_data']);

    Route::get('medicines/{id}/edit/', [DoctorMedicalExamController::class, 'get_medicine']);

    Route::get('labs/{id}/edit/', [DoctorMedicalExamController::class, 'get_lab']);

    Route::post('update_medicines', [DoctorMedicalExamController::class, 'update_medicines']);

    Route::post('update_labs', [DoctorMedicalExamController::class, 'update_labs']);

    Route::post('/delete_medicine', [DoctorMedicalExamController::class, 'delete_medicine']);

    Route::post('/delete_lab', [DoctorMedicalExamController::class, 'delete_lab']);

    Route::post('/inpatients_history', [DoctorMedicalExamController::class, 'inpatients_history']);



    Route::get('/appointment_summary/{id}', [DoctorMedicalExamController::class, 'appointment_summary']);

    Route::post('/insert_inpatientdischarge_medication', [DoctorMedicalExamController::class, 'insert_inpatientdischarge_medication']);

    Route::get('/edit_prescriptions/{id}', [DoctorMedicalExamController::class, 'editprescription']);

    //Route::get('sickreportss', [MedicalExamController::class, 'sickreportss']);




    Route::get('/adddata', function () {
        return view('doctor.adddata');
    });
      Route::get('/viewdata', function () {
        return view('doctor.viewdata');
    });
    Route::get('/viewlabreports', function () {
        return view('doctor.viewlabreports');
    });
    Route::get('/dischargesummary', function () {
        return view('doctor.dischargesummary');
    });

    Route::get('/sickreport', function () {
        return view('doctor.sickreport');
    });

    Route::get('/healthprofiles', function () {
        return view('doctor.healthprofiles');
    });

    return redirect("/login");
});

/***********  End Hospital Dashboard   *************/
