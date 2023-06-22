{{-- Extends Pb Dashboard Template --}}
<?php
$app_view = session('app_view');
?>
@extends(($app_view) ? 'layouts.pbdash.mobile-template' : 'layouts.pbdash.template')

{{-- Content --}}
@section('content')
<h4 class="myTr_title">View Targets</h4>
<section id="viewmytarget" class="">
    <div class="view_activity_sec">
        <div class="row ">
            <div class="col-md-6">
                <!-- <h4 style="text-align: center;">View Targets</h4> -->
            </div>
        </div>

        <?php
        $user = Auth::user();
        $user_id = $user->id;
        $probationer_id = probationer_id($user_id);
        $batch_id = App\Models\probationer::where('id', $probationer_id)->value('batch_id');
        $activities = App\Models\Activity::where('batch_id', $batch_id)->where('type', 'activity')->get();

        ?>

        <div id="get_mytarget_form" class="container">
            <div class="row justify-content-sm-center">
                <div class="col-md-3 mb-0">
                    <div class="form-group">
                        <label for="activity">Activity</label>
                        <select name="pb_activity_id" id="pb_activity_id" class="form-control reqField" onchange="window.get_pbSubactivityOptions(this, 'sub_activity_id');" required>
                            <option value="">Select Activity</option>
                                @foreach ($activities as $activity)
                                    <option value="{{ $activity->id }}">{{ $activity->name }}</option>
                                @endforeach
                        </select>
                    </div>
                </div>
                {{-- <div class="col-md-3 mb-0">
                    <div class="form-group">
                        <label for="activity">Select Month</label>
                        <div class="input-group" id="insert_datetimepicker" data-target-input="nearest" name="Dob">
                            <input type="text" class="p-2 form-control datetimepicker-input reqField" data-target="#insert_datetimepicker"
                                data-toggle="datetimepicker" name="Dob" id="activity_month" autocomplete="off" required readonly="true"/>
                        </div>
                    </div>
                </div> --}}
                <div class="col-12 col-md-1">
                    <div class="viewtargetsubmit" style="margin-top: 35px">
                        <a href="#" class="desktop-button" onclick="window.get_mytarget_data()"><img src="{{ asset('images/submit.png') }}" /></a>
                        <a href="#" class="btn btn-success btn-sm mobile-button" onclick="window.get_mytarget_data()">Submit</a>
                    </div>
                </div>
            </div>
        </div>



        <div id="mytarget_data"></div>

        <div class="mytarget-viewtable table-responsive">
            <table id="mytarget_table" class="table table-bordered table-striped mt-3 text-center">
                <thead>
                    <tr>
                        <th scope="col">Activity</th>
                        <th scope="col">Sub Activity</th>
                        <th scope="col">Component</th>
                        <th scope="col">Date</th>
                        <th scope="col">Your Goal</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>
<script>

$(document).ready (function (){
    $("#insert_datetimepicker").datetimepicker ({
        viewMode: 'months',
        format: 'MM-YYYY',
        ignoreReadonly: true
    });

});



</script>
@endsection

<style>
    .view_activity_sec{
        position: relative;
        top:25px;
        left:-25px;
        background:#fff;
        border-radius:15px;
        padding:15px;
    }
    @media screen and (min-width:320px) and (max-width:767px){


#subwrapper {
    padding-left: 0px !important;
    margin-top: 10px;
}
#subwrapper .myTr_title{
    display: none;
}
.view_activity_sec{
    top:0px !important;
    left: 0px !important;
}

}
</style>
