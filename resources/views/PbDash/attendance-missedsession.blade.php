<?php

$month_year = $request->month_Year;
$month_year = explode('-', $month_year);

$dateStart = "";
$dateEnd = "";

if(count($month_year) === 2) {

$dateStart  = date('Y-m-d', mktime(0,0,0, $month_year[0], 1, $month_year[1]));
$dateEnd = date('Y-m-t', strtotime($dateStart));
} else {
echo "Select Month";
return false;
}

$probationer = probationer_data();
$pb_id = $probationer->id;

$timetables = \App\Models\Timetable::whereNotNull("activity_id")
    ->whereDate("date", ">=", "{$dateStart}")
    ->whereDate("date", "<=", "{$dateEnd}")
    ->where('squad_id',  $probationer->squad_id)
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
        if(empty($tt_subactivity_id)) {
            $tt_subactivity_id  = 0;
        }

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
?>

<div class="missedsession-report mt-5">
    <div class="missedsession-title">
        <span>PHYSICAL TRAINING</span>
    </div>
    <table class="table table-bordered">
        <thead class="thead">
            <tr>
                <th scope="col" width="60%"></th>
                <th scope="col" width="40%">Missed</th>
            </tr>
        </thead>
        <tbody>
            <?php
                foreach($activities as $activity_id => $data) {
                    foreach($data as $subactivity_id) {

                        // $activity = App\Models\Activity::withTrashed()->find($activity_id);

                        $activityName = activity_name($activity_id);

                        $subactivityName = "";
                        if( !empty($subactivity_id) ) {
                            $subactivityName = activity_name($subactivity_id);
                            $subactivityName = "({$subactivityName})";
                        }

                        // $getAttns = DB::table('probationers_dailyactivity_data')
                        //     ->join('timetables', 'probationers_dailyactivity_data.timetable_id', '=', 'timetables.id')
                        //     ->whereRaw("probationers_dailyactivity_data.probationer_id = ? AND probationers_dailyactivity_data.date BETWEEN ? AND ? AND timetables.activity_id = ? AND timetables.subactivity_id = ?", [$pb_id, $dateStart, $dateEnd, $activity_id, $subactivity_id])
                        //     ->select('probationers_dailyactivity_data.attendance', 'probationers_dailyactivity_data.timetable_id')
                        //     ->groupBy('probationers_dailyactivity_data.timetable_id')
                        //     ->get();

                        $getAttnsQ = App\Models\Timetable::query()
                                // ->whereRaw("probationers_dailyactivity_data.probationer_id = ? AND probationers_dailyactivity_data.date BETWEEN ? AND ? AND timetables.activity_id = ? AND timetables.subactivity_id = ?", [$pb_id, $dateStart, $dateEnd, $activity_id, $subactivity_id])
                                ->where("probationers_dailyactivity_data.probationer_id", $pb_id)
                                ->whereBetween("probationers_dailyactivity_data.date", [$dateStart, $dateEnd])
                                ->where("probationers_dailyactivity_data.activity_id", $activity_id);
                            if(!empty($subactivity_id)) {
                                $getAttnsQ->where("probationers_dailyactivity_data.subactivity_id", $subactivity_id);
                            }
                            $getAttns  = $getAttnsQ->leftJoin('probationers_dailyactivity_data', 'probationers_dailyactivity_data.timetable_id', '=', 'timetables.id')
                                ->select('probationers_dailyactivity_data.attendance', 'probationers_dailyactivity_data.timetable_id')
                                ->groupBy('probationers_dailyactivity_data.timetable_id')
                                ->get();

                            $total = count($getAttns);
                            $attended = 0;
                            $missed = 0;

                            if( $total > 0 ) {
                                foreach($getAttns as $getAttn) {
                                    $timetable_id   = $getAttn->timetable_id;
                                    if( in_array($getAttn->attendance, ['P', 'MDO', 'NCM']) ) {
                                        $attended++;
                                    } else {
                                        $Extrasession = \App\Models\ExtraSessionmeta::where('probationer_id', $pb_id)
                                            ->whereIn('attendance', ['P', 'MDO', 'NCM'])
                                            ->where('timetable_id', $timetable_id)
                                            ->count();

                                        if($Extrasession > 0) {
                                            $attended++;
                                        }
                                    }
                                }

                                $missed = $total - $attended;

                            } else {
                                $total = "--";
                                $attended = "--";
                                $missed = "--";
                            }

                        echo "<tr>";
                        echo "<td>{$activityName} {$subactivityName}</td>";
                        echo "<td>{$missed}</td>";
                        echo "</tr>";
                    }
                }
            ?>
        </tbody>
    </table>
</div>
