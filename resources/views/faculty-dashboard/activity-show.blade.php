{{-- Extends layout --}}
@extends('layouts.faculty.template')

{{-- Content --}}
@section('content')

{{ $activity_id }}


<section id="viewactivity" class="content-wrapper_sub activities-wrapper tab-content">

    <div class="user_manage">
        <div class="row">
            <div class="col-md-6">
                <h4>Activity</h4>
            </div>
            <div class="col-md-3"></div>
            <div class="col-md-3">
                <div class="useractionBtns">

                </div>
            </div>
        </div>

        <form name="editactivity_form" id="editactivity_form" action=""
            method="post" class="userform activityform" accept-charset="utf-8">
            @csrf
            @method('PUT')

            @php
            $batches = DB::table('batches')->get();

            $activity       = App\Models\Activity::find($activity_id);
            $sub_activities = App\Models\Activity::where('parent_id', $activity_id)->get();
            $sub_activities_count   = count($sub_activities);
            @endphp

            <div class="row">
                <div class="col">
                    <label>Activity Name</label>
                    <input type="text" name="activity_name[{{ $activity_id }}]" id="activity_name" value="{{ $activity->name }}"
                        class="form-control" disabled="disabled">
                </div>
                <div class="col">
                    <label>Unit</label>
                    <input type="text" name="activity_unit[{{ $activity_id }}]" id="activity_unit" value="{{ $activity->unit }}"
                        class="form-control" disabled="disabled">
                </div>
            </div>

            <div id="sub_activities" class="sub-activities">
                @php
                $si = 0;
                @endphp

                @foreach ($sub_activities as $sub_activity)
                @php
                $dataSubactivitySL = $si+1;
                @endphp
                <div class="sub-activity-item" data-subactivity-sl="{{ $dataSubactivitySL }}">
                    <div class="sub-activity-item-inner">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Sub Activity</label>
                                <div>
                                    <input type="text" name="activity_name[{{ $sub_activity->id }}]" value="{{ $sub_activity->name }}"
                                        id="sub_activity_{{ $dataSubactivitySL }}" class="form-control sub-activity-name" disabled="disabled" />
                                </div>
                            </div>
                            <div class="col">
                                <label>Unit</label>
                                <input type="text" name="activity_unit[{{ $sub_activity->id }}]" value="{{ $sub_activity->unit }}"
                                    id="sub_activity_unit_{{ $dataSubactivitySL }}" class="form-control sub-activity-unit" disabled="disabled" />
                            </div>
                        </div>
                        <div class="components-wrapper">
                            <div id="components_{{ $dataSubactivitySL }}" class="components">
                                @php
                                $components = App\Models\Activity::where('parent_id', $sub_activity->id)->get();
                                $ci = 0;
                                @endphp

                                @foreach ($components as $component)
                                    <div class="component-item" data-subactivity-sl="{{ $dataSubactivitySL }}">
                                        <div class="row">
                                            <div class="col">
                                                <label class="float-left">Components</label>
                                                <input type="text" name="activity_name[{{ $component->id }}]" value="{{ $component->name }}" class="form-control component-name reqField"  disabled="disabled">
                                            </div>
                                            <div class="col">
                                                <label>Unit</label>
                                                <input type="text" name="activity_unit[{{ $component->id }}]" value="{{ $component->unit }}" class="form-control component-unit reqField" disabled="disabled" />
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                @php
                    $si++;
                @endphp
                @endforeach
            </div>

            <div class="usersubmitBtns mt-5">

                <div>
                    <a href="{{ url('activity-list') }}"><button type="button" class="btn formBtn cancelBtn">Back</button></a>
                </div>
            </div>
        </form>

    </div>
    {{-- <a href="#" class="float">
        <i class="fa fa-plus my-float"></i>
    </a> --}}
</section>

@endsection

{{-- @section('scripts')
<script src="{{ asset('js/activities.js') }}" type="text/javascript"></script>
@endsection --}}
