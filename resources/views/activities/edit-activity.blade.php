{{-- Extends layout --}}
@extends('layouts.default')

{{-- Content --}}
@section('content')

<section id="editactivity" class="content-wrapper_sub activities-wrapper tab-content">

    <div class="user_manage">
        <div class="row">
            <div class="col-md-6">
                <h4>Edit Activity</h4>
            </div>
            <div class="col-md-3"></div>
            <div class="col-md-3">
                <div class="useractionBtns">
                    {{-- <a href="#" onclick="window.deleteActivity({{ $activity->id }}, 'activity')" data-toggle="tooltip" title="delete"><img src="{{ asset('images/delete.png') }}" /></a> --}}
                    <a href="#" onclick="window.get_deleteActivity({{ $activity->id }}, 'Activity'); return false;" data-toggle="tooltip" title="delete"><img src="{{ asset('images/delete.png') }}" /></a>
                    {{--  <a href="#" data-toggle="tooltip" title="import"><img src="{{ asset('images/import.png') }}" /></a>  --}}
                </div>
            </div>
        </div>

        <form name="editactivity_form" id="editactivity_form" action="{{ route('activities.update',$activity->id)  }}"
            method="post" class="userform activityform" accept-charset="utf-8">
            @csrf
            @method('PUT')

            @php
            $batches = DB::table('batches')->get();

            $sub_activities = App\Models\Activity::where('parent_id', $activity->id)->get();
            $sub_activities_count   = count($sub_activities);
            @endphp

            <div class="row">
                <div class="col">
                    <label>Activity Name</label>
                    <input type="text" name="activity_name[{{ $activity->id }}]" id="activity_name" value="{{ $activity->name }}"
                        class="form-control">

                    <input type="hidden" name="activity_id" id="activity_id" value="{{ $activity->id }}"
                        class="hidden">

                    <div class="add" id="AddSubActivity" data-subactivity-count="{{$sub_activities_count}}">
                        <img src="{{ asset('images/add.png') }}" />
                        <span>Add Sub activity</span>
                    </div>
                </div>
                <div class="col">
                    <label>Unit</label>
                    <input type="text" name="activity_unit[{{ $activity->id }}]" id="activity_unit" value="{{ $activity->unit }}" class="form-control">
                    <div class="d-flex justify-content-start has_grading_wrapper">
                        <div class="form-group form-check">
                            <input type="checkbox" name="activity_has_grading[{{ $activity->id }}]" value="1" class="form-check-input activity_has_grading-input" id="activity_has_grading_{{ $activity->id }}" {{ ($activity->has_grading === 1) ? "checked" : "" }}>
                            <label class="form-check-label ml-0" for="activity_has_grading_{{ $activity->id }}">Use Grading</label>
                        </div>
                        <div class="form-group form-check ml-3">
                            <input type="checkbox" name="activity_has_qualify[{{ $activity->id }}]" value="1" class="form-check-input activity_has_qualify-input" id="activity_has_qualify_{{ $activity->id }}" {{ ($activity->has_qualify === 1) ? "checked" : "" }}>
                            <label class="form-check-label ml-0" for="activity_has_qualify_{{ $activity->id }}">Use Qualify</label>
                        </div>
                    </div>
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
                                <span onclick="window.get_deleteActivity({{ $sub_activity->id }}, 'Sub Activity');" class="delete small float-right">Delete</span>
                                <div>
                                    <input type="text" name="activity_name[{{ $sub_activity->id }}]" value="{{ $sub_activity->name }}"
                                        id="sub_activity_{{ $dataSubactivitySL }}" class="form-control sub-activity-name" />
                                    <div class="add addComponentBtn" data-subactivity-id="{{ $sub_activity->id }}" data-subactivity-sl="{{ $dataSubactivitySL }}">
                                        <img src="{{ asset('images/add.png') }}" />
                                        <span>Add components</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <label>Unit</label>
                                <input type="text" name="activity_unit[{{ $sub_activity->id }}]" value="{{ $sub_activity->unit }}" id="sub_activity_unit_{{ $dataSubactivitySL }}" class="form-control sub-activity-unit">
                                <div class="d-flex justify-content-start has_grading_wrapper">
                                    <div class="form-group form-check">
                                        <input type="checkbox" name="activity_has_grading[{{ $sub_activity->id }}]" value="1" class="form-check-input sub_activity_has_grading-input" id="activity_has_grading_{{ $sub_activity->id }}" {{ ($sub_activity->has_grading === 1) ? "checked" : "" }}>
                                        <label class="form-check-label ml-0" for="activity_has_grading_{{ $sub_activity->id }}">Use Grading</label>
                                    </div>
                                    <div class="form-group form-check ml-3">
                                        <input type="checkbox" name="activity_has_qualify[{{ $sub_activity->id }}]" value="1" class="form-check-input sub_activity_has_qualify-input" id="activity_has_qualify_{{ $sub_activity->id }}" {{ ($sub_activity->has_qualify === 1) ? "checked" : "" }}>
                                        <label class="form-check-label ml-0" for="activity_has_qualify_{{ $sub_activity->id }}">Use Qualify</label>
                                    </div>
                                </div>
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
                                                <span onclick="window.get_deleteActivity({{ $component->id }}, 'Component');" class="delete small float-right">Delete</span>
                                                <input type="text" name="activity_name[{{ $component->id }}]" value="{{ $component->name }}" class="form-control component-name reqField">
                                            </div>
                                            <div class="col">
                                                <label>Unit</label>
                                                <input type="text" name="activity_unit[{{ $component->id }}]" value="{{ $component->unit }}" class="form-control component-unit reqField">
                                                <div class="d-flex justify-content-start has_grading_wrapper">
                                                    <div class="form-group form-check">
                                                        <input type="checkbox" name="activity_has_grading[{{ $component->id }}]" value="1" class="form-check-input component_has_grading-input" id="activity_has_grading_{{ $component->id }}" {{ ($component->has_grading === 1) ? "checked" : "" }}>
                                                        <label class="form-check-label ml-0" for="activity_has_grading_{{ $component->id }}">Use Grading</label>
                                                    </div>
                                                    <div class="form-group form-check ml-3">
                                                        <input type="checkbox" name="activity_has_qualify[{{ $component->id }}]" value="1" class="form-check-input component_has_qualify-input" id="activity_has_qualify_{{ $component->id }}" {{ ($component->has_qualify === 1) ? "checked" : "" }}>
                                                        <label class="form-check-label ml-0" for="activity_has_qualify_{{ $component->id }}">Use Qualify</label>
                                                    </div>
                                                </div>
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

            <div id="editactivity_form_status"></div>

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
    {{-- <a href="#" class="float">
        <i class="fa fa-plus my-float"></i>
    </a> --}}
</section>

@endsection

{{-- @section('scripts')
<script src="{{ asset('js/activities.js') }}" type="text/javascript"></script>
@endsection --}}
