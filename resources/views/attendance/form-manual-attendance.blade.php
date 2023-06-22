<form name="manualAttendance_form" id="manualAttendance_form" action="{{ url('attendance/ajax') }}" method="post" class=""
    accept-charset="utf-8">
    @csrf

    <?php
        $batch_id = $request->batch_id;
        $squad_id = $request->squad_id;

        if( empty($batch_id) || empty($squad_id) ) {
            echo "Batch Id and/or Squad Id is missing";
            return;
        }

        $date = $request->date;

        if( empty($date) ) {
            echo "Invalid date selected";
            return;
        }

        $date = date('Y-m-d', strtotime($date));

        $probationers = DB::table('probationers')
            ->where('batch_id', $batch_id)
            ->where('squad_id', $squad_id)
            ->orderBy('position_number', 'asc')
            ->get();

        $timetableQ = App\Models\Timetable::where('squad_id', $squad_id)
            ->whereDate('date', $date)
            ->where('session_type', 'regular')
            ->orderBy('session_number', 'asc');
        $timetables = $timetableQ->get()->toArray();


        $maxSessionNum = $timetableQ->max('session_number');
        $sessionCount   = max(5, $maxSessionNum);

    ?>

    <input type="hidden" name="batch_id" value="{{ $batch_id }}" class="hidden" />
    <input type="hidden" name="squad_id" value="{{ $squad_id }}" class="hidden" />
    <input type="hidden" name="date" value="{{ $date }}" class="hidden" />

    <table class="table attendance_table">
        <thead>
            <?php

                $timeTableData   = range(1, $sessionCount);

                if( !empty($timetables) ) {
                    foreach ($timetables as $timetableD) {
                        for ($si=0; $si<$sessionCount; $si++) {
                            $sn = $si + 1;
                            if($sn === intval($timetableD["session_number"])) {
                                $timeTableData[$si]  = $timetableD;
                            }
                        }
                    }
                }
            ?>
            <tr>
                <th>Name of the probationer</th>
                <?php

                $th_ti = 1;
                if( count($timeTableData) > 0 ):
                    foreach($timeTableData as $timetable) {
                        if( is_array($timetable) ) {
                            $activity_id    = $timetable["activity_id"];
                            $subactivity_id = $timetable["subactivity_id"];
                            $tt_start       = $timetable["session_start"];
                            $tt_end         = $timetable["session_end"];

                            if( !empty($activity_id) && !empty($tt_start) && !empty($tt_end) ) {
                                if(!empty($subactivity_id)) {
                                    $activity    = App\Models\Activity::withTrashed()->find($subactivity_id);
                                } else {
                                    $activity    = App\Models\Activity::withTrashed()->find($activity_id);
                                }

                                $activity_name  = $activity->name;

                                echo "<th>Session {$th_ti}<br />({$activity_name})</th>";
                            } else {
                                echo "<th>Session {$th_ti}<br />(N/A)</th>";
                            }
                        } else {
                            echo "<th>Session {$th_ti}<br />(N/A)</th>";
                        }

                        $th_ti++;
                    }
                endif;

                // for($th_ti; $th_ti <= 6; $th_ti++) {
                //     echo "<th>Session {$th_ti}<br />(N/A)</th>";
                // }
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
                <tr class="probationer-attendance-tr" data-probationer-id="{{ $pb_id }}">
                    <td>{{ $probationer->Name }}</td>
                    <?php
                    $td_ti = 1;
                    if( count($timeTableData) > 0 ):
                        foreach($timeTableData as $timetable) {
                            if( is_array($timetable) ) {
                                $timetable_id   = $timetable["id"];
                                $activity_id    = $timetable["activity_id"];
                                $tt_start       = $timetable["session_start"];
                                $tt_end         = $timetable["session_end"];

                                if( !empty($activity_id) && !empty($tt_start) && !empty($tt_end) ) {
                                    $attendances = DB::table('probationers_dailyactivity_data')
                                        ->where('probationer_id', $pb_id)
                                        ->whereDate('date', $date)
                                        ->where('timetable_id', $timetable_id)
                                        ->orderBy('updated_at', 'desc')
                                        ->first();

                                    $attendance    = "";
                                    if($attendances) {
                                        $attendance = $attendances->attendance;
                                    }
                                    ?>
                                    <td class="attendance-td" data-timetable-id="{{ $timetable_id }}">
                                        <div>
                                            @if(empty($attendance))
                                            <span class="blank-attendance">--</span>
                                            @endif

                                            <ul class="attendance-list">
                                                <li class="<?php echo ($attendance === 'P')? 'active' : ''; ?>" data-attendance="P"><img src="{{ asset('images/present.png') }}" /></li>
                                                <li class="<?php echo ($attendance === 'MDO')? 'active' : ''; ?>" data-attendance="MDO"><img src="{{ asset('images/mdo.png') }}" /></li>
                                                <li class="<?php echo ($attendance === 'NCM')? 'active' : ''; ?>" data-attendance="NCM"><img src="{{ asset('images/ncm.png') }}" /></li>
                                                <li class="<?php echo ($attendance === 'NAP')? 'active' : ''; ?>" data-attendance="NAP"><img src="{{ asset('images/nap.png') }}" /></li>
                                                <li class="<?php echo ($attendance === 'L')? 'active' : ''; ?>" data-attendance="L"><img src="{{ asset('images/leave.png') }}" /></li>
                                                <li class="<?php echo ($attendance === 'M')? 'active' : ''; ?>" data-attendance="M"><img src="{{ asset('images/medicalleave.png') }}" /></li>
                                                <li class="<?php echo ($attendance === 'OT')? 'active' : ''; ?>" data-attendance="OT"><img src="{{ asset('images/ot.png') }}" /></li>
                                            </ul>

                                            <input type="hidden" id="attendance_{{ $pb_id }}_{{ $timetable_id }}" name="attendance[{{ $pb_id }}][{{ $timetable_id }}]" value="{{ $attendance }}" class="hidden" />
                                        </div>
                                    </td>
                                    <?php
                                } else {
                                    echo "<td class=\"tt-not-available\"><small>(Timetable Not Available)</small></th>";
                                }
                                $td_ti++;
                            } else {
                                echo "<td class=\"tt-not-available\"><small>(Timetable Not Available)</small></th>";
                            }
                        }
                    endif;

                    // for($td_ti; $td_ti <= 6; $td_ti++) {
                    //     echo "<td><small>(Timetable Not Available)</small></th>";
                    // }
                    ?>
                </tr>
                @endforeach
            @endif
        </tbody>
    </table>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="usersubmitBtns">
                <div class="mr-4">
                    <button type="button" onclick="window.manualAttendance_Submit();"
                        class="btn formBtn submitBtn">Submit</button>
                </div>
            </div>
        </div>
    </div>
</form>
