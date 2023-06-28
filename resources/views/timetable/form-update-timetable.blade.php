<style>
    .timetable-weekend2 {
        background-color: red !important;
    }
</style>
<form name="timetableUpdate_form" id="timetableUpdate_form" action="{{ url('timetables/ajax') }}" method="post"
    class="" accept-charset="utf-8">
    @csrf
    <?php
    $batch_id = $request->batch_id;
    $squad_id = $request->squad_id;
    $id = $request->squad_2;
    //echo $id;exit;
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
    //echo $from_date; exit;
    $to_date = date('Y-m-d', strtotime($to_date));
    
    $date1 = new DateTime($from_date);
    $date2 = new DateTime($to_date);
    //print_r($date1); exit;
    
    $diff = $date1->diff($date2);
    if ($diff->invert === 1) {
        echo 'Invalid date range selected';
        return;
    }
    $days = $diff->days;
    // echo $days;exit;
    
    $activities = App\Models\Activity::where('type', 'activity')
        ->where('batch_id', $batch_id)
        ->get();
    
    $maxSessionNum = App\Models\Timetable::where('squad_id', $squad_id)
        ->whereDate('date', '>=', $from_date)
        ->whereDate('date', '<=', $to_date)
        ->where('session_type', 'regular')
        ->max('session_number');
    
    $sessionCount = max(5, $maxSessionNum);
    //$destinationSquadId = $request->squad_2;
    //return gettype($destinationSquadId);
    $timetables = \App\Models\Timetable::where('batch_id', $batch_id)
        ->where('squad_id', $squad_id)
        ->whereBetween('date', [$date1, $date2])
        ->whereNotNull('activity_id')
        ->get();
    //print_r(json_encode($timetables));exit;
    ?>
    <input type="hidden" name="batch_id" value="{{ $batch_id }}" class="hidden" />
    <input type="hidden" name="squad_id" value="{{ $squad_id }}" class="hidden" />

    <table class="table table-bordered timetable">
        <thead>
            <tr class="tr-header">
                <th class="text-center" style="min-width:auto">Holiday</th>
                <th>Weekdays</th>
                @for ($ssn = 1; $ssn <= $sessionCount; $ssn++)
                    <th>
                        {{-- //// --}}
                        {{-- <th class="text-center" style="min-width:auto">Holiday</th> --}}
                        Session {{ $ssn }}
                        <span class="add_more_session"><i class="fas fa-plus"></i> Add</span>
                    </th>
                @endfor
            </tr>
        </thead>
        <tbody>
            <?php
            for ($i = 0; $i <= $days; $i++) {
                $date       = date('Y-m-d', strtotime($from_date . ' + ' . $i . ' days'));
                $dayName    = date('l', strtotime($date));
                //print_r($dayName);exit;
                //kkk
               // $row_class  = in_array(strtolower($dayName), ['saturday', 'sunday'])? 'timetable-weekend' : 'timetable-weekdays';
//holiday checkbox
                 $hoilday_check = App\Models\Hoilday::where('batch_id', $batch_id)->where('squad_id', $squad_id)->whereDate('date', $date)->first();
                 $check = ($hoilday_check == '') ? '' : 'checked';
                 $row_class = ($hoilday_check == '') ? 'timetable-weekdays' : 'timetable-weekend';


                echo "<tr class=\"tr-body {$row_class}\">
                    <th style='min-width:auto'><input class='form-control holiday_check' type='checkbox' id='mark_hoilday' name='mark_hoilday[{$date}]' {$check}></th>
                    <th>{$dayName}<br /><small>{$date}</small></th>";


                $timetables = App\Models\Timetable::where('squad_id', $squad_id)
                    ->whereDate('date', $date)
                    ->where('session_type', 'regular')
                    ->orderBy('session_number', 'asc')->get()->toArray();
                    
                // echo '<pre>';
                // print_r($timetables);
                // echo '</pre>';

                // return;

                $timeTableData   = range(1, $sessionCount);
            //    print_r(json_encode($timeTableData));exit;



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

                //print_r(json_encode($timeTableData));exit;

             

                    $ti = 1;
                    foreach ($timeTableData as $sessionData) {
                        if( is_array($sessionData) ) {
                            $tt_id              = $sessionData["id"];
                            $tt_activity_id    = $sessionData["activity_id"];
                            $tt_subactivity_id    = $sessionData["subactivity_id"];

                            $tt_date    = $sessionData["date"];
                            $session_no = $sessionData["session_number"];
                            if(empty($session_no)) {
                                $session_no = $ti;
                            }

                            $tt_start    = $sessionData["session_start"];
                            $tt_end      = $sessionData["session_end"];

                            $session_time_start    = "";
                            $session_time_end      = "";
                            if( !empty($tt_start) && !empty($tt_end) ) {
                                $session_time_start  = date('H:i', $tt_start);
                                $session_time_end    = date('H:i', $tt_end);
                            }

                            $activityOptions    = "";
                            if (!empty($activities)) {
                                foreach ($activities as $activity) {
                                    $activity_id = $activity->id;
                                    $activity_name = $activity->name;

                                    $selected   = ($tt_activity_id === $activity_id)? "selected" : "";
                                    $activityOptions .= "<option value=\"{$activity_id}\" {$selected}>{$activity_name}</option>";
                                }
                            }

                            $activitySelect = "
                                <select name=\"activity_id[{$tt_date}][{$ti}]\" class=\"form-control form-control-sm\">
                                    <option value=\"\">Select activity...</option>
                                    {$activityOptions}
                                </select>
                            ";
                            ?>

            <td data-sequence-id="{{ $ti }}" data-timetable-id="{{ $tt_id }}"
                data-timetable-date="{{ $tt_date }}">
                ////kkkk
                {{-- //$hoilday_check = App\Models\Hoilday::where('batch_id', $batch_id)
                // ->where('squad_id', $squad_id)
                // ->whereDate('date', $date)
                // ->first();
                //$check = $hoilday_check == '' ? '' : 'checked';
                //$row_class = $hoilday_check == '' ? 'timetable-weekdays' : 'timetable-weekend'; --}}
                {{-- <input class='timetable-weekend2' type='checkbox' id='mark_hoilday' name='mark_hoilday[{$date}]'> --}}
                {{-- <th>{$dayName}<br /><small>{$date}</small></th>"; --}}
                <input type='checkbox' class='timetable-weekend2' />
                <div class="form-group mb-0">
                    <div>
                        <div class="timetable-activity"><?php echo $activitySelect; ?></div>
                        <div class="timetable-subactivity"></div>
                    </div>

                    <div class="session-timerange-row">
                        <div class="session-timerange-col">
                            <input type="text" name="session_time_start[{{ $tt_date }}][{{ $ti }}]"
                                value="{{ $session_time_start }}" placeholder="HH:MM" data-valid-example="08:30"
                                class="form-control form-control-sm jquery-timeinput-mask mt-2" />
                        </div>
                        <div class="session-timerange-col">-</div>
                        <div class="session-timerange-col">
                            <input type="text" name="session_time_end[{{ $tt_date }}][{{ $ti }}]"
                                value="{{ $session_time_end }}" placeholder="HH:MM" data-valid-example="08:30"
                                class="form-control form-control-sm jquery-timeinput-mask mt-2" />
                        </div>
                    </div>

                    <input type="hidden" name="activity_date[{{ $tt_date }}][{{ $ti }}]"
                        value="{{ $tt_date }}" class="hidden" />
                    <input type="hidden" name="session_number[{{ $tt_date }}][{{ $ti }}]"
                        value="{{ $session_no }}" class="hidden" />
                </div>
            </td>
            <?php

                        } else {
                            $activityOptions    = "";
                            if (!empty($activities)) {
                                foreach ($activities as $activity) {
                                    $activity_id = $activity->id;
                                    $activity_name = $activity->name;

                                    $activityOptions .= "<option value=\"{$activity_id}\">{$activity_name}</option>";
                                }
                            }

                            $activitySelect = "
                                <select name=\"activity_id[{$date}][{$ti}]\" class=\"form-control form-control-sm\">
                                    <option value=\"\">Select activity...</option>
                                    {$activityOptions}
                                </select>
                            ";

                            $session_no = $ti;

                                $timetables = App\Models\Timetable::where('batch_id', $batch_id)->where('session_number', $ti)
                                ->where('session_type', 'regular')
                                ->orderBy('id', 'desc')->first();

                                $session_time_start  = date('H:i', isset($timetables->session_start) ?  $timetables->session_start : 0);
                                $session_time_end    = date('H:i', isset($timetables->session_end) ? $timetables->session_end : 0);

                            ?>
            <td data-sequence-id="{{ $ti }}" data-timetable-id="" data-timetable-date="{{ $date }}">
                <div class="form-group mb-0">
                    <div>
                        <div class="timetable-activity"><?php echo $activitySelect; ?></div>
                        <div class="timetable-subactivity"></div>
                    </div>

                    <div class="session-timerange-row">
                        <div class="session-timerange-col">
                            <input type="text" name="session_time_start[{{ $date }}][{{ $ti }}]"
                                value="{{ $session_time_start }}" placeholder="HH:MM" data-valid-example="08:30"
                                class="form-control-sm form-control jquery-timeinput-mask mt-2" />
                        </div>
                        <div class="session-timerange-col">-</div>
                        <div class="session-timerange-col">
                            <input type="text" name="session_time_end[{{ $date }}][{{ $ti }}]"
                                value="{{ $session_time_end }}" placeholder="HH:MM" data-valid-example="08:30"
                                class="form-control form-control-sm jquery-timeinput-mask mt-2" />
                        </div>
                    </div>

                    <input type="hidden" name="activity_date[{{ $date }}][{{ $ti }}]"
                        value="{{ $date }}" class="hidden" />
                    <input type="hidden" name="session_number[{{ $date }}][{{ $ti }}]"
                        value="{{ $session_no }}" class="hidden" />
                </div>
            </td>
            <?php
                        }

                        $ti++;
                    }
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>

    <div class="row mt-4">
        <div class="col-md-12">
            {{-- nnn --}}
            <div class="form-group">
                <label for="sel1">To Copy Same Timetable for below Squad:</label>
                <select name="squad_2[]" id="squad_id" class="form-control" multiple='multiple'>
                    @php
                        $distinctValues = DB::table('squads')
                            ->select('id', 'SquadNumber')
                            ->where('Batch_Id', '=', $batch_id)
                            ->get();
                    @endphp
                    <option value="">Select...</option>
                    @foreach ($distinctValues as $d)
                        <option value={{ $d->id }}>{{ $d->SquadNumber }}</option>
                    @endforeach
                </select>

                {{-- <input name="squad_2" class="form-control"> --}}
            </div>
            <div class="usersubmitBtns">
                <div class="mr-4">

                    <button type="button" onclick="window.timetableUpdate_Submit();"
                        class="btn formBtn submitBtn">Submit</button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    $('tr input[type="checkbox"]').change(function() {
        //enable/disable all except checkboxes, based on the row is checked or not
        var row = $(this).closest('tr').find('input:text, select').attr('disabled', this.checked);
        $(this).closest('tr').find('select').val('');

        var ischecked = $(this).is(':checked');

        if (ischecked) {
            $(this).closest('tr').removeClass('timetable-weekdays');
            $(this).closest('tr').addClass('timetable-weekend');
            $(this).closest('tr').find('input:text, select').removeClass('reqField');
        } else {
            $(this).closest('tr').addClass('timetable-weekdays');
            $(this).closest('tr').removeClass('timetable-weekend');
        }
    });

    $('td input[type="checkbox"]').each(function() {
        var ischecked = $(this).is(':checked');
        if (ischecked) {
            $(this).closest('tr').find('input:text, select').attr('disabled', this.checked);
        }
    });
</script>
//
<script>
    $(document).ready(function() {
        $('input[type="checkbox"].timetable-weekend2').each(function() {
            var isChecked = $(this).is(':checked');
            var td = $(this).closest('td');

            if (isChecked) {
                td.addClass('timetable-weekend2');
            } else {
                td.removeClass('timetable-weekend2');
            }
        });
    });

    $('input[type="checkbox"].timetable-weekend2').change(function() {
        var isChecked = $(this).is(':checked');
        var td = $(this).closest('td');

        if (isChecked) {
            td.addClass('timetable-weekend2');
        } else {
            td.removeClass('timetable-weekend2');
        }
    });
</script>
{{-- <script>
    $('td input[type="checkbox"]').change(function() {
        //enable/disable all except checkboxes, based on the row is checked or not
        var row = $(this).closest('td').find('input:text, select').attr('disabled', this.checked);
        $(this).closest('td').find('select').val('');

        var ischecked = $(this).is(':checked');

        if (ischecked) {
            $(this).closest('td').removeClass('timetable-weekdays');
            $(this).closest('td').addClass('timetable-weekend2');
            $(this).closest('td').find('input:text, select').removeClass('reqField');
        } else {
            $(this).closest('td').addClass('timetable-weekdays');
            $(this).closest('td').removeClass('timetable-weekend2');
        }
    });

    $('td input[type="checkbox"]').each(function() {
        var ischecked = $(this).is(':checked');
        if (ischecked) {
            $(this).closest('tr').find('input:text, select').attr('disabled', this.checked);
        }
    });
</script>
{{-- <script>
    // Handle 'tr input[type="checkbox"]'
    $('tr input[type="checkbox"]').change(function() {
        var isChecked = $(this).is(':checked');
        var row = $(this).closest('tr');

        if (isChecked) {
            row.removeClass('timetable-weekdays');
            row.addClass('timetable-weekend2');
            row.find('input:text, select').removeClass('reqField');
        } else {
            row.addClass('timetable-weekdays');
            row.removeClass('timetable-weekend2');
        }

        var inputElements = row.find('input:text, select');
        inputElements.attr('disabled', isChecked);
        inputElements.val('');
    });

    // Handle 'td input[type="checkbox"]'
    $('td input[type="checkbox"]').change(function() {
        var isChecked = $(this).is(':checked');
        var td = $(this).closest('td');

        if (isChecked) {
            td.removeClass('timetable-weekdays');
            td.addClass('timetable-weekend2');
            td.find('input:text, select').removeClass('reqField');
        } else {
            td.addClass('timetable-weekdays');
            td.removeClass('timetable-weekend2');
        }

        var inputElements = td.find('input:text, select');
        inputElements.attr('disabled', isChecked);
        inputElements.val('');
    });
</script> --}}
