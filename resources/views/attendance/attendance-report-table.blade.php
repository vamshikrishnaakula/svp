<?php
    $errors = [];

    $batch_id = $request->batch_id;
    $squad_id = $request->squad_id;
    $dater_range = $request->report_datetimerange;
  

    if( empty($batch_id) || empty($squad_id) ) {
        echo "Batch Id and/or Squad Id is missing";
        return;
    }

    // $year   = $request->year;
    // $month  = $request->month;

    // if( empty($year) || empty($month) ) {
    //     echo "Year and/or Month is missing";
    //     return;
    // }

    $date1  = "";
    $date2  = "";

    if (empty($dater_range)) {
        $errors[] = "Select Date Range.";
    } else {
        $daterangeArray = explode(" - ", $dater_range);
        if(count($daterangeArray) !== 2) {
            $errors[] = "Invalid Date Range (Expected: DD/MM/YYYY - DD/MM/YYYY).";
        } else if(!isValidDate($daterangeArray[0], 'd/m/Y') || !isValidDate($daterangeArray[1], 'd/m/Y')) {
            $errors[] = "Invalid Date Format (Expected: DD/MM/YYYY - DD/MM/YYYY).";
        } else {
            $date1 = convert_date($daterangeArray[0], 'd/m/Y');
            $date2 = convert_date($daterangeArray[1], 'd/m/Y');
        }
    }
   

    if (!empty($errors)) {
        echo "Error: " . implode('<br />', $errors);
        return;
    }

    $dateBegin  = new DateTime($date1);
     
    $dateEnd    = new DateTime(date("Y-m-d", strtotime($date2 ."+1 days")));

    $dateInterval   = DateInterval::createFromDateString('1 day');
   
    $datePeriod     = new DatePeriod($dateBegin, $dateInterval, $dateEnd);
    



    // print_r(json_encode($datePeriod));exit;
   
    // $days   = cal_days_in_month(0, $month, $year);

    $probationers = DB::table('probationers')
        ->select('id','Name')
        ->where('batch_id', $batch_id)
        ->where('squad_id', $squad_id)
        ->orderBy('position_number', 'asc')
        ->get();

    $batch_name = batch_name($batch_id);
    $squad_name = squad_number($squad_id);
    $month_name = date('M-Y', strtotime($date1));

    $data_summery   = "Batch: {$batch_name} | Squad: {$squad_name} | Date: ". date("M d, Y", strtotime($date1)) ." to ". date("M d, Y", strtotime($date2));
   // print_r($data_summery);exit;
    // Get session counts for each days
    // $sessionCount = [];
    // for ($d=1; $d<=$days; $d++) {
    //     $date   = $year.'-'. sprintf('%02d', $month) .'-'. sprintf('%02d', $d);

    //     $maxSessionNum = App\Models\Timetable::where('squad_id', $squad_id)
    //         ->whereDate('date', $date)
    //         ->where('session_type', 'regular')
    //         ->max('session_number');
    //     $sessionCount[$d]   = max(6, $maxSessionNum);
    // }

    // echo '<pre>';
    // print_r($sessionCount);
    // echo '</pre><hr />';

    $maxSessionNum1 = App\Models\Timetable::where('squad_id', $squad_id)
        ->whereBetween('date', [$date1, $date2])
        ->where('session_type', 'regular')
        ->selectRaw('date, max(session_number) as max_session')
        ->groupBy('date')
        ->get();
      //  return json_encode($maxSessionNum1);
        //print_r($maxSessionNum1);exit;
        //echo json_encode($maxSessionNum1);

    $sessionCount1 = [];
    foreach($maxSessionNum1 as $mx) {
        $sessionCount1[$mx->date]   = max(5, $mx->max_session);
    }
  //  print_r($sessionCount1[$mx->date]);exit;
    

    $sessionCount = [];
    foreach ($datePeriod as $dt) {
        $dt1   = $dt->format('Y-m-d');
        //print_r($dt1);exit;
        $sessionCount[$dt1]   = isset($sessionCount1[$dt1])? max(5, $sessionCount1[$dt1]) : 5;
    }
  //  print_r($sessionCount);exit;
    //print_r($sessionCount);exit;
    // echo '<pre>';
    // print_r($sessionCount1);
    // echo '</pre><hr />';
?>
<div class="row">
    <div class="col-md-7">
        <p class="mb-1">{{ $data_summery }}</p>
    </div>
    <div class="col-md-5">

    </div>
</div>
<div class="table-responsive">
    <table class="table table-bordered monthlyreportinfo attendance-report-table">
        <thead>
            <tr>
                <th rowspan="2">Name of the Probationer</th>
                @php
                foreach ($datePeriod as $dt) {
                    $dt1   = $dt->format('Y-m-d');
                    $dt2   = $dt->format('d/m/Y');

                    echo "<th colspan=\"{$sessionCount[$dt1]}\">{$dt2}</th>";
                }
               // echo $dt2;exit;
                @endphp
            </tr>
            <tr>
                @php
                foreach ($datePeriod as $dt) {
                    $dt1   = $dt->format('Y-m-d');
                    for($si=1; $si<=$sessionCount[$dt1]; $si++) {
                        echo "<th>S{$si}</th>";
                    }
                }
                @endphp
            </tr>
        </thead>
        <tbody>
            @if($probationers)
                @foreach ($probationers as $probationer)
                    @php
                    $pb_id      = $probationer->id;
                    $pb_name    = $probationer->Name;
                    @endphp

                    <tr>
                        <td>{{ $pb_name }}</td>
                        @php
                        foreach ($datePeriod as $dt) {
                            $date   = $dt->format('Y-m-d');

                            $timetables = App\Models\Timetable::where('squad_id', $squad_id)
                                ->whereDate('date', $date)
                                ->where('session_type', 'regular')
                                ->orderBy('session_number', 'asc')->get()->toArray();
                                

                            $timeTableData   = range(1, $sessionCount[$date]);

                            if( !empty($timetables) ) {
                                foreach ($timetables as $data) {
                                    for ($si=0; $si<$sessionCount[$date]; $si++) {
                                        $sn = $si + 1;
                                        if($sn === intval($data["session_number"])) {
                                            $timeTableData[$si]  = $data;
                                        }
                                    }
                                }
                            }

                            $ai = 0;
                            if(count($timeTableData) > 0) {
                                foreach($timeTableData as $timetable) {
                                    if( is_array($timetable) ) {
                                        $getAttn = DB::table('probationers_dailyactivity_data')
                                            ->where('probationer_id', $pb_id)
                                            ->where('timetable_id', $timetable["id"])
                                            ->select('attendance')->first();

                                        if($getAttn) {
                                            $cel_class   = attendance_bg_color($getAttn->attendance);
                                            echo "<td class=\"{$cel_class}\">{$getAttn->attendance}</td>";
                                        } else {
                                            echo "<td>-</td>";
                                        }

                                        $ai++;
                                    } else {
                                        echo "<td>-</td>";
                                    }
                                }
                            }
                            // for($ai; $ai<6; $ai++) {
                            //     echo "<td>-</td>";
                            // }
                        }
                        @endphp
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>
