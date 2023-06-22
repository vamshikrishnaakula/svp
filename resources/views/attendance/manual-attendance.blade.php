{{-- Extends layout --}}
@extends('layouts.default')

{{-- Content --}}
@section('content')

<section id="manual_attendance" class="content-wrapper_sub tab-content attendance_report">
    <div class="user_manage">
        <div class="row">
            <div class="col-md-6">
                <h4>Manual Attendance</h4>
            </div>
            <div class="col-md-6">
                <div class="userBtns" style="justify-content: flex-end;">
                    {{-- <a href="#" data-toggle="tooltip" title="import"> <img src="{{ asset('images/download1.png') }}" /></a>
                    <a href="#" data-toggle="tooltip" title="excel"> <img style="width:29px !important" src="{{ asset('images/print1.png') }}" /></a> --}}
                    <a href="#" onclick="window.getImportDataBtn(); return false;" data-toggle="tooltip" title="Import data"><img src="{{ asset('images/import.png') }}" /></a>
                </div>
            </div>
        </div>



        <form name="get_manualAttendance_form" id="get_manualAttendance_form" action="{{ url('attendance/ajax') }}"
            method="post" class="width-three-fourth rl-margin-auto" accept-charset="utf-8">
            @csrf

            @php
            $batches = DB::table('batches')->get();
            @endphp

            <div class="row mt-5">
                <div class="col-md-4">
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
                <div class="col-md-4"> 
                    <div class="form-group">
                        <label for="sel1">Select Squad:</label>
                        <select name="squad_id" id="squad_id" class="form-control">
                            <option value="">Select...</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-4">
                    <label>Date:</label>
                    <div class="form-group">
                        <input type="text" name="date" id="date" class="form-control datePicker" autocomplete="off" />
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="usersubmitBtns mt-4">
                        <div class="mr-4">
                            <button type="button" onclick="window.get_manualAttendance_Submit();"
                                class="btn formBtn submitBtn">Proceed</button>
                        </div>
                    </div>

                </div>
            </div>
        </form>

        <div id="manualAttendance_form_status" class="mt-5"></div>
        <div id="manualAttendance-container" class="table-responsive mt-5"></div>

    </div>

</section>
@endsection

@section('scripts')
<script src="{{ asset('/js/statistics.js') }}"></script>
@endsection
