{{-- Extends layout --}}
@extends('layouts.default')

{{-- Content --}}
@section('content')

<section id="createactivity" class="content-wrapper_sub activities-wrapper tab-content">
    <div class="user_manage">
        <div class="row">
            <div class="col-md-6">
                <h4>Add Activity</h4>
            </div>
            <div class="col-md-6">
                <div class="useractionBtns" style="justify-content: flex-end;">
                   <!-- <a href="#" data-toggle="tooltip" title="import"><img src="{{ asset('images/import.png') }}" /></a> -->
                </div>
            </div>
        </div>
        <form name="createactivity_form" id="createactivity_form" action="{{ url('activities') }}" method="post"
            class="userform activityform" accept-charset="utf-8">
            @csrf

            @php
            $batches = DB::table('batches')->get();
            @endphp
            <div class="row">
                <div class="col-md-6">
                    <label>Select Batch:</label>
                    <select name="batch_id" id="batch_id" class="form-control reqField">
                        <option value="">Select...</option>
                        @if( !empty($batches) )
                            @foreach($batches as $batch)
                            <option value="{{ $batch->id }}" @if($batch->id == Session::get('current_batch')) selected @endif>{{ $batch->BatchName }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label>Activity Name</label>
                    <input type="text" name="activity_name" id="activity_name" class="form-control reqField">
                    <div id="AddSubActivity" data-subactivity-count="0" class="add hidden">
                        <img src="{{ asset('images/add.png') }}" />
                        <span>Add Sub activity</span>
                    </div>
                </div>
                <div class="col">
                    <label>Unit</label>
                    <input type="text" name="activity_unit" id="activity_unit" class="form-control">
                    <div class="d-flex justify-content-start has_grading_wrapper">
                        <div class="form-group form-check">
                            <input type="checkbox" name="activity_has_grading" value="1" class="form-check-input activity_has_grading-input" id="activity_has_grading">
                            <label class="form-check-label ml-0" for="activity_has_grading">Use Grading</label>
                        </div>
                        <div class="form-group form-check ml-3">
                            <input type="checkbox" name="activity_has_qualify" value="1" class="form-check-input activity_has_qualify-input" id="activity_has_qualify">
                            <label class="form-check-label ml-0" for="activity_has_qualify">Use Qualify</label>
                        </div>
                    </div>
                </div>
            </div>

            <div id="sub_activities" class="sub-activities"></div>

            <div id="createactivity_form_status" class="mt-3"></div>

            <div class="usersubmitBtns mt-5">
                <div class="mr-4">
                    <button type="submit" class="btn formBtn submitBtn">Submit</button>
                </div>
                <div>
                    <a href="{{ route('activities.index') }}"><button type="button" class="btn formBtn cancelBtn">Cancel</button></a>
                </div>
            </div>
        </form>
    </div>
</section>

@endsection

@section('scripts')
{{-- <script src="{{ asset('js/activities.js') }}" type="text/javascript"></script> --}}
<script>
    $(document).on("keyup", "#activity_name", function(){
        if($(this).val().length >= 1) {
            $("#AddSubActivity").removeClass("hidden");
        } else {
            $("#AddSubActivity").addClass("hidden");
        }
    });
</script>
@endsection
