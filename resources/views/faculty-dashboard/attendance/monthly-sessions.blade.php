{{-- Extends layout --}}
@extends('layouts.faculty.template')

{{-- Content --}}
@section('content')

<section id="monthlysessions" class="content-wrapper_sub tab-content attendance_report">
    <div class="user_manage">
        <div class="row">
            <div class="col-md-6">
                <h4>Monthly Sessions Report</h4>
            </div>
        </div>

        <div class="mt-5">
            <div id="report_monthly">
                <form name="monthlySessions_form" id="monthlySessions_form" action="{{ url('attendance/ajax') }}"
                    method="post" class="width-two-third rl-margin-auto" accept-charset="utf-8">
                    @csrf

                    @php
                    $batches = DB::table('batches')->get();
                    @endphp
                    <div class="row mt-5">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Select Batch:</label>
                                    <select name="batch_id" id="batch_id" onchange="window.select_batchId_changed(this, 'squad_id');" class="form-control">
                                        <option value="">Select batch...</option>
                                        @if( !empty($batches) )
                                        @foreach($batches as $batch)
                                        <option value="{{ $batch->id }}" @if($batch->id == Session::get('current_batch')) selected @endif>{{ $batch->BatchName }}</option>
                                        @endforeach
                                        @endif
                                    </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sel1">Select Squad:</label>
                                    <select name="squad_id" id="squad_id" class="form-control">
                                        <option value="">Select...</option>
                                    </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Select Year:</label>
                                <select name="year" id="year" class="form-control">
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
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Select Month:</label>
                                <select name="month" id="month" class="form-control">
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
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="usersubmitBtns mt-4">
                                <div class="mr-4">
                                    <button type="button" onclick="window.get_monthlySessions_submit();"
                                            class="btn formBtn submitBtn">Proceed</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <div id="monthlySessions_form_status" class="mt-5"></div>
                <div id="monthlySessions-container" class="table-responsive mt-5"></div>
            </div>

        </div>
    </div>


</section>

@endsection
