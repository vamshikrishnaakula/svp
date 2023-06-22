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
<section id="addstaff" class="content-wrapper_sub">
      <div class="user_manage">
        <div class="row">
         <div class="col-md-10">
          <h4>Charts</h4>
        </div>
            <div class="col-md-2">
                <div class="userBtns">
                <!-- <a href="#" data-toggle="tooltip" title="import"> <img src="./images/import.png" /></a>
                    <a href="#" data-toggle="tooltip" title="excel"> <img style="width:29px !important"  src="./images/excel.png" /></a> -->
                </div>
            </div>
      </div>
      <div class="row mt-5">
    <div class="col">
        <form id="reports" autocomplete="off">
        @csrf
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label>Batch</label>
                        <select class="form-control" id="batch_id" name="batch_id" onchange="window.select_batchId_changed(this, 'squad_id');" required>
                <option value="">Select Batch</option>
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
                        <label>Squad</label>
                        <select name="squad_id" id="squad_id" class="form-control" onchange="window.select_squad_id_changed(this, 'probationer_id');" required>
                            <option value="">Select Squad</option>
                        </select>
                      </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                          <label>Activity</label>
                          <select name="activity_id" id="activity_id" class="form-control" onchange="window.select_activityId_changed(this, 'sub_activity_id'); reset_component(); check_subactivites(this);">
                            <option value="">Select Activity</option>
                        </select>
                        </div>
                      </div>
                      <div class="col">
                        <div class="form-group">
                          <label>Sub Activity</label>
                          <select name="sub_activity_id" id="sub_activity_id" class="form-control" onchange="window.select_sub_activityId_changed(this, 'component'); check_components(this); reset_component();">
                            <option value="">Select Sub  Activity</option>
                        </select>
                        </div>
                      </div>

                      <div class="col">
                        <div class="form-group">
                          <label>Component</label>
                          <select name="component" id="component" class="form-control">
                            <option value="">Select Component</option>
                        </select>
                        </div>
                      </div>

                       <div class="col">
                      <div class="form-group">
                        <label>Probationers</label>
                        <select name="probationer_id" id="probationer_id" class="form-control" required>
                            <option value="">Select Probationer</option>
                        </select>
                      </div>
                    </div>

                      {{--  <div class="col">
                          <label>Date</label>
                        <div class="input-group" id="charts_datetimepicker" data-target-input="nearest" name="date">
                            <input type="text" class="form-control datetimepicker-input"
                                data-target="#charts_datetimepicker"  data-toggle="datetimepicker" name="date" id="month_datepicker"/>

                        </div>
                      </div>  --}}
                      <div class="col" style="margin-top: 35px;">
                        <a class="pl-2 pt-1" class="btn formBtn submitBtn"><img src="{{ asset('images/submit.png') }}" width="25" onclick="problist()" /></a>
                        <!-- <a class="pl-2 pt-1"><img src="{{ asset('images/download1.png') }}" width="25" /></a> -->
                      </div>
            </div>
        </form>
    </div>

  </div>
  <div id="error"></div>
<!-- <div id="test" style="height:600px; width:600px;">
    <canvas id="lineChart" style="border: 1px solid black; margin: 25px 25px" height="300">Canvas</canvas>
</div> -->
{{--  <div id="chart">
  <canvas id="myChart" height="300px" width="800px"></canvas>
</div>  --}}
  <div class="panel-body">
                    <div id="chart_area" style="width: 1200px; height: 400px;"></div>
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
            if(sData != '0')
            {
                $('#component').attr('required', true);
            }
           }
       })
}

google.charts.load('current', {packages: ['corechart', 'line']});
google.charts.setOnLoadCallback();

    function problist()
        {
           var validate =  $("#reports").validate({
            rules: {
                squad_id: {
                required: true,
            },
            activity_id: {
                required: true,
            }
            }
        }).form();
        if(validate == true)
        {
          var temp_title = 'Charts';
            var bid = $('#batch_id').val();
            var sid = $('#squad_id').val();
            var pid = $('#probationer_id').val();
            var activity_id = $('#activity_id').val();
            var sub_activity_id = $('#sub_activity_id').val();
            var component = $('#component').val();
            var date = $('#month_datepicker').val();
            if(pid == '')
            {
            $.ajax({
            url: '/probationerchart',
            type: "POST",
            dataType: "json",
            data:{
               "_token": "{{ csrf_token() }}",
                "bid":bid,
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
               $('#error').empty();
               $("#chart_area").css("display","");
               drawMonthwiseChart(results, temp_title);
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
                "bid":bid,
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
               drawsingleMonthwiseChart(results, temp_title);
            }
           }
       })
            }
      }
   }

   function drawMonthwiseChart(chart_data, chart_main_title)
{
    var jsonData = chart_data;
    var chart = new google.visualization.LineChart(document.getElementById('chart_area'));
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
            title: chart_main_title,

            viewWindow:{
                min:0
              }
        }
    };

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
        var unit = jsonData.unit;
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
            title: '',

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
