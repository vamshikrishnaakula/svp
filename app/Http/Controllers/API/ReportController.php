<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\ExtraSessionmeta;
use App\Models\Activity;

class ReportController extends Controller
{
    public function getprobationersinglereport(Request $request)
    {
        $request = (json_decode($request->getContent(), true));

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
                            'message'  => "Unauthorized"
                        ];
                        return response()->json($response, 401);
                    }
        }
        isset($request['activity_id']) ? $activity_id = remove_specialcharcters($request['activity_id']) : $activity_id = '';
        isset($request['sub_activity_id']) ? $sub_activity_id = remove_specialcharcters($request['sub_activity_id']) : $sub_activity_id = '';
        isset($request['component_id']) ? $component_id = remove_specialcharcters($request['component_id']) : $component_id = '';
        isset($request['from_date']) ? $from = $request['from_date'] : $from = '';
        isset($request['to_date']) ? $to = $request['to_date'] : $to = '';


            if($from== '' && $to == '')
            {
                $from = Date('Y/m/d', strtotime('-90 days'));
                $to = date('Y/m/d');
             }
        else{
            $from_date = new \DateTime($from);
            $to_date = new \DateTime($to);

            if($from_date->diff($to_date)->days > 90) {
                $response   = [
                    'code' => "201",
                    'status'    => "success",
                    'message' => 'The Date Range Selection Should be Within 90 days',
                    'data'      => [],
                ];
                return response()->json($response, 200);
            }
        }

        $component_id = sanitize_activity_id($component_id);
        $sub_activity_id = sanitize_activity_id($sub_activity_id);

        $data = array();


        $day_wise = DB::table('probationers_dailyactivity_data')->select(DB::raw("CASE when component_id IS NOT NULL then component_id when subactivity_id IS NOT NULL THEN subactivity_id else activity_id end AS ACT_ID"), 'date', 'timetable_id', 'activity_id', 'subactivity_id', 'component_id', 'id')
        ->where('probationer_id', $probationer_Id)
        ->where('activity_id', $activity_id)
        ->whereBetween('date', [$from, $to])
        ->orderBy('date', 'asc')
        ->groupBy('ACT_ID');


        if(!empty($sub_activity_id))
        {
            $day_wise->where('probationers_dailyactivity_data.subactivity_id', $sub_activity_id);
        }

        if(!empty($component_id))
        {
            $day_wise->where('probationers_dailyactivity_data.component_id', $component_id);
        }

        $results = $day_wise->get();

       // return json_encode($results);exit;

        if(count($results) > 0)
        {
            foreach($results as $day)
            {
                $activity_unit = Activity::where('activities.id', $day->ACT_ID)->first();
                $act_id = $day->ACT_ID;
                $usersexport = DB::table('probationers_dailyactivity_data')->select('grade','count','qualified', 'attendance','probationers_dailyactivity_data.date', 'timetables.session_number', 'probationers_dailyactivity_data.id', 'probationers_dailyactivity_data.probationer_id', 'probationers_dailyactivity_data.timetable_id')
                ->leftjoin('timetables','timetables.id','=','probationers_dailyactivity_data.timetable_id')
                ->where('probationers_dailyactivity_data.probationer_id', $probationer_Id)
                ->whereBetween('probationers_dailyactivity_data.date', [$from, $to])
                ->where(function($q)use($act_id) {
                    $q->where('probationers_dailyactivity_data.activity_id', $act_id)
                    ->orWhere('probationers_dailyactivity_data.subactivity_id', $act_id)
                    ->orWhere('probationers_dailyactivity_data.component_id', $act_id);
                })
                ->get();

                if(count($usersexport) > 0)
                {
                    foreach($usersexport as $user)
                    {
                        if($user->attendance == 'P' || $user->attendance == 'MDO' || $user->attendance == 'NCM')
                        {

                            $data[] = array(
                                "count"=>  (check_activity_unit_type($day->ACT_ID) == 'count') ? ( $user->count ) : ( check_activity_unit_type($day->ACT_ID) == 'grade' ? ($user->grade ) : ( qualified_values($user->qualified))),
                                "date"=> isset($user->date) ? date('d-m-Y', strtotime($user->date))  : "-",
                                "session_number"=> isset($user->session_number) ? $user->session_number : "-",
                                "id"=> isset($user->id) ? $user->id : "-",
                            );
                          }
                    else
                        {
                            $missed_classes = ExtraSessionmeta::where('timetable_id', $user->timetable_id)->where('probationer_id', $user->probationer_id)->first();
                            if(!empty($missed_classes))
                            {
                                $data[] = array(
                                    "count"=>  (check_activity_unit_type($day->ACT_ID) == 'count') ? ( $missed_classes->count ) : ( check_activity_unit_type($day->ACT_ID) == 'grade' ? ($missed_classes->grade ) : ( qualified_values($missed_classes->qualified))),
                                    "date"=> isset($user->date) ? date('d-m-Y', strtotime($user->date))  : "-",
                                    "session_number"=> isset($user->session_number) ? $user->session_number : "-",
                                    "id"=> isset($user->id) ? $user->id : "-",
                                );
                            }
                            else
                            {

                                $data[] = array(
                                    "count"=>  $user->attendance,
                                    "date"=> isset($user->date) ? date('d-m-Y', strtotime($user->date))  : "-",
                                    "session_number"=> isset($user->session_number) ? $user->session_number : "-",
                                    "id"=> isset($user->id) ? $user->id : "-",
                                );
                            }
                        }
                    }
                }
                else
                {
                    $data[] = [
                    ];
                }


                $dd[] =[
                    "activity_name" => $activity_unit->name,
                    "activity_unit" => (($activity_unit->unit) != '') ? ( $activity_unit->unit ) : ( ($activity_unit->has_grading) !== 0 ? ('Grade') : ('Qualified')),
                    "report_data" => $data,
                ];
                unset($data);
            }

            $response   = [
                'code' => "200",
                'status'    => "success",
                'message' => '',
                "date" => date('d-m-Y', strtotime($from)) .' / '. date('d-m-Y', strtotime($to)) ,
                'data'      => $dd,
            ];
         }
        else
            {
                $response   = [
                    'code' => "201",
                    'status'    => "success",
                    'message' => 'No data from last 90 days',
                    'data'      => [],
                ];
            }

        return response()->json($response, 200);

    }

    public function getprobationermonthlyreport(Request $request)
    {
        $request = (json_decode($request->getContent(), true));

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
                            'message'  => "Unauthorized"
                        ];
                        return response()->json($response, 401);
                    }
        }


        isset($request['activity_id']) ? $activity_id = remove_specialcharcters($request['activity_id']) : $activity_id = '';
        isset($request['sub_activity_id']) ? $sub_activity_id = remove_specialcharcters($request['sub_activity_id']) : $sub_activity_id = '';
        isset($request['component_id']) ? $component_id = remove_specialcharcters($request['component_id']) : $component_id = '';


        if($component_id == ''){$component_id='0';}


        if(isset($request['month']) == '0' && isset($request['year']) == '0')
        {
            $latest_day =DB::table('probationers_dailyactivity_data')->where('probationer_id', $probationer_Id)->groupBy('probationer_id')->max('date');
            if($latest_day != '')
            {
                $year = date('Y', strtotime($latest_day));
                $month = date('m', strtotime($latest_day));
            }
            else
            {
                $year = '-';
                $month = '-';
            }
        }
        else{
            $month = remove_specialcharcters($request['month']);
            $year = remove_specialcharcters($request['year']);
        }
        $data = array();

        // $day_wise = DB::select("select CASE when component_id != '0' then component_id when subactivity_id != '0' THEN subactivity_id else activity_id end AS ACT_ID, date, timetable_id, activity_id, subactivity_id, component_id from probationers_dailyactivity_data where probationer_id = $probationer_Id and activity_id = $activity_id and subactivity_id = $sub_activity_id  and month(date)= $month AND year(date) = $year group by ACT_ID order by date ASC");

        $day_wise = DB::table('probationers_dailyactivity_data')->select(DB::raw("CASE when component_id IS NOT NULL then component_id when subactivity_id IS NOT NULL THEN subactivity_id else activity_id end AS ACT_ID"), 'date', 'timetable_id', 'activity_id', 'subactivity_id', 'component_id', 'id')
        ->where('probationer_id', $probationer_Id)
        ->where('activity_id', $activity_id)
        ->where('subactivity_id', $sub_activity_id)
        ->whereMonth('date', $month)
        ->whereYear('date', $year)
        ->groupBy('ACT_ID')
        ->orderBy('date', 'asc')
        ->get();



        foreach($day_wise as $day)
        {
            // $usersexport = DB::select("select name, ROUND(AVG(
            //     CASE grade
            //         WHEN 'A' THEN 5
            //         WHEN 'B' THEN 4
            //         WHEN 'C' THEN 3
            //         WHEN 'D' THEN 2
            //         WHEN 'E' THEN 1
            //         ELSE 0
            //     END
            //     )) AS avg_gpa from `probationers_dailyactivity_data11` left join activities on activities.batch_id = probationers_dailyactivity_data.Batch_id where (`probationers_dailyactivity_data`.`activity_id` = $day->ACT_ID or `probationers_dailyactivity_data`.`subactivity_id` = $day->ACT_ID or `probationers_dailyactivity_data`.`component_id` = $day->ACT_ID) and probationer_id = $probationer_Id and `activities`.`id` = $day->ACT_ID  order by date asc");

                $usersexport = DB::table('probationers_dailyactivity_data')->select(DB::raw("ROUND(AVG(
                    CASE grade
                        WHEN 'A' THEN 5
                        WHEN 'B' THEN 4
                        WHEN 'C' THEN 3
                        WHEN 'D' THEN 2
                        WHEN 'E' THEN 1
                        ELSE 0
                    END
                    )) AS avg_gpa"),'name')
                ->leftjoin('activities','activities.batch_id','=','probationers_dailyactivity_data.Batch_id')
                ->where('probationer_id', $probationer_Id)
                ->where('activity_id', $day->ACT_ID)
                ->orWhere('subactivity_id', $day->ACT_ID)
                ->orWhere('component_id', $day->ACT_ID)
                ->where('activities.id', $day->ACT_ID)
                ->orderBy('date', 'asc')
                ->get();


                foreach($usersexport as $user)
                    {
                        $data[] = $user;
                    }
        }
        $response   = [
            'code' => "200",
            'status'    => "success",
            'date'  => $month . '/'. $year,
            'data'      => $data,
        ];
        return response()->json($response, 200);
    }

    /**
     * API Name: get-user-mytarget-url
     *
     * User Role: probationer
     * Brief: To get a temporary url for probationer to Set Mytarget
     *
     * Author: rahaman-m
     */
    public function get_mytarget_set_url(Request $request)
    {
        // if(Auth::user()->role !== 'probationer') {
        //     return response()->json([
        //         'code'      => "401",
        //         'status'    => "error",
        //         'message'   => 'Unauthorized user role.',
        //     ], 401);
        // }
        $user_id    = Auth::id();
        $access_token    = urlencode( create_webpage_access_token($user_id) );
        if(!empty($access_token)) {
            $url    = url("user-mytarget-mobile?id={$user_id}&access_token={$access_token}");

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
    }

    /**
     * API Name: mytarget-view-mobile
     *
     * User Role: probationer
     * Brief: To get a temporary url for probationer to View Mytarget
     *
     * Author: rahaman-m
     */
    public function get_mytarget_view_url(Request $request)
    {
        // if(Auth::user()->role !== 'probationer') {
        //     return response()->json([
        //         'code'      => "401",
        //         'status'    => "error",
        //         'message'   => 'Unauthorized user role.',
        //     ], 401);
        // }
        $user_id    = Auth::id();
        $probationer_id    = $request->probationer_id;

        $access_token    = urlencode( create_webpage_access_token($user_id) );
        if(!empty($access_token)) {
            $url    = url("mytarget-view-mobile?id={$user_id}&pid={$probationer_id}&access_token={$access_token}");

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
    }

    /**
     * API Name: get-statistics-url
     *
     * User Role: probationer
     * Brief: To get a temporary url for probationer to View Statistics
     *
     * Author: rahaman-m
     */
    public function get_statistics_url(Request $request)
    {
        // if(Auth::user()->role !== 'probationer') {
        //     return response()->json([
        //         'code'      => "401",
        //         'status'    => "error",
        //         'message'   => 'Unauthorized user role.',
        //     ], 401);
        // }
        $user_id    = Auth::id();
        $probationer_id    = $request->probationer_id;

        $access_token    = urlencode( create_webpage_access_token($user_id) );
        if(!empty($access_token)) {
            $url    = url("user-statistics-mobile?id={$user_id}&probationerid={$probationer_id}&access_token={$access_token}");

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
    }

    /**
     * API Name: get-monthly-statistics-url
     *
     * User Role: probationer
     * Brief: To get a temporary url for probationer to View Monthly Statistics
     *
     * Author: rahaman-m
     */
    public function get_squad_statistics_url(Request $request)
    {
        // if(Auth::user()->role !== 'probationer') {
        //     return response()->json([
        //         'code'      => "401",
        //         'status'    => "error",
        //         'message'   => 'Unauthorized user role.',
        //     ], 401);
        // }
        $user_id    = Auth::id();
        $squad_id    = $request->squad_id;

        $access_token    = urlencode( create_webpage_access_token($user_id) );
        if(!empty($access_token)) {
            $url    = url("user-squad-statistics-mobile?id={$user_id}&squadid={$squad_id}&access_token={$access_token}");

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
    }
}
