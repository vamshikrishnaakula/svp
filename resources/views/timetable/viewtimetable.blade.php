{{-- Extends layout --}}
@extends('layouts.default')

{{-- Content --}}
@section('content')

    <section id="viewtimetable" class="content-wrapper_sub">
        <div class="user_manage">
            <div class="row">
                <div class="col-md-8">
                    <h4>View Time Table</h4>
                </div>

                {{-- kkkk --}}
                <div class="col-md-4">
                    <div class="useractionBtns" style="max-width:120px; margin-left:auto;">
                        {{-- <a href="#" data-toggle="tooltip" title="download"> <img src="{{ asset('images/download1.png') }}" /></a> --}}
                        {{-- <a href="#" onclick="window.print();" data-toggle="tooltip" title="print"> <img src="{{ asset('images/print1.png') }}" /></a> --}}
                    </div>
                </div>
            </div>
            <form name="get_timetableView_form" id="get_timetableView_form" action="{{ url('timetables/ajax') }}"
                method="post" class="" accept-charset="utf-8">
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
                                <button type="button" onclick="window.get_timetableView();"
                                    class="btn formBtn submitBtn">Proceed</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <div id="timetableView_form_status" class="mt-5"></div>
            <div id="timetable-view-container" class="table-responsive mt-5"></div>
        </div>

    </section>

@endsection

@section('scripts')
    <script src="{{ asset('js/jquery.inputmask.bundle.js') }}"></script>
@endsection
