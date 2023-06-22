<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ExtraSession;
use App\Models\ExtraSessionmeta;
use App\Models\ExtraClass;
use App\Models\ExtraClassmeta;
use Illuminate\Support\Facades\DB;
use App\Models\Activity;
use App\Models\Timetable;

use Illuminate\Support\Facades\Auth;
use PHPUnit\TextUI\XmlConfiguration\Extension;
use Exception;

class ExtraSessionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
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
                $batch_id   = intval($requestData['batch_id']);
            }

            $di_id = intval($requestData['drillinspector_id']);

            $session_date   = "";
            if (isset($requestData['session_date'])) {
                $session_date   = $requestData['session_date'];
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

                $ExtraSessionsQuery   = ExtraSession::where('drillinspector_id', $di_id)
                    ->where('activity_id', '!=', 0)
                    ->whereNotNull('activity_id')
                    ->where('session_start', '>', 0)
                    ->orderBy('session_start', 'asc');

                if (empty($session_date)) {
                    $ExtraSessionsQuery->whereDate('date', '>=', $today);
                } else {
                    $ExtraSessionsQuery->whereDate('date', $session_date);
                }
                if (!empty($batch_id)) {
                    $ExtraSessionsQuery->where('batch_id', $batch_id);
                }

                $ExtraSessions  = $ExtraSessionsQuery->get()->toArray();

                $sessionData   = [];
                if (count($ExtraSessions) > 0) {
                    foreach ($ExtraSessions as $ExtraSession) {
                        $activity_name      = "";
                        $subactivity_name   = "";
                        $Components         = [];

                        if (isset($ExtraSession["activity_id"])) {
                            $activity_name  = activity_name($ExtraSession["activity_id"]);
                        }
                        if (isset($ExtraSession["subactivity_id"])) {
                            $subactivity_name  = activity_name($ExtraSession["subactivity_id"]);

                            // Get components
                            $Components = \App\Models\Activity::where('parent_id', $ExtraSession["subactivity_id"])->get()->toArray();
                        }
                        $ExtraSession["activity_name"]      = $activity_name;
                        $ExtraSession["subactivity_name"]   = $subactivity_name;

                        $ExtraSession["components"] = [];
                        if (count($Components) > 0) {
                            $ExtraSession["components"]   = $Components;
                        }

                        if (isset($ExtraSession["session_start"])) {
                            $ExtraSession["session_start"]   = date('c', $ExtraSession["session_start"]);
                        }
                        if (isset($ExtraSession["session_end"])) {
                            $ExtraSession["session_end"]   = date('c', $ExtraSession["session_end"]);
                        }
                        unset($ExtraSession["created_at"]);
                        unset($ExtraSession["updated_at"]);

                        // Get Session metas
                        $Metas  = ExtraSessionmeta::where("extra_session_id", $ExtraSession["id"])->get()->toArray();

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

                        $ExtraSession["probationers"] = $metaData;
                        $sessionData[] = $ExtraSession;
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
                    'status'    => 'error',
                    'message'   => implode('<br />', $errors),
                    'data'      => '',
                ], 200);
            }

            return;
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

    /**
     * API: missed-class-attendance
     * Submit Extra sessions attendance.
     * Brief: To provide attendance for probationers in extra sessions from the DI app
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function attendance(Request $request)
    {
      //  try {
            $errors  = [];

            $requestData = (json_decode($request->getContent(), true));

            $attendanceData = [];

            foreach ($requestData['Probationers'] as $probationer_data) {
                $session_id     = $probationer_data['session_id'];
                $component_id     = isset($probationer_data['component_Id'])? $probationer_data['component_Id'] : null;
                $probationer_id = $probationer_data['probationer_id'];
                $attendance     = $probationer_data['attendance'];
                $count      = $probationer_data['count'];
                $qualified     = isset($probationer_data['qualified'])? $probationer_data['qualified'] : null;
                $grade     = $probationer_data['grade'];
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

                if (empty($errors)) {
                    $ExtraSession   = ExtraSession::find($session_id);
                    if (empty($ExtraSession)) {
                        $errors[]   = 'Session Id not exist for probationer_id ' . $probationer_id;
                    }
                }

                $attendanceData[]   = [
                    "session_id"        => $session_id,
                    "component_id"        => $component_id,
                    "probationer_id"    => $probationer_id,
                    "attendance"    => $attendance,
                    "count"         => $count,
                    "qualified"    => $qualified,
                    "grade"    => $grade,
                ];

                // $Metas  = set_extra_session_attendance([
                //     "session_id"        => $session_id,
                //     "component_id"        => $component_id,
                //     "probationer_id"    => $probationer_id,
                //     "attendance"    => $attendance,
                //     "count"         => $count,
                //     "qualified"    => $qualified,
                //     "grade"    => $grade,
                // ]);
            }

            if (empty($errors)) {
                foreach ($attendanceData as  $attendance) {
                    $Metas  = set_extra_session_attendance($attendance);

                    if ($Metas['status'] === 'error') {
                        $errors[]   = $Metas['message'] . ' for probationer_id ' . $probationer_id;
                    }
                }
            }
            if (empty($errors)) {
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
        // } catch (\Illuminate\Database\QueryException $e) {
        //     $errorCode = $e->errorInfo[1];
        //     $response   = [
        //         'code' => 400,
        //         'status'    => "error",
        //         'message' => 'Something went wrong Please try again',
        //         // 'message' => 'ERROR: '. $e->getMessage(),
        //     ];
        //     return response()->json($response, 200);
        // }
    }

    /**
     * API: get-missed-class-attendance
     * Get Extra session attendance of the probationer for a month.
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

            $ExtraSessions  = ExtraSession::where('extra_sessionmetas.probationer_id', $probationer_id)
                ->whereMonth('extra_sessions.date', $month)
                ->whereYear('extra_sessions.date', $year)
                ->leftJoin('extra_sessionmetas', 'extra_sessions.id', '=', 'extra_sessionmetas.extra_session_id')
                ->select('extra_sessions.id as session_id', 'activity_id', 'subactivity_id', 'date', 'probationer_id', 'attendance', 'timetable_id')
                ->orderBy('extra_sessions.session_start', 'asc')
                ->get()->toArray();

            $data = [];
            foreach ($ExtraSessions as $ExtraSession) {
                if (!empty($ExtraSession['subactivity_id'])) {
                    $ExtraSession['activity_name']  = activity_name($ExtraSession['subactivity_id']);
                } else {
                    $ExtraSession['activity_name']  = activity_name($ExtraSession['activity_id']);
                }

                $ExtraSession['regular_session'] = '-';

                if (empty($ExtraSession['attendance'])) {
                    $ExtraSession['attendance'] = '-';
                } else {
                    if(!empty($ExtraSession['timetable_id'])) {
                        $timetable_id   = $ExtraSession['timetable_id'];
                        $Timetable  = \App\Models\Timetable::find($timetable_id);
                        $tt_date  = $Timetable->date;
                        $tt_session  = $Timetable->session_number;
                        $ExtraSession['regular_session']   = "(Session ". $tt_session .")\n". $tt_date;
                    }
                }

                unset($ExtraSession['activity_id']);
                unset($ExtraSession['subactivity_id']);
                unset($ExtraSession['timetable_id']);

                $data[]  = $ExtraSession;
            }
            return response()->json([
                'code'      => 200,
                'status'    => 'success',
                'data'      => $data,
            ], 200);
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * API: get-missed-classes
     * To get sessions for the DI for a date.
     * Brief: To provide attendance for probationers in missed classes from the DI app
     */
    public function get_missed_classes(Request $request)
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

            $query   = DB::table('extra_sessions')
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
                'code' => "200",
                'status'    => "failed",
                'message' => 'Something went wrong Please try again'
            ];
            return response()->json($response, 200);
        }
    }


    /** --------------------------------------------------------------------------------------
     * API Name: missed-class-attendance-data
     * User Role: drillinspector
     * Description: To get the attendance data to give attendance, grade and count in Activity section
     *
     * Author: https://github.com/rahaman-m/
     * ------------------------------------------------------------------------------------- */

    public function missed_class_attendance_data(Request $request)
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

            $extraSession   = ExtraSession::find($timetable_id);
            if(!$extraSession) {
                return response()->json([
                    [
                        'code'      => "400",
                        'status'    => "error",
                        'message'   => 'timetable_id not exist'
                    ]
                ], 200);
            }

            $activity_id    = $extraSession->activity_id;
            $subactivity_id = $extraSession->subactivity_id;

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


            $metaQ   = DB::table('extra_sessionmetas')
                ->where('extra_session_id', $timetable_id);

            if(!empty($component_id)) {
                $GLOBALS["component_id"]    = $component_id;
                $metaQ->leftJoin('extra_session_components', function($join){
                        $join->on('extra_session_components.sessionmetas_id', '=', 'extra_sessionmetas.id')
                            ->where('extra_session_components.component_id', '=', $GLOBALS["component_id"]);
                    })
                    ->leftJoin('probationers', 'probationers.id', '=', 'extra_sessionmetas.probationer_id')
                    ->select(
                        'probationers.id as Probationer_Id',
                        'probationers.Name',
                        'probationers.gender',
                        'extra_session_components.grade',
                        'extra_session_components.count',
                        'extra_session_components.qualified',
                        'extra_sessionmetas.attendance',
                        'extra_sessionmetas.updated_at',
                    );
                ;
            } else {
                $metaQ->leftJoin('probationers', 'probationers.id', '=', 'extra_sessionmetas.probationer_id')
                    ->select(
                        'probationers.id as Probationer_Id',
                        'probationers.Name',
                        'probationers.gender',
                        'extra_sessionmetas.grade',
                        'extra_sessionmetas.count',
                        'extra_sessionmetas.qualified',
                        'extra_sessionmetas.attendance',
                        'extra_sessionmetas.updated_at',
                    );
            }
            $sessionData   = $metaQ->get();

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
                'data'      => $probationer_data,
                'message' => $message,
            ];
        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->errorInfo[1];
            $response   = [
                'code' => "400",
                'status'    => "failed",
                // 'message' => 'Something went wrong Please try again'
                'message' => 'ERROR: '. $e->getMessage()
            ];
        }
        return response()->json($response, 200);
    }


    /**
     * API: missedclass-timetables
     * Description: Extra session Timetables for DI and Probationers
     */
    public function missed_class_timetables(Request $request)
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

            // $date1  = date("Y-m-01", strtotime("{$year}-{$month}-01"));
            // $date2  = date("Y-m-t", strtotime($date1));

            $Ex_Query   = ExtraSession::query();
            $ExtraSessions  = [];
            // For DI
            if ($role === 'drillinspector' || $role === 'si' || $role === 'adi') {
                $ExtraSessions  = ExtraSession::where('drillinspector_id', $user_id)
                    ->where('batch_id', $batch_id)
                    ->select('extra_sessions.id', 'batch_id', 'activity_id', 'subactivity_id', 'drillinspector_id', 'date', 'session_start', 'session_end')
                    ->orderBy('session_start', 'asc')
                    ->get()->toArray();
            } elseif ($role === 'probationer') {
                $probationer_id = probationer_id($user_id);
                $ExtraSessions  = ExtraSession::where('extra_sessionmetas.probationer_id', $probationer_id)
                    ->leftJoin('extra_sessionmetas', 'extra_sessions.id', '=', 'extra_sessionmetas.extra_session_id')
                    ->select('extra_sessions.id', 'batch_id', 'activity_id', 'subactivity_id', 'drillinspector_id', 'date', 'session_start', 'session_end')
                    ->orderBy('extra_sessions.session_start', 'asc')
                    ->get()->toArray();
            } else {
                return response()->json([
                    'code'      => 400,
                    'status'    => 'error',
                    'message'   => "Unauthorized access",
                ], 200);
            }

            $data = [];
            if (!empty($ExtraSessions)) {
                foreach ($ExtraSessions as $ExSession) {
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
                'code' => "200",
                'status'    => "failed",
                'message' => 'Something went wrong Please try again'
            ];
            return response()->json($response, 200);
        }
    }
    public function missed_class_probationers(Request $request)
    {
        $result = [];
        $errors  = [];

        $request = (json_decode($request->getContent(), true));
        $sessionId       = $request['sessionId'];
        $type       = $request['type'];
        if($type == 'missed')
        {
            $ExtraSession   = \App\Models\ExtraSession::find($sessionId);
            $Sessionmetas   = $ExtraSession->metas;

            $sessionId   = $ExtraSession->id;
            $batch_id   = $ExtraSession->batch_id;
            $batch      = batch_name($batch_id);

            $activity_id     = $ExtraSession->activity_id;
            $activity        = activity_name($activity_id);

            $subActivity_id  = $ExtraSession->subactivity_id;
            $subActivity     = "";
            if (!empty($subActivity_id)) {
                $subActivity     = activity_name($subActivity_id);
            }

            $di_id      = $ExtraSession->drillinspector_id;
            $di_name    = user_name($di_id);

            $date     = $ExtraSession->date;
            $session_start  = $ExtraSession->session_start;
            $session_start  = date('h:i A', $session_start);

            $session_end    = $ExtraSession->session_end;
            $session_end    = date('h:i A', $session_end);

            $session_time   = $session_start .' - '. $session_end;


            if (count($Sessionmetas) > 0) {

                $sl = 1;
                foreach ($Sessionmetas as $Sessionmeta) {
                    $pb_id      = $Sessionmeta->probationer_id;

                    $probationer   = \App\Models\probationer::find($pb_id);
                    $pb_name    = $probationer->Name;
                    $squad_id   = $probationer->squad_id;
                    $squad_num  = squad_number($squad_id);

                    $attendance      = $Sessionmeta->attendance;
                    $unit_count      = $Sessionmeta->count;
                    $grade      = $Sessionmeta->grade;

                    $rSession   = "";
                    $timetable_id   = $Sessionmeta->timetable_id;
                    if(!empty($timetable_id)) {
                        $Timetable  = Timetable::find($timetable_id);
                        $tt_date  = $Timetable->date;
                        $tt_session  = $Timetable->session_number;
                        $rSession   = "Session ". $tt_session ."". " (" . $tt_date .")";
                    }

                    $probationerList[] = [
                        "pb_name" => $pb_name,
                        "squad_number" => $squad_num,
                        "attendance" => $attendance,
                        "count" => $unit_count,
                        "grade" => $grade,
                        "regular_session" => $rSession
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
        elseif($type == 'extra')
        {
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
                        "attendance" => IsEmptyString($attendance),
                        "count" => IsEmptyString($unit_count),
                        "grade" => IsEmptyString($grade),
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
        else
        {
            return response()->json([
                'code'      => 201,
                'status'    => 'success',
                'message'   => 'Please send valid details',
                'data'      => '',
            ], 200);
        }
    }

    public function missedsessions(Request $request)
    {
        try{
            $request = (json_decode($request->getContent(), true));
            $drillinspector  = intval($request['DrillInspector_Id']);
            $class_type  = $request['type'];
            $Batch  = $request['batch_id'];



            $from = date('Y-m-d');
            $to = date('Y-m-d', strtotime($from . '+2 days'));

            $query = array();
            if($class_type === 'missed')
            {
                $extrasessions   = ExtraSession::select('date', 'extra_sessions.id')->leftJoin('extra_sessionmetas', 'extra_sessions.id', '=', 'extra_sessionmetas.extra_session_id')
                ->whereNull('attendance')->where('drillinspector_id', $drillinspector)->where('batch_id', $Batch)
                ->groupBy('date')
                ->get();
                $extrasessions_present   = ExtraSession::select('date', 'extra_sessions.id')->leftJoin('extra_sessionmetas', 'extra_sessions.id', '=', 'extra_sessionmetas.extra_session_id')->whereBetween(DB::raw('DATE(extra_sessionmetas.updated_at)'), array($from, $to))
                ->whereNotNull('attendance')->where('drillinspector_id', $drillinspector)->where('batch_id', $Batch)
                ->groupBy('date')
                ->get();

                 $data = $extrasessions->merge($extrasessions_present);
            }
            elseif($class_type === 'extra')
            {
                $extraclasses   = ExtraClass::select('date', 'extra_classes.id')->leftJoin('extra_classmetas', 'extra_classes.id', '=', 'extra_classmetas.extra_class_id')
                ->whereNull('attendance')->where('drillinspector_id', $drillinspector)->where('batch_id', $Batch)->groupBy('date')->get();
                $extraclasses_present   = ExtraClass::select('date', 'extra_classmetas.id')->leftJoin('extra_classmetas', 'extra_classes.id', '=', 'extra_classmetas.extra_class_id')->whereBetween(DB::raw('DATE(extra_classmetas.updated_at)'), array($from, $to))
                ->whereNotNull('attendance')->where('drillinspector_id', $drillinspector)->where('batch_id', $Batch)->groupBy('date')->get();

                 $data = $extraclasses->merge($extraclasses_present);
            }
            else
            {
                $data = '';
            }
            if(!empty($data))
            {
                $response   = [
                    'code' => "200",
                    'status'    => "success",
                    'data' => $data,
                ];
            }
            else
            {
                $response   = [
                    'code' => "200",
                    'status'    => "success",
                    'data' => "No Sessions",
                ];
            }
        }
        catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->errorInfo[1];
            $response   = [
                'code' => "401",
                'status'    => "failed",
                'message' => 'Something went wrong Please try again'
            ];
        }

        return response()->json($response, 200);
    }

    public function probationer_missed_classes(Request $request)
    {
        try {
            $result = [];
            $errors  = [];

            isset($request->probationer_id) ? $probationer_id = remove_specialcharcters($request->probationer_id) : $probationer_id = '';

            if (empty($probationer_id)) {
                $errors[] = 'probationer_id is missing.';
            }
            if (!empty($errors)) {
                return response()->json([
                    'code'      => 400,
                    'status'    => 'error',
                    'message'   => implode(' ', $errors),
                ], 200);
            }
            $squad_id = squad_id($probationer_id);

            $timetables = \App\Models\Timetable::where('squad_id', $squad_id)
            ->whereNotNull("activity_id")
            ->orderBy("activity_id")
            ->orderBy("subactivity_id")
            ->get();

            $timetables_count   = count($timetables);

            $activities     = [];
            $subactivities  = [];
            $tt_ids         = [];

            if($timetables_count > 0) {
                foreach($timetables as $timetable) {

                    $tt_ids[]     = $timetable->id;
                    $tt_activity_id     = $timetable->activity_id;
                    $tt_subactivity_id  = $timetable->subactivity_id;

                    if( isset($activities[$tt_activity_id]) ) {
                        if(!in_array($tt_subactivity_id, $activities[$tt_activity_id])) {
                            $activities[$tt_activity_id][]    = $tt_subactivity_id;
                            $subactivities[]    = $tt_subactivity_id;
                        }
                    } else {
                        $activities[$tt_activity_id][]    = $tt_subactivity_id;
                        $subactivities[]    = $tt_subactivity_id;
                    }
                }
            }
            foreach($activities as $activity_id => $data) {
                foreach($data as $subactivity_id) {
                    $total = 0;
                    $total = \App\Models\Timetable::where('squad_id', $squad_id)
                        ->where("activity_id", $activity_id)
                        ->where("subactivity_id", $subactivity_id)
                        ->where("session_start", '>', 0)
                        ->count();

                    $getAttnsQ = \App\Models\Timetable::query()
                        ->where("probationers_dailyactivity_data.probationer_id", $probationer_id)
                        ->where("probationers_dailyactivity_data.activity_id", $activity_id);
                    if(!empty($subactivity_id)) {
                        $getAttnsQ->where("probationers_dailyactivity_data.subactivity_id", $subactivity_id);
                    }
                    $getAttns  = $getAttnsQ->leftJoin('probationers_dailyactivity_data', 'probationers_dailyactivity_data.timetable_id', '=', 'timetables.id')
                        ->select('probationers_dailyactivity_data.attendance', 'probationers_dailyactivity_data.timetable_id', 'probationers_dailyactivity_data.activity_id' , 'probationers_dailyactivity_data.subactivity_id')
                        ->groupBy('probationers_dailyactivity_data.timetable_id')
                        ->get();

                    $attended_count = count($getAttns);

                    // return response()->json([
                    //     'code'      => 200,
                    //     'status'    => 'success',
                    //     'data'      => $attended_count,
                    // ], 200);

                    $attended = 0;

                    if( $total > 0 ) {
                        foreach($getAttns as $getAttn) {
                            $timetable_id   = $getAttn->timetable_id;
                            if( in_array($getAttn->attendance, ['P', 'MDO', 'NCM']) ) {
                                $attended++;
                            } else {
                                $Extrasession = \App\Models\ExtraSessionmeta::where('probationer_id', $probationer_id)
                                    ->whereIn('attendance', ['P', 'MDO', 'NCM'])
                                    ->where('timetable_id', $timetable_id)
                                    ->count();

                                if($Extrasession > 0) {
                                    $attended++;
                                }
                            }
                        }
                        $missed = $attended_count - $attended;


                        $dt[] = [
                            'activity' => activity_name($subactivity_id) . ' ('. activity_name($activity_id) .')',
                            'missed' => $missed,
                        ];
                    } else {
                        $dt[] = [
                            'activity' => activity_name($subactivity_id) . ' ('. activity_name($activity_id) .')',
                            'missed' => '0',

                        ];
                    }
                }
            }
            return response()->json([
                'code'      => 200,
                'status'    => 'success',
                'data'      => $dt,
            ], 200);
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

}
