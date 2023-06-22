<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;


/** ----------------------------------------------------------------------------------
 * Function: current_batch
 *
 * @return  int current batch id
 *
 * Author: https://github.com/rahaman-m
 * ------------------------------------------------------------------------------- */
function current_batch()
{
    $current_batch = Session::get('current_batch');
    if(!empty($current_batch) && is_numeric($current_batch))
    {
        return $current_batch;
    }else
    {
       $get_batch = App\Models\Batch::orderBy('BatchName', 'DESC')->first();
    //    $batch_id = $get_batch->id;
    //    Session::put('current_batch', intval($batch_id));
    //     return $batch_id;
    }
}

/** ----------------------------------------------------------------------------------
 * Function: set_current_batch
 * @param   int $batch_id
 *
 * @return  int current batch id
 *
 * Author: https://github.com/rahaman-m
 * ------------------------------------------------------------------------------- */
function set_current_batch(int $batch_id)
{
    return Session::put('current_batch', intval($batch_id));
}

/** ----------------------------------------------------------------------------------
 * Function: user_name
 * @param   int $user_id
 *
 * @return  string User Name
 *
 * Author: https://github.com/rahaman-m
 * ------------------------------------------------------------------------------- */
function user_name(int $user_id = null)
{
    if(empty($user_id)) {
        if(Auth::check()) {
            return Auth::user()->name;
        }
    }
    $name   = App\Models\User::where('id', $user_id)->value('name');
    return $name;
}

/** ----------------------------------------------------------------------------------
 * Function: batch_name
 * @param   int $batch_id
 *
 * @return  string Batch Name
 *
 * Author: https://github.com/rahaman-m
 * ------------------------------------------------------------------------------- */
function batch_name(int $batch_id)
{
    $Batch   = App\Models\Batch::find($batch_id);
    if($Batch) {
        return $Batch->BatchName;
    }
    return "";
}

/** ----------------------------------------------------------------------------------
 * Function: batch_id
 * @param   string $batch Name
 *
 * @return  int Batch id
 *
 * Author: https://github.com/abirudhk
 * ------------------------------------------------------------------------------- */
function get_batch_id(string $batch_name)
{
    $Batch   = App\Models\Batch::where('BatchName', $batch_name)->first();
    if($Batch) {
        return $Batch->id;
    }
    return "";
}

/** ----------------------------------------------------------------------------------
 * Function: squad_number
 * @param   int $squad_id
 *
 * @return  string Squad Number
 *
 * Brief: Get Squad Number from Squad Id
 * Author: https://github.com/rahaman-m
 * ------------------------------------------------------------------------------- */
function squad_number($squad_id)
{
    $Squad   = App\Models\Squad::find($squad_id);
    if($Squad) {
        return $Squad->SquadNumber;
    }
    return false;
}

/** ----------------------------------------------------------------------------------
 * Function: probationer_id
 * @param   int $user_id (optional)
 *
 * @return  int Probationer Id
 * Brief:   Get Logged in Probationer Id (or from user id, if supplied)
 *
 * Author: https://github.com/rahaman-m
 * ------------------------------------------------------------------------------- */
function probationer_id(int $user_id = 0)
{
    if(empty($user_id)) {
        $user    = Auth::user();
        if($user->role !== 'probationer') {
            return false;
        }
        $user_id = $user->id;
    }
    $probationer_id   = App\Models\probationer::where('user_id', $user_id)->value('id');
    return $probationer_id;
}

/** ----------------------------------------------------------------------------------
 * Function: probationer_name
 * @param   int $probationer_id
 *
 * @return  string Probationer Name
 *
 * Author: https://github.com/rahaman-m
 * ------------------------------------------------------------------------------- */
function probationer_name(int $probationer_id)
{
    if(empty($probationer_id)) {
        return false;
    }
    $probationer   = App\Models\probationer::where('id', $probationer_id)->value('Name');
    // return $probationer->Name;
    return $probationer;
}

/** ----------------------------------------------------------------------------------
 * Function: probationer_name
 * @param   int $probationer_id
 *
 * @return  string Probationer Name
 *
 * Author: https://github.com/abirudhk
 * ------------------------------------------------------------------------------- */
function probationer_rollnumber(int $probationer_id)
{
    if(empty($probationer_id)) {
        return false;
    }
    $probationer   = App\Models\probationer::where('id', $probationer_id)->value('RollNumber');
    // return $probationer->Name;
    return $probationer;
}

/** ----------------------------------------------------------------------------------
 * Function: squad_id
 * @param   int $probationer_id
 *
 * @return  int squad id
 *
 * Author: https://github.com/abirudhk
 * ------------------------------------------------------------------------------- */
function squad_id($probationer_id)
{
    if(empty($probationer_id)) {
        return false;
    }
    $squad_id   = App\Models\probationer::where('id', $probationer_id)->value('squad_id');
    return $squad_id;
}

/** ----------------------------------------------------------------------------------
 * Function: probationer_data
 * @param   array|int $model_id
 *
 * @return  object Probationer
 *
 * Brief: To get probationer data/model with probationer_id (or user_id passed as associative array with key user_id in the parameter)
 * Author: https://github.com/rahaman-m
 * ------------------------------------------------------------------------------- */
function probationer_data($model_id = null)
{
    if(empty($model_id)) {
        $User = Auth::user();
        if($User->role !== "probationer") {
            return false;
        }

        return App\Models\probationer::where('user_id', $User->id)->first();

    } elseif(is_array($model_id) && array_key_exists('user_id', $model_id)) {
        return App\Models\probationer::where('user_id', $model_id["user_id"])->first();
    } else {
        return App\Models\probationer::find($model_id);
    }
    return false;
}

/** ----------------------------------------------------------------------------------
 * Function: probationer_list
 * @param   array|int $squad_id
 *
 * @return  object Probationer list
 *
 * Brief: To get probationer list within the squad
 * Author: https://github.com/abirudhk
 * ------------------------------------------------------------------------------- */
function probationer_list(int $squad_id)
{
    if(empty($squad_id)) {
        return false;
    }

    $probationer  = App\Models\probationer::where('squad_id', $squad_id)->select('id')->orderBy('position_number', 'asc')->get();
    return $probationer;

}

/** ----------------------------------------------------------------------------------
 * Function: get_staffs
 * @param   array $roles    = ['adi', 'drillinspector', 'si']
 *
 * @return  \App\Models\User
 *
 * Author: https://github.com/rahaman-m
 * ------------------------------------------------------------------------------- */
function get_staffs(array $roles = ['adi', 'drillinspector', 'si'])
{
    return \App\Models\User::whereIn('role', ['drillinspector', 'si', 'adi'])->orderBy('name', 'asc')->get();
}

/** ----------------------------------------------------------------------------------
 * Function: activity_name
 * @param   int $activity_id
 *
 * @return  string Activity Name
 *
 * Author: https://github.com/rahaman-m
 * ------------------------------------------------------------------------------- */
function activity_name($activity_id)
{
    $activity_id    = intval($activity_id);
    if(empty($activity_id)) {
        return false;
    }
    return App\Models\Activity::withTrashed()->where('id', $activity_id)->value('name');
}

/** ----------------------------------------------------------------------------------
 * Function: activity_unit
 * @param   int $activity_id
 *
 * @return  string To retrive the Activity unit using activity Id
 *
 * Author: https://github.com/abirudhk
 * ------------------------------------------------------------------------------- */
function activity_unit(int $activity_id)
{
    if(empty($activity_id)) {
        return false;
    }
    return App\Models\Activity::withTrashed()->where('id', $activity_id)->value('unit');
}

/** ----------------------------------------------------------------------------------
 * Function: check_activity_unit_type
 * @param   int $activity_id
 *
 * @return  string To retrive the Activity Unit type using activity id
 *
 * Author: https://github.com/abirudhk
 * ------------------------------------------------------------------------------- */
function check_activity_unit_type(int $activity_id)
{
    if(empty($activity_id)) {
        return false;
    }
    $check_grade = App\Models\Activity::withTrashed()->where('id', $activity_id)->first();
    ($check_grade->unit !== '') ? ($type = 'count') : (($check_grade->has_grading !== 0) ? $type = 'grade' : $type = 'qualify');



    return $type;
}

/** ----------------------------------------------------------------------------------
 * Function: check_sub_activity
 * @param   int $activity_id
 *
 * @return  string subactivity count
 *
 * Author: https://github.com/abirudhk
 * ------------------------------------------------------------------------------- */
function check_sub_activity($activity_id)
{
    $activity_id = intval($activity_id);
    if (empty($activity_id)) {
        return false;
    }
    return App\Models\Activity::withTrashed()->where('parent_id', $activity_id)->count();
}

/** ----------------------------------------------------------------------------------
 * Function: valid_attendances
 *
 * @return  array valid attendances
 *
 * Author: https://github.com/rahaman-m
 * ------------------------------------------------------------------------------- */
function valid_attendances()
{
    return ['P', 'MDO', 'NCM', 'NAP', 'L', 'M', 'OT'];
}

/** ----------------------------------------------------------------------------------
 * Function: attendance_bg_color
 *
 * @param   string $attendance
 * @return  array valid attendances
 *
 * Author: https://github.com/rahaman-m
 * ------------------------------------------------------------------------------- */
function attendance_bg_color($attendance)
{
    $attendance = strtoupper($attendance);
    $class = "";
    if(in_array($attendance, ['P', 'MDO', 'NCM'])) {
        $class = "bg-success";
    } elseif(in_array($attendance, ['L', 'M', 'OT'])) {
        $class = "bg-danger";
    } elseif($attendance === 'NAP') {
        $class = "bg-warning";
    }

    return $class;
}


/** ----------------------------------------------------------------------------------
 * Function: qualified_values
 *
 * @param   int $index
 * @param   boolean $single = true
 * @return  string|array qualified value(s)
 *
 * Author: https://github.com/rahaman-m
 * ------------------------------------------------------------------------------- */
function qualified_values(int $index = null, $single = true)
{
    $valus = [
        'Clear',
        'With Assistance',
        'Not Clear',
    ];

    if( $single !== true ) {
        return $valus;
    } elseif($index === null) {
        return "";
    }

    return $valus[$index];
}

/** ----------------------------------------------------------------------------------
 * Function: valid_grades
 *
 * @return  array valid grades
 *
 * Author: https://github.com/rahaman-m
 * ------------------------------------------------------------------------------- */
function valid_grades()
{
    return ['A', 'B', 'C', 'D', 'E'];
}

/** ----------------------------------------------------------------------------------
 * Function: grade_to_num
 * @param   string $grade
 *
 * @return  int number
 *
 * Author: https://github.com/rahaman-m
 * ------------------------------------------------------------------------------- */
function grade_to_num($grade)
{
    $data   = [
        'A' => 5,
        'B' => 4,
        'C' => 3,
        'D' => 2,
        'E' => 1,
    ];

    return isset($data[$grade]) ? $data[$grade] : 0;
}

/** ----------------------------------------------------------------------------------
 * Function: num_to_grade
 * @param   int|float $number
 *
 * @return  string grade
 *
 * Author: https://github.com/rahaman-m
 * ------------------------------------------------------------------------------- */
function num_to_grade($number)
{
    $number = round($number);
    $data   = [
        5   => 'A',
        4   => 'B',
        3   => 'C',
        2   => 'D',
        1   => 'E',
    ];

    return isset($data[$number]) ? $data[$number] : false;
}

/** ----------------------------------------------------------------------------------
 * Function: grade_to_num
 * @param   string $grade
 *
 * @return  int number
 *
 * Author: https://github.com/abirudhk
 * ------------------------------------------------------------------------------- */
function grade_to_text($grade)
{
    $data   = [
        'A' => 'A-OUTSTANDING',
        'B' => 'B-VERYGOOD',
        'C' => 'C-GOOD',
        'D' => 'D-AVERAGE',
        'E' => 'E-POOR',
    ];

    return isset($data[$grade]) ? $data[$grade] : $grade;
}

/** ----------------------------------------------------------------------------------
 * Function: fitness_number_to_text
 * @param   string $number
 *
 * @return  int String
 *
 * Author: https://github.com/abirudhk
 * ------------------------------------------------------------------------------- */
function fitness_number_to_text($grade)
{
    $data   = [
        '0' => 'weight',
        '1' => 'bmi',
        '2' => 'bodyfat',
        '3' => 'fitnessscore',
        '4' => 'endurancegrade',
        '5' => 'strengthgrade',
        '6' => 'flexibilitygrade',
    ];

    return isset($data[$grade]) ? $data[$grade] : $grade;
}
/** ----------------------------------------------------------------------------------
 * Function: fitness_number_to_text
 * @param   string $number
 *
 * @return  int String
 *
 * Author: https://github.com/abirudhk
 * ------------------------------------------------------------------------------- */
function fitness_name_to_text($grade)
{
    $data   = [
        'weight' => 'Weight (in kgs)',
        'bmi' => 'BMI',
        'bodyfat' => 'Body Fat Percentage',
        'fitnessscore' => 'Fitness Score',
        'endurancegrade' => 'Endurance Grade',
        'strengthgrade' => 'Strength Grade',
        'flexibilitygrade' => 'Flexibility',
    ];

    return isset($data[$grade]) ? $data[$grade] : $grade;
}

/** ----------------------------------------------------------------------------------
 * Function: get_new_notifications_count
 * @param   object $user
 *
 * @return  int count
 *
 * Author: https://github.com/rahaman-m
 * ------------------------------------------------------------------------------- */
function get_new_notifications_count(object $user)
{
    $user_role  = $user->role;

    if( !in_array($user_role, ['probationer', 'doctor', 'drillinspector']) ) {
        return 0;
    }

    $nfCount    = 0;
    $NF_query   = \App\Models\Notification::query();

    if($user_role === 'probationer') {
        $probationer   = \App\Models\probationer::where('user_id', $user->id)->first();

        $NF_query->whereRaw("
            (recipient_type IS NULL OR recipient_type IN ('', '0', 'probationer'))
            AND
            (batch_id IS NULL OR batch_id IN ('', '0', $probationer->batch_id))
            AND
            (squad_id IS NULL OR squad_id IN ('', '0', $probationer->squad_id))
            AND
            id NOT IN (
                SELECT notification_read_statuses.notification_id
                FROM notification_read_statuses
                WHERE notification_read_statuses.notification_id = notifications.id AND notification_read_statuses.user_id = {$user->id}
            )
        ");

    } elseif( in_array($user_role, ['doctor', 'drillinspector']) ) {
        $NF_query->whereRaw("
            (recipient_type IS NULL OR recipient_type IN ('', '0', '{$user_role}'))
            AND
            id NOT IN (
                SELECT notification_read_statuses.notification_id
                FROM notification_read_statuses
                WHERE notification_read_statuses.notification_id = notifications.id AND notification_read_statuses.user_id = {$user->id}
            )
        ");
    }

    // $nfCount    = $NF_query->leftJoin('notification_read_statuses', 'notifications.id', '=', 'notification_read_statuses.notification_id')->count();
    $nfCount    = $NF_query->count();

    return $nfCount;
}

/** ----------------------------------------------------------------------------------
 * Function: notification_mark_read
 * @param   object $user
 *
 * @return  int count
 *
 * Author: https://github.com/rahaman-m
 * ------------------------------------------------------------------------------- */
function notification_mark_read(int $notification_id, int $user_id)
{
    \App\Models\NotificationReadStatus::updateOrCreate(
        [
            'notification_id'   => $notification_id,
            'user_id'           => $user_id,
        ],
        [
            'read_status'   => 1,
        ]
    );
}
/** ----------------------------------------------------------------------------------
 * Function: notification_attachment_url
 * @param   string $file_name
 *
 * @return  string file_url
 *
 * Author: https://github.com/rahaman-m
 * ------------------------------------------------------------------------------- */
function notification_attachment_url(string $file_name)
{
    return asset("storage/notification_attachments/{$file_name}");
}

/** ----------------------------------------------------------------------------------
 * Function: set_extra_session_attendance
 * @param   array $data [
 *                  "session_id"        => int
 *                  "probationer_id"    => int
 *                  "component_id"  => int
 *                  "attendance"    => string
 *                  "count"         => string
 *                  "grade"         => string
 *                  "qualified"     => int 0|1|2
 *              ]
 *
 * @return  array [
 *                  "status"    => string (error|success)
 *                  "message"   => string (if status is error)
 *                  "data"      => array (if status is success)
 *              ]
 *
 * Author: https://github.com/rahaman-m
 * ------------------------------------------------------------------------------- */
function set_extra_session_attendance(array $data)
{
    $result = [];
    $errors  = [];
    $timestamp  = date('Y-m-d H:i:s');
    $session_id     = $data["session_id"];
    $probationer_id = $data["probationer_id"];
    $attendance     = $data["attendance"];
    $component_id   = $data["component_id"];
    $count          = $data["count"];
    $grade          = $data["grade"];
    $qualified    = isset($data["qualified"])? $data["qualified"] : null;

    $qualified  = ($qualified === "" || $qualified === null)? null : (!in_array(intval($qualified), [0, 1, 2], true) ? null : $qualified);

    $ExtraSession   = App\Models\ExtraSession::find($session_id);
    if(empty($ExtraSession)) {
        return [
            'status'    => 'error',
            'message'   => 'Session Id not exist.',
        ];
    }
    // $ExtraSessionM  = $ExtraSession->metas->where('probationer_id', $probationer_id)->first();
    $ExtraSessionM  = App\Models\ExtraSessionmeta::where('extra_session_id', $session_id)
        ->where('probationer_id', $probationer_id)
        ->first();
    if(empty($ExtraSessionM)) {
        return [
            'status'    => 'error',
            'message'   => 'Given probationer not assigned for this session id.',
        ];
    }

    $activity_id    = $ExtraSession->activity_id;
    $subactivity_id = $ExtraSession->subactivity_id;
    $ex_session_date   = $ExtraSession->date;
    $ex_session_end   = $ExtraSession->session_end;

    if(strtotime($ex_session_date) > time()) {
        $errors[]   = 'Attendance update not allowed for future date.';
    }

    // $allowedTime    = intval($ex_session_end) + (48 * 60 * 60);
    // if(time() > $allowedTime) {
    //     $errors[]   = 'Attendance update not allowed after 48 hours from the session time.';
    // }

    if( !empty($errors) ) {
        return [
            'status'    => 'error',
            'message'   => implode('; ', $errors),
        ];
    }

    // echo $ex_session_date;

    $date1  = date('Y-m-01', strtotime("{$ex_session_date} -1 month"));
    $date2  = date('Y-m-t', strtotime($ex_session_date));
    if(strtotime($date2) > time()) {
        $date2  = date('Y-m-d');
    }

    if(in_array($attendance, ['P', 'MDO', 'NCM'])) {
        // Timetable
        $Timetables  = App\Models\ProbationersDailyactivityData::whereDate('probationers_dailyactivity_data.date', '<=', $date2) //whereDate('timetables.date', '>=', $date1)
            ->where('probationers_dailyactivity_data.activity_id', $activity_id)
            ->where('probationers_dailyactivity_data.subactivity_id', $subactivity_id)
            ->where('probationers_dailyactivity_data.probationer_id', $probationer_id)
            ->whereNotIn('probationers_dailyactivity_data.attendance', ['P', 'MDO', 'NCM'])
            ->select('probationers_dailyactivity_data.timetable_id as timetableId')
            //->leftJoin('probationers_dailyactivity_data', 'timetables.id', '=', 'probationers_dailyactivity_data.timetable_id')
            ->orderBy('probationers_dailyactivity_data.timetable_id', 'asc')
            ->get();

        foreach($Timetables as $Timetable) {
            $timetableId    = $Timetable->timetableId;
            $timetableIdExist  = App\Models\ExtraSessionmeta::where('probationer_id', $probationer_id)
                ->where('timetable_id', $timetableId)->count();
            if( $timetableIdExist === 0) {
                $ExtraSessionM->timetable_id = $timetableId;

                break;
            }
        }
    } else {
        $ExtraSessionM->timetable_id    = NULL;
    }

    if(!empty($component_id)) {
        App\Models\ExtraSessionComponent::updateOrCreate(
            [
                'sessionmetas_id'    => $ExtraSessionM->id,
                'probationer_id'    => $probationer_id,
                'component_id'      => $component_id,
            ],
            [
                'session_id'    => $session_id,
                'count' => $count,
                'grade' => $grade,
                'qualified' => $qualified,
            ]
        );
    }

    // Store avarage component values for the subactivity
    $avgCount = null;
    $avgGrade = null;
    $avgQlfy  = null;

    $sessionComponents  = App\Models\ExtraSessionComponent::where('session_id', $session_id)->where('probationer_id', $probationer_id)->get();
    $sessionComponentCount    = $sessionComponents->count();
    if($sessionComponentCount > 0) {
        $totalCount = null;
        $totalGrade = null;

        foreach($sessionComponents as $components) {
            $cmpCount   = $components->count;
            $cmpGrade   = $components->grade;
            $cmpQlfy    = $components->qualified;

            if($cmpCount !== null) {
                $totalCount += intval($cmpCount);
            }
            if($cmpGrade !== null) {
                $cmpGrade   = grade_to_num($cmpGrade);
                $totalGrade += $cmpGrade;
            }
            if($cmpQlfy !== null) {
                $avgQlfy   = ($cmpQlfy === 2)? 2 : (($avgQlfy === 2)? 2 : 0);
            }
        }

        $avgCount   = ($totalCount !== null)? round($totalCount / $sessionComponentCount, 2) : null;
        $avgGrade   = ($totalGrade !== null)? round($totalGrade / $sessionComponentCount) : null;
        $avgGrade   = ($totalGrade !== null)? num_to_grade($avgGrade) : null;
    } else {
        $avgCount = $count;
        $avgGrade = $grade;
        $avgQlfy  = $qualified;
    }

    $ExtraSessionM->attendance  = $attendance;
    $ExtraSessionM->count       = $avgCount;
    $ExtraSessionM->qualified   = $avgQlfy;
    $ExtraSessionM->grade   = $avgGrade;
    $ExtraSessionM->updated_at   = $timestamp;
    $ExtraSessionM->save();

    // echo '<pre>';
    // print_r($Timetables);
    // echo '</pre>';

    return [
        'status'    => 'success',
        'data'      => $ExtraSessionM,
    ];
}

/** ----------------------------------------------------------------------------------
 * Function: set_extra_class_attendance
 * @param   array $data [
 *                  "session_id"        => int
 *                  "probationer_id"    => int
 *                  "component_id"  => int
 *                  "attendance"    => string
 *                  "count"         => string
 *                  "grade"         => string
 *                  "qualified"     => int 0|1|2
 *              ]
 *
 * @return  array [
 *                  "status"    => string (error|success)
 *                  "message"   => string (if status is error)
 *                  "data"      => array (if status is success)
 *              ]
 *
 * Author: https://github.com/rahaman-m
 * ------------------------------------------------------------------------------- */
function set_extra_class_attendance(array $data)
{
    $result = [];
    $errors  = [];
    $timestamp  = date('Y-m-d H:i:s');

    $session_id     = $data["session_id"];
    $probationer_id = $data["probationer_id"];
    $attendance     = $data["attendance"];
    $component_id   = $data["component_id"];
    $count          = $data["count"];
    $grade          = $data["grade"];
    $qualified    = isset($data["qualified"])? $data["qualified"] : null;

    $qualified  = ($qualified === "" || $qualified === null)? null : (!in_array(intval($qualified), [0, 1, 2], true) ? null : $qualified);

    $attendance = ($attendance === "NAP") ? "A" : $attendance;

    $ExtraClass   = App\Models\ExtraClass::find($session_id);
    if(empty($ExtraClass)) {
        return [
            'status'    => 'error',
            'message'   => 'Session Id not exist.',
        ];
    }
    // $ExtraClassM  = $ExtraClass->metas->where('probationer_id', $probationer_id)->first();
    $ExtraClassM  = App\Models\ExtraClassmeta::where('extra_class_id', $session_id)
        ->where('probationer_id', $probationer_id)
        ->first();
    if(empty($ExtraClassM)) {
        return [
            'status'    => 'error',
            'message'   => 'Given probationer not assigned for this session id.',
        ];
    }

    $ex_session_date   = $ExtraClass->date;

    if(strtotime($ex_session_date) > time()) {
        $errors[]   = 'Attendance update not allowed for future date.';
    }

    // $allowedTime    = intval($ex_session_end) + (48 * 60 * 60);
    // if(time() > $allowedTime) {
    //     $errors[]   = 'Attendance update not allowed after 48 hours from the session time.';
    // }

    if( !empty($errors) ) {
        return [
            'status'    => 'error',
            'message'   => implode('; ', $errors),
        ];
    }

    if(!empty($component_id)) {
        App\Models\ExtraClassComponent::updateOrCreate(
            [
                'classmetas_id'    => $ExtraClassM->id,
                'probationer_id'    => $probationer_id,
                'component_id'      => $component_id,
            ],
            [
                'session_id'    => $session_id,
                'count' => $count,
                'grade' => $grade,
                'qualified' => $qualified,
            ]
        );
    }

    // Store avarage component values for the subactivity
    $avgCount = null;
    $avgGrade = null;
    $avgQlfy  = null;

    $classComponents  = App\Models\ExtraClassComponent::where('classmetas_id', $ExtraClassM->id)->get();
    $classComponentCount    = $classComponents->count();
    if($classComponentCount > 0) {
        $totalCount = null;
        $totalGrade = null;

        foreach($classComponents as $components) {
            $cmpCount   = $components->count;
            $cmpGrade   = $components->grade;
            $cmpQlfy    = $components->qualified;

            if($cmpCount !== null) {
                $totalCount += intval($cmpCount);
            }
            if($cmpGrade !== null) {
                $cmpGrade   = grade_to_num($cmpGrade);
                $totalGrade += $cmpGrade;
            }
            if($cmpQlfy !== null) {
                $avgQlfy   = ($cmpQlfy === 2)? 2 : (($avgQlfy === 2)? 2 : 0);
            }
        }

        $avgCount   = ($totalCount !== null)? round($totalCount / $classComponentCount, 2) : null;
        $avgGrade   = ($totalGrade !== null)? round($totalGrade / $classComponentCount) : null;
        $avgGrade   = ($totalGrade !== null)? num_to_grade($avgGrade) : null;
    } else {
        $avgCount = $count;
        $avgGrade = $grade;
        $avgQlfy  = $qualified;
    }

    $ExtraClassM->attendance  = $attendance;
    $ExtraClassM->count       = $avgCount;
    $ExtraClassM->qualified   = $avgQlfy;
    $ExtraClassM->grade   = $avgGrade;
    $ExtraClassM->updated_at   = $timestamp;
    $ExtraClassM->save();

    // echo '<pre>';
    // print_r($Timetables);
    // echo '</pre>';

    return [
        'status'    => 'success',
        'data'      => $ExtraClassM,
    ];
}

/** ----------------------------------------------------------------------------------
 * Function: csvToArray
 * @param   string $filepath        => Absolute path of the csv file
 * @param   string $delimiter       => Column separator used. Default comma (,)
 * @param   string $rows_to_skip    => Skip this number of rows starting from the first one. Default 0
 *
 * @return  array CSV Data as array
 *
 * Author: https://github.com/rahaman-m
 * ------------------------------------------------------------------------------- */
function csvToArray(string $filepath = '', string $delimiter = ',', $rows_to_skip = 0)
{
    if (!file_exists($filepath) || !is_readable($filepath))
        return false;

    $header = null;
    $data = array();
    if (($handle = fopen($filepath, 'r')) !== false) {
        $i = 1;
        while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
            if ($i > $rows_to_skip) {
                if (!$header) {
                    $header = array_map('trim', $row);;
                } else {
                    $data[] = array_combine($header, $row);
                }
            }

            $i++;
        }
        fclose($handle);
    }

    return $data;
}

/** ----------------------------------------------------------------------------------
 * Function: isTimeslotAvailable
 * @param   array $sessionInfo  ->
 *                    'batch_id' => 0,
 *                    'squad_id' => 0,
 *                    'date' => 0, // Valid date
 *                    'session_start' => 0, // Unix timestamp
 *                    'session_end' => 0, // Unix timestamp
 *
 * @return  bool    -> true if available, false if not available
 *
 * Author: https://github.com/rahaman-m
 * ------------------------------------------------------------------------------- */
function isTimeslotAvailable(array $sessionInfo = [])
{
    if (
        !isset($sessionInfo['batch_id']) ||
        !isset($sessionInfo['squad_id']) ||
        !isset($sessionInfo['date']) ||
        !isset($sessionInfo['session_start']) ||
        !isset($sessionInfo['session_end'])
    ) {
        return "Invalid data supplied";
    }

    $date   = date('Y-m-d', strtotime($sessionInfo['date']));
    $sStart   = $sessionInfo['session_start'];
    $sEnd   = $sessionInfo['session_end'];

    $timetable  = DB::table('timetable')
        ->where('batch_id', $sessionInfo['batch_id'])
        ->where('squad_id', $sessionInfo['squad_id'])
        ->whereDate('date', $date)
        ->whereRaw("
                (
                    session_start BETWEEN ({$sStart}, {$sEnd})
                    OR session_end BETWEEN ({$sStart}, {$sEnd})
                ) OR (
                    session_start > {$sStart} AND session_end < {$sEnd}
                )
            ")->first();

    if (empty($timetable)) {
        return true;
    }

    return false;
}

/** ----------------------------------------------------------------------------------
 * Function: convert_date
 * @param   string  $date
 * @param   string  $format         - Given date format (d-m-Y)
 * @param   string  $return_format  - Return date in this format (Y-m-d)
 *
 * @return  bool    -> true if valid, else false
 *
 * Author: https://gitlab.com/rahaman-m/
 * ------------------------------------------------------------------------------- */
function convert_date($date, $format = 'd-m-Y', $return_format = 'Y-m-d')
{
    $dateObj    = date_create_from_format($format, $date);
    if(!$dateObj) {
        return false;
    }
    return date_format($dateObj, $return_format);
}

/** ----------------------------------------------------------------------------------
 * Function: isValidDate
 * @param   string  $date
 * @param   string  $format     - Given date format (d-m-Y)
 * @param   string  $separator  - Date format separator (-)
 *
 * @return  bool    -> true if valid, else false
 *
 * Author: https://gitlab.com/rahaman-m/
 * ------------------------------------------------------------------------------- */
function isValidDate($date, $format = 'd-m-Y', $separator = '-', $datetime_separator = ' ')
{
    $result = true;

    // $date1 = explode($datetime_separator, $date);
    $date1 = convert_date($date, $format, 'Y-m-d');
    if(!$date1) {
        return false;
    }
    $date2 = explode($separator, $date1);

    $date3 = date_parse_from_format($format, $date);

    if ( intval($date2[2]) !== $date3['day'] || intval($date2[1]) !== $date3['month'] || intval($date2[0]) !== $date3['year'] ) {
        $result = false;
    } else {
        $dt = new DateTime($date1);

        $errors = DateTime::getLastErrors();
        if (!empty($errors['warning_count'])) {
            $result = false;
        }
    }

    return $result;
}

/* ---------------------------------------------------------------
* Function: data_crypt()
* Arguments: $string - Any string / data to encrypt or decrypt
*             $action - e = enctrypt / d = decrypt
* Brief: Encrypt / Decrypt a string or data
* Author: https://github.com/rahaman-m
* ------------------------------------------------------------- */
function data_crypt($string, $action = 'e')
{
    // you may change these values to your own
    $secret_key = '5WVPW3ESF';
    $secret_iv = '3671387C47DA4';
    $encrypt_method = "AES-256-CBC";

    $output = false;
    $key = hash('sha256', $secret_key);
    $iv = substr(hash('sha256', $secret_iv), 0, 16);

    if ($action == 'e') {
        $output = base64_encode(openssl_encrypt($string, $encrypt_method, $key, 0, $iv));
    } else if ($action == 'd') {
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }
    return $output;
}

/** ----------------------------------------------------------------------------------
 * Function: create_webpage_access_token
 * @param   int user_id
 *
 * @return  string token
 *
 * Author: https://github.com/rahaman-m
 * ------------------------------------------------------------------------------- */
function create_webpage_access_token(int $user_id)
{
    $user = App\Models\User::find($user_id);
    if(!$user) {
        return false;
    }

    $token  = bin2hex( random_bytes(20) );

    // Create or update token
    App\Models\WebpageAccessToken::create(
        [
            'user_id' => $user_id,
            'token' => $token,
            'expires_at' => now()->addHours(1),
        ]
    );

    return data_crypt($user->email .'|'. $token);
}

/** ----------------------------------------------------------------------------------
 * Function: validate_webpage_access_token
 * @param   int user_id
 * @param   string access_token
 *
 * @return  bool
 *
 * Author: https://github.com/rahaman-m
 * ------------------------------------------------------------------------------- */
function validate_webpage_access_token(int $user_id, string $access_token)
{
    // delete all expired tokens
    App\Models\WebpageAccessToken::whereDate('expires_at', '<', now())->delete();

    $user = App\Models\User::find($user_id);
    if(!$user) {
        return false;
    }

    $txtData    = data_crypt($access_token, 'd');
    $txtData    = explode('|', $txtData, 2);
    $email      = $txtData[0];
    $token      = $txtData[1];
    if(empty($token)) {
        return false;
    }

    // Verify token
    $expires_at   = App\Models\WebpageAccessToken::where('user_id', $user_id)->where('token', $token)->value('expires_at');
    if( ($email === $user->email) && !empty($expires_at) && (time() <= strtotime($expires_at)) ) {
        return true;
    }

    return false;
}


/** ----------------------------------------------------------------------------------
 * Function: toAlpha
 * @param   int number
 *
 * @return  string Alphabate
 *
 * Author: https://github.com/rahaman-m
 * ------------------------------------------------------------------------------- */
function toAlpha(int $number){
    $alphabet =   range('A', 'Z');
    $alpha_flip = array_flip($alphabet);
    if($number <= 25){
      return $alphabet[$number];
    }
    elseif($number > 25){
      $dividend = ($number + 1);
      $alpha = '';
      $modulo   = 0;
      while ($dividend > 0){
        $modulo = ($dividend - 1) % 26;
        $alpha = $alphabet[$modulo] . $alpha;
        $dividend = floor((($dividend - $modulo) / 26));
      }
      return $alpha;
    }
}

/** ----------------------------------------------------------------------------------
 * Function: spreadsheet_header
 * @param   string file_name
 *
 * @return  string spreadsheet document header content
 *
 * Author: https://github.com/rahaman-m
 * ------------------------------------------------------------------------------- */
function spreadsheet_header($file_name = null)
{
    if(empty($file_name)) {
        $file_name   = "File_" . gmdate('c') . ".xlsx";
    }

    // Redirect output to a client’s web browser (Xlsx)
    // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="' . $file_name . '"');
    header('Cache-Control: max-age=0');
    header("Pragma: no-cache");
    header("Expires: 0");
    // If you're serving to IE 9, then the following may be needed
    // header('Cache-Control: max-age=1');

    // If you're serving to IE over SSL, then the following may be needed
    // header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    // header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
    // header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    // header('Pragma: public'); // HTTP/1.0

    return;
}

/** ----------------------------------------------------------------------------------
 * Function: remove_specialcharcters
 * @param   string String
 *
 * @return  string To Remove special charcters in string
 *
 * Author: https://github.com/abirudhk
 * ------------------------------------------------------------------------------- */
function remove_specialcharcters($string) {
    return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
 }

 /** ----------------------------------------------------------------------------------
 * Function: validate Numeric
 * @param   string String
 *
 * @return  string To validate the request to numeric
 *
 * Author: https://github.com/abirudhk
 * ------------------------------------------------------------------------------- */
function validate_numeric($string) {
    if(!is_numeric($string))
        {
            $response   = [
                'code' => "201",
                'status'    => "success",
                'message'  => "Invalid Probationer Id"
            ];
            return response()->json($response, 201);
        }
        // else


        // {
        //     return $string;
        // }
 }

  /** ----------------------------------------------------------------------------------
 * Function: Login Authentication
 * @param   string String
 *
 * @return  string To authenticate the login api
 *
 * Author: https://github.com/abirudhk
 * ------------------------------------------------------------------------------- */

 function authentication($Authorization)
 {

 if ($Authorization != NULL and $Authorization != '') {

     $Authorization = explode(':', "$Authorization");
     $clientid = 'SAMS67489362';
     $protocol = (strstr('https',$_SERVER['SERVER_PROTOCOL']) === false)?'http://':'https://';
     $url = "https://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
     $requesturi = strtolower(urlencode("$url"));
     $requestmethod = 'POST';



     if (count($Authorization) == '4') {
         $requesttimestamp = $Authorization[3];
         $nonce = $Authorization[2];
          $token = $Authorization[1];


         $param1 =  $clientid . $requesturi . $requestmethod . $requesttimestamp . $nonce;
         $param2 = '4d1piK86IyxPn7LvNJk9usGmFDEg2tfa'; // Secret Key


         $servertoken = base64_encode(hash_hmac("sha256", base64_encode($param1), $param2, true));
        // print_r($servertoken);exit;
         if ($servertoken == $token) {

             return true;
         } else {
             return false;
         }
     } else {
         return false;
     }
 } else {
     return false;
 }
 }

 /** ----------------------------------------------------------------------------------
 * Function: sanitize_activity_id
 * @param    $value
 *
 * @return  string activity value
 *
 * Author: https://github.com/abirudhk
 * ------------------------------------------------------------------------------- */
function sanitize_activity_id($value)
{
    return (empty($value)) ? null : $value;
}

 /** ----------------------------------------------------------------------------------
 * Function: Isemptystring
 * @param    $value
 *
 * @return  string activity value
 *
 * Author: https://github.com/abirudhk
 * ------------------------------------------------------------------------------- */

function IsEmptyString($value){
    return ($value === null) ? '' : $value;
}

/** ----------------------------------------------------------------------------------
 * Function: Isemptystring
 * @param    $batch_id, $activityid, $subactivityid,
 *
 * @return  int sessions count
 *
 * Author: https://github.com/abirudhk
 * ------------------------------------------------------------------------------- */

function missed_sessions_count($activity_id, $subactivity_id, $probationer_id){


    $extraSessionPbIds  = \App\Models\ExtraSessionmeta::where('probationer_id', $probationer_id)
                ->whereIn('extra_sessionmetas.attendance', ['P', 'MDO', 'NCM'])
                ->whereNotNull('extra_sessionmetas.timetable_id')
                ->pluck('extra_sessionmetas.timetable_id')->toArray();

            $probationerQ  = \App\Models\probationer::query()
                ->where('timetables.activity_id', $activity_id)
                ->where('timetables.subactivity_id', $subactivity_id)
                ->whereNotIn('timetables.id', $extraSessionPbIds)
                ->whereNotIn('probationers_dailyactivity_data.attendance', ['P', 'MDO', 'NCM'])




                ->join('probationers_dailyactivity_data', 'probationers.id', '=', 'probationers_dailyactivity_data.probationer_id')
                ->join('timetables', 'probationers_dailyactivity_data.timetable_id', '=', 'timetables.id');

            $missedcount    = $probationerQ ->where('probationers.id', $probationer_id)
                ->selectRaw("count(probationers_dailyactivity_data.attendance) as session_count")
                ->count();

        $check_missed_classes = \App\Models\ExtraSession::join('extra_sessionmetas', 'extra_sessionmetas.extra_session_id', '=', 'extra_sessions.id')->where('activity_id', $activity_id)
        ->where('subactivity_id', $subactivity_id)->where('extra_sessionmetas.probationer_id', $probationer_id)->whereNull('extra_sessionmetas.attendance')->count();
         $count = ($missedcount - $check_missed_classes);
        return $count;

}



