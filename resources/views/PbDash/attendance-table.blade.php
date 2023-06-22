<?php
$year   = $request->year;
$month  = $request->month;

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
    ->where('Email', $user_email)->select('id','Name', 'squad_id')->get()->first();

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
    <table class="table table-bordered monthlyreportinfo">
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
                    $day    = date('d-m-Y', strtotime($date));
                    echo "<tr>";
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

                        echo "<td>{$attendance}</td>";
                    }

                    echo "</td>";
                    echo "</tr>";
                }
            }
            ?>
        </tbody>
    </table>
</div>
