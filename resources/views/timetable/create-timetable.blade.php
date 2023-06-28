{{-- Extends layout --}}
@extends('layouts.default')

{{-- Content --}}
@section('content')
    {{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> --}}

    <section id="createtimetable" class="content-wrapper_sub">
        <div class="user_manage">
            <div class="row">
                <div class="col-md-8">
                    <h4>Create Time Table</h4>
                </div>

                <div class="col-md-4">
                    <div class="useractionBtns d-flex justify-content-end">
                        <a href="#" onclick="window.get_timetableImport_modal()" data-toggle="tooltip"
                            title="Import Timetable"> <img src="{{ asset('images/import.png') }}" /></a>
                        {{-- <a href="{{ asset('csv/import-timetables-sample.csv') }}" data-toggle="tooltip" title="Download Sample CSV for Bulk Import"> <img src="{{ asset('images/excel.png') }}" /></a> --}}
                        {{-- <a href="#" data-toggle="tooltip" title="download"> <img src="{{ asset('images/download1.png') }}" /></a>
                        <a href="#" data-toggle="tooltip" title="print"> <img src="{{ asset('images/print1.png') }}" /></a> --}}
                    </div>
                </div>
            </div>

            <form name="get_timetableUpdate_form" id="get_timetableUpdate_form" action="{{ url('timetables/ajax') }}"
                method="post" class="width-three-fourth rl-margin-auto" accept-charset="utf-8">
                @csrf

                @php
                    $batches = DB::table('batches')->get();
                @endphp
                <div class="row mt-5">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Select Batch:</label>
                            <select name="batch_id" id="batch_id"
                                onchange="window.select_batchId_changed(this, 'squad_id');" class="form-control">
                                <option value="">Select batch...</option>
                                @if (!empty($batches))
                                    @foreach ($batches as $batch)
                                        <option value="{{ $batch->id }}"
                                            @if ($batch->id == Session::get('current_batch')) selected @endif>{{ $batch->BatchName }}
                                        </option>
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
                        <label>Date (From):</label>
                        <div class="form-group">
                            <input type="text" name="from_date" id="from_date" class="form-control datePicker"
                                autocomplete="off" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label>Date (To):</label>
                        <div class="form-group">
                            <input type="text" name="to_date" id="to_date" class="form-control datePicker"
                                autocomplete="off" />
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="usersubmitBtns mt-4">
                            <div class="mr-4">
                                <button type="button" onclick="window.get_timetableUpdate_Submit();"
                                    class="btn formBtn submitBtn">Proceed</button>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
            {{-- ///forcopy// --}}
            {{-- <button type="button" class="btn formBtn submitBtn" style='margin-left:940px'><a href="{{ '/copy_squad' }}"
                    style=" color: white;">copy-Squads</a></button> --}}
            {{-- // --}}
            {{-- <form id=form_id name='form_id' method='POST' action='copy_field' accept-charset="utf-8">
                @csrf --}}
                {{-- <input type="checkbox" id="myCheckbox"> Select for copying squad time table
                <div class="form-group">
                    <select name="squad_2[]" id="squad2_id" class="form-control" multiple='multiple'>
                        @php
                            $distinctValues = DB::table('squads')
                                ->select('id', 'SquadNumber')
                                ->where('Batch_Id', '=', 77)
                                ->get();
                        @endphp
                        <option value="">Select...</option>
                        @foreach ($distinctValues as $d)
                            <option value={{ $d->id }}>{{ $d->SquadNumber }}</option>
                        @endforeach
                    </select> --}}

            {{-- </form> --}}

            {{-- <input name="squad_2" class="form-control"> --}}
        </div>

        <div id="timetableUpdate_form_status" class="mt-5"></div>
        <div id="timetable-form-container" class="table-responsive mt-5"></div>

        {{-- ************************** Import Timetable ************************** --}}

        </div>
        {{-- <script>
            $(document).ready(function() {
                // Hide the dropdown initially
                $('#squad2_id').hide();

                // When checkbox is clicked
                $('#myCheckbox').click(function() {
                    // If checkbox is checked, show the dropdown
                    if ($(this).is(':checked')) {
                        $('#squad2_id').show();
                    } else {
                        $('#squad2_id').hide();
                    }
                });
            });
        </script> --}}
    </section>
@endsection

@section('header-scripts')
    <script src="{{ asset('js/jquery.inputmask.bundle.js') }}"></script>
    <script src="{{ asset('jquery-time-picker/timepicker.js') }}"></script>
@endsection
@section('styles')
    <link rel="stylesheet" href="{{ asset('jquery-time-picker/timepicker.css') }}" type="text/css" />
@endsection
