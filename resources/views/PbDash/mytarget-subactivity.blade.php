{{-- Extends Pb Dashboard Template --}}
<?php
$app_view = session('app_view');
?>
@extends(($app_view) ? 'layouts.pbdash.mobile-template' : 'layouts.pbdash.template')

{{-- Content --}}
@section('content')
<h4 class="myTr_title">My Targets</h4>
<section id="mytargetSubactivity" class="">
    <div class="subactivity_child_sec">

        <?php
        $activity = App\Models\Activity::withTrashed()->find($activity_id);
        $sub_activities = App\Models\Activity::where('parent_id', $activity_id)->where('has_qualify', '0')->get();
        $day = date("d");
        $date = date("Y-m-d");
        $disabled = "";//($day>18)?"disabled":"";
        $user = Auth::user();
        $user_id = $user->id;
        $probationer_id = probationer_id($user_id);
        ?>

        <div class="row">
            <div class="col-md-6 mb-0">
                <h4><a href="{{ url('user-mytarget') }}"><i class="fas fa-chevron-left"></i></a> &nbsp; {{activity_name($activity_id)}}</h4>
            </div>
        </div>

        <div class="">
            <div class="accordion" id="mytargetAccordion">
                @if (count($sub_activities)>0)
                @foreach ($sub_activities as $sub_activity)

                <?php
                    $components = App\Models\Activity::where('parent_id', $sub_activity->id)->where('has_qualify', '0')->get();

                ?>

                    <div class="card mb-2">
                        <div class="card-header" id="accordionHeading">

                            <a class="collapsed" data-toggle="collapse" data-target="#targetCollapse{{ $sub_activity->id }}" aria-expanded="false" aria-controls="targetCollapse{{ $sub_activity->id }}">
                                <div class="accordion-title"> <span class="mb-0 d-inline">{{ $sub_activity->name }}</span>
                                </div>
                            </a>

                        </div>

                        <div id="targetCollapse{{ $sub_activity->id }}" class="collapse collapsedSec" aria-labelledby="targetCollapse{{ $sub_activity->id }}" data-parent="#accordionExample">
                            <div class="card-body">
                                <form name="mytargetForm" class="mytargetForm" method="post">
                                    <div class="row justify-content-center align-items-center">
                                    <div class="col-md-6">


                                        @if (count($components)>0)
                                            @foreach ($components as $component)
                                            <?php

                                            $my_target_data = DB::table('probationers_mytargets')->where('component_id', $component->id)->where('probationer_id', $probationer_id)
                                            ->where('month',$date)->first();



                                            //echo $my_target_data->goal;exit;

                                            ?>
                                            <div class="row mt-3 mb-3 targetInputRow ">
                                                <div class="col-sm-3">
                                                    <div>
                                                        <label for="vol" style="font-size: 13px;">{{ $component->name }} :</label>
                                                    </div>
                                                    <input type="hidden" name="activityType[{{ $component->id }}]" class="" value="component" />
                                                    <input type="hidden" name="activityId[{{ $component->id }}]" class="" value="{{ $activity_id }}" />
                                                    <input type="hidden" name="subactivityId[{{ $component->id }}]" class="" value="{{ $sub_activity->id }}" />
                                                </div>
                                                    @php
                                                        $grading = ($component->has_grading);
                                                        $unit = ($component->unit === null) ? '0' : '1';
                                                    @endphp

                                                <div class="col-sm-8">
                                                    <div class="align-items-center d-flex">
                                                    @if (!is_null($unit))
                                                     <div class="border-0"> <input class="form-control" type="input" name="targetInput[{{ $component->id }}]" value={{( isset($my_target_data->goal))}} ></div>
                                                @elseif ($grading === 1)
                                                <select name="targetInput[{{ $component->id }}]" id="targetInput[{{ ($component->id) ? $component->id : '' }}]" class="form-control reqField">
                                                    <option value="">Select Grade</option>
                                                            <option value="A" {{isset($my_target_data['goal']) == 'A'  ? 'selected' : ''}} >A</option>
                                                            <option value="B" {{isset($my_target_data['goal']) == 'B'  ? 'selected' : ''}}>B</option>
                                                            <option value="C" {{isset($my_target_data['goal']) == 'C'  ? 'selected' : ''}}>C</option>
                                                            <option value="D" {{isset($my_target_data['goal']) == 'D'  ? 'selected' : ''}}>D</option>
                                                            <option value="E" {{isset($my_target_data['goal']) == 'E'  ? 'selected' : ''}}>E</option>
                                                </select>
                                                @endif

                                                    <?php ($component->unit != '') ? $unit = $component->unit : $unit = "No Units" ?>
                                                <div class="ml-3 unit">{{ ($component->has_grading === 0) ? $unit : "Grade" }}</div>
                                                    </div>
                                                </div>

                                        </div>
                                            @endforeach
                                            @else

                                            <?php
                                            $my_target_data = DB::table('probationers_mytargets')->where('subactivity_id', $sub_activity->id)->where('probationer_id', $probationer_id)
                                            ->where('month',$date)->first();
                                            ?>

                                            <div class="row mt-3 mb-3 targetInputRow">
                                                <div class="col-sm-3">
                                                    <div>
                                                        <label for="vol" style="font-size: 13px;">{{ $sub_activity->name }} :</label>
                                                    </div>
                                                    <input type="hidden" name="activityType[{{ $sub_activity->id }}]" class="" value="subactivity" />
                                                    <input type="hidden" name="activityId[{{ $sub_activity->id }}]" class="" value="{{ $activity_id }}" />
                                                    <input type="hidden" name="subactivityId[{{ $sub_activity->id }}]" class="" value="{{ $sub_activity->id }}" />
                                                </div>


                                                <div class="col-sm-8">
                                                    <div class="align-items-center d-flex">
                                                    @if ($sub_activity->has_grading === 0)
                                                    <div class="border-0"> <input class="form-control" type="input" name="targetInput[{{ $sub_activity->id }}]"  value={{(isset($my_target_data['goal'])) ? $my_target_data['goal'] : ''}}></div>
                                                    @else
                                                <select name="targetInput[{{ $sub_activity->id }}]" id="targetInput[{{ $sub_activity->id }}]" class="form-control reqField">
                                                    <option value="">Select Grade</option>
                                                    <option value="A" {{isset($my_target_data['goal']) == 'A'  ? 'selected' : ''}} >A</option>
                                                            <option value="B" {{isset($my_target_data['goal']) == 'B'  ? 'selected' : ''}}>B</option>
                                                            <option value="C" {{isset($my_target_data['goal']) == 'C'  ? 'selected' : ''}}>C</option>
                                                            <option value="D" {{isset($my_target_data['goal']) == 'D'  ? 'selected' : ''}}>D</option>
                                                            <option value="E" {{isset($my_target_data['goal']) == 'E'  ? 'selected' : ''}}>E</option>
                                                </select>
                                                @endif
                                                <?php ($sub_activity->unit != '') ? $unit = $sub_activity->unit : $unit = "No Units" ?>
                                                <div class="ml-3 unit">{{ ($sub_activity->has_grading === 0) ? $unit : "Grade" }}</div>
                                            </div>
                                        </div>

                                    </div>
                                        @endif

                                    </div>
                                </div>
                                    <div class="text-center">
                                        <button type="button" class="btn formBtn submitBtn myTargetSubmit {{ $disabled }}">Set Goal</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                @endforeach

                @else

                    <div class="card mb-3">
                        <div class="card-header" id="accordionHeading">
                            <a class="collapsed" data-toggle="collapse" data-target="#targetCollapse{{ $activity->id }}" aria-expanded="true" aria-controls="targetCollapse{{ $activity->id }}">
                                <div class="accordion-title"> <span class="mb-0 d-inline">{{ $activity->name }}</span></div>
                            </a>
                        </div>


                        <div id="targetCollapse{{ $activity->id }}" class="collapse show" aria-labelledby="targetCollapse{{ $activity->id }}" data-parent="#accordionExample">
                            <div class="card-body">
                                <form name="mytargetForm" class="mytargetForm" method="post">
                                    <div class="row justify-content-center align-items-center">
                                        <div class="col-md-6">
                                            <?php

                                            $my_target_data =  \App\Models\probationersMytarget::where('activity_id', $activity->id)->where('probationer_id', $probationer_id)->where('month',$date)->first();

                                            ?>

                                            <div class="row mt-3 mb-3 targetInputRow">
                                                <div class="col-sm-3">
                                                    <div>
                                                        <label for="vol" style="font-size: 13px;">{{ $activity->name }} :</label>
                                                    </div>
                                                    <input type="hidden" name="activityType[{{ $activity->id }}]" class="" value="activity" />
                                                    <input type="hidden" name="activityId[{{ $activity->id }}]" class="" value="{{ $activity_id }}" />
                                                    <input type="hidden" name="subactivityId[{{ $activity->id }}]" class="" value="{{ $activity->id }}" />
                                                </div>


                                                <div class="col-sm-8">
                                                    <div class="align-items-center d-flex">
                                                    @if ($activity->has_grading === 0)
                                                    <div class="border-0"> <input class="form-control" type="input" name="targetInput[{{ $activity->id }}]" value={{isset($my_target_data['goal'])}} ></div>
                                                   @else
                                                <select name="targetInput[{{ $activity->id }}]" id="targetInput[{{ $activity->id }}]" class="form-control reqField">
                                                        <option value="">Select Grade</option>
                                                        <option value="A" {{isset($my_target_data->goal) == 'A'  ? 'selected' : ''}} >A</option>
                                                        <option value="B" {{isset($my_target_data->goal) == 'B'  ? 'selected' : ''}}>B</option>
                                                        <option value="C" {{isset($my_target_data->goal) == 'C'  ? 'selected' : ''}}>C</option>
                                                        <option value="D" {{isset($my_target_data->goal) == 'D'  ? 'selected' : ''}}>D</option>
                                                        <option value="E" {{isset($my_target_data->goal) == 'E'  ? 'selected' : ''}}>E</option>
                                                </select>
                                                @endif
                                                <?php ($activity->unit != '') ? $unit = $activity->unit : $unit = "No Units" ?>
                                                <div class="ml-3 unit">{{ ($activity->has_grading === 0) ? $unit : "Grade" }}</div>
                                                    </div>
                                                </div>

                                            </div>

                                    <div class="text-center">
                                        <button type="button" class="btn formBtn submitBtn myTargetSubmit {{ $disabled }}">Set Goal</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
<script>

</script>
@endsection

<style>
    .subactivity_child_sec{
        position: relative;
        left:-25px;
        top:30px;
        background:#fff;
        padding:15px;
        border-radius:15px;
    }
    .targetInputRow td{
        vertical-align: baseline !important;
    }
    .targetInputRow{
    align-items: center
    }
    .targetInputRow input,
    .targetInputRow select{
        flex: 0 0 100%;
        max-width: 100%;
    }
    .targetInputRow .unit{
        font-size: 13px;
    }
    @media screen and (min-width:320px) and (max-width:767px){

    .targetInputRow select{
        flex: 0 0 80%;
        max-width: 80%;
    }
    .targetInputRow .unit{
        font-size: 10px;
    }
    #subwrapper {
        padding-left: 0px !important;
        margin-top: 10px;
    }
    #subwrapper .myTr_title{
        display: none;
    }
    .subactivity_child_sec{
        top:0px !important;
        left: 0px !important;
    }

    }
</style>
