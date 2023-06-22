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
                <h4>Pass/Fail Report</h4>
            </div>
            <div class="col-md-3">
                <div class="userBtns" style="justify-content: flex-end;">
                    {{--  <a href="#" data-toggle="tooltip" title="import"> <img src="{{ asset('images/download1.png') }}" /></a>
                    <a href="#" data-toggle="tooltip" title="excel"> <img style="width:29px !important" src="{{ asset('images/print1.png') }}" /></a>  --}}
                </div>
            </div>
        </div>

        <form id="pass_fail_report_form" autocomplete="off">

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
                        <label>Squad</label>
                        <select name="squad_id" id="squad_id" class="form-control squad_id" required>
                            <option value="">Select Squad</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="for-required">Date Range</label>
                        <input type="text" name="report_daterange" id="report_daterange" class="form-control" size="24" required>
                    </div>
                </div>
                <div class="col-md-3 d-flex align-items-center justify-content-end">
                    <a class="pt-1" onclick="window.download_pass_fail_report();"><img src="{{ asset('images/download1.png') }}" width="25" /></a>
                </div>
            </div>
          

            <div id="pass_fail_report_status" class="mt-3"></div>
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
