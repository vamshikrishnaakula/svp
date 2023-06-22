{{-- Extends layout --}}
<?php
use Carbon\Carbon;
?>
@extends('layouts.default')

{{-- Content --}}
@section('content')

<section id="dashboard" class="content-wrapper tab-content">
<?php

        $b_id = current_batch();

        $batchno = App\Models\Batch::where('id',$b_id)
        ->select('BatchName')
        ->value('BatchName');

        $tsquads = App\Models\Squad::where('Batch_Id', $b_id)->select('SquadNumber')->count();

        $tprobationers = \App\Models\probationer::where('batch_id',$b_id)->select('id')->count();
        $tprobationersmale = \App\Models\probationer::where('batch_id',$b_id)
        ->where('gender','Male')->count();
        $tprobationerfemale = \App\Models\probationer::where('batch_id',$b_id)
        ->where('gender','Female')->count();

        $tactivities = App\Models\Activity::where('batch_id',$b_id)
        ->where('type','activity')->count();

        // $tstaff = App\Models\Squad::where('Batch_Id',$b_id)
        // ->select('DrillInspector_Id')->count();

        $tstaff1 = App\Models\User::where('role','drillinspector')->count();
        $tstaff2 = App\Models\User::where('role','receptionist')->count();
        $tstaff3 = App\Models\User::where('role','doctor')->count();
        $tstaff4 = App\Models\User::where('role','faculty')->count();
        $tstaff5 = App\Models\User::where('role','si')->count();
        $tstaff6 = App\Models\User::where('role','adi')->count();



         $tstaff = $tstaff1 + $tstaff2 + $tstaff3 + $tstaff4 + $tstaff5 + $tstaff6;



          $timetable_sessions_weekwise =  App\Models\Timetable::where('batch_id',$b_id)
            ->where('date', '>=', Carbon::now()->startOfWeek())
            ->whereNotNull('activity_id')
            ->where('date', '<', Carbon::now()->endOfWeek())
            ->count();

            $missed_sessions_weekwise = App\Models\ExtraSession::where('batch_id',$b_id)
            ->where('date', '>=', Carbon::now()->startOfWeek())
            ->whereNotNull('activity_id')
            ->where('date', '<', Carbon::now()->endOfWeek())
            ->count();


            $extraclass_sessions_weekwise = App\Models\ExtraClass::where('batch_id',$b_id)
            ->where('date', '>=', Carbon::now()->startOfWeek())
            ->whereNotNull('activity_id')
            ->where('date', '<', Carbon::now()->endOfWeek())
            ->count();


            $tsessionsweek = $timetable_sessions_weekwise + $missed_sessions_weekwise + $extraclass_sessions_weekwise;


        $currentyear = date("Y");
        $currentmonth = date("m");
        $timetable_sessions_monthwise = App\Models\Timetable::where('batch_id',$b_id)
        ->whereNotNull('activity_id')
        ->whereYear('date','=',$currentyear)
        ->whereMonth('date','=',$currentmonth)
        ->count();

        $missedclass_sessions_monthwise = App\Models\ExtraSession::where('batch_id',$b_id)
        ->whereNotNull('activity_id')
        ->whereYear('date','=',$currentyear)
        ->whereMonth('date','=',$currentmonth)
        ->count();

        $extraclass_sessions_monthwise = App\Models\ExtraClass::where('batch_id',$b_id)
        ->whereNotNull('activity_id')
        ->whereYear('date','=',$currentyear)
        ->whereMonth('date','=',$currentmonth)
        ->count();

        $tsessionmonth = $timetable_sessions_monthwise + $missedclass_sessions_monthwise + $extraclass_sessions_monthwise;


        $selectbatch = App\Models\Batch::all();

        $user = Auth::User()->role;
        if($user == "admin" || $user == "superadmin")

        {
            //$allnotifications = App\Models\Notification::take(4)->get();
            $allnotifications = App\Models\Notification::all()->sortByDesc('created_at')->take(4);
        }
        ?>



        {{-- ---------- Timetable ---------- --}}
         <?php
        $ldate = date('Y-m-d');
        $current_batch = Session::get('current_batch');
        $squads = App\Models\Squad::where('Batch_Id', $current_batch)->orderBy('id', 'asc')->get();
        $batches = App\Models\Batch::all();
         ?>

<div class="row">
    <div class="col-md-2">
        <p id="selectTriggerFilter" class="mb-0"><label class="mr-3"> Batch : {{$batchno}}</label></p>
        {{-- <select class="form-control col-md-5" id="batch_id" name="batch_id">
           <option value=''>Select Batch</option>
           @foreach($selectbatch as $batch)
           <option value="{{ $batch->id }}" @if($batch->id == Session::get('current_batch')) selected @endif>{{ $batch->BatchName }}</option>
           @endforeach
       </select> --}}


    </div>
</div>
         <div class="row">
            <div class="col-md-2 d-flex align-items-stretch">
                <div class="title__card">
                    <div>
                        <h3>Probationers {{ $tprobationers }}</h3>
                    </div>
                    <div class="gender">
                        <h6>Male {{ $tprobationersmale }}</h6>
                        <h6>Female {{ $tprobationerfemale }}</h6>
                    </div>
                </div>
            </div>
            <div class="col-md-2 d-flex align-items-stretch">
                <div class="title__card">
                    <div>
                        <h3>Squads</h3>
                    </div>
                    <div>
                        <p>{{ $tsquads }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2 d-flex align-items-stretch">
                <div class="title__card">
                    <div>
                        <h3>Activities</h3>
                    </div>
                    <div>
                        <p>{{ $tactivities }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2 d-flex align-items-stretch">
                <div class="title__card">
                    <div>
                        <h3>Staff</h3>
                    </div>
                    <div>
                        <p>{{ $tstaff }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2 d-flex align-items-stretch">
                <div class="title__card">
                    <div>
                        <h3>Sessions this week</h3>
                    </div>
                    <div>
                        <p>{{ $tsessionsweek }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2 d-flex align-items-stretch">
                <div class="title__card">
                    <div>
                        <h3>Sessions this month</h3>
                    </div>
                    <div>
                        <p>{{ $tsessionmonth }}</p>
                    </div>
                </div>
            </div>

         </div>
         <div class="row mt-5 landing_title">
            <div class="col-md-6">
        <div style="background-color: #fff; border: 1px solid #43808d70">
            <div class="p-3">
                <div class="d-flex justify-content-between align-items-center ">
                    <h5 class="mb-0">Notifications</h5>
                    <a href="{{ url('/notifications') }}" class="btn btn-default btn-sm view_btn">View All</a>
                </div>
                <hr>
            <div>
                @foreach($allnotifications as $allnotification)
                <div class="notification-item read-notification">
                    <div class="notification-title-bar">
                        <h5 class="notification-title">{{ $allnotification->title }}</h5>
                        {{-- <p class="notification-timestamp">{{ $createdAt }}</p> --}}
                    </div>
                    <div class="notification-message">{{ $allnotification->message }}</div>
                </div>
             @endforeach

             </div>
            </div>
        </div>
        </div>

            <div class="col-md-6" style="border: 1px solid #43808d70">
                <div>
                    <div class="row align-items-center py-2" style="background-color: #fff; border-bottom:1px solid #43808d">
                        <div class="col-md-6">
                            <h5 class="mb-0">Timetable</h5>
                        </div>
                        <div class="col-md-6 text-right">
                                <div class="form-group row align-items-center justify-content-end mb-0">
                                    <label class="col-sm-4 mb-0 text-right pr-0">Batch</label>
                                    <div class="col-sm-5">
                                    <select class="form-control form-control-sm mb-0" id="batch_id" name="batch_id" onchange="window.batchid_timetable();">
                                        <option value="">Select</option>
                                        @if( !empty($batches) )
                                        @foreach($batches as $batch)
                                        <option value="{{ $batch->id }}" @if($batch->id == Session::get('current_batch')) selected @endif>{{ $batch->BatchName }}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                    </div>
                                </div>
                            </div>
                            </div>
                            <div class ="row" id ="timetabletabs" style="background-color: #fff;">
                                <div class="tab">
                                        @foreach($squads as $index => $squad)
                                             <button class="tablinks {{ $index == 0 ? 'active' : '' }}"  id = "defaultOpen" onclick="openCity(event, {{$squad->id}})">{{$squad->SquadNumber}}</button>
                                        @endforeach
                                    </div>

                                    @foreach($squads as $squad)
                                      <?php
                                            $Timetables  = App\Models\Timetable::whereDate('timetables.date', '=', $ldate)->where('squad_id', $squad->id)
                                            ->select('activities.name', 'squads.SquadNumber', 'subactivity_id', 'timetables.session_number')
                                            ->leftJoin('squads', 'timetables.squad_id', '=', 'squads.id')
                                            ->leftJoin('activities', 'timetables.activity_id', '=', 'activities.id')
                                            ->orderBy('session_number', 'asc')

                                            ->get();
                                       ?>
                                         <div id="{{$squad->id}}" class="tabcontent">
                                            <h3>{{ isset($Timetables[0]->SquadNumber) ? $Timetables[0]->SquadNumber : '' }}</h3>
                                            @if (isset($Timetables[0]->name) != '')
                                            @foreach ($Timetables as $Timetable)
                                                @if ($Timetable->name != '')
                                                   <p> Session {{ $Timetable->session_number }} : {{ $Timetable->name }}  </p>
                                                @endif
                                             @endforeach
                                             @else
                                                <p>No sessions </p>
                                             @endif
                                        </div>
                                      @endforeach
                                    </div>
                                </div>
        </div>
            </div>
    </div>


</section>

@endsection

@section('scripts')
<script>

   /** -------------------------------------------------------------------
     * Get batch Wise timetable
     * ----------------------------------------------------------------- */
     if (!window.batchid_timetable) {
        window.batchid_timetable = function () {
            var batch_id = $('#batch_id').val();
            $.ajax({
                url: appUrl +'/batch_timetable',
                data: {
                    batch_id: batch_id
                },
                type: "POST",
                beforeSend: function (xhr) {
                    window.loadingScreen("show");
                },
                success: function (rData) {
                    window.loadingScreen("hide");
                    $('#timetabletabs div').remove();
                    $("#timetabletabs").html(rData);


                }
            });
        }
    };

    window.onload = function () {
        var chart = new CanvasJS.Chart("chartContainer",
        {
            theme: "light2",
            title:{
                text: "Monthly Attendace Percentage"
            },
            data: [
            {
                type: "pie",
                showInLegend: true,
                toolTipContent: "{y} - #percent %",
                legendText: "{indexLabel}",
                dataPoints: [
                    {  y: 82, indexLabel: "P" },
                    {  y: 20, indexLabel: "L" },
                    {  y: 22, indexLabel: "NAP" },
                    {  y: 30, indexLabel: "MDO"},
                    {  y: 15, indexLabel: "M" },
                    {  y: 5, indexLabel: "OT"}
                ]
            }
            ]
        });
        chart.render();
    }

    function openCity(evt, cityName) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
          tabcontent[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("tablinks");
        for (i = 0; i < tablinks.length; i++) {
          tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
        document.getElementById(cityName).style.display = "block";
        evt.currentTarget.className += " active";
      }

      // Get the element with id="defaultOpen" and click on it
      document.getElementById("defaultOpen").click();
</script>
@endsection


<style>
.tabcontent{
    padding-left: 15px !important;
}
.title__card{
    background: #43808d;
    padding: 10px;
    border-radius: 5px;
    margin: 10px 0px;
    background-clip: padding-box;
    border: 1px solid rgba(0,0,0,.2);
    border-radius: 0.3rem;
    outline: 0;
    width:100%;
    box-shadow: 1px 1px 4px 2px lightsteelblue;
}
.title__card h3{
    font-size: 14px;
    color:#fff;
}
.title__card p{
    font-size: 10px;
    margin-bottom: 0px;
     color:#fff;
}

.gender h6
{
    font-size: 12px;
     color:#fff;
}
.gender h6:last-child{
    margin:0px;
}
</style>
