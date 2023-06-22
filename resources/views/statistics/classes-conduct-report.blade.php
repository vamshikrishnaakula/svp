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
<div id="error"></div>
@if ($message = Session::get('success'))
<div class="alert alert-success">
    <p>{{ $message }}</p>
</div>
@elseif ($message = Session::get('delete'))
<div class="alert alert-danger">
    <p>{{ $message }}</p>
</div>

@endif
<section id="grades" class="content-wrapper_sub">
    <div class="user_manage">
        <div class="row">
            <div class="col-md-9">
                <h4>Classes Conduct Report</h4>
            </div>
            <div class="col-md-3">
                <div class="userBtns" style="justify-content: flex-end;">
                    {{--  <a href="#" data-toggle="tooltip" title="import"> <img src="{{ asset('images/download1.png') }}" /></a>
                    <a href="#" data-toggle="tooltip" title="excel"> <img style="width:29px !important" src="{{ asset('images/print1.png') }}" /></a>  --}}
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col">
                <form id="reports" name="reports" autocomplete="off">
                    <div class="row">
                        <div class="col-md-3 ct-col-6">
                            <div class="form-group">
                                <label>Batch</label>
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
                        <div class="col-md-3 ct-col-6">
                            <div class="form-group">
                                <label>Squad</label>
                                <select name="squad_id" id="squad_id" class="form-control" required>
                                    <option value="">Select Squad</option>
                                </select>
                            </div>
                        </div>
                        <div class="dateFieldWidth col-md-3 ct-col-6">
                            <label>Date</label>
                                <input type="text" class="form-control" id="datetimerange-input" name="date" size="24" required>
                            </div>

                            <div class="col-md-3 d-flex align-items-center justify-content-end">
                                {{--  <button class="pt-1" type="button" onclick = "window.view_session_count();" id="check_button"><img src="{{ asset('images/submit.png') }}" width="30" /></button>  --}}
                                <a class="pt-1" onclick="window.export_session_count();"><img src="{{ asset('images/download1.png') }}" width="25" /></a>
                            </div>
                        </div>
                       
                    </div>
                </form>

            </div>
        </div>


        <div id="download_classes_conduct_status" class="mt-3"></div>
    </div>

</section>

@endsection
@section('scripts')
<script>
    $(document).ready(function () {

        new DateRangePicker('datetimerange-input',
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
            })
});




        /** -------------------------------------------------------------------
     * Export squad wise sessions count Details
     * ----------------------------------------------------------------- */
     if (!window.export_session_count) {
        window.export_session_count = function () {

            var batch_id = $('#batch_id').val();
            var squad_id = $('#squad_id').val();
            var date = $('#datetimerange-input').val();
            var statusDiv = $("#download_classes_conduct_status");
            $.ajax({
                url: appUrl +'/reports/ajax',
                data: {
                    requestName: "download_session_count",
                    batch_id:batch_id,
                    squad_id:squad_id,
                    date:date
                },
                type: "POST",
                beforeSend: function (xhr) {
                    window.loadingScreen("show");
                },
                success: function (rData) {
                    window.loadingScreen("hide");
                    console.log(rData);
                    let rObj = JSON.parse(rData);
                    if (rObj.status == "success") {
                        let newTab = window.open(rObj.datasheet_url);
                    } else {
                       statusDiv.html('<div class="msg msg-danger msg-full">'+rObj.message+'</div>');
                    }

                }
            });
        }
    };


        /** -------------------------------------------------------------------
     * View Squad wise Sessions count
     * ----------------------------------------------------------------- */
     if (!window.view_session_count) {
        window.view_session_count = function () {

            var batch_id = $('#batch_id').val();
            var squad_id = $('#squad_id').val();
            var date = $('#datetimerange-input').val();
            $.ajax({
                url: appUrl +'/reports/ajax',
                data: {
                    requestName: "get_classes_conduct_report",
                    batch_id:batch_id,
                    squad_id:squad_id,
                    date:date
                },
                type: "POST",
                beforeSend: function (xhr) {
                   // window.loadingScreen("show");
                },
                success: function (rData) {
                    window.loadingScreen("hide");
                    console.log(rData);
                    let rObj = JSON.parse(rData);
                    {{--  if (rObj.status == "success") {
                        let newTab = window.open(rObj.datasheet_url);
                    } else {
                       statusDiv.html('<div class="msg msg-danger msg-full">'+rObj.message+'</div>');
                    }  --}}

                }
            });
        }
    };
</script>
@endsection
