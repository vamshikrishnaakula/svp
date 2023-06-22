{{-- Extends layout --}}
@extends('layouts.default')

{{-- Content --}}
@section('content')

<section id="monthlysessions" class="content-wrapper_sub tab-content attendance_report">
    <div class="user_manage">
        <div class="row">
            <div class="col-md-8">
                <h4>Monthly Classes Report</h4>
            </div>
            <div class="col-md-4">
                <div class="useractionBtns" style="max-width:120px; margin-left:auto;">
                    <a href="#" data-toggle="tooltip" title="download" onclick="window.get_monthlySessions_Download(); return false;"> <img src="{{ asset('images/download1.png') }}" /></a>
                    {{--  <a href="javacript:void(0)" id="print_report_monthly_btn" data-toggle="tooltip" title="print"> <img src="{{ asset('images/print1.png') }}" /></a>  --}}
                </div>
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
                                <label>Date (From):</label>
                                <div class="form-group">
                                    <input type="text" name="from_date" id="from_date" class="form-control datePicker reqField" autocomplete="off" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Date (To):</label>
                                <div class="form-group">
                                    <input type="text" name="to_date" id="to_date" class="form-control datePicker" autocomplete="off" />
                                </div>
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

                <div id="get_monthlySessions_status" class="mt-5"></div>
                <div id="monthlySessions-container" class="table-responsive mt-5"></div>
            </div>

        </div>
    </div>


</section>

@endsection
