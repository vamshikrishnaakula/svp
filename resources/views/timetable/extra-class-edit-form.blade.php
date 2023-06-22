<?php
$sessionId   = $ExtraClass->id;
$batch_id   = $ExtraClass->batch_id;
$batch      = batch_name($batch_id);

$activity_id     = $ExtraClass->activity_id;
$activity        = activity_name($activity_id);

$subActivity_id  = $ExtraClass->subactivity_id;
$subActivity     = "";
if (!empty($subActivity_id)) {
    $subActivity     = activity_name($subActivity_id);
}

$di_id      = $ExtraClass->drillinspector_id;
// $di_name    = user_name($di_id);

$date     = $ExtraClass->date;
$session_start  = $ExtraClass->session_start;
$session_start  = date('H:i', $session_start);

$session_end    = $ExtraClass->session_end;
$session_end    = date('H:i', $session_end);
?>
<h5 class="text-center mb-5">Edit Class</h5>

<form action="" id="editExtraClassForm" data-class-id="{{ $sessionId }}">
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
            <td><input type="text" name="session_date" value="{{ $date }}" class="form-control datePicker reqField" autocomplete="off" /></td>
        </tr>
        <tr>
            <td>Time</td>
            <td>
                <input type="text" name="session_time" value="{{ $session_start .' - '. $session_end }}" placeholder="HH:MM - HH:MM" data-valid-example="08:30 - 09:30" class="form-control jquery-timerange-mask reqField" />
            </td>
        </tr>
        <tr>
            <td>Drill Inspector</td>
            <td>
                <select name="new_di_id" id="new_di_id" class="form-control reqField">
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

    <div id="editExtraClass_status" class="my-2"></div>

</form>
