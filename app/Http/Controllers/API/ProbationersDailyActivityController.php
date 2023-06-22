<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Activity;
use App\Models\Timetable;
use App\Models\ProbationersDailyactivityData;
use App\Models\Notification;
use Carbon\Carbon;
use \stdClass;
use Illuminate\Support\Facades\Auth;

class ProbationersDailyActivityController extends Controller
{
    /** -----------------------------------------------------------------------
     * API Name: probationerattendance
     * User Role: probationer, drillinspector
     * Description: To get probationer's attendance
     *
     * Author: https://github.com/rahaman-m
     * --------------------------------------------------------------------- */
    public function Probattendance(Request $request)
    {
        $user       = Auth::user();
        $user_id    = $user->id;

        try {
            $request = json_decode($request->getContent(), true);

            if ($user->role === 'drillinspector' || $user->role === 'si' || $user->role === 'adi') {
                isset($request['probationer_id']) ? $probationer_Id = remove_specialcharcters($request['probationer_id']) : $probationer_Id = '';
            } else {
                $probationer_Id = probationer_id($user_id);
                if($probationer_Id != $request['probationer_id'])
                    {
                        $response   = [
                            'code' => "401",
                            'message'  => "Unauthorized"
                        ];
                        return response()->json($response, 401);
                    }
            }
            isset($request['month']) ? $month = remove_specialcharcters($request['month']) : $month = '';
            isset($request['year']) ? $year = remove_specialcharcters($request['year']) : $year = '';


            $user_id    = Auth::id();
            $access_token    = urlencode( create_webpage_access_token($user_id) );
            if(!empty($access_token)) {
                $url    = url("user-probationer-attendance-mobile?id={$user_id}&pid={$probationer_Id}&month={$month}&year={$year}&access_token={$access_token}");

                return response()->json([
                    'code'      => "200",
                    'status'    => "success",
                    'message'       => $url,
                ], 200);
            }

            return response()->json([
                'code'      => "400",
                'status'    => "error",
                'message'   => 'Can not create access token',
            ], 200);

        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->errorInfo[1];
            $response   = [
                'code' => "200",
                'status'    => "failed",
                'message' => 'Something went wrong Please try again'
            ];
        }
        return response()->json($response, 200);
    }

    public function probationermonthlysession(Request $request)
    {
        try {
            $request = (json_decode($request->getContent(), true));

            $month  = isset($request['month'])? remove_specialcharcters($request['month']) : '';
            $year   = isset($request['year'])? remove_specialcharcters($request['year']) : '';

            $user_role = Auth::user()->role;
            if ($user_role === 'drillinspector' || $user_role === 'si' || $user_role === 'adi') {
                $probationer_Id   = isset($request['probationer_id'])? remove_specialcharcters($request['probationer_id']) : '';
            } else {
                $user_id = Auth::id();
                $probationer_Id = probationer_id($user_id);
                if($probationer_Id != $request['probationer_id'])
                    {
                        $response   = [
                            'code' => "401",
                            'message'  => "Unauthorized"
                        ];
                        return response()->json($response, 401);
                    }
            }

            if (!is_numeric($probationer_Id) || !is_numeric($month) || !is_numeric($year)) {
                $response   = [
                    'code' => "201",
                    'status'    => "success",
                    'message'  => "Invalid Details"
                ];
                return response()->json($response, 200);
            }
            $data       = [];

            $dateStart  = date('Y-m-d', mktime(0, 0, 0, $month, 1, $year));
            $dateEnd    = date('Y-m-t', strtotime($dateStart));

            $probationer = probationer_data($probationer_Id);
            if (empty($probationer)) {
                return response()->json([
                    'code'      => '401',
                    'status'    => 'error',
                    'message'   => 'probationer_id not exist',
                ], 200);
            }
            $squad_id   = $probationer->squad_id;

            $timetables = \App\Models\Timetable::where("activity_id", "!=", 0)
                ->whereNotNull("activity_id")
                ->whereDate("date", ">=", "{$dateStart}")
                ->whereDate("date", "<=", "{$dateEnd}")
                ->where('squad_id',  $squad_id)
                ->orderBy("activity_id")
                ->orderBy("subactivity_id")
                ->get();

            $timetables_count   = count($timetables);

            $activities     = [];
            $subactivities  = [];
            $tt_ids         = [];
            if ($timetables_count > 0) {
                foreach ($timetables as $timetable) {

                    $tt_ids[]     = $timetable->id;
                    $tt_activity_id     = $timetable->activity_id;
                    $tt_subactivity_id  = $timetable->subactivity_id;

                    if (isset($activities[$tt_activity_id])) {
                        if (!in_array($tt_subactivity_id, $activities[$tt_activity_id])) {
                            $activities[$tt_activity_id][]    = $tt_subactivity_id;
                            $subactivities[]    = $tt_subactivity_id;
                        }
                    } else {
                        $activities[$tt_activity_id][]    = $tt_subactivity_id;
                        $subactivities[]    = $tt_subactivity_id;
                    }
                }
            }

            $activityData   = [];
            foreach ($activities as $activity_id => $data) {
                foreach ($data as $subactivity_id) {

                    // $activity = App\Models\Activity::withTrashed()->find($activity_id);

                    $activityName = activity_name($activity_id);

                    $subactivityName = "";
                    if (!empty($subactivity_id)) {
                        $subactivityName = activity_name($subactivity_id);
                    } else {
                        $subactivity_id = null;
                    }

                    // $getAttns = DB::table('probationers_dailyactivity_data')
                    //     ->join('timetables', 'probationers_dailyactivity_data.timetable_id', '=', 'timetables.id')
                    //     ->whereRaw("probationers_dailyactivity_data.probationer_id = ? AND probationers_dailyactivity_data.date BETWEEN ? AND ? AND timetables.activity_id = ? AND timetables.subactivity_id = ?", [$probationer_Id, $dateStart, $dateEnd, $activity_id, $subactivity_id])
                    //     ->select('probationers_dailyactivity_data.attendance', 'probationers_dailyactivity_data.timetable_id')->get();

                    $total = Timetable::where('squad_id', $squad_id)
                        ->whereBetween("date", [$dateStart, $dateEnd])
                        ->where("activity_id", $activity_id)
                        ->where("subactivity_id", $subactivity_id)
                        ->where("session_start", '>', 0)
                        ->count();

                    // $getAttns = Timetable::whereRaw("probationers_dailyactivity_data.probationer_id = ? AND probationers_dailyactivity_data.date BETWEEN ? AND ? AND timetables.activity_id = ? AND timetables.subactivity_id = ?", [$probationer_Id, $dateStart, $dateEnd, $activity_id, $subactivity_id])
                    //     ->leftJoin('probationers_dailyactivity_data', 'probationers_dailyactivity_data.timetable_id', '=', 'timetables.id')
                    //     ->select('probationers_dailyactivity_data.attendance', 'probationers_dailyactivity_data.timetable_id')
                    //     ->groupBy('probationers_dailyactivity_data.timetable_id')
                    //     ->get();

                    $getAttnsQ = Timetable::query()
                        // ->whereRaw("probationers_dailyactivity_data.probationer_id = ? AND probationers_dailyactivity_data.date BETWEEN ? AND ? AND timetables.activity_id = ? AND timetables.subactivity_id = ?", [$pb_id, $dateStart, $dateEnd, $activity_id, $subactivity_id])
                        ->where("probationers_dailyactivity_data.probationer_id", $probationer_Id)
                        ->whereBetween("probationers_dailyactivity_data.date", [$dateStart, $dateEnd])
                        ->where("probationers_dailyactivity_data.activity_id", $activity_id);
                    if(!empty($subactivity_id)) {
                        $getAttnsQ->where("probationers_dailyactivity_data.subactivity_id", $subactivity_id);
                    }
                    $getAttns  = $getAttnsQ->leftJoin('probationers_dailyactivity_data', 'probationers_dailyactivity_data.timetable_id', '=', 'timetables.id')
                        ->select('probationers_dailyactivity_data.attendance', 'probationers_dailyactivity_data.timetable_id')
                        ->groupBy('probationers_dailyactivity_data.timetable_id')
                        ->get();

                    // $total = count($getAttns);
                    $attended = 0;
                    $missed = 0;

                    if ($total > 0) {
                        foreach ($getAttns as $getAttn) {
                            $timetable_id   = $getAttn->timetable_id;
                            if (in_array($getAttn->attendance, ['P', 'MDO', 'NCM'])) {
                                $attended++;
                            } else {
                                $Extrasession = \App\Models\ExtraSessionmeta::where('probationer_id', $probationer_Id)
                                    ->whereIn('attendance', ['P', 'MDO', 'NCM'])
                                    ->where('timetable_id', $timetable_id)
                                    ->count();

                                if ($Extrasession > 0) {
                                    $attended++;
                                }
                            }
                        }

                        $missed = $total - $attended;
                    } else {
                        $total = '-';
                        $attended = '-';
                        $missed = '-';
                    }

                    if (!empty($subactivityName)) {
                        $activityName   = $subactivityName . " \n(" . $activityName . ")";
                    }

                    $activityData[]   = [
                        'activity'      => $activityName,
                        'subactivities' => [],
                        'total'     => $total,
                        'attended'  => $attended,
                        'missed'    => $missed,
                    ];
                }
            }

            $response   = [
                'code'  => '200',
                'status'    => "success",
                'data'      => $activityData
            ];
        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->errorInfo[1];
            $response   = [
                'code' => "200",
                'status'    => "failed",
                'message' => 'Something went wrong Please try again'
            ];
        }
        return response()->json($response, 200);
    }

    public function rescheduledSessions(Request $request)
    {
        try {
            $request = (json_decode($request->getContent(), true));
            isset($request['probationer_id']) ? $probationer_Id = remove_specialcharcters($request['probationer_id']) : $probationer_Id = '';
            if (!is_numeric($probationer_Id)) {
                $response   = [
                    'code' => "201",
                    'status'    => "success",
                    'message'  => "Invalid Probationer Id"
                ];
                return response()->json($response, 200);
            }
            $data   = [];

            $probationers   = DB::table('probationers')
                ->select('squad_id')
                ->where('id', $probationer_Id)->first();

            if (!empty($probationers)) {
                $squad_id   = $probationers->squad_id;

                $dt = date('Y-m-d');

                $timetables = DB::table('timetables')
                    ->where('squad_id', $squad_id)
                    ->where('activity_id', '>', 0)
                    ->whereDate('date', '>=', $dt)
                    ->where('session_type', 'extra')
                    ->where('session_start', '>', 0)->get();

                if (count($timetables)) {
                    foreach ($timetables as $timetable) {
                        $squad_id   = $timetable->squad_id;
                        $activity_id   = $timetable->activity_id;
                        $subactivity_id   = $timetable->subactivity_id;
                        $date            = $timetable->date;
                        $session_number   = $timetable->session_number;

                        $session_start   = $timetable->session_start;
                        $session_start   = date('h:i A', $session_start);

                        $session_end   = $timetable->session_end;
                        $session_end   = date('h:i A', $session_end);

                        $activities    = DB::table('activities')
                            ->where('id', $activity_id)->first();
                        $activity_name  = $activities->name;

                        $subactivity_name  = "";
                        if (!empty($subactivity_id)) {
                            $subactivities    = DB::table('activities')
                                ->where('id', $subactivity_id)->first();

                            $subactivity_name  = $subactivities->name;
                        }

                        $data[] = [
                            "squad_id"      => $squad_id,
                            "activity_id"   => $activity_id,
                            "activity_name"   => $activity_name,
                            "subactivity_id" => $subactivity_id,
                            "subactivity_name" => $subactivity_name,
                            "date"          => $date,
                            "session_number" => $session_number,
                            "session_start" => $session_start,
                            "session_end"   => $session_end,
                        ];
                    }
                }
            }

            $response   = [
                'code'  => '200',
                'status'    => "success",
                'data'      => $data
            ];
        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->errorInfo[1];
            $response   = [
                'code' => "200",
                'status'    => "failed",
                'message' => 'Something went wrong Please try again'
            ];
        }
        return response()->json($response, 200);
    }

    public function prescriptions(Request $request)
    {
        try {
            $request = (json_decode($request->getContent(), true));
           // isset($request['probationer_id']) ? $probationer_Id = remove_specialcharcters($request['probationer_id']) : $probationer_Id = '';
            $user_role = Auth::user()->role;
            if($user_role === 'drillinspector' || $user_role === 'si' || $user_role === 'adi')
            {
                isset($request['probationer_id']) ? $probationer_Id = remove_specialcharcters($request['probationer_id']) : $probationer_Id = '';
            }
            else
            {
                $user_id = Auth::id();
                $probationer_Id = probationer_id($user_id);
                 if($probationer_Id != $request['probationer_id'])
                    {
                        $response   = [
                            'code' => "401",
                            'message'  => "Unauthorised"
                        ];
                        return response()->json($response, 401);
                    }
            }
            if (!is_numeric($probationer_Id)) {
                $response   = [
                    'code' => "201",
                    'status'    => "success",
                    'message'  => "Invalid Probationer Id"
                ];
                return response()->json($response, 200);
            }
            $records_push = array();
            $records_push1 = array();
            $prescriptions_date   = DB::table('probationer_prescription')->where('probationer_prescription.Probationer_Id', $probationer_Id)->select('date')->groupBy('date')->orderBy('id', 'ASC')->get();
            if (count($prescriptions_date) >= 1) {
                foreach ($prescriptions_date as $dt) {
                    $datewiseprescription = DB::table('probationer_prescription')->where('probationer_prescription.Probationer_Id', $probationer_Id)->where('date', $dt->date)->get();

                    // print_r($datewiseprescription);exit;

                    foreach ($datewiseprescription as $datepres) {
                        $records_push1 = array(
                            "drug" => isset($datepres->drug) ? $datepres->drug : '-',
                            "dosage" => isset($datepres->dosage) ? $datepres->dosage : '-',
                            "frequency" => isset($datepres->frequency) ? $datepres->frequency : '-',
                            "duration" => isset($datepres->duration) ? $datepres->duration : '-',
                            "instructions" => isset($datepres->instructions) ? $datepres->instructions : '-',
                        );
                        $test[] = $records_push1;
                        unset($records_push1);
                    }

                    $records_push =  array(
                        "date" => date('d-m-Y', strtotime($dt->date)),
                        "symptoms" => "High Fewer and body pains",
                        "daterecord" => $test
                    );
                    $final_prescription[] = $records_push;
                    unset($test);
                }
                $response   = [
                    'code'  => '200',
                    'status'    => "success",
                    'data'      => $final_prescription,
                ];
            } else {
                $records_push = array(
                    "message" =>  "No records exits",
                );
                $response   = [
                    'code'  => '204',
                    'status'    => "No records exits",
                ];
            }
        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->errorInfo[1];
            $response   = [
                'code' => "200",
                'status'    => "failed",
                'message' => 'Something went wrong Please try again'
            ];
        }

        return response()->json($response, 200);
    }

    public function labreports(Request $request)
    {
        try {
            $request = (json_decode($request->getContent(), true));
            //isset($request['probationer_id']) ? $probationer_Id = remove_specialcharcters($request['probationer_id']) : $probationer_Id = '';
            $user_role = Auth::user()->role;
            if($user_role === 'drillinspector' || $user_role === 'si' || $user_role === 'adi')
            {
                isset($request['probationer_id']) ? $probationer_Id = remove_specialcharcters($request['probationer_id']) : $probationer_Id = '';
            }
            else
            {
                $user_id = Auth::id();
                $probationer_Id = probationer_id($user_id);
                 if($probationer_Id != $request['probationer_id'])
                    {
                        $response   = [
                            'code' => "401",
                            'message'  => "Unauthorised"
                        ];
                        return response()->json($response, 401);
                    }
            }
            if (!is_numeric($probationer_Id)) {
                $response   = [
                    'code' => "201",
                    'status'    => "success",
                    'message'  => "Invalid Probationer Id"
                ];
                return response()->json($response, 200);
            }
            $records_push = array();
            $records_push1 = array();

            $baseurl = url('/uploads');
            // $test = '';
            $labreports_date   = DB::table('labreports')->where('labreports.Probationer_Id', $probationer_Id)->select(DB::raw('DATE(created_at) as created_at'))->groupBy(DB::raw('Date(created_at)'))->orderBy('id', 'ASC')->get();
            //  print_r($labreports_date);exit;
            if (count($labreports_date) >= 1) {
                foreach ($labreports_date as $dt) {

                    $datewisereports = DB::table('labreports')->where('labreports.Probationer_Id', $probationer_Id)->whereDate('created_at', $dt->created_at)->get();
                    foreach ($datewisereports as $datepres) {
                        $records_push1 = array(
                            "ReportName" => isset($datepres->ReportName) ? $datepres->ReportName : '-',
                            "FileDirectory" => isset($datepres->FileDirectory) ? $baseurl . '/' . $datepres->FileDirectory : '-',
                        );
                        // print_r($records_push1);exit;
                        $test[] = $records_push1;
                        unset($records_push1);
                    }

                    $records_push =  array(
                        "date" => date('d-m-Y', strtotime($dt->created_at)),
                        "daterecord" => $test
                    );
                    $final_prescription[] = $records_push;
                    unset($test);
                }
                $response   = [
                    'code'  => '200',
                    'status'    => "success",
                    'data'      => $final_prescription,
                ];
            } else {
                $records_push = array(
                    "message" =>  "No records exits",
                );
                $response   = [
                    'code'  => '204',
                    'status'    => "No records exits",
                ];
            }
        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->errorInfo[1];
            $response   = [
                'code' => "200",
                'status'    => "failed",
                'message' => 'Something went wrong Please try again'
            ];
        }
        return response()->json($response, 200);
    }

    public function sickreports(Request $request)
    {
        try {
            $request = (json_decode($request->getContent(), true));
           // isset($request['probationer_id']) ? $probationer_Id = remove_specialcharcters($request['probationer_id']) : $probationer_Id = '';
            $user_role = Auth::user()->role;
            if($user_role === 'drillinspector' || $user_role === 'si' || $user_role === 'adi')
            {
                isset($request['probationer_id']) ? $probationer_Id = remove_specialcharcters($request['probationer_id']) : $probationer_Id = '';
            }
            else
            {
                $user_id = Auth::id();
                $probationer_Id = probationer_id($user_id);
                    if($probationer_Id != $request['probationer_id'])
                    {
                        $response   = [
                            'code' => "401",
                            'message'  => "Unauthorised"
                        ];
                        return response()->json($response, 401);
                    }
            }
            isset($request['report']) ? $report = remove_specialcharcters($request['report']) : $report = '';
            isset($request['report_id']) ? $report_id = remove_specialcharcters($request['report_id']) : $report_id = '';


            if (!is_numeric($probationer_Id)) {
                $response   = [
                    'code' => "201",
                    'status'    => "success",
                    'message'  => "Invalid Probationer Id"
                ];
                return response()->json($response, 200);
            }
            $timestamp  = date('Y-m-d H:i:s');
            $date  = date('d-m-Y');
            if (!empty($probationer_Id)) {
                if ($report_id == '' || $report_id == null) {
                    $data  = DB::table('probationer_sickreports')->insert([
                        'Probationer_Id' => $probationer_Id,
                        'sickreport' => $report,
                        'date' => $date,
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp,
                    ]);
                } else {
                    $data  = DB::table('probationer_sickreports')->where('id', $report_id)->update([
                        'Probationer_Id' => $probationer_Id,
                        'sickreport' => $report,
                        'date' => $date,
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp,
                    ]);
                }

                if (empty($data)) {
                    $response   = [
                        'code'  => '500',
                        'status'    => "failed",
                    ];
                    return response()->json($response, 500);
                } else {
                    $response   = [
                        'code'  => '200',
                        'status'    => "success",
                    ];
                    return response()->json($response, 200);
                }
            }
        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->errorInfo[1];
            $response   = [
                'code' => "200",
                'status'    => "failed",
                'message' => 'Something went wrong Please try again'
            ];
            return response()->json($response, 200);
        }
    }
    public function viewsickreports(Request $request)
    {
        try {
            $request = (json_decode($request->getContent(), true));
           // isset($request['probationer_id']) ? $probationer_Id = remove_specialcharcters($request['probationer_id']) : $probationer_Id = '';
           $user_role = Auth::user()->role;
           if($user_role === 'drillinspector' || $user_role === 'si' || $user_role === 'adi')
           {
               isset($request['probationer_id']) ? $probationer_Id = remove_specialcharcters($request['probationer_id']) : $probationer_Id = '';
           }
           else
           {
               $user_id = Auth::id();
               $probationer_Id = probationer_id($user_id);
                if($probationer_Id != $request['probationer_id'])
                   {
                       $response   = [
                           'code' => "401",
                           'message'  => "Unauthorised"
                       ];
                       return response()->json($response, 401);
                   }
           }
            if (!is_numeric($probationer_Id)) {
                $response   = [
                    'code' => "201",
                    'status'    => "success",
                    'message'  => "Invalid Probationer Id"
                ];
                return response()->json($response, 200);
            }
            if (!empty($probationer_Id)) {
                $data  = DB::table('probationer_sickreports')->where('Probationer_Id', $probationer_Id)->orderBy('id', 'DESC')->get();

                if (count($data) == '0') {

                    $response   = [
                        'code'  => '204',
                        'status'    => "No records exits",
                    ];
                    return response()->json($response, 200);
                } else {
                    foreach ($data as $dt) {
                        $sickreportlist[] = array(
                            'id' => isset($dt->id) ? $dt->id : '-',
                            'Probationer_Id' => isset($dt->Probationer_Id) ? $dt->Probationer_Id : '',
                            'sickreport' => isset($dt->sickreport) ? $dt->sickreport : '',
                            'date' => isset($dt->date) ? date('d-m-Y', strtotime($dt->date)) : '',
                            'created_at' => isset($dt->created_at) ? date('d-m-Y H:i:s', strtotime($dt->created_at)) : '',
                            'updated_at' => isset($dt->updated_at) ?  date('d-m-Y H:i:s', strtotime($dt->updated_at)) : '',
                        );
                    }
                    $response   = [
                        'code'  => '200',
                        'status'    => "success",
                        'data'      => $sickreportlist,
                    ];
                    return response()->json($response, 200);
                }
            } else {
                $response   = [
                    'code'  => '204',
                    'status'    => "Probationer Id doesn't exits",
                ];
                return response()->json($response, 200);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->errorInfo[1];
            $response   = [
                'code' => "200",
                'status'    => "failed",
                'message' => 'Something went wrong Please try again'
            ];
            return response()->json($response, 200);
        }
    }
    public function attenance_notification(Request $request)
    {

        $today_date = date('Y-m-d');
        $get_timetables = Timetable::where('date', $today_date)
                         ->leftJoin('squads', 'squads.id', '=', 'timetables.squad_id')
                         ->whereNotNull('activity_id')
                         ->select('timetables.id as timetable_id', 'DrillInspector_Id','timetables.batch_id','squads.id as  squad_id', 'session_number')
                         ->get();
        foreach($get_timetables as $get_timetable)
        {
            $check_attendance = ProbationersDailyactivityData::where('timetable_id', $get_timetable->timetable_id)->count();
            if($check_attendance === 0)
            {
                $di_notifications = Notification::Insert([
                    'recipient_type' => "drillinspector",
                    'batch_id' => $get_timetable->batch_id,
                    'squad_id' => $get_timetable->squad_id,
                    'title' => "Attendance Alert",
                    'message' => "Please give attendance For day '$today_date', Session number : '$get_timetable->session_number'",
                    'attachment' => "",
                    'created_by' => '7',
                ]);
            }
        }

       
        // if(empty($di_notifications))
        // {
            $response   = [
                'code' => "200",
                'status'    => "Success",
                'message' => 'Notification inserted succesfully'
            ];
            return response()->json($response, 200);
       // }       
    }   
}
