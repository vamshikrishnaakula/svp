{{-- Extends layout --}}
@extends('layouts.default')

{{-- Content --}}
@section('content')

    <section id="viewtimetable" class="content-wrapper_sub">
        <div class="user_manage">
            <div class="row">
                <div class="col-md-8">
                    <h4>copy Time Table</h4>
                </div>
                {{-- <button type="button" class="btn formBtn submitBtn" style='margin-left:100px'><a href="{{ '/copy_squad' }}"
                        style=" color: white;">copy-Squads</a></button> --}}
                {{-- kkkk --}}
                <div class="col-md-4">
                    <div class="useractionBtns" style="max-width:120px; margin-left:auto;">
                        {{-- <a href="#" data-toggle="tooltip" title="download"> <img src="{{ asset('images/download1.png') }}" /></a> --}}
                        {{-- <a href="#" onclick="window.print();" data-toggle="tooltip" title="print"> <img src="{{ asset('images/print1.png') }}" /></a> --}}
                    </div>
                </div>
            </div>
            {{-- /// copy from 1 squad to another squad<br> --}}

            {{-- <form id=form_id name='form_id' method='POST' action='copy_field'>
    @csrf
    BATCH_id <input text name=BATCH_ID><br><br>
    From squad <input text name=squad_1><br><br>
    To squad <input text name=squad_2><br><br>
    Start date <input type="text" name="session_time_start"><br><br>
    End date <input type="text" name="session_time_end"><br><br>
    <input type='submit'> --}}
            {{-- </form> --}}
            {{-- <form name="get_timetableView_form" id="get_timetableView_form" action="{{ url('timetables/ajax') }}"
                method="post" class="" accept-charset="utf-8"> --}}
            <form id=form_id name='form_id' method='POST' action='copy_field' accept-charset="utf-8">
                @csrf

                @php
                    $batches = DB::table('batches')->get();
                    //$distinctValues = DB::table('squads')->get();
                    // where('project_id', $id)->get()->pluck($columnname)->unique();
                @endphp
                <div class="row mt-5">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Select Batch:</label>
                            <select name="BATCH_ID" id="batch_id"
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
                            <label for="sel1">From Copy Squad:</label>
                            <select name="squad_1" id="squad_id" class="form-control">
                                <option value="">Select...</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="sel1">To Copy Squad:</label>
                            <select name="squad_2[]" id="squad_id" class="form-control" multiple='multiple'>
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
                            </select>

                            {{-- <input name="squad_2" class="form-control"> --}}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label>Date (From):</label>
                        <div class="form-group">
                            <input type="text" name="session_time_start" id="from_date" class="form-control datePicker"
                                autocomplete="off" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label>Date (To):</label>
                        <div class="form-group">
                            <input type="text" name="session_time_end" id="to_date" class="form-control datePicker"
                                autocomplete="off" />
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="usersubmitBtns mt-4">
                            <div class="mr-4">
                                {{-- <button type="button"
                                    class="btn formBtn submitBtn">Proceed</button> --}}
                                <button class="btn formBtn submitBtn" type='submit'>Proceed</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            {{-- 
            <div id="timetableView_form_status" class="mt-5"></div>
            <div id="timetable-view-container" class="table-responsive mt-5"></div> --}}
        </div>

    </section>

@endsection

@section('scripts')
    <script src="{{ asset('js/jquery.inputmask.bundle.js') }}"></script>
@endsection
