<form name="timetableUpdate_form" id="timetableUpdate_form" action="{{ url('timetables/ajax') }}" method="post" class=""
    accept-charset="utf-8">
    @csrf

    <?php

        $timetable_selection = $request->timetableSelector;

        if ($timetable_selection === "Today") {
            $from_date = date("Y-m-d");
            $to_date = $from_date;
        } else if ($timetable_selection === "Tomorrow") {
            $from_date = date("Y-m-d", strtotime(' +1 day'));
            $to_date = $from_date;
        } else if ($timetable_selection === "Week") {
            $from_date = date("Y-m-d");
            $to_date = date("Y-m-d", strtotime(' +6 day'));
        } else {
            echo "Invalid date range selected";
            return;
        }

        $user_id = Auth::user()->id;

        $probationer = DB::table('probationers')
            ->where('user_id', $user_id)
            ->select('id','batch_id','Name','squad_id')->get()->first();

        if( empty($probationer) ) {
            echo "Unable to retrive user data";
            return;
        }

        $pb_id  = $probationer->id;

        $batch_id = $probationer->batch_id;
        $squad_id = $probationer->squad_id;

        $date1 = new DateTime($from_date);
        $date2 = new DateTime($to_date);

        $diff = $date1->diff($date2);
        $days = $diff->days;

        $maxSessionNum = App\Models\Timetable::where('squad_id', $squad_id)
            ->whereDate('date', '>=', $from_date)
            ->whereDate('date', '<=', $to_date)
            ->where('session_type', 'regular')
            ->max('session_number');

        $sessionCount   = max(5, $maxSessionNum);
    ?>

    <table class="table table-bordered timetable">
        <thead>
            <tr>
                <th>Weekdays</th>
                @for ($ssn=1; $ssn<=$sessionCount; $ssn++)
                    <th>
                        Session {{ $ssn }}
                    </th>
                @endfor
            </tr>
        </thead>
        <tbody>
            <?php
            for ($i = 0; $i <= $days; $i++) {
                $date       = date('Y-m-d', strtotime($from_date . ' + ' . $i . ' days'));
                $dayName    = date('l', strtotime($date));

                echo "<tr><th>{$dayName}<br /><small>{$date}</small></th>";

                $timetables = App\Models\Timetable::where('squad_id', $squad_id)
                    ->whereDate('date', $date)
                    ->where('session_type', 'regular')
                    ->orderBy('session_number', 'asc')->get()->toArray();

                // echo '<pre>';
                // print_r($timetables);
                // echo '</pre>';

                // return;

                $timeTableData   = range(1, $sessionCount);

                if( !empty($timetables) ) {
                    foreach ($timetables as $timetable) {
                        for ($si=0; $si<$sessionCount; $si++) {
                            $sn = $si + 1;
                            if($sn === intval($timetable["session_number"])) {
                                $timeTableData[$si]  = $timetable;
                            }
                        }
                    }
                }

                $ti = 0;
                foreach ($timeTableData as $sessionData) {
                    if( is_array($sessionData) ) {
                            $tt_id              = $sessionData["id"];
                            $tt_activity_id    = $sessionData["activity_id"];
                            $tt_subactivity_id    = $sessionData["subactivity_id"];

                            // $subactivity_disp   = !empty($tt_subactivity_id)? " ({$tt_subactivity_id})" : "";

                            $tt_date    = $sessionData["date"];

                            $tt_start    = $sessionData["session_start"];
                            $tt_end      = $sessionData["session_end"];

                            if( !empty($tt_activity_id) && !empty($tt_start) && !empty($tt_end) ) {
                                $activity_time  = date('h:i A', $tt_start) .' - '. date('h:i A', $tt_end);

                                if(!empty($tt_subactivity_id)) {
                                    $activity = App\Models\Activity::withTrashed()->find($tt_subactivity_id);
                                } else {
                                    $activity = App\Models\Activity::withTrashed()->find($tt_activity_id);
                                }
                                $activity_name = $activity->name;

                                echo "<td>{$activity_name}<br />{$activity_time}</td>";
                            } else {
                                echo "<td></td>";
                            }

                            $ti++;
                    } else {
                        echo "<td></td>";
                    }
                }
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</form>
