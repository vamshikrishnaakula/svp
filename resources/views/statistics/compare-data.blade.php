@php
    // print_r($request->all());
    $probationer_ids    = $request->probationer_ids;
    $activity_ids       = $request->activity_ids;

    $pb_count   = count($probationer_ids);
@endphp


@foreach ($activity_ids as $activity_id)
    <table class="data_result_table table-borderless">
        <tr class="blank-row">
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        @php
            // Get activity data
            $activity   = App\Models\Activity::withTrashed()->find($activity_id);
            $subactivities   = App\Models\Activity::where('parent_id', $activity_id)->get();
        @endphp
        <tr class="activity-name-heading">
            <td colspan="5">Activity: {{ $activity->name }}</td>
        </tr>

        @if (count($subactivities) > 0)
            @foreach ($subactivities as $subactivity)
                <tr class="subactivity-name-heading">
                    <td>Sub Activity: {{ $subactivity->name }}</td>
                    @for ($i=0; $i<$pb_count; $i++)
                        <td>
                            <div class="d-flex justify-content-around w-100">
                                <div>GRADE</div>
                                <div>SCORE</div>
                            </div>
                        </td>
                    @endfor
                    @for ($i=$pb_count; $i<4; $i++)
                        <td></td>
                    @endfor
                </tr>

                @php
                $components   = App\Models\Activity::where('parent_id', $subactivity->id)->get();
                @endphp

                @if (count($components) > 0)
                    @foreach ($components as $component)
                        <tr class="pb-attendance-data-row">
                            <td class="br-1">{{ $component->name }}</td>
                            @foreach ($probationer_ids as $probationer_id)
                                <td class="br-1">
                                    @php
                                        // Get probationer data
                                        $attnDataQ = App\Models\ProbationersDailyactivityData::where('component_id', $component->id)
                                            ->where('probationer_id', $probationer_id)
                                            ->where('attendance', '!=', '')
                                            ->whereNotNull('attendance')
                                            ->select('grade', 'count')
                                            ->get();

                                        $attnDataCount  = count($attnDataQ);

                                        if($attnDataCount > 0) {
                                            $grades = 0;
                                            $counts = 0;
                                            foreach ($attnDataQ as $attnData) {
                                                $grades +=  grade_to_num($attnData->grade);
                                                $counts +=  intval($attnData->count);
                                            }

                                            $gradeAvg = round($grades / $attnDataCount, 1);
                                            $countAvg = round($counts / $attnDataCount, 1);
                                        } else {
                                            $gradeAvg = 0;
                                            $countAvg = 0;
                                        }

                                        $unit   = $component->unit;
                                        $score  = ($countAvg > 0) ? $countAvg : '-';
                                        $score  .= ($countAvg > 0) ? ' '.$unit : '';
                                    @endphp

                                    <div class="d-flex justify-content-around w-100">
                                        <div>{{ ($gradeAvg > 0) ? num_to_grade($gradeAvg) : "-" }}</div>
                                        <div>{{ $score }}</div>
                                    </div>
                                </td>
                            @endforeach
                            @for ($i=$pb_count; $i<4; $i++)
                                <td></td>
                            @endfor
                        </tr>
                    @endforeach
                @else
                    <tr class="pb-attendance-data-row">
                        <td class="br-1">{{ $subactivity->name }}</td>
                        @foreach ($probationer_ids as $probationer_id)
                            <td class="br-1">
                                @php
                                    // Get probationer data
                                    $attnDataQ = App\Models\ProbationersDailyactivityData::where('subactivity_id', $subactivity->id)
                                        ->where('probationer_id', $probationer_id)
                                        ->where('attendance', '!=', '')
                                        ->whereNotNull('attendance')
                                        ->select('grade', 'count')
                                        ->get();

                                    $attnDataCount  = count($attnDataQ);

                                    if($attnDataCount > 0) {
                                        $grades = 0;
                                        $counts = 0;
                                        foreach ($attnDataQ as $attnData) {
                                            $grades +=  grade_to_num($attnData->grade);
                                            $counts +=  intval($attnData->count);
                                        }

                                        $gradeAvg = round($grades / $attnDataCount, 1);
                                        $countAvg = round($counts / $attnDataCount, 1);
                                    } else {
                                        $gradeAvg = 0;
                                        $countAvg = 0;
                                    }

                                    $unit   = $subactivity->unit;
                                    $score  = ($countAvg > 0) ? $countAvg : '-';
                                    $score  .= ($countAvg > 0) ? ' '.$unit : '';
                                @endphp

                                <div class="d-flex justify-content-around w-100">
                                    <div>{{ ($gradeAvg > 0) ? num_to_grade($gradeAvg) : "-" }}</div>
                                    <div>{{ $score }}</div>
                                </div>
                            </td>
                        @endforeach
                        @for ($i=$pb_count; $i<4; $i++)
                            <td></td>
                        @endfor
                    </tr>
                @endif
            @endforeach
        @else
            <tr class="subactivity-name-heading">
                <td class="br-1"></td>
                @for ($i=0; $i<$pb_count; $i++)
                    <td>
                        <div class="d-flex justify-content-around w-100">
                            <div>GRADE</div>
                            <div>SCORE</div>
                        </div>
                    </td>
                @endfor
                @for ($i=$pb_count; $i<4; $i++)
                    <td></td>
                @endfor
            </tr>
            <tr class="pb-attendance-data-row">
                <td class="br-1">{{ $activity->name }}</td>
                @foreach ($probationer_ids as $probationer_id)
                    <td class="br-1">
                        @php
                            // Get probationer data
                            $attnDataQ = App\Models\ProbationersDailyactivityData::where('activity_id', $activity->id)
                                ->where('probationer_id', $probationer_id)
                                ->where('attendance', '!=', '')
                                ->whereNotNull('attendance')
                                ->select('grade', 'count')
                                ->get();

                            $attnDataCount  = count($attnDataQ);

                            if($attnDataCount > 0) {
                                $grades = 0;
                                $counts = 0;
                                foreach ($attnDataQ as $attnData) {
                                    $grades +=  grade_to_num($attnData->grade);
                                    $counts +=  intval($attnData->count);
                                }

                                $gradeAvg = round($grades / $attnDataCount, 1);
                                $countAvg = round($counts / $attnDataCount, 1);
                            } else {
                                $gradeAvg = 0;
                                $countAvg = 0;
                            }

                            $unit   = $activity->unit;
                            $score  = ($countAvg > 0) ? $countAvg : '-';
                            $score  .= ($countAvg > 0) ? ' '.$unit : '';
                        @endphp

                        <div class="d-flex justify-content-around w-100">
                            <div>{{ ($gradeAvg > 0) ? num_to_grade($gradeAvg) : "-" }}</div>
                            <div>{{ $score }}</div>
                        </div>
                    </td>
                @endforeach
                @for ($i=$pb_count; $i<4; $i++)
                    <td></td>
                @endfor
            </tr>
        @endif
    </table>
@endforeach


