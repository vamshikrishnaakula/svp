<form name="timetableUpdate_form" id="timetableUpdate_form" action="{{ url('timetables/ajax') }}" method="post"
    class="" accept-charset="utf-8">
    @csrf
    <?php
    $batch_id = $request->batch_id;
    $squad_id = $request->squad_id;
    
    if (empty($batch_id) || empty($squad_id)) {
        echo 'Batch Id and/or Squad Id is missing';
        return;
    }
    
    $from_date = $request->from_date;
    $to_date = $request->to_date;
    
    if (empty($from_date) || empty($to_date)) {
        echo 'Invalid date range selected';
        return;
    }
    
    $from_date = date('Y-m-d', strtotime($from_date));
    $to_date = date('Y-m-d', strtotime($to_date));
    
    $date1 = new DateTime($from_date);
    $date2 = new DateTime($to_date);
    
    $diff = $date1->diff($date2);
    if ($diff->invert === 1) {
        echo 'Invalid date range selected';
        return;
    }
    $days = $diff->days;
    
    $maxSessionNum = App\Models\Timetable::where('squad_id', $squad_id)
        ->whereDate('date', '>=', $from_date)
        ->whereDate('date', '<=', $to_date)
        ->where('session_type', 'regular')
        ->max('session_number');
    
    $sessionCount = max(5, $maxSessionNum);
    ?>

    <input type="hidden" name="batch_id" value="{{ $batch_id }}" class="hidden" />
    <input type="hidden" name="squad_id" value="{{ $squad_id }}" class="hidden" />

    <table class="table table-bordered timetable">
        <thead>
            <tr>
                <th>Weekdays</th>
                @for ($ssn = 1; $ssn <= $sessionCount; $ssn++)
                    <th>
                        Session {{ $ssn }}
                    </th>
                @endfor
            </tr>
        </thead>
        <tbody>
            <?php
            for ($i = 0; $i <= $days; $i++) {
                $date = date('Y-m-d', strtotime($from_date . ' + ' . $i . ' days'));
                $dayName = date('l', strtotime($date));
            
                // $row_class  = in_array(strtolower($dayName), ['saturday', 'sunday'])? 'timetable-weekend' : 'timetable-weekdays';
            
                $hoilday_check = App\Models\Hoilday::where('batch_id', $batch_id)
                    ->where('squad_id', $squad_id)
                    ->whereDate('date', $date)
                    ->first();
                $check = $hoilday_check == '' ? '' : 'checked';
                $row_class = $hoilday_check == '' ? 'timetable-weekdays' : 'timetable-weekend';
            
                echo "<tr class=\"{$row_class}\"><th>{$dayName}<br /><small>{$date}</small></th>";
            
                $timetables = App\Models\Timetable::where('squad_id', $squad_id)
                    ->whereDate('date', $date)
                    ->where('session_type', 'regular')
                    ->orderBy('session_number', 'asc')
                    ->get()
                    ->toArray();
                // echo '<pre>';
               $timetabledata = App\Models\Timetable::where('batch_id', $batch_id)
                    ->where('squad_id', $squad_id)
                    ->whereBetween('date', [$date1, $date2])
                    ->whereNotNull('activity_id')
                    ->get();
                // print_r(isset(json_encode($timetable)));exit;
                // echo '</pre>';
                // return;
                $timeTableData = range(1, $sessionCount);
            
                if (!empty($timetables)) {
                    foreach ($timetables as $timetable) {
                        for ($si = 0; $si < $sessionCount; $si++) {
                            $sn = $si + 1;
                            if ($sn === intval($timetable['session_number'])) {
                                $timeTableData[$si] = $timetable;
                            }
                        }
                    }
                }
            
                $ti = 0;
                foreach ($timeTableData as $sessionData) {
                    if (is_array($sessionData)) {
                        $tt_id = $sessionData['id'];
                        $tt_activity_id = $sessionData['activity_id'];
                        $tt_subactivity_id = $sessionData['subactivity_id'];
            
                        // $subactivity_disp   = !empty($tt_subactivity_id)? " ({$tt_subactivity_id})" : "";
            
                        $tt_date = $sessionData['date'];
                        $tt_start = $sessionData['session_start'];
                        $tt_end = $sessionData['session_end'];
            
                        if (!empty($tt_activity_id) && !empty($tt_start) && !empty($tt_end)) {
                            $activity_time = date('h:i A', $tt_start) . ' - ' . date('h:i A', $tt_end);
            
                            if (!empty($tt_subactivity_id)) {
                                $activity = App\Models\Activity::withTrashed()->find($tt_subactivity_id);
                            } else {
                                $activity = App\Models\Activity::withTrashed()->find($tt_activity_id);
                            }
                            $activity_name = $activity->name;
            
                            echo "<td>{$activity_name}<br />{$activity_time}</td>";
                        } else {
                            echo '<td></td>';
                        }
            
                        $ti++;
                    } else {
                        echo '<td></td>';
                    }
                }
            
                echo '</tr>';
            }
            //print_r($timetable);
            ?>
        </tbody>
    </table>
</form>
