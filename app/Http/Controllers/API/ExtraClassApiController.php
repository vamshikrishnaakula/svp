<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ExtraClass;
use App\Models\ExtraClassmeta;
use Illuminate\Support\Facades\DB;
use App\Models\Activity;

use Illuminate\Support\Facades\Auth;
use PHPUnit\TextUI\XmlConfiguration\Extension;
use Exception;

class ExtraClassApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $result = [];
            $errors  = [];

            $requestData = (json_decode($request->getContent(), true));

            $batch_id   = "";
            if (isset($requestData['batch_id'])) {
                $batch_id   = remove_specialcharcters($requestData['batch_id']);
            }

            $di_id = remove_specialcharcters($requestData['drillinspector_id']);

            $session_date   = "";
            if (isset($requestData['session_date'])) {
                $session_date   = remove_specialcharcters($requestData['session_date']);
            }

            if (empty($di_id)) {
                $errors[]   = "Drillinspactor Id missing.";
            }

            if (!empty($session_date)) {
                if (!isValidDate($session_date, 'Y-m-d')) {
                    $errors[]   = "Invalid session date.";
                }
            }

            if (empty($errors)) {
                $today   = date('Y-m-d');

                $ExtraClassesQuery   = ExtraClass::where('drillinspector_id', $di_id)
                    ->where('activity_id', '!=', 0)
                    ->whereNotNull('activity_id')
                    ->where('session_start', '>', 0)
                    ->orderBy('session_start', 'asc');

                if (empty($session_date)) {
                    $ExtraClassesQuery->whereDate('date', '>=', $today);
                } else {
                    $ExtraClassesQuery->whereDate('date', $session_date);
                }
                if (!empty($batch_id)) {
                    $ExtraClassesQuery->where('batch_id', $batch_id);
                }

                $ExtraClasses  = $ExtraClassesQuery->get()->toArray();

                $sessionData   = [];
                if (count($ExtraClasses) > 0) {
                    foreach ($ExtraClasses as $ExtraClass) {
                        $activity_name      = "";
                        $subactivity_name   = "";
                        $Components         = [];

                        if (isset($ExtraClass["activity_id"])) {
                            $activity_name  = activity_name($ExtraClass["activity_id"]);
                        }
                        if (isset($ExtraClass["subactivity_id"])) {
                            $subactivity_name  = activity_name($ExtraClass["subactivity_id"]);

                            // Get components
                            $Components = \App\Models\Activity::where('parent_id', $ExtraClass["subactivity_id"])->get()->toArray();
                        }
                        $ExtraClass["activity_name"]      = $activity_name;
                        $ExtraClass["subactivity_name"]   = $subactivity_name;

                        $ExtraClass["components"] = [];
                        if (count($Components) > 0) {
                            $ExtraClass["components"]   = $Components;
                        }

                        if (isset($ExtraClass["session_start"])) {
                            $ExtraClass["session_start"]   = date('c', $ExtraClass["session_start"]);
                        }
                        if (isset($ExtraClass["session_end"])) {
                            $ExtraClass["session_end"]   = date('c', $ExtraClass["session_end"]);
                        }
                        unset($ExtraClass["created_at"]);
                        unset($ExtraClass["updated_at"]);

                        // Get Session metas
                        $Metas  = ExtraClassmeta::where("extra_class_id", $ExtraClass["id"])->get()->toArray();

                        $metaData   = [];
                        if (count($Metas) > 0) {
                            foreach ($Metas as $Meta) {
                                $probationer_name   = "";
                                if (isset($Meta["probationer_id"])) {
                                    $probationer_name  = probationer_name($Meta["probationer_id"]);
                                }
                                $Meta["probationer_name"]   = $probationer_name;

                                unset($Meta["timetable_id"]);
                                unset($Meta["created_at"]);
                                unset($Meta["updated_at"]);
                                $metaData[] = $Meta;
                            }
                        }

                        $ExtraClass["probationers"] = $metaData;
                        $sessionData[] = $ExtraClass;
                    }
                }

                return response()->json([
                    'code'    => 200,
                    'status'    => 'success',
                    'message'   => '',
                    'data'      => $sessionData,
                ], 200);
            } else {
                return response()->json([
                    'code'    => 400,
                    'status'    => 'error',
                    'message'   => implode('<br />', $errors),
                    'data'      => '',
                ], 200);
            }

            return;
        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->errorInfo[1];
            $response   = [
                'code' => 400,
                'status'    => "error",
                'message' => 'Something went wrong Please try again'
            ];
            return response()->json($response, 200);
        }
    }

    /**
     * Submit Extra class attendance.
     * Brief: To provide attendance for probationers in extra classes from the DI app
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function submit_attendance(Request $request)
    {
        try {
            $result = [];
            $errors  = [];

            $requestData = (json_decode($request->getContent(), true));

            $attendanceData = [];

            foreach ($requestData['Probationers'] as $probationer_data) {
                $session_id     = $probationer_data['session_id'];
                $component_id     = isset($probationer_data['component_Id'])? $probationer_data['component_Id'] : null;
                $probationer_id = $probationer_data['probationer_id'];
                $attendance = strtoupper($probationer_data['attendance']);
                $count      = $probationer_data['count'];
                $qualified  = isset($probationer_data['qualified'])? $probationer_data['qualified'] : null;
                $grade      = $probationer_data['grade'];

                if (empty($session_id)) {
                    $errors[]   = "Session Id missing.";
                }
                if (empty($probationer_id)) {
                    $errors[]   = "Probationer Id missing.";
                }

                // $attendance = ($attendance === "NAP") ? "A" : $attendance;
                if (!in_array($attendance, valid_attendances())) {
                    $errors[]   = "Invalid attendance.";
                }

                if(empty($errors)) {
                    $ExtraClass   = ExtraClass::find($session_id);
                    if(empty($ExtraClass)) {
                        $errors[]   = 'Session Id not exist for probationer_id '. $probationer_id;
                    }
                }

                $attendanceData[]   = [
                    "session_id"        => $session_id,
                    "component_id"      => $component_id,
                    "probationer_id"    => $probationer_id,
                    "attendance"    => $attendance,
                    "count"         => $count,
                    "qualified"    => $qualified,
                    "grade"    => $grade,
                ];
            }

            if(empty($errors)) {
                foreach( $attendanceData as  $attendance) {
                    $Metas  = set_extra_class_attendance($attendance);

                    if($Metas['status'] === 'error') {
                        $errors[]   = $Metas['message'] .' for probationer_id '. $probationer_id;
                    }
                }
            }

            if(empty($errors)) {
                return response()->json([
                    'code' => 200,
                    'status'    => "success",
                    'message' => "Data saved successfully",
                ], 200);
            } else {
                return response()->json([
                    'code' => 400,
                    'status'    => "error",
                    'message'    => implode('; ', $errors),
                ], 200);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->errorInfo[1];
            $response   = [
                'code' => 400,
                'status'    => "error",
                // 'message' => 'Something went wrong Please try again',
                'message' => 'ERROR: '. $e->getMessage(),
            ];
            return response()->json($response, 200);
        }
    }

    /**
     * Get Extra class attendance of the probationer for a month.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function get_attendance(Request $request)
    {
        try {
            $result = [];
            $errors  = [];

            isset($request->probationer_id) ? $probationer_id = remove_specialcharcters($request->probationer_id) : $probationer_id = '';
            isset($request->month) ? $month = remove_specialcharcters($request->month) : $month = '';
            isset($request->year) ? $year = remove_specialcharcters($request->year) : $year = '';

            if (empty($probationer_id)) {
                $errors[] = 'probationer_id is missing.';
            }
            if (empty($month)) {
                $errors[] = 'month is missing.';
            }
            if (empty($year)) {
                $errors[] = 'year is missing.';
            }
            if (!empty($errors)) {
                return response()->json([
                    'code'      => 400,
                    'status'    => 'error',
                    'message'   => implode(' ', $errors),
                ], 200);
            }

            $ExtraClasses  = ExtraClass::where('extra_classmetas.probationer_id', $probationer_id)
                ->whereMonth('extra_classes.date', $month)
                ->whereYear('extra_classes.date', $year)
                ->leftJoin('extra_classmetas', 'extra_classes.id', '=', 'extra_classmetas.extra_class_id')
                ->select('extra_classes.id as session_id', 'activity_id', 'subactivity_id', 'date', 'probationer_id', 'attendance')
                ->orderBy('extra_classes.session_start', 'asc')
                ->get()->toArray();

            $data = [];
            foreach ($ExtraClasses as $ExtraClass) {
                if (!empty($ExtraClass['subactivity_id'])) {
                    $ExtraClass['activity_name']  = activity_name($ExtraClass['subactivity_id']);
                } else {
                    $ExtraClass['activity_name']  = activity_name($ExtraClass['activity_id']);
                }

                if (empty($ExtraClass['attendance'])) {
                    $ExtraClass['attendance'] = '-';
                }

                unset($ExtraClass['activity_id']);
                unset($ExtraClass['subactivity_id']);

                $data[]  = $ExtraClass;
            }
            return response()->json([
                'code'      => 200,
                'status'    => 'success',
                'data'      => $data,
            ], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->errorInfo[1];
            $response   = [
                'code' => 400,
                'status'    => "error",
                'message' => 'Something went wrong Please try again'
            ];
            return response()->json($response, 200);
        }
    }

    /**
     * API: get-extraclasses
     * To get sessions for the DI for a date.
     * Brief: To provide attendance for probationers in extra classes from the DI app
     */
    public function get_extra_classes(Request $request)
    {
        try {
            $request = (json_decode($request->getContent(), true));

            $DrillInspector_Id  = intval($request['DrillInspector_Id']);

            if (isset($request['session_date'])) {
                $session_date   = remove_specialcharcters($request['session_date']);
            }

            if (!empty($DrillInspector_Id)) {
                $drillinspector   = Auth::id();
            }

            if ($drillinspector !== $DrillInspector_Id) {
                $response   = [
                    'code' => "401",
                    'message'  => "Unauthorised"
                ];
                return response()->json($response, 401);
            }

            $query   = DB::table('extra_classes')
                ->where('drillinspector_id', $drillinspector)
                ->where('date', $session_date)
                ->get();


            if (count($query) > 0) {

                $i = 0;
                $data = array();
                foreach ($query as $dt) {
                    $i = 0;
                    $activities     = Activity::withTrashed()->where('id', $dt->activity_id)->where('type', 'activity')->get()->toArray();
                    $subactivities  = Activity::withTrashed()->where('id', $dt->subactivity_id)->get()->toArray();
                    // $components  = Activity::withTrashed()->where('id', $dt->component_id)->get()->toArray();
                    // if (count($components) > 0) {
                    //     $si = 0;
                    //         $subactivities[$si]['components']   = $components;
                    //         $si++;
                    // }
                    if (count($subactivities) > 0) {
                        $si = 0;
                        foreach ($subactivities as $subactivity) {
                            $components   = Activity::where('parent_id', $subactivity['id'])->get()->toArray();
                            $subactivities[$si]['components']   = $components;
                            $si++;
                        }
                    }
                    $activities[$i]['subactivities']   = $subactivities;

                    $data1 = array(
                        'ActivitySessionsData' => $activities,
                    );

                    $data[] = array(
                        'session_number' => date("g:i a", $dt->session_start) . '-' . date("g:i a", $dt->session_end),
                        'date' => $dt->date,
                        'timetable_id' => $dt->id,
                        'session_start' => date("g:i a", $dt->session_start),
                        'session_end' => date("g:i a", $dt->session_end),
                        'activities' => $data1,
                    );
                    unset($activities);

                    $i++;
                }
                $test['sessionsdata'] = $data;
                return response()->json($test, 200);
            } else {
                $test['sessionsdata'] = array();
                return response()->json($test, 200);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->errorInfo[1];
            $response   = [
                'code' => 400,
                'status'    => "error",
                'message' => 'Something went wrong Please try again'
            ];
            return response()->json($response, 200);
        }
    }


    /** --------------------------------------------------------------------------------------
     * API Name: extraclass-attendance-data
     * User Role: drillinspector
     * Description: To get the attendance data to give attendance, grade and count in Activity section
     *
     * Author: https://github.com/rahaman-m/
     * ------------------------------------------------------------------------------------- */

    public function extraclass_attendance_data(Request $request)
    {
        try {
            $request = (json_decode($request->getContent(), true));

            $timetable_id   = isset($request['timetable_id'])? remove_specialcharcters($request['timetable_id']) : null;
            $component_id   = isset($request['component_Id'])? intval($request['component_Id']) : null;

            if(empty($timetable_id)) {
                return response()->json([
                    [
                        'code'      => "400",
                        'status'    => "error",
                        'message'   => 'timetable_id missing'
                    ]
                ], 200);
            }

            $extraClass   = ExtraClass::find($timetable_id);
            if(!$extraClass) {
                return response()->json([
                    [
                        'code'      => "400",
                        'status'    => "error",
                        'message'   => 'timetable_id not exist'
                    ]
                ], 200);
            }

            $activity_id    = $extraClass->activity_id;
            $subactivity_id = $extraClass->subactivity_id;

            if (!empty($component_id)) {
                $activity_types = Activity::where('id', $component_id)->select(DB::raw("CASE when unit != null then 'unit' when has_grading != '0' THEN 'grade' when has_qualify != '0' THEN 'qualify' else '' end AS activity_type, unit"))->first();
            } elseif (!empty($subactivity_id)) {
                $activity_types = Activity::where('id', $subactivity_id)->select(DB::raw("CASE when unit != null then 'unit' when has_grading != '0' THEN 'grade' when has_qualify != '0' THEN 'qualify' else '' end AS activity_type, unit"))->first();
            } else {
                $activity_types = Activity::where('id', $activity_id)->select(DB::raw("CASE when unit != null then 'unit' when has_grading != '0' THEN 'grade' when has_qualify != '0' THEN 'qualify' else '' end AS activity_type, unit"))->first();
            }

            $unit = !empty($activity_types->unit) ?  $activity_types->unit : '';
            $activityType = $activity_types->activity_type;
            $activityType = empty($activityType)? "unit" : $activityType;

            $metaQ   = DB::table('extra_classmetas')
                ->where('extra_class_id', $timetable_id);

            if(!empty($component_id)) {
                $GLOBALS["component_id"]    = $component_id;
                $metaQ->leftJoin('extra_class_components', function($join){
                        $join->on('extra_class_components.classmetas_id', '=', 'extra_classmetas.id')
                            ->where('extra_class_components.component_id', '=', $GLOBALS["component_id"]);
                    })
                    ->leftJoin('probationers', 'probationers.id', '=', 'extra_classmetas.probationer_id')
                    ->select(
                        'probationers.id as Probationer_Id',
                        'probationers.Name',
                        'probationers.gender',
                        'extra_class_components.grade',
                        'extra_class_components.count',
                        'extra_class_components.qualified',
                        'extra_classmetas.attendance',
                        'extra_classmetas.updated_at',
                    );
                ;
            } else {
                $metaQ->leftJoin('probationers', 'probationers.id', '=', 'extra_classmetas.probationer_id')
                    ->select(
                        'probationers.id as Probationer_Id',
                        'probationers.Name',
                        'probationers.gender',
                        'extra_classmetas.grade',
                        'extra_classmetas.count',
                        'extra_classmetas.qualified',
                        'extra_classmetas.attendance',
                        'extra_classmetas.updated_at',
                    );
            }
            $sessionData   = $metaQ->get();

            // $query   = DB::table('extra_classmetas')
            //     ->where('extra_class_id', $timetable_id)
            //     ->leftJoin('probationers', 'probationers.id', '=', 'extra_classmetas.probationer_id')
            //     ->select(
            //         'probationers.id as Probationer_Id',
            //         'probationers.Name',
            //         'probationers.gender',
            //         'extra_classmetas.grade',
            //         'extra_classmetas.count',
            //         'extra_classmetas.qualified',
            //         'extra_classmetas.attendance'
            //     )
            //     ->get();

            $probationer_data   = [];
            $message = '';
            foreach ($sessionData as $data) {
                $qualified    = $data->qualified;

                $probationer_data[] = array(
                    "Probationer_Id" => isset($data->Probationer_Id) ? $data->Probationer_Id : '',
                    "Name" => isset($data->Name) ? $data->Name : '',
                    "gender" => isset($data->gender) ? $data->gender : '',
                    "grade" => isset($data->grade) ? $data->grade : '',
                    "count" => isset($data->count) ? $data->count : '',
                    "qualified" => ($qualified === null) ? '' : qualified_values($qualified),
                    "attendance" => isset($data->attendance) ? $data->attendance : 'P',
                    "unit" => $unit,
                    "activity_type" => $activityType,
                    "Inserted_id" => 0,
                    "Squad_Id" => 0,
                );
                if($data->attendance == 'P')
                {
                    $message = isset($data->updated_at) ? "Attendance given for this session on " . date('d-m-Y h:i a', strtotime($data->updated_at)) : '';
                }

            }
            $response   = [
                'code'  => '200',
                'status'    => "success",
                'message'   => 'Data saved successfully',
                'data'      => $probationer_data,
                'message' => $message,
            ];
        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->errorInfo[1];
            $response   = [
                'code' => 400,
                'status'    => "error",
                // 'message' => 'Something went wrong Please try again'
                'message' => 'ERROR: '. $e->getMessage()
            ];
        }
        return response()->json($response, 200);
    }

    /**
     * API: Extra class Timetables for DI and Probationers
     */
    public function timetables(Request $request)
    {
        try {
            $errors = [];
            isset($request->batch_id) ? $batch_id = remove_specialcharcters($request->batch_id) : $batch_id = '';

            $userData   = Auth::user();
            $user_id    = $userData->id;
            $role       = $userData->role;

            if (($role === 'drillinspector' || $role === 'si' || $role === 'adi') && empty($batch_id)) {
                $errors[]   = "batch_id is missing.";
            }
            // if (empty($month) || empty($year)) {
            //     $errors[]   = "month and/or year is missing.";
            // }

            if (!empty($errors)) {
                return response()->json([
                    'code'      => 400,
                    'status'    => 'error',
                    'message'   => implode(' ', $errors),
                ], 200);
            }

            $Ex_Query   = ExtraClass::query();
            $ExtraClasses  = [];
            // For DI
            if ($role === 'drillinspector' || $role === 'si' || $role === 'adi') {
                $ExtraClasses  = ExtraClass::where('drillinspector_id', $user_id)
                    ->where('batch_id', $batch_id)
                    ->select('extra_classes.id', 'batch_id', 'activity_id', 'subactivity_id', 'drillinspector_id', 'date', 'session_start', 'session_end')
                    ->orderBy('session_start', 'asc')
                    ->get()->toArray();
            } elseif ($role === 'probationer') {
                $probationer_id = probationer_id($user_id);
                $ExtraClasses  = ExtraClass::where('extra_classmetas.probationer_id', $probationer_id)
                    ->leftJoin('extra_classmetas', 'extra_classes.id', '=', 'extra_classmetas.extra_class_id')
                    ->select('extra_classes.id', 'batch_id', 'activity_id', 'subactivity_id', 'drillinspector_id', 'date', 'session_start', 'session_end')
                    ->orderBy('extra_classes.session_start', 'asc')
                    ->get()->toArray();
            } else {
                return response()->json([
                    'code'      => 400,
                    'status'    => 'error',
                    'message'   => "Unauthorized access",
                ], 200);
            }

            $data = [];
            if (!empty($ExtraClasses)) {
                foreach ($ExtraClasses as $ExSession) {
                    intval($ExSession['batch_id']);
                    $session_start  = date('h:i A', $ExSession['session_start']);
                    $session_end    = date('h:i A', $ExSession['session_end']);
                    $session_time   = $session_start . ' - ' . $session_end;

                    $ExSession['session_time']   = $session_time;

                    $ExSession['activity_name']  = activity_name($ExSession['activity_id']);

                    $ExSession['subactivity_name']   = "--";
                    if (!empty($ExSession['subactivity_id'])) {
                        $ExSession['subactivity_name']   = activity_name($ExSession['subactivity_id']);
                    }

                    $ExSession['drillinspector_name']   = \App\Models\User::where('id', $ExSession['drillinspector_id'])->value('name');

                    unset($ExSession['session_start']);
                    unset($ExSession['session_end']);
                    unset($ExSession['created_at']);
                    unset($ExSession['updated_at']);

                    $data[] = $ExSession;
                }
            }

            return response()->json([
                'code'      => 200,
                'status'    => 'success',
                'message'   => '',
                'data'      => $data,
            ], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->errorInfo[1];
            $response   = [
                'code' => 400,
                'status'    => "error",
                'message' => 'Something went wrong Please try again'
            ];
            return response()->json($response, 200);
        }
    }
    public function extraclass_probationers(Request $request)
    {
        $result = [];
        $errors  = [];
        $request = (json_decode($request->getContent(), true));


            $sessionId       = $request['sessionId'];
            $ExtraClass   = \App\Models\ExtraClass::find($sessionId);
            $Sessionmetas   = $ExtraClass->metas;

            $sessionId   = $ExtraClass->id;
            $batch_id   = $ExtraClass->batch_id;
            $batch      = batch_name($batch_id);

            $activity_id     = $ExtraClass->activity_id;
            $activity        = activity_name($activity_id);

            $subActivity_id  = $ExtraClass->subactivity_id;
            $subActivity     = "";
            if (!empty($subActivity_id)) {
                $subActivity     = activity_name($subActivity_id);
            }

            $di_id      = $ExtraClass->drillinspector_id;
            $di_name    = user_name($di_id);

            $date     = $ExtraClass->date;
            $session_start  = $ExtraClass->session_start;
            $session_start  = date('h:i A', $session_start);

            $session_end    = $ExtraClass->session_end;
            $session_end    = date('h:i A', $session_end);

            $session_time   = $session_start .' - '. $session_end;



            if (count($Sessionmetas) > 0) {

                foreach ($Sessionmetas as $Sessionmeta) {
                    $pb_id      = $Sessionmeta->probationer_id;

                    $probationer   = \App\Models\probationer::find($pb_id);
                    $pb_name    = $probationer->Name;
                    $squad_id   = $probationer->squad_id;
                    $squad_num  = squad_number((int) $squad_id);

                    $attendance      = $Sessionmeta->attendance;
                    $unit_count      = $Sessionmeta->count;
                    $grade      = $Sessionmeta->grade;

                    $probationerList[] = [
                        "pb_name" => $pb_name,
                        "squad_number" => $squad_num,
                        "attendance" => $attendance,
                        "count" => $unit_count,
                        "grade" => $grade,
                    ];

                }

            }
            return response()->json([
                'code'      => 200,
                'status'    => 'success',
                'message'   => '',
                'data'      => $probationerList,
            ], 200);

    }

}
