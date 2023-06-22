{{-- Extends layout --}}
@extends('layouts.default')

{{-- Content --}}
@section('content')

<section id="missedsessions" class="content-wrapper_sub tab-content attendance_report">
    <div class="user_manage">
        <div class="row">
            <div class="col-md-8">
                <h4>Missed Classes Report</h4>
            </div>
            <div class="col-md-4">
                <div class="useractionBtns" style="max-width:120px; margin-left:auto;">
                    <a href="#" data-toggle="tooltip" title="download" onclick="window.get_missedSessions_Download(); return false;"> <img src="{{ asset('images/download1.png') }}" /></a>
                    <a href="#" onclick="window.print();" data-toggle="tooltip" title="print"> <img src="{{ asset('images/print1.png') }}" /></a>
                </div>
            </div>
        </div>
        <div id="report_weekly">
            <form name="get_missedSessions_form" id="get_missedSessions_form" action="{{ url('attendance/ajax') }}"
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
                            <input type="text" name="from_date" id="from_date" class="form-control datePicker reqField" autocomplete="off" />

                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Date (To):</label>
                            <input type="text" name="to_date" id="to_date" class="form-control datePicker" autocomplete="off" />

                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="usersubmitBtns mt-4">
                            <div class="mr-4">
                                <button type="button" onclick="window.get_missedSessions_submit();"
                                        class="btn formBtn submitBtn">Proceed</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <div id="get_missedSessions_status" class="mt-5"></div>
            <div id="missedSessions-container" class="table-responsive mt-5"></div>
        </div>
    </div>
</section>

@endsection
