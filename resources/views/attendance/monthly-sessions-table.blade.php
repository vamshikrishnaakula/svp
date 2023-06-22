<?php
    $batch_id = $request->batch_id;
    $squad_id = $request->squad_id;

    if( empty($batch_id) || empty($squad_id) ) {
        echo "Batch Id and/or Squad Id is missing";
        return;
    }

    $from_date = $request->from_date;
    $to_date = $request->to_date;

    if( empty($from_date) || empty($to_date) ) {
        echo "Invalid date range selected";
        return;
    }

    $dateStart = date('Y-m-d', strtotime($from_date));
    $dateEnd = date('Y-m-d', strtotime($to_date));




    $batch  = App\Models\Batch::find($batch_id);
    $squad  = App\Models\Squad::find($squad_id);

    $probationers = DB::table('probationers')
        ->select('id','Name')
        ->where('squad_id', $squad_id)
        ->orderBy('position_number', 'asc')
        ->get();

    // $timetables = DB::select(
    //     "SELECT activity_id, subactivity_id, ANY_VALUE(id) AS id
    //     FROM timetables
    //     WHERE activity_id != 0 AND activity_id IS NOT NULL
    //         AND date BETWEEN '{$dateStart}' AND '{$dateEnd}'
    //     GROUP BY activity_id, subactivity_id
    //     ORDER BY activity_id"
    // );
    $timetables = \App\Models\Timetable::where('squad_id', $squad_id)
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
            $tt_subactivity_id  = empty($timetable->subactivity_id)? null : $timetable->subactivity_id;

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
    </div>
    <div class="table-responsive">
        <table class="table table-bordered monthlyreportinfo monthly-sessions-table">
            <thead>
                <tr>
                    <th rowspan="3">Name of the Probationer</th>
                    <?php
                    foreach($activities as $activity_id => $data) {
                        foreach($data as $subactivity_id) {
                            $activityName = activity_name($activity_id);
                            if( empty($activityName) ) {
                                $activityName = "--";
                            }
                            echo "<th colspan=\"3\">{$activityName}</th>";
                        }
                    }
                    ?>
                </tr>
                <tr>
                    <?php
                    foreach($activities as $activity_id => $data) {
                        foreach($data as $subactivity_id) {

                            $subactivityName = "--";
                            if( !empty($subactivity_id) ) {
                                $subactivityName = activity_name($subactivity_id);
                            }
                            echo "<th colspan=\"3\">{$subactivityName}</th>";
                        }
                    }
                    ?>
                </tr>
                <tr>
                    @for($ti=0; $ti<count($subactivities); $ti++)
                        <th>Total</th>
                        <th>Attended</th>
                        <th>Missed</th>
                    @endfor
                </tr>
            </thead>
            <tbody>
                @if($probationers)
                @foreach ($probationers as $probationer)
                    @php
                    $pb_id = $probationer->id;
                    $pb_name = $probationer->Name;
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

                            $getAttnsQ = App\Models\Timetable::query()
                                // ->whereRaw("probationers_dailyactivity_data.probationer_id = ? AND probationers_dailyactivity_data.date BETWEEN ? AND ? AND timetables.activity_id = ? AND timetables.subactivity_id = ?", [$pb_id, $dateStart, $dateEnd, $activity_id, $subactivity_id])
                                ->where("probationers_dailyactivity_data.probationer_id", $pb_id)
                               // ->whereIn('attendance', ['P', 'MDO', 'NCM'])
                                  ->whereNotNull('attendance')
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

                                echo "<td>{$total}</td>";
                                echo "<td>{$attended}</td>";
                                echo "<td>{$missed}</td>";
                            } else {
                                echo "<td>-</td>";
                                echo "<td>-</td>";
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
