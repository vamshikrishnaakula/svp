{{-- Extends layout --}}

<?php
$app_view = session('app_view');
?>
@extends(($app_view) ? 'layouts.pbdash.mobile-template' : 'layouts.default')

{{-- Content --}}
@section('content')

@if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
  @elseif ($message = Session::get('delete'))
        <div class="alert alert-danger">
            <p>{{ $message }}</p>
        </div>

    @endif

<section id="addstaff" class="m-0 p-0">
    <div class="container m-0 p-0">
        <div class="chart_mng_sec mt-3">
            <div class="row">
                <div class="col-md-10">
                     <!-- <h4 class="text-center">Charts</h4> -->
                </div>
                <div class="col-md-2">
                    <div class="userBtns">
                    <!-- <a href="#" data-toggle="tooltip" title="import"> <img src="./images/import.png" /></a>
                        <a href="#" data-toggle="tooltip" title="excel"> <img style="width:29px !important"  src="./images/excel.png" /></a> -->
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                     <form id="reports" autocomplete="off">
                @csrf
                @php
                $batches = DB::table('batches')->get();
                $user = Auth::user();
                $user_id = $user->id;

            @endphp
            <div class="row">

                    <div class="col-md-4">
                        <div class="form-group mb-4">
                          <label>Activity</label>
                          <select name="activity_id1" id="activity_id1" class="form-control" onchange="window.select_activityId_changed(this, 'sub_activity_id'); reset_component(); check_subactivites(this);">
                            <option value="">Select Activity</option>
                            @foreach ($activities as $activity)
                            <option value="{{ $activity->id }}">{{ $activity->name }}</option>
                            @endforeach
                        </select>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="form-group mb-4">
                          <label>Sub Activity</label>
                          <select name="sub_activity_id" id="sub_activity_id" class="form-control" onchange="window.select_sub_activityId_changed(this, 'component'); check_components(this); reset_component();">
                            <option value="">Select Sub  Activity</option>
                        </select>
                        </div>
                      </div>

                      <div class="col-md-4">
                        <div class="form-group mb-4">
                          <label>Component</label>
                          <select name="component" id="component" class="form-control">
                            <option value="">Select Component</option>
                        </select>
                        </div>
                      </div>

                      @if ($squad_id != '')
                           <input type="hidden" class="form-control" id="squad_id" name='squad_id' value={{ $squad_id }}>
                           <input type="hidden" class="form-control" id="probationer_id" name='probationer_id' value=''>
                        @else
                        <input type="hidden" class="form-control" id="probationer_id" name='probationer_id' value={{ $probationer_id }}>
                      @endif




                      {{--  <div class="col">
                          <label>Date</label>
                        <div class="input-group" id="charts_datetimepicker" data-target-input="nearest" name="date">
                            <input type="text" class="form-control datetimepicker-input"
                                data-target="#charts_datetimepicker"  data-toggle="datetimepicker" name="date" id="month_datepicker"/>

                        </div>
                      </div>  --}}
                      <div class="col-md-12 ">
                          <div class="text-right mt-2">
                        <a class="pl-2 pt-1" class="btn formBtn submitBtn"><img src="{{ asset('images/submit.png') }}" width="30" onclick="problist()" /></a>
                          </div>

                    </div>

            </div>

        </form>


                </div>
            </div>
<div class="row">
    <div class="col-md-12">
        <div id="error"></div>
    </div>
        </div>
    </div>



<!-- <div id="test" style="height:600px; width:600px;">
    <canvas id="lineChart" style="border: 1px solid black; margin: 25px 25px" height="300">Canvas</canvas>
</div> -->

<div class="chart_block_mb">
    <div class="panel-body chart_section_mb">
        <div id="chart_area"></div>
    </div>
</div>
</section>
@endsection



@section('scripts')
    <script>
$(document).ready (function (){
    $("#charts_datetimepicker").datetimepicker ({
            viewMode: 'months',
            format: 'MM/YYYY'
        });
});

function reset_component(){
        $("select#component").html('<option value="">Select Component</option>');
        $('#component').attr('required', false);
    }

function check_subactivites(el)
{
    $('#sub_activity_id').attr('required', false);
    var activity_id = $(el).val();
    $.ajax({
           url: '/sub_activity_count',
           type: "POST",
           data:{
               "_token": "{{ csrf_token() }}",
                "activity_id":activity_id,
               },
           success: function(data){
            var sData = data.replace( /[\r\n]+/gm, "" );
                if (sData != '0') {
                    $('#sub_activity_id').attr('required', true);
                }
           }
       })
}

function check_components(el)
{
    $('#component').attr('required', false);
    var sub_activity_id = $(el).val();
    $.ajax({
           url: '/component_count',
           type: "POST",
           data:{
               "_token": "{{ csrf_token() }}",
                "sub_activity_id":sub_activity_id,
               },
           success: function(data){
            var sData = data.replace( /[\r\n]+/gm, "" );
            if (sData != '0') {
                $('#component').attr('required', true);
            }
           }
       })
}

{{--  google.charts.load('current', {packages: ['corechart', 'line']});
google.charts.setOnLoadCallback();  --}}


    function problist()
        {
           var validate =  $("#reports").validate({
            rules: {
                squad_id: {
                required: true,
            },
            activity_id1: {
                required: true,
            }
            }
        }).form();
        if(validate == true)
        {
          var temp_title = 'Charts';
            var sid = $('#squad_id').val();
            var activity_id = $('#activity_id1').val();
            var sub_activity_id = $('#sub_activity_id').val();
            var component = $('#component').val();
            var date = $('#month_datepicker').val();
            var pid = $('#probationer_id').val();
            if(pid == '')
            {
            $.ajax({
            url: '/probationerchart',
            type: "POST",
            dataType: "json",
            data:{
               "_token": "{{ csrf_token() }}",
                "sid":sid,
                "pid":pid,
                "activity_id":activity_id,
                "sub_activity_id":sub_activity_id,
                "component":component,
                "date":date,
            },
            beforeSend: function beforeSend(xhr) {
            window.loadingScreen("show");
            },
            success: function(results){
              window.loadingScreen("hide");
              if(results == '1')
               {
                $('#error').empty();
                var e = $('<div class="alert alert-danger"><p>No data avaliable</p></div>');
                $('#error').append(e);
                $("div.alert").fadeTo(2000, 500).slideUp(500, function(){
                $("div.alert").slideUp(500);
             });
            }
            else
            {
               $('#error').empty();
               google.charts.load('current', {
                'callback': function () {
                    drawMonthwiseChart(results, temp_title);
                },
                'packages': ['corechart','line'],
                'language': 'es'
            });
            google.charts.setOnLoadCallback();
            }
           }
       })
            }
            else
            {
              $.ajax({
            url: '/probationersinglechart',
            type: "POST",
            dataType: "json",
            data:{
               "_token": "{{ csrf_token() }}",
                "sid":sid,
                "pid":pid,
                "activity_id":activity_id,
                "sub_activity_id":sub_activity_id,
                "component":component,
                "date":date,
               },
               beforeSend: function beforeSend(xhr) {
                window.loadingScreen("show");
              },
            success: function(results){
            window.loadingScreen("hide");
             if(results == '1')
             {
                $('#error').empty();
                $("#chart_area").css("display","none");
                var e = $('<div class="alert alert-danger"><p>No data avaliable</p></div>');
                $('#error').append(e);
                $("div.alert").fadeTo(2000, 500).slideUp(500, function(){
                $("div.alert").slideUp(500);
            });
            }
            else
            {
                $("#chart_area").css("display","");
               google.charts.load('current', {
                'callback': function () {
                    drawsingleMonthwiseChart(results, temp_title);
                },
                'packages': ['corechart','line'],
                'language': 'es'
            });
            google.charts.setOnLoadCallback();
            }
           }
       })
            }
      }
   }

   function drawMonthwiseChart(chart_data, chart_main_title)
{
    var jsonData = chart_data;
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'date');
 //   data.addColumn('number', 'count');
  //  data.addColumn('number', 'count');

    $.each(jsonData['0']['probationer'], function(i, nData){
       data.addColumn('number', nData.Name);
    });
    $.each(jsonData, function(i, jsonData){

        var date = jsonData.date;
        var count = jsonData.count;
        var items = jsonData.count.split(',');

        var addunit = [date];
        //addunit = addunit.concat(items);
        $.each(items, function (i, items) {
          addunit.push( parseFloat($.trim(items)) );
        });
        data.addRows([addunit]);

    });
    var options = {
        title:chart_main_title,
        curveType: 'function',
        pointSize: 7,
        dataOpacity: 0.3,
        hAxis: {
            title: "Date"
        },
        vAxis: {
            title: 'Count',

            viewWindow:{
                min:0
              }
        }
    };
    var chart = new google.visualization.LineChart(document.getElementById('chart_area'));
    chart.draw(data, options);
}

function drawsingleMonthwiseChart(chart_data, chart_main_title)
{
    var jsonData = chart_data;
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'date');
    data.addColumn('number', 'count');
    $.each(jsonData, function(i, jsonData){

        var date = jsonData.date;
        var count = parseFloat($.trim(jsonData.count));
        data.addRows([[date, count]]);

    });
    var options = {
        title:chart_main_title,
        curveType: 'function',
        pointSize: 7,
        dataOpacity: 0.3,
        hAxis: {
            title: "Date"
        },
        vAxis: {
            title: 'Count',

            viewWindow:{
                min:0
              }
        }
    };

    var chart = new google.visualization.LineChart(document.getElementById('chart_area'));
    chart.draw(data, options);
}


</script>
@endsection


<style>
  @media screen and (min-width:320px) and (max-width:767px){
    .chart_mng_sec{
        background-color:#fff;
        padding:15px;
        width:100%;
        border-radius:10px;
        position: relative;
        left:-25px;
        top:20px;
    }
    .chart_block_mb{
        position: relative;
        left:-25px;
        top:25px;
    }
    .chart_section_mb{
        position: absolute;
        right:0px;
        left:0px;
        top:25px;
        margin:auto;
    }
}
</style>

