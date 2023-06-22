<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\probationer;
use App\Models\Squad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ProbationersDailyactivityData;
use App\Models\ExtraSession;
use App\Models\ExtraSessionmeta;
use App\Models\ExtraClass;
use App\Models\ExtraClassmeta;

class Squads extends Controller
{
    /** -----------------------------------------------------------------------
     * API Name: squads
     * User Role: drillinspector
     * Description: Squad list for DI App after selecting Batch after login
     *
     * Author: https://github.com/abirudhk/
     * --------------------------------------------------------------------- */
    public function SquadList(Request $request)
    {
        try {
            isset($request->DrillInspector_Id) ? $Drillinspector = remove_specialcharcters($request->DrillInspector_Id) : $Drillinspector = '';
            isset($request->Batch_Id) ? $Batch = remove_specialcharcters($request->Batch_Id) : $Batch = '';

            $from = date('Y-m-d');
            $to = date('Y-m-d', strtotime($from . '+2 days'));

            if (!is_numeric($Drillinspector) || !is_numeric($Batch)) {
                $response = [
                    'code' => "201",
                    'status' => "success",
                    'message' => "Invalid Batch Id",
                ];
                return response()->json($response, 200);
            }

             $squad_id = Squad::where('Batch_Id', $Batch)->pluck('id')->toArray();
             $check_assign_activity = DB::table('squad_activity_trainer')->where('staff_id', $Drillinspector)->whereIn('squad_id', $squad_id)->count();

         //   $check_main_squad = Squad::where('batch_id', $Batch)->where('DrillInspector_Id', $Drillinspector)->count();

           // print_r($check_assign_activity);exit;
            if ($check_assign_activity === 0) {
                $data = DB::select("select `squads`.`id`, `squads`.`Batch_Id`, `squads`.`DrillInspector_Id`, `squads`.`SquadNumber`, `squads`.`created_at`, `squads`.`updated_at`, CASE when id != '0' THEN '0' else '0' end AS assignedid from `squads`
             where `DrillInspector_Id` = '$Drillinspector' and `batch_id` = '$Batch' group by `squads`.`id`");

            } else {
                $squad_activity = DB::table('squad_activity_trainer')->where('staff_id', $Drillinspector)
                ->where('batch_id', $Batch)
                ->leftJoin('squads', 'squads.id', '=', 'squad_activity_trainer.squad_id')
                ->select('squads.id', 'squads.Batch_Id', 'squads.DrillInspector_Id', 'squads.SquadNumber', 'squads.created_at', 'squads.updated_at', DB::Raw('IFNULL( `squad_activity_trainer`.`id`, 0 ) as assignedid'))
                ->groupBy('squads.id')
                ->get();

                $squads = DB::table('squads')->where('DrillInspector_Id', $Drillinspector)
                ->where('Batch_Id', $Batch)
                ->select('squads.id', 'squads.Batch_Id', 'squads.DrillInspector_Id', 'squads.SquadNumber', 'squads.created_at', 'squads.updated_at', DB::raw("(CASE WHEN squads.id != '0' THEN '0' else '0' end) AS assignedid"))
                ->groupBy('squads.id')
                ->get();

                $data = array_merge($squads->toArray(), $squad_activity->toArray());
            }

           // $extra_sessions = DB::table('extra_sessions')->where('drillinspector_id', $Drillinspector)->where('batch_id', $Batch)->count();
           // $extra_classes = DB::table('extra_classes')->where('drillinspector_id', $Drillinspector)->where('batch_id', $Batch)->count();

           $extraclasses   = ExtraClass::select('date', 'extra_classes.id')->leftJoin('extra_classmetas', 'extra_classes.id', '=', 'extra_classmetas.extra_class_id')
                ->whereNull('attendance')->where('drillinspector_id', $Drillinspector)->where('batch_id', $Batch)->groupBy('extra_class_id')->count();
            $extraclasses_present   = ExtraClass::select('date', 'extra_classmetas.id')->leftJoin('extra_classmetas', 'extra_classes.id', '=', 'extra_classmetas.extra_class_id')->whereBetween(DB::raw('DATE(extra_classmetas.updated_at)'), array($from, $to))
                ->whereNotNull('attendance')->where('drillinspector_id', $Drillinspector)->where('batch_id', $Batch)->groupBy('extra_class_id')->count();


            // $extra_sessions = DB::table('extra_sessions')->where('drillinspector_id', $Drillinspector)->where('batch_id', $Batch)->whereBetween('date', [$to, $from])->count();
            // $extra_classes = DB::table('extra_classes')->where('drillinspector_id', $Drillinspector)->where('batch_id', $Batch)->whereBetween('date', [$to, $from])->count();

            $extrasession   = ExtraSession::select('date', 'extra_sessions.id')->leftJoin('extra_sessionmetas', 'extra_sessions.id', '=', 'extra_sessionmetas.extra_session_id')
                                ->whereNull('attendance')->where('drillinspector_id', $Drillinspector)->where('batch_id', $Batch)->groupBy('extra_session_id')->count();
            $extrasessions_present   = ExtraSession::select('date', 'extra_sessions.id')->leftJoin('extra_sessionmetas', 'extra_sessions.id', '=', 'extra_sessionmetas.extra_session_id')->whereBetween(DB::raw('DATE(extra_sessionmetas.updated_at)'), array($from, $to))
                                ->whereNotNull('attendance')->where('drillinspector_id', $Drillinspector)->where('batch_id', $Batch)->groupBy('extra_session_id')->count();


            $extrasessions = $extrasession+$extrasessions_present;
            $extra_classes = $extraclasses+$extraclasses_present;


            if ($extrasessions === 0) {
                $extrasessions_status = 'false';
            } else {
                $extrasessions_status = 'true';
            }

            if ($extra_classes === 0) {
                $extraclasses_status = 'false';
            } else {
                $extraclasses_status = 'true';
            }

            $response = [
                'code' => '200',
                'status' => "success",
                'extra_session_status' => $extrasessions_status,
                'extra_class_status' => $extraclasses_status,
                'data' => $data,
            ];
        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->errorInfo[1];
            $response = [
                'code' => "400",
                'status' => "failed",
                'message' => 'Something went wrong Please try again',
            ];
        }
        return response()->json($response, 200);
    }

    /** -----------------------------------------------------------------------
     * API Name: probationerslist
     * User Role: drillinspector
     * Description: Squad wise probationer list for DI App
     *
     * Author: https://github.com/abirudhk/
     * --------------------------------------------------------------------- */

    public function ProbationerList(Request $request)
    {
        try
        {
            $user_role = Auth::user()->role;
            if ($user_role === 'drillinspector' || $user_role === 'si' || $user_role === 'adi') {
                $user_id = Auth::id();
                // $squad_Id = Squad::where('DrillInspector_Id', $user_id)->value('id');
                $squad_Id = $request->squad_id;
            } else {
                $user_id = Auth::id();
                $squad_Id = probationer::where('user_id', $user_id)->value('squad_id');
                if ($squad_Id !== $request->squad_id) {
                    $response = [
                        'code' => "401",
                        'message' => "Unauthorised",
                    ];
                    return response()->json($response, 401);
                }
            }

            if (!is_numeric($squad_Id)) {
                $response = [
                    'code' => "201",
                    'status' => "success",
                    'message' => "Invalid Squad Id",
                ];
                return response()->json($response, 200);
            }
            $data = Probationer::where('probationers.squad_id', $squad_Id)->select('probationers.id as id', 'probationers.squad_id as Squad_Id', 'probationers.id as Probationer_Id', 'probationers.Name', 'probationers.gender')->orderBy('position_number', 'asc')->get();

            $response = [
                'status' => "success",
                'data' => $data,
            ];
        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->errorInfo[1];
            $response = [
                'code' => "200",
                'status' => "failed",
                'message' => 'Something went wrong Please try again',
            ];
        }
        return response()->json($response, 200);
    }

    /** -----------------------------------------------------------------------
     * API Name: sessions
     * User Role: drillinspector
     * Description:
     *
     * Author: https://github.com/abirudhk/
     * --------------------------------------------------------------------- */

    public function sessiondata(Request $request)
    {
        try
        {
        isset($request->squad_id) ? $squad_Id = remove_specialcharcters($request->squad_id) : $squad_Id = '';
        isset($request->session_date) ? $session_date = remove_specialcharcters($request->session_date) : $session_date = '';
        isset($request->DrillInspector_Id) ? $drillinspector = remove_specialcharcters($request->DrillInspector_Id) : $drillinspector = '';
        isset($request->assignedid) ? $assignedid = remove_specialcharcters($request->assignedid) : $assignedid = '';
        //  $assignedid = remove_specialcharcters($request->assignedid);

        if (!is_numeric($squad_Id) || !is_numeric($drillinspector)) {
            $response = [
                'code' => "201",
                'status' => "success",
                'message' => "Invalid Details",
            ];
            return response()->json($response, 200);
        }

        if (empty($assignedid)) {
            $query = DB::table('timetables')
                ->where('squad_id', $squad_Id)
                ->where('date', $session_date)
                ->where('session_type', 'regular')
                ->where('activity_id', '>', 0)
                ->whereNotNull('activity_id')
                ->select('activity_id', 'subactivity_id', 'session_number', 'session_type', 'session_start', 'session_end', 'id')->orderBy('session_number')
                ->get();
        } else {

            $staff_assigned_id = DB::table('squad_activity_trainer')->where('staff_id', $drillinspector)->where('squad_id', $squad_Id)->pluck('activity_id')->toArray();
            if (!empty($staff_assigned_id)) {
                $query = DB::table('timetables')
                    ->where('squad_id', $squad_Id)
                    ->where('date', $session_date)
                    ->where('session_type', 'regular')
                    ->whereIn('activity_id', $staff_assigned_id)
                    ->select('activity_id', 'subactivity_id', 'session_number', 'session_type', 'session_start', 'session_end', 'id')->orderBy('session_number')
                    ->get();
            } else {
                $response = [
                    'code' => "201",
                    'status' => "success",
                    'message' => "Invalid Details",
                ];
                return response()->json($response, 200);
            }
        }

        if (count($query) > 0) {

            $i = 0;
            $data = array();
            foreach ($query as $dt) {
                $i = 0;
                $activities = Activity::withTrashed()->where('id', $dt->activity_id)->where('type', 'activity')->select('activities.*', DB::raw("CASE when unit != '' then 'unit' when has_grading != '0' THEN 'grade' when has_qualify != '0' THEN 'qualify' else '' end AS activity_type"))->get()->toArray();
                $subactivities = Activity::withTrashed()->where('id', $dt->subactivity_id)->select('activities.*', DB::raw("CASE when unit != '' then 'unit' when has_grading != '0' THEN 'grade' when has_qualify != '0' THEN 'qualify' else '' end AS activity_type"))->get()->toArray();
                if (count($subactivities) > 0) {
                    $si = 0;
                    foreach ($subactivities as $subactivity) {
                        $components = Activity::where('parent_id', $subactivity['id'])->select('activities.*', DB::raw("CASE when unit != '' then 'unit' when has_grading != '0' THEN 'grade' when has_qualify != '0' THEN 'qualify' else '' end AS activity_type"))->get()->toArray();
                        $subactivities[$si]['components'] = $components;
                        $si++;
                    }
                }
                $activities[$i]['subactivities'] = $subactivities;

                $data1 = array(
                    'ActivitySessionsData' => $activities,
                );

                $data[] = array(
                    'session_number' => (string) $dt->session_number,
                    'timetable_id' => $dt->id,
                    'session_start' => (string) $dt->session_start,
                    'session_end' => (string) $dt->session_end,
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
        }
        catch(\Illuminate\Database\QueryException $e){
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
     * API Name: insertactivitydata
     * User Role: drillinspector
     * Description: To store attendance data for the probationers from DI App Activity section
     *
     * Author: https://github.com/abirudhk/
     * ------------------------------------------------------------------------------------- */
    public function ActivityData(Request $request)
    {
        DB::beginTransaction();
        try
        {
            $request = (json_decode($request->getContent(), true));

            isset($request['DrillInspector_Id']) ? $DrillinspectorId = remove_specialcharcters($request['DrillInspector_Id']) : $DrillinspectorId = '';
            isset($request['timetable_id']) ? $timetable = remove_specialcharcters($request['timetable_id']) : $timetable = '';
            isset($request['Batch_Id']) ? $BatchId = remove_specialcharcters($request['Batch_Id']) : $BatchId = '';
            isset($request['Squad_Id']) ? $SquadId = remove_specialcharcters($request['Squad_Id']) : $SquadId = '';
            isset($request['activity']) ? $activity = remove_specialcharcters($request['activity']) : $activity = '';
            isset($request['sub_activity']) ? $subactivity = remove_specialcharcters($request['sub_activity']) : $subactivity = '';
            isset($request['component']) ? $component = remove_specialcharcters($request['component']) : $component = '';

            $timestamp = date('Y-m-d H:i:s');

            if (!is_numeric($DrillinspectorId) || !is_numeric($timetable) || !is_numeric($BatchId) || !is_numeric($SquadId) || !is_numeric($activity) || !is_numeric($subactivity) || !is_numeric($component)) {
                $response = [
                    'code' => "201",
                    'status' => "success",
                    'message' => "Invalid  Details",
                ];
                return response()->json($response, 200);
            }

            $subactivity  = empty($subactivity) ? null : $subactivity;
            $component  = empty($component) ? null : $component;

            foreach ($request['Probationers'] as $prob) {
                ($prob['qualified'] === '') ? $qualify = null : $qualify = $prob['qualified'];

                isset($prob['id']) ? $prob_id = remove_specialcharcters($prob['id']) : $prob_id = '';
                if (!is_numeric($prob_id)) {
                    $response = [
                        'code' => "201",
                        'status' => "success",
                        'message' => "Invalid Details",
                    ];
                    return response()->json($response, 200);
                }
                $data = DB::table('probationers_dailyactivity_data')->updateOrInsert(
                    [
                        'probationer_id' => $prob_id,
                        'timetable_id' => $timetable,
                        'component_id' => $component,
                    ],
                    [
                        'staff_id' => $DrillinspectorId,
                        'Batch_id' => $BatchId,
                        'squad_id' => $SquadId,
                        'activity_id' => $activity,
                        'subactivity_id' => $subactivity,
                        'grade' => remove_specialcharcters($prob['grade']),
                        'count' => $prob['count'],
                        'qualified' => $qualify,
                        'date' => remove_specialcharcters($prob['date']),
                        'attendance' => remove_specialcharcters($prob['attendance']),
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp,
                    ]
                );
            }
            DB::commit();
            $response = [
                'code' => "200",
                'status' => "success",
                'message' => "Data saved successfully",
            ];
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            $errorCode = $e->errorInfo[1];
            $response = [
                'code' => "201",
                'status' => "failed",
                'message' => 'ERROR: '. $e->getMessage(),
            ];
        }
        return response()->json($response, 200);
    }

    /** ------------------------------------------------------------------------------------------------
     * API Name: squaddata
     * User Role: drillinspector
     * Description: To get the attendance data to give attendance, grade and count in Activity section
     *
     * Author: https://github.com/abirudhk/
     * ---------------------------------------------------------------------------------------------- */
    public function verifysquaddata(Request $request)
    {
        try
        {
        $request = (json_decode($request->getContent(), true));

        isset($request['squad_id']) ? $squad_Id = remove_specialcharcters($request['squad_id']) : $squad_Id = '';
        isset($request['Timetable_Id']) ? $TimetableId = remove_specialcharcters($request['Timetable_Id']) : $TimetableId = '';
        isset($request['component_Id']) ? $componentid = remove_specialcharcters($request['component_Id']) : $componentid = '';
        $subactivityid = isset($request['subactivity_Id']) ? $request['subactivity_Id'] : 0;

        $componentid = sanitize_activity_id($componentid);
        $subactivityid = sanitize_activity_id($subactivityid);

        $probationers_list = Probationer::where('probationers.squad_id', $squad_Id)->select('id')->orderBy('position_number', 'asc')->get();
        $check_attendance_data = ProbationersDailyactivityData::where('timetable_id', $TimetableId)->where('component_id', $componentid)->get();

        if($componentid != null)
        {
            $activity_types = Activity::where('id', $componentid)->select(DB::raw("CASE when unit != '' then 'unit' when has_grading != '0' THEN 'grade' when has_qualify != '0' THEN 'qualify' else '' end AS activity_type, unit"))->first();
        }
        elseif($subactivityid != null)
        {
            $activity_types = Activity::where('id', $subactivityid)->select(DB::raw("CASE when unit != '' then 'unit' when has_grading != '0' THEN 'grade' when has_qualify != '0' THEN 'qualify' else '' end AS activity_type, unit"))->first();
        }
        else
        {
            $activity_types = Activity::where('id', $request['activity_id'])->select(DB::raw("CASE when unit != '' then 'unit' when has_grading != '0' THEN 'grade' when has_qualify != '0' THEN 'qualify' else '' end AS activity_type, unit"))->first();
        }


        $unit = ($activity_types->unit != null) ?  $activity_types->unit : '';
        $activityType = $activity_types->activity_type;
        $activityType = empty($activityType)? "unit" : $activityType;

        if (count($check_attendance_data) === 0) {
            $check_attendance_data = ProbationersDailyactivityData::where('timetable_id', $TimetableId)->where('subactivity_id', $subactivityid)->where('component_id', $componentid)->get();
        }




        if (count($check_attendance_data) > 0) {
            foreach ($probationers_list as $probationerlists) {
                $data = ProbationersDailyactivityData::where('timetable_id', $TimetableId)
                    ->where('probationers.id', $probationerlists->id)
                    ->where('component_id', $componentid)
                    ->leftJoin('probationers', 'probationers.id', '=', 'probationers_dailyactivity_data.probationer_id')
                    ->select('probationers.squad_id as Squad_Id', 'probationers.id as Probationer_Id', 'probationers.Name', 'probationers.gender', 'probationers_dailyactivity_data.id as Inserted_id', 'probationers_dailyactivity_data.grade', 'probationers_dailyactivity_data.count','probationers_dailyactivity_data.qualified', 'probationers_dailyactivity_data.attendance' , 'probationers_dailyactivity_data.updated_at')
                    ->first();

                if (empty($data)) {
                    $Prdt = Probationer::where('probationers.id', $probationerlists->id)
                        ->select('probationers.squad_id as Squad_Id', 'probationers.id as Probationer_Id', 'probationers.Name', 'probationers.gender')
                        ->first();

                    $data = array(
                        "Squad_Id" => $Prdt->Squad_Id,
                        "Probationer_Id" => $Prdt->Probationer_Id,
                        "Name" => $Prdt->Name,
                        "gender" => $Prdt->gender,
                        "Inserted_id" => '',
                        "grade" => "",
                        "count" => "",
                        "qualified" => "",
                        "unit" => $unit,
                        "activity_type" => $activityType,
                        "attendance" => "",
                    );
                }
                else
                {
                    $data->qualified = qualified_values($data->qualified);
                    $data->unit = $unit;
                    $data->activity_type = $activityType;
                }
                $dt[] = $data;
                $updated_time = $data->updated_at;
               global $updated_time;
                unset($data);


            }

            $response = [
                'code' => '200',
                'status' => "success",
                'data' => $dt,
                'message' => "Attendance given for this session on " . date('d-m-Y h:i a', strtotime($updated_time)),
            ];
            return response()->json($response, 200);
        } else {
            $data = Probationer::where('probationers.squad_id', $squad_Id)
                ->select('probationers.id as id', 'probationers.squad_id as Squad_Id', 'probationers.id as Probationer_Id', 'probationers.Name', 'probationers.gender')
                ->orderBy('position_number', 'asc')
                ->get();
            if (count($data) > '0') {
                foreach ($data as $dt) {
                    $probationers[] = array(
                        "Squad_Id" => $dt->Squad_Id,
                        "Probationer_Id" => $dt->id,
                        "Name" => $dt->Name,
                        "gender" => $dt->gender,
                        "Inserted_id" => '',
                        "grade" => "",
                        "count" => "",
                        "qualified" => '',
                        "unit" => $unit,
                        "activity_type" => $activityType,
                        "attendance" => "P",
                    );

                }
                $response = [
                    'code' => '200',
                    'status' => "success",
                    'data' => $probationers,
                    'message' => '',
                ];
            } else {
                $response = [
                    'code' => '200',
                    'status' => "success",
                    'data' => "Invalid details",
                ];
            }

            return response()->json($response, 200);
        }
        }
        catch(\Illuminate\Database\QueryException $e){
            $errorCode = $e->errorInfo[1];
            $response   = [
                'code' => "200",
                'status'    => "failed",
                'message' => 'Something went wrong Please try again'
            ];
        }
    }
}
