<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Activity;
use App\Models\Timetable;
use Carbon\Carbon;
use \stdClass;

class ProbationersDailyActivityController extends Controller
{
     /*
        probationer Basic details
    */

    public function Probattendance(Request $request)
    {
        $request = json_decode($request->getContent(), true);
        $probationer_Id   = $request['probationer_id'];
        $month   = $request['month'];
        $year   = $request['year'];

        $daysInMonth   = cal_days_in_month(0, $month, $year);

        $atten = array();
        $test = array();

        $probationer_squadid = DB::table('probationers')
            ->select('squad_id')
            ->where('id', $probationer_Id)
            ->first();

        for($d=1; $d<=$daysInMonth; $d++) {
            $date   = $year.'-'. sprintf('%02d', $month) .'-'. sprintf('%02d', $d);

            $timetables = Timetable::where('squad_id', $probationer_squadid->squad_id)
                ->whereDate('date', $date)
                ->where('session_type', 'regular')
                ->orderBy('session_number', 'asc')->get()->toArray();

            $timeTableData   = range(1,6);
            if( !empty($timetables) ) {
                foreach ($timetables as $data) {
                    for ($si=0; $si<6; $si++) {
                        $sn = $si + 1;
                        if($sn === intval($data["session_number"])) {
                            $timeTableData[$si]  = $data;
                        }
                    }
                }
            }
            $atten_array = array();
            $atten_array1 = array();
          //  $atten = array();
            $ai = 0;
            if(count($timeTableData) > 0) {
                foreach($timeTableData as $timetable) {
                    if( is_array($timetable) ) {
                        $getAttn = DB::table('probationers_dailyactivity_data')
                            ->where('probationer_id', $probationer_Id)
                            ->where('timetable_id', $timetable["id"])
                            ->select('attendance', 'date')->first();
                        if($getAttn) {
                           $label = "session".$ai;
                           $atten1 = isset($getAttn->attendance) ? $getAttn->attendance : '-';
                        } else {
                            $label = "session".$ai;
                            $atten1 = '-';
                        }
                        $ai++;
                    }
                    else {
                        $label = "session".$ai;
                        $atten1 = '-';
                    }
                    array_push( $atten_array1, $atten1);
                }
                array_unshift( $atten_array1, $date);

                $aa = $atten_array1;
                $object = (object) $aa;
                $atten[] = $object;
                unset($atten_array);
            }
        }
        $response   = [
            'code'  => '200',
            'status'    => "success",
            'data'      => $atten,
        ];
       return response()->json($response, 200);
    }

    public function probationermonthlysession(Request $request)
    {
        $request = (json_decode($request->getContent(), true));
        $probationer_Id   = $request['probationer_id'];
        $month   = $request['month'];
        $year   = $request['year'];
        $data   = [];

        $daysInMonth   = cal_days_in_month(0, $month, $year);

        $dateStart  = date('Y-m-d', mktime(0,0,0,$month, 1, $year));
        $dateEnd    = date('Y-m-d', mktime(0,0,0,$month, $daysInMonth, $year));

        $timetables = DB::select(
            "SELECT activity_id, ANY_VALUE(id) AS id
            FROM timetables
            WHERE activity_id != 0 AND activity_id IS NOT NULL
                AND date BETWEEN '{$dateStart}' AND '{$dateEnd}'
            GROUP BY activity_id
            ORDER BY activity_id"
        );

        $timetables_count   = count($timetables);

        if($timetables_count > 0) {

            $activityData   = [];

            foreach($timetables as $timetable) {
                $activity_id    = $timetable->activity_id;

                $get_subactivities  = DB::table('activities')
                    ->select('id')
                    ->where('parent_id', $activity_id)->get();

                $subactivities_count    = count($get_subactivities);
                $subactivityData   = [];
                if( $subactivities_count > 0) {

                    foreach($get_subactivities as $get_subactivity) {
                        $subactivity_id = $get_subactivity->id;

                        $getAttns = DB::table('probationers_dailyactivity_data')
                            ->join('timetables', 'probationers_dailyactivity_data.timetable_id', '=', 'timetables.id')
                            ->whereRaw("probationers_dailyactivity_data.probationer_id = ? AND probationers_dailyactivity_data.date BETWEEN ? AND ? AND timetables.activity_id = ? AND timetables.subactivity_id = ?", [$probationer_Id, $dateStart, $dateEnd, $activity_id, $subactivity_id])
                            ->select('probationers_dailyactivity_data.attendance')->get();

                        $total  = count($getAttns);
                        $attended   = 0;
                        $missed     = 0;

                        if( $total > 0 ) {
                            foreach($getAttns as $getAttn) {
                                if (in_array($getAttn->attendance, ['P', 'MDO', 'NCM'])) {
                                    $attended++;
                                }
                            }

                            $missed = $total - $attended;

                            $subactivity    = "0";
                            if(! empty($subactivity_id) ) {
                                $subactivities = DB::table('activities')
                                    ->select('name')
                                    ->where('id', $subactivity_id)->first();

                                $subactivity    = $subactivities->name;
                            }

                            $subactivityData[] = [
                                'subactivity'       => $subactivity,
                                'subactivity_id'    => $subactivity_id,
                                'total'     => $total,
                                'attended'  => $attended,
                                'missed'    => $missed,
                            ];
                        }
                    }

                } else {

                    $getAttns = DB::table('probationers_dailyactivity_data')
                        ->join('timetables', 'probationers_dailyactivity_data.timetable_id', '=', 'timetables.id')
                        ->whereRaw("probationers_dailyactivity_data.probationer_id = ? AND probationers_dailyactivity_data.date BETWEEN ? AND ? AND timetables.activity_id = ?", [$probationer_Id, $dateStart, $dateEnd, $activity_id])
                        ->select('probationers_dailyactivity_data.attendance')->get();

                    $total  = count($getAttns);
                    $attended   = 0;
                    $missed     = 0;

                    if( $total > 0 ) {
                        foreach($getAttns as $getAttn) {
                            if (in_array($getAttn->attendance, ['P', 'MDO', 'NCM'])) {
                                $attended++;
                            }
                        }
                        $missed = $total - $attended;
                    }

                    $activityData = [
                        'total'     => $total,
                        'attended'  => $attended,
                        'missed'    => $missed,
                    ];
                }

                $activities = DB::table('activities')
                    ->select('name')
                    ->where('id', $activity_id)->first();

                $activity    = $activities->name;

                if(!empty($subactivityData)) {
                    $data[]   = [
                        'activity'  => $activity,
                        'subactivities' => $subactivityData
                    ];
                } else {
                    $data[]   = array_merge(
                        [
                            'activity'  => $activity,
                            'subactivities' => []
                        ],
                        $activityData
                    );
                }
            }
        }
        $response   = [
            'code'  => '200',
            'status'    => "success",
            'data'      => $data
        ];

        // $response   =json_encode($response,JSON_FORCE_OBJECT);

        return response()->json($response, 200);
   }

   public function rescheduledSessions(Request $request)
    {
        $request = (json_decode($request->getContent(), true));
        $probationer_Id   = $request['probationer_id'];

        $data   = [];

        $probationers   = DB::table('probationers')
            ->select('squad_id')
            ->where('id', $probationer_Id)->first();

        if(!empty($probationers)){
            $squad_id   = $probationers->squad_id;

            $dt = date('Y-m-d');

            $timetables = DB::table('timetables')
                ->where('squad_id', $squad_id)
                ->where('activity_id', '>', 0)
                ->whereDate('date', '>=', $dt)
                ->where('session_type', 'extra')
                ->where('session_start', '>', 0)->get();

            if(count($timetables)) {
                foreach($timetables as $timetable) {
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
                    if( !empty($subactivity_id) ) {
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

        return response()->json($response, 200);
   }

   public function prescriptions(Request $request)
   {
      $request = (json_decode($request->getContent(), true));
      $probationer_Id   = $request['probationer_id'];
      $records_push = array();
      $records_push1 = array();
      $prescriptions_date   = DB::table('probationer_prescription')->where('probationer_prescription.Probationer_Id', $probationer_Id)->select('date')->groupBy('date')->orderBy('id', 'ASC')->get();
      if (count($prescriptions_date) >= 1)
      {
        foreach($prescriptions_date as $dt)
        {
            $datewiseprescription = DB::table('probationer_prescription')->where('probationer_prescription.Probationer_Id', $probationer_Id)->where('date', $dt->date)->get();

           // print_r($datewiseprescription);exit;

            foreach($datewiseprescription as $datepres)
            {
                $records_push1 = array(
                    "drug" => isset($datepres->drug) ? $datepres->drug: '-',
                    "dosage" => isset($datepres->dosage) ? $datepres->dosage: '-',
                    "frequency" => isset($datepres->frequency) ? $datepres->frequency: '-',
                    "duration" => isset($datepres->duration) ? $datepres->duration: '-',
                    "instructions" => isset($datepres->instructions) ? $datepres->instructions: '-',
                );
                $test[] = $records_push1;
                unset($records_push1);
            }

            $records_push =  array(
                "date" => date('d-m-Y', strtotime($dt->date)),
                "symptoms" => "High Fewer and body pains",
                "daterecord"=> $test
            );
            $final_prescription[] = $records_push;
            unset($test);

        }
        $response   = [
            'code'  => '200',
            'status'    => "success",
            'data'      => $final_prescription,
        ];
      }
      else
      {
          $records_push = array(
            "message" =>  "No records exits",
        );
          $response   = [
            'code'  => '204',
            'status'    => "No records exits",
        ];
      }

      return response()->json($response, 200);
   }

   public function labreports(Request $request)
   {
    $request = (json_decode($request->getContent(), true));
    $probationer_Id   = $request['probationer_id'];
    $records_push = array();
    $records_push1 = array();

    $baseurl = url('/uploads');
   // $test = '';
    $labreports_date   = DB::table('labreports')->where('labreports.Probationer_Id', $probationer_Id)->select(DB::raw('DATE(created_at) as created_at'))->groupBy(DB::raw('Date(created_at)'))->orderBy('id', 'ASC')->get();
  //  print_r($labreports_date);exit;
    if (count($labreports_date) >= 1)
    {
      foreach($labreports_date as $dt)
      {

          $datewisereports = DB::table('labreports')->where('labreports.Probationer_Id', $probationer_Id)->whereDate('created_at', $dt->created_at)->get();
          foreach($datewisereports as $datepres)
          {
              $records_push1 = array(
                  "ReportName" => isset($datepres->ReportName) ? $datepres->ReportName: '-',
                  "FileDirectory" => isset($datepres->FileDirectory) ? $baseurl .'/'. $datepres->FileDirectory: '-',
              );
             // print_r($records_push1);exit;
              $test[] = $records_push1;
              unset($records_push1);
          }

        $records_push =  array(
            "date" => date('d-m-Y', strtotime($dt->created_at)),
            "daterecord"=> $test
        );
          $final_prescription[] = $records_push;
          unset($test);

      }
      $response   = [
          'code'  => '200',
          'status'    => "success",
          'data'      => $final_prescription,
      ];
    }
    else
    {
        $records_push = array(
            "message" =>  "No records exits",
        );
        $response   = [
          'code'  => '204',
          'status'    => "No records exits",
      ];
    }

    return response()->json($response, 200);

   }

   public function sickreports(Request $request)
   {
        $request = (json_decode($request->getContent(), true));
        $probationer_Id   = $request['probationer_id'];
        $report   = $request['report'];
        $report_id   = $request['report_id'];
        $timestamp  = date('Y-m-d H:i:s');
        $date  = date('d-m-Y');
        if(!empty($probationer_Id))
        {
            if($report_id == '' || $report_id == null)
            {
                $data  = DB::table('probationer_sickreports')->insert([
                    'Probationer_Id' => $probationer_Id,
                    'sickreport' => $report,
                    'date' => $date,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ]);
            }
            else{
                $data  = DB::table('probationer_sickreports')->where('id', $report_id)->update([
                    'Probationer_Id' => $probationer_Id,
                    'sickreport' => $report,
                    'date' => $date,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ]);
            }

            if( empty($data) ){
                $response   = [
                    'code'  => '500',
                    'status'    => "failed",
                ];
                return response()->json($response, 500);
             }
             else
             {
                $response   = [
                    'code'  => '200',
                    'status'    => "success",
                ];
                return response()->json($response, 200);
             }
        }

   }
   public function viewsickreports(Request $request)
   {
        $request = (json_decode($request->getContent(), true));
        $probationer_Id   = $request['probationer_id'];
        if(!empty($probationer_Id))
        {
            $data  = DB::table('probationer_sickreports')->where('Probationer_Id', $probationer_Id)->orderBy('id', 'DESC')->get();

            if( count($data) == '0' ){

                $response   = [
                    'code'  => '204',
                    'status'    => "No records exits",
                ];
                return response()->json($response, 200);
             }
             else
             {
                foreach($data as $dt)
                {
                    $sickreportlist[] = array(
                        'id' => isset($dt->id) ? $dt->id: '-',
                        'Probationer_Id' => isset($dt->Probationer_Id) ? $dt->Probationer_Id: '',
                        'sickreport' => isset($dt->sickreport) ? $dt->sickreport: '',
                        'date' => isset($dt->date) ? date('d-m-Y', strtotime($dt->date)): '',
                        'created_at' => isset($dt->created_at) ? date('d-m-Y H:i:s', strtotime($dt->created_at)) : '',
                        'updated_at' => isset($dt->updated_at) ?  date('d-m-Y H:i:s', strtotime($dt->updated_at)): '',
                    );
                }
                $response   = [
                    'code'  => '200',
                    'status'    => "success",
                    'data'      => $sickreportlist,
                ];
                return response()->json($response, 200);
             }
        }
        else{
            $response   = [
                'code'  => '204',
                'status'    => "Probationer Id doesn't exits",
            ];
            return response()->json($response, 200);
        }

   }

}
