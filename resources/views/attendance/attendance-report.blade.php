{{-- Extends layout --}}
@extends('layouts.default')

{{-- Content --}}
@section('content')

<section id="monthlyreport" class="content-wrapper_sub tab-content attendance_report">
    <div class="user_manage">
        <div class="row">
            <div class="col-md-9">
                <h4>Attendance Report</h4>
            </div>
            <div class="col-md-3">
                <div class="userBtns" style="max-width:120px; margin-left:auto;">
                    {{-- <a href="#" data-toggle="tooltip" title="import"> <img src="{{ asset('images/import.png') }}" /></a> --}}
                    {{-- <a href="#" data-toggle="tooltip" title="Download Sample CSV for Bulk Import"> <img src="{{ asset('images/excel.png') }}" /></a> --}}
                    <a href="#" data-toggle="tooltip" title="download" onclick="window.get_monthlyReport_Download(); return false;"> <img src="{{ asset('images/download1.png') }}" /></a>
                    {{-- <a href="#" data-toggle="tooltip" title="print"> <img src="{{ asset('images/print1.png') }}" /></a> --}}
                </div>
            </div>
        </div>
        <form name="monthlyReport_form" id="monthlyReport_form" class="width-two-third rl-margin-auto" accept-charset="utf-8">
            @csrf

            @php
            $batches = DB::table('batches')->get();
            @endphp

            <div class="row mt-5">
                <div class="col">
                    <div class="form-group">
                        <label>Select Batch:</label>
                        <select name="batch_id" id="batch_id" onchange="window.select_batchId_changed(this, 'squad_id');" class="form-control reqField">
                            <option value="">Select batch...</option>
                            @if( !empty($batches) )
                            @foreach($batches as $batch)
                            <option value="{{ $batch->id }}" @if($batch->id == Session::get('current_batch')) selected @endif>{{ $batch->BatchName }}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="sel1">Select Squad:</label>
                        <select name="squad_id" id="squad_id" class="form-control reqField">
                            <option value="">Select...</option>
                        </select>
                    </div>
                </div>
                {{-- <div class="col">
                    <div class="form-group">
                        <label>Select Year:</label>
                        <select name="year" id="year" class="form-control reqField">
                            <option value="">Select year...</option>
                            @php
                            $cYear  = date('Y');

                            for ($y=2020; $y<=$cYear; $y++) {
                                $ySelected  = ($y == $cYear)? "selected" : "";

                                echo "<option value=\"{$y}\" {$ySelected}>{$y}</option>";
                            }
                            @endphp
                        </select>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label>Select Month:</label>
                        <select name="month" id="month" class="form-control reqField">
                            <option value="">Select month...</option>
                            @php
                            $cMonth  = date('m');

                            for ($m=1; $m<=12; $m++) {
                                $month = date('F', mktime(0,0,0,$m, 1, date('Y')));
                                $mSelected  = ($m == $cMonth)? "selected" : "";

                                echo "<option value=\"{$m}\" {$mSelected}>{$month}</option>";
                            }
                            @endphp
                        </select>
                    </div>
                </div> --}}
                <div class="col">
                    <div class="form-group">
                        <label>Date:</label>
                        <input type="text" name="report_datetimerange" id="report_datetimerange" size="24" class="form-control reqField" required>
                    </div>
                </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="usersubmitBtns mt-4">
                        <div class="mr-4">
                            <button type="button" onclick="window.get_monthlyReport_Submit();"
                                class="btn formBtn submitBtn">Proceed</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div id="monthlyReport_form_status" class="mt-5"></div>
        <div id="monthlyReport-container" class="mt-5"></div>
    </div>

</section>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {

        new DateRangePicker('report_datetimerange',
            {
                opens: 'right',
                autoApply: true,
                // showDropdowns: true,
                //showWeekNumbers: true,
                maxSpan: { "days": 31 },
                // maxDate: moment().endOf('day'),
                ranges :  {
                    'Today': [moment().startOf('day'), moment().endOf('day')],
                    'This Month': [moment().startOf('month').startOf('day'), moment().endOf('month')],
                    'last Month':  [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    // 'last 6 Months':  [moment().subtract(6, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    // 'This Year': [moment().startOf('year').startOf('day'), moment().endOf('day').endOf('day')],
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
