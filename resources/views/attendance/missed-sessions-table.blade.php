<?php
    $batch_id = $request->batch_id;
    $squad_id = $request->squad_id;

    if( empty($batch_id) || empty($squad_id) ) {
        echo "Batch Id and/or Squad Id is missing";
        return;
    }

    // $year   = $request->year;
    // $month  = sprintf('%2d', $request->month);
    // $monthName = date('F', mktime(0,0,0,$month, 1, $year));

    $from_date = $request->from_date;
    $to_date = $request->to_date;

    if( empty($from_date) || empty($to_date) ) {
        echo "Invalid date range selected";
        return;
    }

    $dateStart = date('Y-m-d', strtotime($from_date));
    $dateEnd = date('Y-m-d', strtotime($to_date));


    // $daysInMonth   = cal_days_in_month(0, $month, $year);

    // $dateStart  = date('Y-m-d', mktime(0,0,0,$month, 1, $year));
    // $dateEnd    = date('Y-m-d', mktime(0,0,0,$month, $daysInMonth, $year));

    $batch  = App\Models\Batch::find($batch_id);
    $squad  = App\Models\Squad::find($squad_id);

    $probationers = DB::table('probationers')
        ->select('id','Name')
        ->where('batch_id', $batch_id)
        ->where('squad_id', $squad_id)
        ->orderBy('position_number', 'asc')
        ->get();

    // $timetables = DB::table('timetables')
    //     ->selectRaw('activity_id, ANY_VALUE(id) AS id')
    //     ->groupBy('activity_id')
    //     ->whereRaw("date BETWEEN ? AND ?", [$dateStart, $dateEnd])
    //     ->get();
    $timetables = \App\Models\Timetable::where('squad_id', $squad_id)
        ->where("activity_id", "!=", 0)
        ->whereNotNull("activity_id")
        ->whereDate("date", ">=", "{$dateStart}")
        ->whereDate("date", "<=", "{$dateEnd}")
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
    ?>

<div class="reporttable">

    <div class="reporttablehead">
        <span>Batch No: {{ $batch->BatchName }}</span>
        <span>Squad No: {{ $squad->SquadNumber }}</span>
        <span>{{ $dateStart .' to '. $dateEnd }}</span>
        {{-- <span>{{ $monthName .' - '. $year }}</span> --}}
    </div>
    <div class="table-responsive">
        <table class="table table-bordered monthlyreportinfo missed-sessions-table">
            <thead>
                <tr>
                    <th rowspan="2">Name of the Probationer</th>
                    <?php
                    foreach($activities as $activity_id => $data) {
                        foreach($data as $subactivity_id) {
                            $activityName = activity_name((int)$activity_id);
                            if( empty($activityName) ) {
                                $activityName = "--";
                            }
                            echo "<th>{$activityName}</th>";
                        }
                    }
                    ?>
                </tr>
                <tr>
                    <?php
                    foreach($activities as $activity_id => $data) {
                        foreach($data as $subactivity_id) {

                            $subactivityName = activity_name((int)$subactivity_id);
                            if( empty($subactivityName) ) {
                                $subactivityName = "--";
                            }
                            echo "<th>{$subactivityName}<br />Missed</th>";
                        }
                    }
                    ?>
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
                            <?php
                            foreach($activities as $activity_id => $data) {
                                foreach($data as $subactivity_id) {

                                    $total = App\Models\Timetable::where('squad_id', $squad_id)
                                        ->whereBetween("date", [$dateStart, $dateEnd])
                                        ->where("activity_id", $activity_id)
                                        ->where("subactivity_id", $subactivity_id)
                                        ->where("session_start", '>', 0)
                                        ->count();
                                    // $getAttns = App\Models\Timetable::whereRaw("probationers_dailyactivity_data.probationer_id = ? AND probationers_dailyactivity_data.date BETWEEN ? AND ? AND timetables.activity_id = ? AND timetables.subactivity_id = ?", [$pb_id, $dateStart, $dateEnd, $activity_id, $subactivity_id])
                                    //     ->leftJoin('probationers_dailyactivity_data', 'probationers_dailyactivity_data.timetable_id', '=', 'timetables.id')
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

                                    $attended_count = count($getAttns);

                                    $attended = 0;

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

                                        $missed = $attended_count - $attended;

                                        echo "<td>{$missed}</td>";
                                    } else {
                                        echo "<td>-</td>";
                                    }
                                }
                            }
                            ?>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>
