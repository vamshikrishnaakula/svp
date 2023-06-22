{{-- Extends layout --}}
@extends('layouts.default')

{{-- Content --}}
@section('content')

<section id="extrasessions" class="content-wrapper_sub">
    <div class="user_manage">
        <div class="row">
            <div class="col-md-9">
                <h4>Create Extra Class</h4>
            </div>
            <div class="col-md-3">
                <div class="userBtns d-flex justify-content-end">
                    {{-- <a href="#" data-toggle="tooltip" title="import"> <img src="{{ asset('images/import.png') }}" /></a>
                    <a href="#" data-toggle="tooltip" title="Download Sample CSV for Bulk Import"> <img src="{{ asset('images/excel.png') }}" /></a>
                    <a href="#" data-toggle="tooltip" title="download"> <img src="{{ asset('images/download1.png') }}" /></a>
                    <a href="#" data-toggle="tooltip" title="print"> <img src="{{ asset('images/print1.png') }}" /></a> --}}
                    <a href="{{ url('timetables/extra-classes') }}" class="btn btn-primary btn-sm ml-3" data-toggle="tooltip" title="Back to Extra Sessions">
                        <i class="fas fa-chevron-left"></i> Back
                    </a>
                </div>
            </div>
        </div>

        <div class="create-extrasession-wrapper position-relative">
            <form id="get_create_extraclass_form" action="" method="post" class="width-three-fourth rl-margin-auto" accept-charset="utf-8">
                @csrf

                <div class="row mt-5">
                    <div class="col">
                        <div class="form-group">
                            <label>Select Batch</label>
                            <select name="batch_id" id="batch_id" onchange="window.get_activity_dropdowns(this);" class="form-control reqField">
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
                            <label>Activity</label>
                            <select name="activity_id" id="activity_id" onchange="window.get_subactivity_dropdowns(this);" class="form-control reqField">
                                <option value="">Select...</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    {{-- <div class="col">
                        <div class="form-group">
                            <label>Sub Activity:</label>
                            <div id="subactivity_id_wrapper">
                                <select name="subactivity_id" id="subactivity_id" class="form-control">
                                    <option value="">Select...</option>
                                </select>
                            </div>
                        </div>
                    </div> --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Staff / DI:</label>
                            <select name="di_id" id="di_id" class="form-control reqField">
                                <option value="">Select Staff...</option>
                                @if( !empty($get_DI) )
                                    @foreach($get_DI as $DI)
                                        <option value="{{ $DI->id }}">{{ $DI->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        {{-- <label>Date</label>
                        <div class="form-group">
                            <input type="text" name="session_date" id="session_date" class="form-control datePicker reqField" autocomplete="off" />
                        </div> --}}
                    </div>
                </div>

                <div id="get_create_extraclass_status" class="mt-2"></div>

                <div class="text-center mt-2">
                    <button type="button" onclick="window.get_extraclass_form();" class="btn formBtn submitBtn">Proceed</button>
                </div>
            </form>

            <div id="extraclass_form_container" class="mt-5"></div>
        </div>
</section>

@endsection

@section('header-scripts')
    <script src="{{ asset('js/jquery.inputmask.bundle.js') }}"></script>
@endsection

@section('scripts')
<script>
    // $(document).ready(function() {
    //     $('#extraclass_prob_list').DataTable();
    // });
</script>
@endsection
