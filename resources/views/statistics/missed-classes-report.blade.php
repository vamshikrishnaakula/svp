{{-- Extends layout --}}
{{-- @extends('layouts.default') --}}
<?php
if($role === 'faculty') {
    $template   = 'layouts.faculty.template';
} else {
    $template   = 'layouts.default';
}
$app_view = session('app_view');
?>

@extends(($app_view) ? 'layouts.pbdash.mobile-template' : $template)

{{-- Content --}}
@section('content')

<section id="grades" class="content-wrapper_sub">
    <div class="user_manage">
        <div class="row mb-3">
            <div class="col-md-9">
                <h4>Missed Classes Report</h4>
            </div>
            <div class="col-md-3">
                <div class="userBtns" style="justify-content: flex-end;">
                    {{--  <a href="#" data-toggle="tooltip" title="import"> <img src="{{ asset('images/download1.png') }}" /></a>
                    <a href="#" data-toggle="tooltip" title="excel"> <img style="width:29px !important" src="{{ asset('images/print1.png') }}" /></a>  --}}
                </div>
            </div>
        </div>

        <form id="mised_class_report_form" autocomplete="off">

            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="for-required">Report Type</label>
                        <select name="report_type" id="report_type" class="form-control" required>
                            <option value="">Select report type...</option>
                            <option value="session_report" selected>Session Report</option>
                            <option value="attendance_report">Attendance Report</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="for-required">Batch</label>
                        <select class="form-control" id="batch_id" name="batch_id" onchange="window.select_batchId_changed(this, 'squad_id');" required>
                            <option value="">Select Batch</option>
                            @if( !empty($batches) )
                            @foreach($batches as $batch)
                            <option value="{{ $batch->id }}" {{$batch->id == Session::get('current_batch')  ? 'selected' : ''}}>{{ $batch->BatchName }}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="col-md-3 activity_id_col">
                    <div class="form-group">
                        <label>Activity</label>
                        <select name="activity_id" id="activity_id" onchange="window.get_subactivity_dropdowns(this);" class="form-control activity_id">
                            <option value="">Select...</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3 subactivity_id_col">
                    <div class="form-group">
                        <label>Sub Activity</label>
                        <select name="subactivity_id" id="subactivity_id" data-has-subactivity="yes" class="form-control subactivity_id">
                            <option value="">Select Sub Activity...</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="for-required">Date Range</label>
                        <input type="text" name="report_daterange" id="report_daterange" class="form-control" size="24" required>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end report_submit_btn">
                <a class="pt-1" onclick="window.download_missedClass_report();"><img src="{{ asset('images/download1.png') }}" width="25" /></a>
            </div>

            <div id="mised_class_report_status" class="mt-3"></div>
        </form>



    </div>
</section>

@endsection
@section('scripts')
<script>
    $(document).ready(function () {

        // Date range picker for Date Range input
        new DateRangePicker('report_daterange',
            {
                opens: 'right',
                autoApply: true,

                ranges :  {
                    'Today': [moment().startOf('day'), moment().endOf('day')],
                    'This Month': [moment().startOf('month').startOf('day'), moment().endOf('month').endOf('day')],
                    'last Month':  [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    'last 6 Months':  [moment().subtract(6, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    'This Year': [moment().startOf('year').startOf('day'), moment().endOf('day').endOf('day')],
                },
                locale: {
                    format: "DD/MM/YYYY",
                }
            },
            function (start, end) {
               // alert(start.format() + " - " + end.format());
        });
});

</script>
@endsection
