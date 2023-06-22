<?php
$sessionId   = $ExtraSession->id;
$batch_id   = $ExtraSession->batch_id;
$batch      = batch_name($batch_id);

$activity_id     = $ExtraSession->activity_id;
$activity        = activity_name($activity_id);

$subActivity_id  = $ExtraSession->subactivity_id;
$subActivity     = "";
if (!empty($subActivity_id)) {
    $subActivity     = activity_name($subActivity_id);
}

$di_id      = $ExtraSession->drillinspector_id;
// $di_name    = user_name($di_id);

$date     = $ExtraSession->date;
$session_start  = $ExtraSession->session_start;
$session_start  = date('H:i', $session_start);

$session_end    = $ExtraSession->session_end;
$session_end    = date('H:i', $session_end);


$check_session_status = App\Models\ExtraSessionmeta::where('extra_session_id', $sessionId)->whereNotNull('timetable_id')->count();
if($check_session_status === 0)
{
    $disabled = "";
}
else
{
    $disabled = "disabled";
}

?>
<h5 class="text-center mb-5">Edit Session</h5>
<form action="" id="editExtraSessionForm" data-session-id="{{ $sessionId }}">
    <table class="table table-borderless">
        <tr>
            <td style="width: 130px;">Batch</td>
            <td>: {{ $batch }}</td>
        </tr>
        <tr>
            <td>Activity</td>
            <td>: {{ $activity }}</td>
        </tr>
        @if(!empty($subActivity))
        <tr>
            <td>Sub Activity</td>
            <td>: {{ $subActivity }}</td>
        </tr>
        @endif

        <tr>
            <td>Date</td>
            @if (empty($disabled))
            <td><input type="text" name="session_date" value="{{ $date }}" class="form-control datePicker reqField" autocomplete="off" /></td>
            @else
            <td><input type="text" name="session_date" value="{{ $date }}" class="form-control" autocomplete="off" /></td>
            @endif

        </tr>
        <tr>
            <td>Time</td>
            <td>
                <input type="text" name="session_time" value="{{ $session_start .' - '. $session_end }}" placeholder="HH:MM - HH:MM" data-valid-example="08:30 - 09:30" class="form-control jquery-timerange-mask reqField" {{ $disabled }} />
            </td>
        </tr>
        <tr>
            <td>Drill Inspector</td>
            <td>
                <select name="new_di_id" id="new_di_id" class="form-control reqField" {{ $disabled }}>
                    <option value="">Select Drill Inspector...</option>
                    @if( !empty($get_DI) )
                        @foreach($get_DI as $DI)
                            <option value="{{ $DI->id }}" {{ ($DI->id === $di_id) ? 'selected' : '' }}>{{ $DI->name }}</option>
                        @endforeach
                    @endif
                </select>
            </td>
        </tr>
    </table>
    @if (empty($disabled))
    <div id="editExtraSession_status" class="my-2"></div>
    @else
    <div id="editExtraSession_status" class="my-2 text-success">Session completed</div>
    @endif

</form>
