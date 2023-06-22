<?php

namespace App\Http\Controllers;

use App\Models\probationer;
use App\Models\ExtraSessionmeta;
use App\Models\Activity;
use App\Models\User;
use App\Models\Batch;
use App\Models\probationersMytarget;
use App\Models\Squad;
use App\Models\ProbationersDailyactivityData;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

use Illuminate\Http\Request;
use Response;

use PDF;

class PbDashboardController extends Controller
{
    public function probationer($user_email)
    {
        $probationer = DB::table('probationers')
            ->where('Email', $user_email)->get()->first();
        return $probationer;
    }

    // public function edit(probationer $probationer)
    // {
 
    //     $probationer = Probationer::where('probationers.id', $probationer->id)->leftJoin('batches', 'batches.id', '=', 'probationers.batch_id')->select('probationers.*', 'batches.BatchName')->first();
    //     $batches = Batch::all();
    //     return view('PbDash.updateprofile', compact('probationer', 'batches'));
    // }

    public function update(Request $request, Probationer $probationer)
    {
            
        try
        {

            // User::where('id', $request->pid)->update([
            //     'name' => $request->Name,
            //     'email' => $request->Email,
            //     'password' => Hash::make($password),
            //     'Dob' => $request->Dob,
            //     'MobileNumber' => $request->MobileContactNumber,
            //     'role' => "Probationer",
            // ]);

            Probationer::where('user_id', $request->pid)->update([
                'Religion' => $request->religion,
                'Category' => $request->category,
                'MartialStatus' => $request->maritalstatus,
                'MotherName' => $request->mothersname,
                'Moccupation' => $request->m_occupation,
                'FatherName' => $request->fathersname,
                'Foccupation' => $request->f_occupation,
                'Stateofdomicile' => $request->stateofdomicile,
                'Hometown' => $request->hometown,
                'District' => $request->district,
                'HomeAddress' => $request->homeaddress,
                'State' => $request->state,
                'Pincode' => $request->pincode,
                'phoneNumberStd' => $request->phonenowithstdcode,
                'OtherState' => $request->whichstateinindia,
                'EmergencyName' => $request->ename,
                'EmergencyPhone' => $request->epnostd,
                'EmergencyEmailId' => $request->eemailid,
                'EmergencyAddress' => $request->eaddress,
            ]);

            return redirect('/updateprobationerprofile/'.$request->pid)
                    ->with('success', 'Profile updated successfully');
        }
                    catch (\Illuminate\Database\QueryException $e) {
                        $errorCode = $e->errorInfo[1];
                        if ($errorCode == '1062') {
                            return redirect('/updateprobationerprofile/'.$request->pid)
                                ->with('delete', 'Probationer Email or Mobile Number Already Registered.');
                        }
                    }
    }

    public function profileview(Request $request)
    {

        $get_probationer = Probationer::where('user_id', $request->id)->first();
        return view('PbDash.updateprofile', compact('get_probationer'));
    }


    /**
     * Display Attendance Report.
     *
     * @return \Illuminate\Http\Response
     */
    public function user_mytarget()
    {
        $page_title = 'My Target';
        $user_id = Auth::id();
        $batchId = probationer::where('user_id', $user_id)->value('batch_id');
        $activities = Activity::where('type', 'activity')->where('batch_id', $batchId)->get();

        return view('PbDash.mytarget', compact('page_title', 'activities', 'user_id'));
    }

    public function user_mytarget_mobile(Request $request)
    {
        $page_title = 'My Target';

        $user_id        = $request->id;
        $access_token   = urldecode( $request->access_token );

        if(validate_webpage_access_token($user_id, $access_token)) {
            if(!Auth::check()) {
                Auth::loginUsingId($user_id, false);
            }
            session(['app_view' => true]);
            $page_title = 'View Target';
            return view('PbDash.mytarget', compact('page_title', 'user_id'));
        }

        return "Unauthorized access";
    }


    public function mytarget_view()
    {
        $page_title = 'View Target';
        return view('PbDash.mytarget-view', compact('page_title'));
    }

    /**
     * API: mytarget-view-mobile
     *
     * Description: To view my targets on mobile app
     */
    public function mytarget_view_mobile(Request $request)
    {
        $user_id        = $request->id;
        $probationer_id = $request->pid;
        $access_token   = urldecode( $request->access_token );

        if(validate_webpage_access_token($user_id, $access_token)) {
            if(!Auth::check()) {
                Auth::loginUsingId($user_id, false);
            }
            session(['app_view' => true]);

            $page_title = 'View Target';
            return view('PbDash.mytarget-view', compact('page_title', 'probationer_id'));
        }

        return "Unauthorized access";
    }

    public function general_assesment()
    {
        $page_title = 'General Assesment';
        $Probationer = probationer_data();
        return view('PbDash.general-assesment-data', compact('page_title', 'Probationer'));
    }

    public function prescription($id)
    {
        $Appointments = DB::table('appoinments')->where('id', $id)->first();

        $user = Auth::user();
        if($user->role === 'probationer') {
            $probationer_id = probationer::where('user_id', $user->id)->value('id');
        } else {
            $probationer_id    = $Appointments->Probationer_Id;
        }

        $Prescriptions = DB::table('probationer_prescription')
            ->where('probationer_id', $probationer_id)
            ->where('appointment_id', $id)
            ->get();

        $pdf_title = "Prescription-".$id;
        $data = [
            'pdf_title' => $pdf_title,
            'Probationer' => $probationer_id,
            'Appointments' => $id,
            'Prescriptions' => $Prescriptions,
            'id' => $id
        ];

        $config = [
        'title' => $pdf_title,
        'format' => 'A4',
        'orientation' => 'P',
        'margin_left' => 7,
        'margin_right' => 7,
        'margin_top' => 7,
        'margin_bottom' => 7,
        ];

        $pdf = PDF::loadView('PbDash.prescription-pdf', $data, [], $config);
        return $pdf->download("{$pdf_title}.pdf");

    }

    public function prescriptions($id)
    {
        $Appointments = DB::table('appoinments')->where('id', $id)->first();

        $user = Auth::user();
        if($user->role === 'probationer') {
            $probationer_id = probationer::where('user_id', $user->id)->value('id');
        } else {
            $probationer_id    = $Appointments->Probationer_Id;
        }

        $dischargesummary = DB::table('in_patients')
            ->where('probationer_id', $probationer_id)
            ->where('appointment_id', $id)
            ->get();

        $pdf_title = "Prescription-".$id;
        $data = [
            'pdf_title' => $pdf_title,
            'Probationer' => $probationer_id,
            'Appointments' => $id,
            'Prescriptions' => $dischargesummary,
            'id' => $id
        ];

        $config = [
        'title' => $pdf_title,
        'format' => 'A4',
        'orientation' => 'P',
        'margin_left' => 7,
        'margin_right' => 7,
        'margin_top' => 7,
        'margin_bottom' => 7,
        ];

        $pdf = PDF::loadView('PbDash.prescription-pdf', $data, [], $config);
        return $pdf->download("{$pdf_title}.pdf");

    }

    /**
     * Display Attendance Report.
     *
     * @return \Illuminate\Http\Response
     */
    public function user_attendance()
    {
        $page_title = 'Attendance';

        return view('PbDash.attendance', compact('page_title'));
    }

    /**
     * Display Timetable
     *
     * @return \Illuminate\Http\Response
     */
    public function user_timetable()
    {
        $page_title = 'Timetable';

        return view('PbDash.timetable', compact('page_title'));
    }

    public function extrasession_view()
    {
        $page_title = 'Extra Session';
        return view('PbDash.view-extrasession', compact('page_title'));
    }

    /**
     * Hospitalization
     *
     * @return \Illuminate\Http\Response
     */
    public function user_hospitalization()
    {
        $page_title = 'Hospitalization';

        return view('PbDash.hospitalization', compact('page_title'));
    }

    /**
     * Healthprofiles
     *
     * @return \Illuminate\Http\Response
     */
    public function user_healthprofiles()
    {
        $page_title = 'Health Profiles';
        $user_id = Auth::id();
        $pb_id = \App\Models\probationer::where('user_id', $user_id)->value('id');
        $pb_familyhist = DB::table('probationer_family_history')->where('Probationer_Id', $pb_id)->first();



        return view('PbDash.healthprofiles', compact('page_title', 'pb_familyhist'));
    }

    /**
     * Fitness Analytics
     *
     * @return \Illuminate\Http\Response
     */
    public function user_fitnessanalytics()
    {
        $page_title = 'Fitness Analytics';

        return view('PbDash.fitnessanalytics', compact('page_title'));
    }

    /**
     * Statistics
     *
     * @return \Illuminate\Http\Response
     */
    public function user_statistics()
    {
        $page_title = 'Statistics';
        $user_id = Auth::id();
        $probationer_id = probationer::where('user_id', $user_id)->value('id');
        return view('PbDash.statistics', compact('page_title', 'probationer_id'));
    }

    /**
     * Statistics
     *
     * @return \Illuminate\Http\Response
     */
    public function user_statistics_mobile(Request $request)
    {
        $user_id    = $request->id;
        $probationer_id = $request->probationerid;
        $access_token   = urldecode( $request->access_token );
        if(validate_webpage_access_token($user_id, $access_token)) {
            if(!Auth::check()) {
                Auth::loginUsingId($user_id, false);
            }
            session(['app_view' => true]);

            $user = Auth::user();

            if( !in_array($user->role, ['probationer', 'drillinspector', 'si', 'adi']) ) {
                abort(403, 'Unauthorized user role.');
            }

            $page_title     = 'Statistics';
            $batch_id = probationer::where('id', $probationer_id)->value('batch_id');
            $activities =  Activity::where('batch_id', $batch_id)->where('type', 'activity')->get();
          //  return view('graphs.graph',compact('batches', 'role'));
           // return view('PbDash.statistics', compact('page_title', 'probationer_id'));
           $squad_id = '';
           return view('PbDash.charts', compact('page_title', 'probationer_id', 'activities', 'squad_id'));
        }

        return "Unauthorized access";
    }


    public function user_squad_statistics_mobile(Request $request)
    {

        $user_id        = $request->id;
        $squad_id        = $request->squadid;
        $probationer_id = '';
        $batch_id = Squad::where('id', $squad_id)->value('Batch_Id');
        $activities =  Activity::where('batch_id', $batch_id)->where('type', 'activity')->get();
        $access_token   = urldecode( $request->access_token );
        if(validate_webpage_access_token($user_id, $access_token)) {
            if(!Auth::check()) {
                Auth::loginUsingId($user_id, false);
            }
            session(['app_view' => true]);


            $user = Auth::user();

            if( !in_array($user->role, ['probationer', 'drillinspector', 'si', 'adi']) ) {
                abort(403, 'Unauthorized user role.');
            }

            $page_title     = 'Squad chart';
            return view('PbDash.charts', compact('page_title', 'squad_id', 'activities', 'probationer_id'));
        }

        return "Unauthorized access";
    }


    public function user_monthly_statistics()
    {
        $page_title = 'Monthly Report';
        $user_id = Auth::id();
        $probationer_id = probationer::where('user_id', $user_id)->value('id');
        return view('PbDash.monthlycharts', compact('page_title', 'probationer_id'));
    }

    public function mytargetSubactivity($activity_id) {
        return view('PbDash.mytarget-subactivity', compact('activity_id'));
    }

        /**
     * Process probationer monthly attendnce in mobile
     *
     * @param  \Illuminate\Http\Request  $request
     */

    public function user_probationer_attendance_mobile(Request $request)
    {

        $user_id        = $request->id;
        $access_token   = urldecode( $request->access_token );

        if(validate_webpage_access_token($user_id, $access_token)) {
            if(!Auth::check()) {
                Auth::loginUsingId($user_id, false);
            }
            session(['app_view' => true]);

            $page_title = 'View Target';
            return view('PbDash.attendance-table-mobile', ['request' => $request]);
        }

        return "Unauthorized access";
    }


    /**
     * Process ajax requests.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function ajax(Request $request)
    {
        $requestName    = $request->requestName;

        // Get Attendance Table
        if ($requestName === "get_attendance_table") {
            return view('PbDash.attendance-table', ['request' => $request]);
        }

        // Get Monthly Sessions Table

        if ($requestName === "get_monthlysession_data") {
            return view('PbDash.attendance-monthlysession', ['request' => $request]);
        }

         // Get Missed Sessions Table

         if ($requestName === "get_missedsession_data") {
            return view('PbDash.attendance-missedsession', ['request' => $request]);
        }

        // Get Attendance Table
        if ($requestName === "get_timetable") {
            return view('PbDash.timetable-view', ['request' => $request]);
        }

        // Get Attendance Table
        if ($requestName === "myTargetSubmit") {

            $targets        = $request->targetInput;
            $activityType   = $request->activityType;
            $activityId     = $request->activityId;
            $subactivityId  = $request->subactivityId;

            $user_id = Auth::id();
            $probationer_id = probationer::where('user_id', $user_id)->value('id');

            $month  = date('Y-m-d');



            foreach ($targets as $id => $goal) {

                // $goals  .= $activity_id .' - '. $goal .'<br />';
                $activity_id    = 0;
                $subactivity_id = 0;
                $component_id   = 0;
                if($activityType[$id] === "activity") {
                    $activity_id    = $id;
                } elseif($activityType[$id] === "subactivity") {
                    $activity_id    = $activityId[$id];
                    $subactivity_id = $id;
                } elseif($activityType[$id] === "component") {
                    $activity_id    = $activityId[$id];
                    $subactivity_id = $subactivityId[$id];
                    $component_id   = $id;
                }

                {

                    if($goal != '')
                    {
                        //return $month;
                        ProbationersMytarget::updateOrCreate(
                            [
                                'probationer_id'    => $probationer_id,
                                'activity_id'       => $activity_id,
                                'subactivity_id'    => $subactivity_id,
                                'component_id'      => $component_id,
                                'month'         => $month,
                            ],
                            [
                                'goal'  => $goal,
                            ]
                        );
                     }
                }

            }

            return json_encode([
                'status' => 'success',
                'message' => "Saved successfully.",
            ]);
        }

         // Get Attendance Table
         if ($requestName === "get_medexam_data") {
            $month_year = $request->month_year;
            $month_year = explode('-', $month_year);

            if(count($month_year) === 2) {
                $month  = $month_year[0];
                $year   = $month_year[1];
            } else {
                return false;
            }

            $user_id = Auth::id();
            $probationer_id = probationer::where('user_id', $user_id)->value('id');
            $medical_exam = DB::table('probationer_medical_exam')
                ->where('Probationer_Id', $probationer_id)
                ->whereMonth('probationer_medical_exam.date', $month)
                ->whereYear('probationer_medical_exam.date', $year)
                ->first();

            if(!empty($medical_exam)) {
                return view('PbDash.medicalexam-data', compact('medical_exam'));
            } else {
                echo "No medical record found";
            }

            return false;
         }

         if ($requestName === "get_subactivities_options") {
            $activity_id = $request->Activity_Id;

            if( !empty($activity_id) ) {
                $subactivities = \App\Models\Activity::where('parent_id', $activity_id)->where('type', 'subactivity')->get();

                if( count($subactivities) > 0 ) {
                    echo "<option value=\"\">Select Sub Activity</option>";

                    foreach($subactivities as $subactivity) {
                        $subactivity_id   = $subactivity->id;
                        $subactivity_name   = $subactivity->name;

                        echo "<option value=\"{$subactivity_id}\">{$subactivity_name}</option>";
                    }
                } else {
                    echo "<option value=\"\">-- No  SubActivity --</option>";
                }
            } else {
                echo "<option value=\"\">-- Activity Id Missing --</option>";
            }
        }

         // Get Set My Target Table
         if ($requestName === "view_mytarget_data") {


            $activityId    = $request->activity_id;
            $month_year = $request->month_year;


            $user_id = Auth::id();
            $probationer_id = probationer::where('user_id', $user_id)->value('id');
            $targets    = ProbationersMytarget::where('Probationer_Id', $probationer_id)
                ->where('activity_id', $activityId)
                ->orderBy('component_id')
                ->get();

            $data   = "";
            if(count($targets)>0) {
                foreach($targets as $target) {
                    $activity_id    = $target->activity_id;
                    $subactivity_id = $target->subactivity_id;
                    $component_id   = $target->component_id;

                    $goal   = $target->goal;
                    $date   = date('m-Y', strtotime($target->month));


                    $activity_name  = activity_name($activity_id);

                    $activity_name  = "";
                    if(!empty($activity_id)) {
                        $activity_name  = activity_name($activity_id);
                    }


                    $subactivity_name  = "-";
                    if(!empty($subactivity_id)) {
                        $subactivity_name = activity_name($subactivity_id);
                    }

                    $component_name = "-";

                    if(!empty($component_id)) {
                        $component_name = activity_name($component_id);
                    }


                    $data   .= <<<EOL
                        <tr>
                            <td>{$activity_name}</td>
                            <td>{$subactivity_name}</td>
                            <td>{$component_name}</td>
                            <td>{$date}</td>
                            <td>{$goal}</td>
                        </tr>
                    EOL;
                }

                echo $data;
            } else {
                echo "No record found";
            }

            return false;
         }


/** -------------------------------------------------------------------
 * Get Fitness Evaluation Data
 * ----------------------------------------------------------------- */

        if ($requestName === "get_fitness_data") {
            $month_year    = $request->month_year;
            $month_ar = explode('-', $month_year);
            $fitness_month  = intval($month_ar[0]);
            $fitness_year  = $month_ar[1];

            $user_id = Auth::id();
            $probationer_id = probationer::where('user_id', $user_id)->value('id');
            $fitness_data = DB::table('fitness_meta')    
                ->where('probationer_id', $probationer_id)
                ->whereMonth('date', $fitness_month)
                ->whereYear('date', $fitness_year)
                ->groupBy('fitness_name')
                ->orderBy('id', 'DESC')
                ->get();

                // return json_encode($fitness_data);
               // echo $fitness_data;exit;

                if(!empty($fitness_data)) {
                    return view('PbDash.fitnessanalytics-data', ['fitness_data' => $fitness_data]);
                } else {
                    echo "No Record Found.";
                }
        }


/** -------------------------------------------------------------------
 * Get Extra Session Data
 * ----------------------------------------------------------------- */

        if ($requestName === "get_extrasession_data") {

            $day = $request->day;

            $user_id = Auth::id();
            $probationer_id = probationer::where('user_id', $user_id)->value('id');
            $extra_sessions    = \App\Models\ExtraSession::whereDate('extra_sessions.date', $day)
                ->where('extra_sessionmetas.probationer_id', $probationer_id)
                ->leftJoin('extra_sessionmetas', 'extra_sessions.id', '=', 'extra_sessionmetas.extra_session_id' )
                ->get();

            $data   = "";
            if(count($extra_sessions)>0) {
                $i=1;

                foreach($extra_sessions as $extra) {
                    $activity_id    = $extra->activity_id;
                    $subactivity_id = $extra->subactivity_id;
                    $time      = date('h:i A', $extra->session_start);

                    $activity_name  = activity_name($activity_id);
                    $subactivity_name  = "";

                    if(!empty($subactivity_id)) {
                        $subactivity_name  = activity_name($subactivity_id);
                        $subactivity_name  = "({$subactivity_name})";
                    }

                    $data   .= <<<EOL
                        <tr>
                            <td>{$i}</td>
                            <td>{$activity_name} {$subactivity_name}</td>
                            <td>{$time}</td>
                        </tr>
                    EOL;
                    $i++;
                }

                echo $data;
            } else {
                echo "No record found";
            }

            return false;
        }

    }

    // public function getDownload($id){

    //     $file = public_path()."/uploads/".$id;
    //     $headers = array('Content-Type: application/pdf',);
    //     return Response::download($file, 'info.pdf',$headers);
    // }

    public function reportDownload($file_name){

        $file = public_path()."/uploads/".$file_name;
        $headers = array('Content-Type: application/pdf',);
        return Response::download($file, 'report.pdf',$headers);
    }


    public function single_activity_reports(Request $request)
    {
            // echo $request->probationer_id;exit;
            // if (isset($request->probationer_id)) { $probationer_Id  = $request->probationer_id;}
            if (isset($request->activity_id)) { $activity_id   = $request->activity_id;}
            if (($request->sub_activity_id != '')) { $sub_activity_id   = $request->sub_activity_id;}else{$sub_activity_id='0';}
            if (($request->component != '')) { $component_id = $request->component;}else{$component_id='0';}

            $sub_activity_id = sanitize_activity_id($sub_activity_id);
            $component_id = sanitize_activity_id($component_id);

            $daterange = $request->date;
            if (empty($daterange)) {
                $errors[] = "Select Date Range.";
            } else {
                $daterangeArray = explode(" - ", $daterange);
                if(count($daterangeArray) !== 2) {
                    $errors[] = "Invalid Date Range (Expected: DD/MM/YYYY - DD/MM/YYYY).";
                } else if(!isValidDate($daterangeArray[0], 'd/m/Y') || !isValidDate($daterangeArray[1], 'd/m/Y')) {
                    $errors[] = "Invalid Date Format (Expected: DD/MM/YYYY - DD/MM/YYYY).";
                } else {
                    $from = convert_date($daterangeArray[0], 'd/m/Y');
                    $to = convert_date($daterangeArray[1], 'd/m/Y');
                }
            }
            // $month = $m_and_y[0];
            // $year = $m_and_y[1];
            $dt = array();
            $data = array();
            $activity_unit ='';

            $user_id = Auth::id();
            $probationer_id = probationer::where('user_id', $user_id)->value('id');
            //echo $probationer_id;exit;
            $probationer_name = probationer::where('user_id', $user_id)->value('Name');
            //echo $probationer_name;exit;

            // $day_wise = DB::select("select CASE when component_id IS NOT NULL then component_id when subactivity_id IS NOT NULL THEN subactivity_id else activity_id end AS ACT_ID, date, timetable_id, activity_id, subactivity_id, component_id, id from probationers_dailyactivity_data where probationer_id = $probationer_id and activity_id = $activity_id and subactivity_id = $sub_activity_id and component_id = $component_id and month(date)= $month AND year(date) = $year order by date ASC");

            $day_wises = ProbationersDailyactivityData::select(DB::raw("CASE when component_id IS NOT NULL then component_id when subactivity_id IS NOT NULL THEN subactivity_id else activity_id end AS ACT_ID"), 'date', 'timetable_id', 'activity_id', 'subactivity_id', 'component_id', 'id')
            ->where('probationer_id', $probationer_id)
            ->where('activity_id', $activity_id)
            ->whereBetween('date', [$from, $to])
            ->orderBy('date', 'asc')
            ->groupBy('ACT_ID');
            if($sub_activity_id != null)
            {
                $day_wises->where('subactivity_id', $sub_activity_id);
            }
            if($component_id != null)
            {
                $day_wises->where('component_id', $component_id);
            }

            $day_wise = $day_wises->get();

            //var_dump($day_wise);exit;
            //return json_encode($day_wise);

            if(count($day_wise) == '0')
                {
                    return '1';
                }

            foreach($day_wise as $squad)
            {
                $activity_unit = DB::table('activities')->where('activities.id', $squad->ACT_ID)->first();

                $usersexport = DB::table('probationers_dailyactivity_data')
                ->where('probationers_dailyactivity_data.activity_id', $squad->ACT_ID)
                ->orWhere('probationers_dailyactivity_data.subactivity_id', $squad->ACT_ID)
                ->orWhere('probationers_dailyactivity_data.component_id', $squad->ACT_ID)
                ->Where('probationers_dailyactivity_data.probationer_id', $probationer_id)
                ->groupBy('timetable_id')
                ->whereBetween('date', [$from, $to])
                ->orderBy('date')
                ->get()
                ->toArray();
                $data1 = array();
                $data = array();

                $dt = array();
                foreach($usersexport as $values)
                {
                    $dt[] = date('d-m', strtotime($values->date));
                    if($values->attendance == 'P' || $values->attendance == 'MDO' || $values->attendance == 'NCM')
                    {
                      //  $pVal[] = (check_activity_unit_type($squad->ACT_ID) === 'count') ? $values->count : $values->grade;
                        $pVal[] =  (check_activity_unit_type($squad->ACT_ID) === 'count') ? ( $values->count ) : ( check_activity_unit_type($squad->ACT_ID) === 'grade' ? ($values->grade ) : ( qualified_values($values->qualified)));
                    }
                    else
                    {
                        $missed_classes = ExtraSessionmeta::where('timetable_id', $values->timetable_id)->where('probationer_id', $probationer_id)->first();
                        if(!empty($missed_classes))
                        {
                            $pVal[] = (check_activity_unit_type($squad->ACT_ID) === 'count') ? $missed_classes->count : $missed_classes->grade;
                        }
                        else
                        {
                            $pVal[] = $values->attendance;
                        }

                    }
                }

                $activities_dates[] = [
                    'act_name' => activity_name($squad->ACT_ID),
                    'unit' => activity_unit($squad->ACT_ID),
                    'count' => count($dt),
                    'dates' => $dt,
                    'data' => $pVal,
                ];
                unset($dt);
                unset($pVal);

                if(count($usersexport) !== 0)
                {
                    foreach($usersexport as $values)
                    {

                        if($values->attendance == 'P' || $values->attendance == 'MDO' || $values->attendance == 'NCM')
                        {
                          //  $pVal[] = (check_activity_unit_type($squad->ACT_ID) === 'count') ? $values->count : $values->grade;
                            $pVal[] =  (check_activity_unit_type($squad->ACT_ID) === 'count') ? ( $values->count ) : ( check_activity_unit_type($squad->ACT_ID) === 'grade' ? ($values->grade ) : ( qualified_values($values->qualified)));
                        }
                        else
                        {
                            $missed_classes = ExtraSessionmeta::where('timetable_id', $values->timetable_id)->where('probationer_id', $probationer_id)->first();
                            if(!empty($missed_classes))
                            {
                                $pVal[] = (check_activity_unit_type($squad->ACT_ID) === 'count') ? $missed_classes->count : $missed_classes->grade;
                            }
                            else
                            {
                                $pVal[] = $values->attendance;
                            }

                        }
                    }
                }
                else
                {
                    $pVal[] ='';
                }

            $pCount[] = [
                'name' => probationer_name($probationer_id),
                'data' => $pVal,

            ];
            unset($pVal);
        }
      //  return json_encode($pCount);


                echo <<<EOL
                <thead>
                <tr>
                <th></th>
            EOL;
                if(!empty($activities_dates))
                {
                    foreach($activities_dates as $activities_date)
                    {
                    $colspan =  $activities_date['count'];
                    $activity_name = $activities_date['act_name'];
                    $activity_unit = ($activities_date['unit'] != '') ? $activities_date['unit'] : "No Units";
                    echo <<<EOL
                            <th colspan="$colspan">$activity_name - ($activity_unit)</th>
                        EOL;
                    }


                    echo   <<<EOL
                </tr>
                <tr>
            EOL;
            $items = array();
            $pb_name = probationer_name($probationer_id);
                    foreach($activities_dates as $key=>$activities_date)
                    {
                        if($key == '0')
                        {
                            echo <<<EOL
                            <th>Probationer Name</th>
                        EOL;
                        }

                        foreach($activities_date['dates'] as $date)
                        {
                           $items[] = $date;
                           echo <<<EOL
                           <th>$date</th>
                       EOL;
                        }
                }
                echo  <<<EOL
                <tr>
                EOL;
                foreach($activities_dates as $key=>$activities_date)
                {
                    if($key == '0')
                    {
                        echo <<<EOL
                        <td>$pb_name</td>
                    EOL;
                    }

                    foreach($activities_date['data'] as $data)
                    {
                       $items[] = $date;
                       echo <<<EOL
                       <td>$data</td>
                   EOL;
                    }
                }
                echo  <<<EOL
                </tr>
            EOL;

                }

            echo  <<<EOL
                </tbody>
            EOL;
        return;

            //return $daily_activity_users;
            //return $day_wise;
            //return;
            //return view('PbDash.statistics',compact('dt', 'data', 'activity_unit', 'probationer_id', 'probationer_name', 'activity_id', 'sub_activity_id'));
    }


    public function monthly_activity_reports(Request $request)
    {

        if (isset($request->activity_id)) { $activity_id   = $request->activity_id;}
        ($request->sub_activity_id != '') ? $sub_activity_id  = $request->sub_activity_id : $sub_activity_id='0';
         if($request->date != '')
            {
                $m_and_y = explode("/", $request->date);
            }
        $month = $m_and_y[0];
        $year = $m_and_y[1];
        $data = array();

        $user_id = Auth::id();
        $probationer_Id = probationer::where('user_id', $user_id)->value('id');
        $probationer_name = probationer::where('user_id', $user_id)->value('Name');

        $users = DB::select("select CASE when component_id IS NOT NULL then component_id when subactivity_id IS NOT NULL THEN subactivity_id else activity_id end AS ACT_ID, date, timetable_id, activity_id, subactivity_id, component_id from probationers_dailyactivity_data where probationer_id = $probationer_Id and activity_id = $activity_id and subactivity_id = $sub_activity_id  and month(date)= $month AND year(date) = $year group by ACT_ID order by date ASC");
        if(count($users) == '0')
        {
            return redirect('/user-monthlyreport')->with('delete', 'No data avaliable');
        }
        $dt = array();
        $data = [];
        foreach($users as $squad)
        {
            $usersexport = DB::select("SELECT * FROM `activities` WHERE id = $squad->ACT_ID");
            foreach($usersexport as $value)
            {
                $dt[] = $value->name;
            }
        }

        foreach($users as $squad)
        {
            $usersexport = DB::select("select ROUND(AVG(
                CASE grade
                    WHEN 'A' THEN 5
                    WHEN 'B' THEN 4
                    WHEN 'C' THEN 3
                    WHEN 'D' THEN 2
                    WHEN 'E' THEN 1
                    ELSE 0
                END
                )) AS avg_gpa from `probationers_dailyactivity_data` left join activities on activities.batch_id = probationers_dailyactivity_data.Batch_id where (`probationers_dailyactivity_data`.`activity_id` = $squad->ACT_ID or `probationers_dailyactivity_data`.`subactivity_id` = $squad->ACT_ID or `probationers_dailyactivity_data`.`component_id` = $squad->ACT_ID) and probationer_id = $probationer_Id and `activities`.`id` = $squad->ACT_ID  order by date asc");
                foreach($usersexport as $user)
                    {
                        $data[] = $user;
                    }
        }
        // print_r(json_encode($data));exit;
        return view('PbDash.monthlyreport',compact('data', 'dt', 'probationer_Id', 'probationer_name'));
    }

    public function single_activity_reports_export(Request $request)
    {


        // if (isset($request->activity_id)) { $activity_id   = $request->activity_id;}
        // if (isset($request->sub_activity_id)) { $sub_activity_id   = $request->sub_activity_id;}
        // if (($request->component != '')) { $component_id = $request->component;}else{$component_id='0';}
        // if($request->date != ''){$m_and_y = explode("-", $request->date);}
        // $month = $m_and_y[0];
        // $year = $m_and_y[1];




        if (isset($request->activity_id)) { $activity_id   = $request->activity_id;}
        if (($request->sub_activity_id != '')) { $sub_activity_id   = $request->sub_activity_id;}else{$sub_activity_id='0';}
        if (($request->component != '')) { $component_id = $request->component;}else{$component_id='0';}

        $sub_activity_id = sanitize_activity_id($sub_activity_id);
        $component_id = sanitize_activity_id($component_id);

        $daterange = $request->date;
        if (empty($daterange)) {
            $errors[] = "Select Date Range.";
        } else {
            $daterangeArray = explode(" - ", $daterange);
            if(count($daterangeArray) !== 2) {
                $errors[] = "Invalid Date Range (Expected: DD/MM/YYYY - DD/MM/YYYY).";
            } else if(!isValidDate($daterangeArray[0], 'd/m/Y') || !isValidDate($daterangeArray[1], 'd/m/Y')) {
                $errors[] = "Invalid Date Format (Expected: DD/MM/YYYY - DD/MM/YYYY).";
            } else {
                $from = convert_date($daterangeArray[0], 'd/m/Y');
                $to = convert_date($daterangeArray[1], 'd/m/Y');
            }
        }




        $user_id = Auth::id();
        $probationer_id = probationer::where('user_id', $user_id)->value('id');

        // $users =  DB::select("select CASE when component_id IS NOT NULL then component_id when subactivity_id IS NOT NULL THEN subactivity_id else activity_id end AS ACT_ID, date, timetable_id, activity_id, subactivity_id, component_id from probationers_dailyactivity_data where probationer_id= $probationer_id and activity_id = $activity_id and subactivity_id = $sub_activity_id and component_id = $component_id and month(date)= $month AND year(date) = $year group by ACT_ID order by date ASC");
        // if(count($users) == '0')
        // {
        //     return true;
        // }

        $day_wises = ProbationersDailyactivityData::select(DB::raw("CASE when component_id IS NOT NULL then component_id when subactivity_id IS NOT NULL THEN subactivity_id else activity_id end AS ACT_ID"), 'date', 'timetable_id', 'activity_id', 'subactivity_id', 'component_id', 'id')
        ->where('probationer_id', $probationer_id)
        ->where('activity_id', $activity_id)
        ->whereBetween('date', [$from, $to])
        ->orderBy('date', 'asc')
        ->groupBy('ACT_ID');
        if($sub_activity_id != null)
        {
            $day_wises->where('subactivity_id', $sub_activity_id);
        }
        if($component_id != null)
        {
            $day_wises->where('component_id', $component_id);
        }

        $users = $day_wises->get();


        $p='0';
        $q = '1';
        $spreadsheet = new Spreadsheet();
        foreach($users as $squad)
        {

            $usersexport = DB::table('probationers_dailyactivity_data')
            ->where('probationers_dailyactivity_data.activity_id', $squad->ACT_ID)
            ->orWhere('probationers_dailyactivity_data.subactivity_id', $squad->ACT_ID)
            ->orWhere('probationers_dailyactivity_data.component_id', $squad->ACT_ID)
            ->where('probationer_id', $probationer_id)
            ->whereBetween('date', [$from, $to])
            ->groupBy('timetable_id')->orderBy('date')->get()->toArray();




            $users1 = DB::table('probationers_dailyactivity_data')->where('probationer_id', $probationer_id)->where('probationers_dailyactivity_data.activity_id', $squad->ACT_ID)->orWhere('probationers_dailyactivity_data.subactivity_id', $squad->ACT_ID)->orWhere('probationers_dailyactivity_data.component_id', $squad->ACT_ID)->whereBetween('date', [$from, $to])->select('timetable_id')->groupBy('timetable_id')->orderBy('date')->get()->toArray();

            $activity_name = DB::table('activities')->where('activities.id', $squad->activity_id)->first();
            $sub_activity_name = DB::table('activities')->where('activities.id', $squad->subactivity_id)->first();
            $component_name = DB::table('activities')->where('activities.id', $squad->component_id)->first();
            // $squad1 = DB::table('squads')->where('squads.id', $request->sid)->first();
            // $batch = DB::table('batches')->where('batches.id', $request->id)->first();
            $activity_unit = DB::table('activities')->where('activities.id', $squad->ACT_ID)->first();
               $spreadsheet->setActiveSheetIndex($p);

            $spreadsheet->getActiveSheet($p)->setTitle('Name of Sheet'. $q);
            //$spreadsheet->getActiveSheet($p)->setCellValue("A1", "Batch No");
            $spreadsheet->getActiveSheet($p)->setCellValue("A2", "Activity");
            $spreadsheet->getActiveSheet($p)->setCellValue("A3", "Sub Activity");
            $spreadsheet->getActiveSheet($p)->setCellValue("A4", "Component");
           // $spreadsheet->getActiveSheet($p)->setCellValue("C1", "SquadNo");
            // $spreadsheet->getActiveSheet($p)->setCellValue("B1", $batch->BatchName);
            $spreadsheet->getActiveSheet($p)->setCellValue("B2", $activity_name->name);
            $spreadsheet->getActiveSheet($p)->setCellValue("B3", isset($sub_activity_name->name) ? $sub_activity_name->name: '');
            $spreadsheet->getActiveSheet($p)->setCellValue("B4", isset($component_name->name) ? $component_name->name:'');
            // $spreadsheet->getActiveSheet($p)->setCellValue("D1", $squad1->SquadNumber);

             $col = 'B';
             $row ='B';
             $k = '6';
             $cell = 'B';
             $usercell = 'A';

             $spreadsheet->getActiveSheet($p)->setCellValue('A6',  'Date');
                foreach($usersexport as $value) {
                    $i = '1';
                    $col++;
                    $spreadsheet->getActiveSheet($p)->setCellValue($row.$k, date('d-m', strtotime($value->date)));
                    $spreadsheet->getActiveSheet($p)->setCellValue('A7', 'Probationer Name');
                    $spreadsheet->getActiveSheet($p)->setCellValue($cell++.'7', isset($activity_unit->unit) ? $activity_unit->unit: 'No Units');
                    $row++;
                    $col++;
                }
                $i = '1';
               // return json_encode($usersexport);
                foreach($users1 as $user)
                {
                    $n = '8';
                    $probationer_data = DB::table('probationers_dailyactivity_data')->where('probationer_id', $probationer_id)->where('probationers_dailyactivity_data.timetable_id', $user->timetable_id)->leftJoin('probationers', 'probationers.id', '=', 'probationers_dailyactivity_data.probationer_id')->select('Name', 'grade', 'count')->orderBy('date')->get();
                    foreach($probationer_data as $probationer_datas)
                    {
                        if($i == '1')
                        {
                            $spreadsheet->getActiveSheet($p)->setCellValue($usercell++.$n, $probationer_datas->Name);
                            $spreadsheet->getActiveSheet($p)->setCellValue($usercell++.$n, $probationer_datas->count);
                       }
                       else
                       {
                         $spreadsheet->getActiveSheet()->setCellValue($usercell++.$n, $probationer_datas->count);

                       }
                    }
                    $i++;
                }
               $spreadsheet->createSheet();
               $p++;
               $q++;
         }

         //   $file_name='/reports.xlsx';
            $file_name = "uploads/reports_" . probationer_name($probationer_id) . "_". date("Y-m-d-Hi") . ".xlsx";


            $baseurl = url('/'.$file_name);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="'.$file_name.'"');
        $writer = new Xlsx($spreadsheet);
        $writer->save($file_name);
        return $baseurl;
    }
    public function monthly_activity_reports_export(Request $request)
    {
        if (isset($request->activity_id)) { $activity_id   = $request->activity_id;}
        if (isset($request->sub_activity_id)) { $sub_activity_id   = $request->sub_activity_id;}
        if (($request->component != '')) { $component_id = $request->component;}else{$component_id='0';}
        if($request->date != ''){$m_and_y = explode("/", $request->date);}
        $month = $m_and_y[0];
        $year = $m_and_y[1];
        $user_id = Auth::id();
        $probationer_id = probationer::where('user_id', $user_id)->value('id');


        function replacewithgraade($template)
        {
            switch ($template) {
                case "5":
                  return "A";
                  break;
                case "4":
                    return "B";
                  break;
                case "3":
                    return "C";
                  break;
                case "2":
                    return "D";
                  break;
                default:
                return "E";
              }
        }

        $users =  DB::select("select CASE when component_id IS NOT NULL then component_id when subactivity_id IS NOT NULL THEN subactivity_id else activity_id end AS ACT_ID, date, timetable_id, activity_id, subactivity_id, component_id from probationers_dailyactivity_data where probationer_id= $probationer_id and activity_id = $activity_id and subactivity_id = $sub_activity_id and component_id = $component_id and month(date)= $month AND year(date) = $year group by ACT_ID order by date ASC");
        if(count($users) == '0')
        {
            return true;
        }
        $dt = array();
        $data = [];
        $p='0';
        $q = '1';
        $usercell = 'A';
        $spreadsheet = new Spreadsheet();
        $row ='B';
        $k = '4';
        $spreadsheet->getActiveSheet($p)->setCellValue("A4", "Probationer Name");
        foreach($users as $squad)
        {
            $usersexport = DB::select("SELECT * FROM `activities` WHERE id = $squad->ACT_ID");
            foreach($usersexport as $value)
            {
                $spreadsheet->getActiveSheet($p)->setCellValue($row++.$k, $value->name);

            }
        }
        // $squad1 = DB::table('squads')->where('squads.id', $request->squad_id)->first();
        // $batch = DB::table('batches')->where('batches.id', $request->batch_id)->first();
        $spreadsheet->getActiveSheet($p)->setCellValue("A1", "Monthly Grade report");
        // $spreadsheet->getActiveSheet($p)->setCellValue("A2", "Batch NO");
        // $spreadsheet->getActiveSheet($p)->setCellValue("C2", "Squad No");
        $spreadsheet->getActiveSheet($p)->setCellValue("B2", '');
        $spreadsheet->getActiveSheet($p)->setCellValue("D2", '');

        $probationers =  DB::select("select CASE when component_id IS NOT NULL then component_id when subactivity_id IS NOT NULL THEN subactivity_id else activity_id end AS ACT_ID, date, timetable_id, activity_id, subactivity_id, component_id from probationers_dailyactivity_data where probationer_id= $probationer_id and activity_id = $activity_id and subactivity_id = $sub_activity_id and component_id = $component_id and month(date)= $month AND year(date) = $year  group by probationer_id order by probationer_id ASC");
        $probationer_count = (count($users));

        $n = '3';
        foreach($probationers as $activites)
        {
            // $activity =  DB::select("select CASE when component_id IS NOT NULL then component_id when subactivity_id IS NOT NULL THEN subactivity_id else activity_id end AS ACT_ID, date, timetable_id, activity_id, subactivity_id, component_id from probationers_dailyactivity_data where Batch_id= $request->batch_id and squad_id= $request->squad_id $query group by ACT_ID order by date ASC");
            $i='1';

            // foreach($activity as $activites)
            // {
                $probationer_data = DB::select("select Name, ROUND(AVG(
                    CASE grade
                        WHEN 'A' THEN 5
                        WHEN 'B' THEN 4
                        WHEN 'C' THEN 3
                        WHEN 'D' THEN 2
                        WHEN 'E' THEN 1
                        ELSE 0
                    END
                    )) AS avg_gpa from `probationers_dailyactivity_data` left join probationers on probationers.id = probationers_dailyactivity_data.probationer_id where (`probationers_dailyactivity_data`.`activity_id` = $activites->ACT_ID or `probationers_dailyactivity_data`.`subactivity_id` = $activites->ACT_ID or `probationers_dailyactivity_data`.`component_id` = $activites->ACT_ID) and probationer_id = $probationer_id order by date asc");
                    foreach($probationer_data as $probs)
                    {
                        if($i == '1')
                        {
                            $spreadsheet->getActiveSheet($p)->setCellValue($usercell++.$n, $probs->Name);
                            $spreadsheet->getActiveSheet($p)->setCellValue($usercell++.$n, replacewithgraade($probs->avg_gpa));
                          //  $n++;
                            $i++;
                       }
                       else
                       {
                       // $highestColumn = $spreadsheet->getActiveSheet($p)->getHighestColumn($n);
                        $spreadsheet->getActiveSheet()->setCellValue($usercell++.$n, replacewithgraade($probs->avg_gpa));
                      //  $n++;
                       }
                    }
           // }
            $n++;
            $usercell = 'A';
        }
            $file_name='monthly_reports.xlsx';

         $baseurl = url('/monthly_reports.xlsx');

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $file_name . '"');

        header('Cache-Control: max-age=0');
        $writer = new Xlsx($spreadsheet);
        $writer->save($file_name);

        return $baseurl;
    }


}
