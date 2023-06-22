{{-- Extends layout --}}

<?php
$role   = Auth::user()->role;
if($role === 'faculty') {
    $template   = 'layouts.faculty.template';
} else {
    $template   = 'layouts.default';
}
$app_view = session('app_view');
?>
@extends(($app_view) ? 'layouts.pbdash.mobile-template' : $template)

{{-- Content --}}
@section('content')
@if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        <div class="alert alert-danger">
            <p>{{ $message }}</p>
        </div>

    @endif

<section id="addstaff" class="content-wrapper_sub">
      <div class="user_manage">
        <div class="row">
         <div class="col-md-10">
          <h4>Fitness Statistics</h4>
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
                <div class="col-md-2">
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
                    <div class="col-md-2">
                      <div class="form-group">
                        <label>Squad</label>
                        <select name="squad_id" id="squad_id" class="form-control" onchange="window.select_squad_id_changed(this, 'probationer_id');" required>
                            <option value="">Select Squad</option>
                        </select>
                      </div>
                    </div>

                     <div class="col-md-2">
                      <div class="form-group">
                        <label>Fitness Analytics</label>
                        <select name="fitness_id" id="fitness_id" class="form-control">
                            <option value="">Select Fitness Analytics</option>
                            <option value="weight">Weight</option>
                            <option value="bmi">BMI</option>
                            <option value="bodyfat">Body Fat</option>
                            <option value="fitnessscore">Fitness Score</option>
                        </select>
                      </div>
                    </div>

                    <div class="col-md-2">
                      <div class="form-group">
                        <label>Probationers</label>
                        <select name="probationer_id" id="probationer_id" class="form-control" onchange='check_date_required_field(this)'>
                            <option value="">Select Probationer</option>
                        </select>
                      </div>
                    </div>


                     <div class="col-md-3">
                          <label>Date</label>
                          <input type="text" class="form-control" id="datetimerange-input" size="24" style="text-align:center; height: calc(2.25rem + 2px);" required>
                      </div>


                      <div class="iconFieldWidth" style="margin-top: 35px;">
                        <a class="pl-2 pt-1" class="btn formBtn submitBtn"><img src="{{ asset('images/submit.png') }}" width="25" onclick="problist()" /></a>
                         <a class="pl-2 pt-1"><img src="{{ asset('images/download1.png') }}" width="25" onclick="downloadfitness()" /></a>
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

        window.addEventListener("load", function (event) {
            new DateRangePicker('datetimerange-input',
                {
                    opens: 'right',
                    autoApply: true,
                    // showDropdowns: true,
                    //showWeekNumbers: true,
                    ranges :  {
                        'Today': [moment().startOf('day'), moment().endOf('day')],
                        'This Month': [moment().startOf('month').startOf('day'), moment().endOf('month').endOf('day')],
                        'last Month':  [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                        'last 6 Months':  [moment().subtract(6, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                        'This Year': [moment().startOf('year').startOf('day'), moment().endOf('day').endOf('day')],
                    },
                    locale: {
                        format: "DD/MM/YYYY",
                    }
                },
                function (start, end) {
                   // alert(start.format() + " - " + end.format());
                })
        });



function check_date_required_field(el)
{
    var probationer_id = $(el).val();
    if(probationer_id === '')
    {
        $('#month_datepicker').attr('required', true);
    }
    else
    {
       $('#month_datepicker').attr('required', false);
    }

}


google.charts.load('current', {packages: ['corechart', 'line']});
google.charts.setOnLoadCallback();

    function problist()
        {debugger;
           var validate =  $("#reports").validate({
            rules: {
                squad_id: {
                required: true,
            },
            fitness_id: {
                required: true,
            },
            }
        }).form();
        if(validate == true)
        {debugger;

          var temp_title = 'Charts';
            var bid = $('#batch_id').val();
            var sid = $('#squad_id').val();
            var pid = $('#probationer_id').val();
            var fitness_id = $('#fitness_id').val();
            var date = $('#datetimerange-input').val();
            $.ajax({
            url: '/fitnesschart',
            type: "POST",
            dataType: "json",
            data:{
               "_token": "{{ csrf_token() }}",
                "bid":bid,
                "sid":sid,
                "pid":pid,
                "fitness_id":fitness_id,
                "date":date,
            },
            beforeSend: function beforeSend(xhr) {
             window.loadingScreen("show");
            },
            success: function(results){debugger;
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
            {debugger;
               $('#error').empty();
               $("#chart_area").css("display","");
         //      drawMonthwiseChart(results, temp_title);
               drawsingleMonthwiseChart(results, temp_title);
            }
           }
       })
      }
      else
      {
        validate == true;
      }
   }


function drawsingleMonthwiseChart(chart_data, chart_main_title)
{
    var jsonData = chart_data;
    var ticks = [];
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'date');
    data.addColumn('number', 'count');
    $.each(jsonData, function(i, jsonData){

        var date = jsonData.date;
        var count = parseFloat($.trim(jsonData.count));
      //  var ticks = parseFloat($.trim(jsonData.count));
        data.addRows([[date, count]]);

    });
    var options = {
        title:chart_main_title,
        curveType: 'function',
        pointSize: 7,
        hAxis: {
            title: ""
        },
        vAxis: {
            title: 'Value',
          //  ticks: [{v: 'a', f: 'a'}, {v: 'b', f: 'b'},{v: 'c', f: 'c'}, {v: 'd', f: 'd'},{v: 'e', f: 'e'}],
         //   ticks: ticks,
            viewWindow:{
                min:0
              }
        }
    };
    var chart = new google.visualization.ColumnChart(document.getElementById('chart_area'));
    chart.draw(data, options);
}

function downloadfitness()
        {
           var validate =  true;
        if(validate == true)
        {
            var bid = $('#batch_id').val();
            var sid = $('#squad_id').val();
            var pid = $('#probationer_id').val();
            var fitness_id = $('#fitness_id').val();
            var date = $('#datetimerange-input').val();
            $.ajax({
            url: '/fitnessdownload',
            type: "POST",
            data:{
               "_token": "{{ csrf_token() }}",
                "bid":bid,
                "sid":sid,
                "pid":pid,
                "fitness_id":fitness_id,
                "date":date,
            },
            beforeSend: function beforeSend(xhr) {
             window.loadingScreen("show");
            },
            success: function(results){debugger;
                window.loadingScreen("hide");
                var sData = results.replace( /[\r\n]+/gm, "");

                if(sData === '1')
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
               window.location=sData;
            }

           }
       })
      }
   }



</script>
@endsection
