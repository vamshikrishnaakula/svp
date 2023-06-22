<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Activity;

use DateTime;
use Illuminate\Support\Facades\Auth;

class Activities extends Controller
{
    public function ActivityList(Request $request)
    {
        try {

            isset($request->batch_id) ? $batch_id = remove_specialcharcters($request->batch_id) : $batch_id = '';

            $activities   = Activity::where('batch_id', $batch_id)
                ->where('type', 'activity')->select('id', 'name', 'type')
                ->get()->toArray();

            if (count($activities) > 0) {
                $i = 0;
                foreach ($activities as $activity) {
                    $subactivities   = Activity::where('parent_id', $activity['id'])->select('id', 'name', 'type')->get()->toArray();

                    if (count($subactivities) > 0) {
                        $si = 0;
                        foreach ($subactivities as $subactivity) {
                            $components   = Activity::where('parent_id', $subactivity['id'])->select('id', 'name', 'type')->get()->toArray();
                            $subactivities[$si]['components']   = $components;
                            $si++;
                        }
                    }
                    $activities[$i]['subactivities']   = $subactivities;

                    $i++;
                }
            }
            $response   = [
                'status'    => "success",
                'data'      => $activities,
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

    /** -----------------------------------------------------------------------
     * API Name: gettimetables
     * User Role: probationer, drillinspector
     * Description: Get Timetables for DI and Probationers
     *
     * Author: https://github.com/rahaman-m
     * --------------------------------------------------------------------- */
    public function get_timetables(Request $request)
    {
        $user   = Auth::user();
        if ($user->role === 'probationer') {
            $probationer    = probationer_data(['user_id' => $user->id]);
            if (!$probationer) {
                return response()->json([
                    'code' => "401",
                    'status'    => "failed",
                    'message' => 'Something went wrong, please try again.'
                ], 401);
            }

            $squad_id = $probationer->squad_id;
        } else {
            $squad_id   = remove_specialcharcters($request->squad_id);
        }

        try {
            $date_range = remove_specialcharcters($request->day);

            if (!in_array($date_range, ['today', 'tomorrow', 'week'])) {
                return response()->json([
                    'code'      => 400,
                    'status'    => 'error',
                    'message'   => 'Invalid date submitted.',
                    'data'      => '',
                ], 200);
            }

            if ($date_range === "today") {
                $from_date = date("Y-m-d");
                $to_date = $from_date;
            } else if ($date_range === "tomorrow") {
                $from_date = date("Y-m-d", strtotime(' +1 day'));
                $to_date = $from_date;
            } else if ($date_range === "week") {
                $from_date = date("Y-m-d");
                $to_date = date("Y-m-d", strtotime(' +6 day'));
            }

            $date1 = new DateTime($from_date);
            $date2 = new DateTime($to_date);

            $diff = $date1->diff($date2);
            $days = $diff->days;

            $TimetableData  = [];

            for ($i = 0; $i <= $days; $i++) {
                $date       = date('Y-m-d', strtotime($from_date . ' + ' . $i . ' days'));
                $dayName    = date('l', strtotime($date));

                $TimetableData[$i]['date']  = $date;
                $TimetableData[$i]['day']   = $dayName;

                $timetableQ = \App\Models\Timetable::where('squad_id', $squad_id)
                    ->where('activity_id', '<>', 0)
                    ->whereNotNull('activity_id')
                    ->whereDate('date', $date)
                    ->orderBy('session_number', 'asc');
                $timetables = $timetableQ->get()->toArray();

                // Max session_number for the day
                $maxSessionNum = $timetableQ->max('session_number');
                $sessionCount   = max(5, $maxSessionNum);

                $data   = range(1, $sessionCount);
                $ti     = 1;
                if (!empty($timetables)) {
                    foreach ($timetables as $timetable) {

                        if (!empty($timetable['activity_id']) && !empty($timetable['session_start'])) {
                            $activity_id    = $timetable['activity_id'];

                            $session_start  = date('h:i A', $timetable['session_start']);
                            $session_end    = date('h:i A', $timetable['session_end']);
                            $session_time   = $session_start . ' - ' . $session_end;

                            $timetable['session_time']   = $session_time;
                            $timetable['date']   = date('d-m-Y', strtotime($timetable['date']));

                            $act_id = $timetable['activity_id'];
                            if (!empty($timetable['subactivity_id'])) {
                                $act_id = $timetable['subactivity_id'];
                            }
                            $activity_name = \App\Models\Activity::where('id', $act_id)->value('name');
                            $timetable['activity_name'] = $activity_name;

                            unset($timetable['session_type']);
                            unset($timetable['session_start']);
                            unset($timetable['session_end']);
                            unset($timetable['created_at']);
                            unset($timetable['updated_at']);
                        }

                        for ($si = 0; $si < $sessionCount; $si++) {
                            $sn = $si + 1;
                            if ($sn === intval($timetable["session_number"])) {
                                $timetable['session_number']   = "Session " . $timetable['session_number'];
                                $data[$si]  = $timetable;
                            }
                        }

                        $ti++;
                    }
                }

                for ($di = 0; $di < $sessionCount; $di++) {
                    if (!is_array($data[$di])) {
                        $data[$di]  = [
                            "id"        => 0,
                            "batch_id"  => 0,
                            "squad_id"  => 0,
                            "activity_id"       => 0,
                            "subactivity_id"    => 0,
                            "date"              => date('d-m-Y', strtotime($date)),
                            "session_number"    => "Session " . ($di + 1),
                            "session_time"      => "",
                            "activity_name"     => "-"
                        ];
                    }
                }

                $TimetableData[$i]['timetable']  = $data;
            }

            return response()->json([
                'code'      => 200,
                'status'    => 'success',
                'data'      => $TimetableData,
            ], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->errorInfo[1];
            $response   = [
                'code' => "400",
                'status'    => "failed",
                'message' => 'Something went wrong Please try again'
            ];
            return response()->json($response, 200);
        }
    }


    /** -----------------------------------------------------------------------
     * API Name: gettodaystimetables
     * User Role: drillinspector
     * Description: Get Todays Timetables
     *
     * Author: https://github.com/rahaman-m
     * --------------------------------------------------------------------- */
    public function get_todays_timetables(Request $request)
    {
        $user   = Auth::user();
        if (!in_array($user->role, ['drillinspector', 'probationer', 'si', 'adi'])) {
            return response()->json([
                'code' => "401",
                'status'    => "failed",
                'message' => 'You are not allowed to access this resource.'
            ], 401);
        }

        if($user->role === 'probationer') {
            $squad_id   = probationer_data()->squad_id;
        } else {
            $squad_id   = intval($request->squad_id);
        }

        try {

            $today      = date('Y-m-d');

            if (empty($squad_id) || !is_numeric($squad_id)) {
                $response   = [
                    'code' => "400",
                    'status'    => "failed",
                    'message'  => "Invalid request parameter(s)."
                ];
                return response()->json($response, 200);
            }

            $timetables = \App\Models\Timetable::where('squad_id', $squad_id)
                ->where('activity_id', '<>', 0)
                ->whereNotNull('activity_id')
                ->whereDate('date', $today)
                ->orderBy('session_number', 'asc')->get()->toArray();

            $data   = [];
            foreach ($timetables as $timetable) {
                if (!empty($timetable['subactivity_id'])) {
                    $activity   = activity_name($timetable['subactivity_id']);
                } else {
                    $activity   = activity_name($timetable['activity_id']);
                }

                $data[] = [
                    'key'   => "Session " . $timetable['session_number'],
                    'value' => $activity,
                ];
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
}
