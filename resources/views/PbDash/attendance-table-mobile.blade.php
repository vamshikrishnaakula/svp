<?php
$year   = $request->year;
$month  = $request->month;
$pid  = $request->pid;

if( empty($year) || empty($month) ) {
    echo "Year and/or Month is missing";
    return;
}
$month  = sprintf('%02d', $request->month);

$days   = cal_days_in_month(0, $month, $year);
$date1  = $year.'-'. sprintf('%02d', $month) .'-01';
$date2  = $year.'-'. sprintf('%02d', $month) .'-'. $days;


$user_email = Auth::user()->email;

$probationer = DB::table('probationers')
    ->where('id', $pid)->select('id','Name', 'squad_id')->get()->first();


if( empty($probationer) ) {
    echo "Unable to retrive user data";
    return;
}

$pb_id  = $probationer->id;
$squad_id  = $probationer->squad_id;

// Get max session number in the month
$maxSessionNum = App\Models\Timetable::where('squad_id', $squad_id)
    ->whereDate('date', '>=', $date1)
    ->whereDate('date', '<=', $date2)
    ->where('session_type', 'regular')
    ->max('session_number');
$sessionCount   = max(5, $maxSessionNum);
?>

<div class="monthly-report-block mt-5">
    <div class="table-responsive">
    <table class="table table-bordered monthlyreportinfo mb_report">
        <thead>
            <tr>
                <th></th>
                @for($si=1; $si<=$sessionCount; $si++)
                    <th>S{{ $si }}</th>

                @endfor
            </tr>
        </thead>
        <tbody>
            <?php
            for($d=1; $d<=$days; $d++) {
                $date   = $year.'-'. sprintf('%02d', $month) .'-'. sprintf('%02d', $d);

                $timetableQ = App\Models\Timetable::where('squad_id', $probationer->squad_id)
                    ->whereDate('date', $date)
                    ->where('session_type', 'regular')
                    ->orderBy('session_number', 'asc');
                $timetables = $timetableQ->get()->toArray();

                // Max session_number for the day
                $maxSessionNum = $timetableQ->max('session_number');
                $sessionCount   = max(5, $maxSessionNum);

                $timeTableData   = range(1, $sessionCount);
                if( !empty($timetables) ) {
                    foreach ($timetables as $data) {
                        for ($si=0; $si<$sessionCount; $si++) {
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
                  //  $sqaud_id = squad_id((int) )
                    $hoilday_check = App\Models\Hoilday::where('squad_id', $squad_id)->whereDate('date', $date)->first();
                    $row_class = ($hoilday_check == '') ? 'timetable-weekdays' : 'timetable-weekend';
                    $day    = date('d-M', strtotime($date));
                    echo "<tr class = \"{$row_class}\">";
                    echo "<td class=\"text-left\">{$day}</td>";

                    foreach($timeTableData as $timetable) {
                        $attendance = '-';

                        if( is_array($timetable) ) {
                            $getAttn = DB::table('probationers_dailyactivity_data')
                                ->where('probationer_id', $pb_id)
                                ->where('timetable_id', $timetable["id"])
                                ->select('attendance', 'date')->first();
                            if($getAttn) {
                            $attendance = isset($getAttn->attendance) ? $getAttn->attendance : '-';
                            }
                            $ai++;
                        }

                        if($attendance == 'P' || $attendance == 'MDO' || $attendance == 'NCM')
                        {
                            echo "<td>{$attendance}</td>";
                        }
                        elseif($attendance == 'L' || $attendance == 'M'  || $attendance == 'NAP' || $attendance == 'OT')
                        {
                            echo "<td style='color:red;''>{$attendance}</td>";
                        }
                        else
                        {
                            echo "<td>{$attendance}</td>";
                        }


                    }

                    echo "</td>";
                    echo "</tr>";
                }
            }
            ?>
        </tbody>
    </table>
 </div>
</div>
<style>
@media screen and (min-width:230px) and (max-width:767px) {

    table.mb_report tbody tr:nth-child(even) {
        background-color: #ddd;
    }

    /* table.mb_report tr td:first-child,
    table.mb_report tr th:first-child {
        position: -webkit-sticky;
        position: sticky;
        min-width:120px;
        left: 0px;
    } */
    table.mb_report thead tr th{
        position: sticky;
        position: -webkit-sticky;
        top: 0px;
    }
    table.mb_report tr th {
        color:#fff;
        padding: 12px;
        /* background-color: #92b1fd; */
        background-color: #03DAC5;
    }
    table.mb_report{
        border: none;
        border-collapse: separate;
        border-spacing: 0;
        border: 1px solid #f1f1f1;
      //  display: block;
        min-width: 100%;
        width:100%;
      //  height:580px;
        overflow: auto;
    }
    table.mb_report td:first-child{
        min-width: 50px;
    }
    table.mb_report td {
        text-align: center;
        border: 1px solid #f1f1f1;
        padding: 8px;
    }
    table.mb_report tbody{
        overflow: auto;
    }
    table.mb_report th{
        border:1px solid #fff;
    }
    attendance-color{
        color: #df2d2d
    }
    tr.timetable-weekend td {
        background-color:#fff0de;
        border-color: #afafaf;
    }

}
</style>
