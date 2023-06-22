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

<section id="fitnessevaluvation" class="content-wrapper_sub">
    <div class="user_manage">
        <div class="row">
            <div class="col-md-8">
                <h4>Fitness Analytics</h4>
            </div>

            <div class="col-md-4">
                @if ($role != 'faculty')
                <div class="useractionBtns d-flex justify-content-end">
                    <a href="#" onclick="window.get_fitnessData_import_modal()" data-toggle="tooltip" title="Import Fitness Analytics"> <img src="{{ asset('images/import.png') }}" /></a>
                </div>
                @endif

            </div>
        </div>
        <form class="width-two-third rl-margin-auto" autocomplete="off">
            <div class="row mt-5">
                <div class="col-md-4">
                    <label>Select Batch</label>
                    <select class="form-control" id="batch_id" name="batch_id" onchange="window.select_batchId_changed(this, 'squad_id');" required>
                        <option value="">Select Batch</option>
                        @if( !empty($batches) )
                        @foreach($batches as $batch)
                        <option value="{{ $batch->id }}" @if($batch->id == Session::get('current_batch')) selected @endif>{{ $batch->BatchName }}</option>
                        @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Select Squad</label>
                    <select name="squad_id" id="squad_id" class="form-control req" onchange="window.select_squad_id_changed(this, 'probationer_id');" required>
                        <option value="">Select Squad</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                      <label>Probationers</label>
                      <select name="probationer_id" id="probationer_id" class="form-control">
                          <option value="">Select Probationer</option>
                      </select>
                    </div>
                  </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="usersubmitBtns mt-5">
                        <div class="mr-4">
                            <button type="button" class="btn formBtn submitBtn" onclick="window.fitness_analysis()">Submit</button>
                        </div>
                    </div>
                </div>
            </div>

            <div id="error"></div>
        </form>
        <div class="squadlisthead mt-5" style="display: none">
            <div class="row">
                <div class="col-md-9">
                    <div class="activityhead">
                        <h5 class="mb-0 ml-4">Probationer List</h5>
                    </div>
                </div>
                <div class="col-md-3 patient_userBtns">

                    <!-- <a href="#" data-toggle="tooltip" title="download"><img src="{{ asset('images/download1.png') }}" />
                      </a>

                      <a href="#" data-toggle="tooltip" title="print"><img src="{{ asset('images/print1.png') }}" />
                      </a> -->
                </div>
            </div>
        </div>
        <div class="row fitnessdrp">
                <div class="col-md-8">

                </div>
                <div class="col-md-4  algin-items-center d-flex">
                    <div class="input-group" id="datetimepicker" data-target-input="nearest" name="Dob">
                        <input type="text" class="form-control datetimepicker-input" data-target="#datetimepicker33" data-toggle="datetimepicker" name="Dob" id="month_datepicker" placeholder="Select Fitness Date" required="" autocomplete="off" onchange="getfitnessdata()">
                    </div>

                        <select name="fitness_id" id="fitness_id" class="form-control mb-0 ml-4" onchange="fitnessanalytics_change(this.value)">
                            <option value="weight">Weight</option>
                            <option value="bmi">BMI</option>
                            <option value="bodyfat">Body Fat</option>
                            <option value="fitnessscore">Fitness Score</option>
                        </select>
                </div>
        </div>


        <div class="row">
            <div class="col-md-12">
                 <div class="graph">
                    <div class="panel-body">
                        <div id="chart_area" style="height: 300px;"></div>
                    </div>
                 </div>
            </div>
        </div>
        <hr />
        <div class="row">
           <div class="col-md-12">
              <div class="fitness_data">
                </div>
           </div>
       </div>
    </div>
</section>

@endsection

@section('scripts')
<script>

    function problist(Bid, Sid)
        {
            var validate =  $("#viewprobationer_fitness").validate({
            rules: {
                batch_id: {
                required: true,
            },
            squad_id: {
                required: true,
            }
            }
        }).form();
        if(validate == true)
        {
          //  var bid = $('#batch_id').val();
            var sid = $('#squad_id').val();
            $.ajax({
           url: '/squads/view-probationers',
           type: "POST",
           data:{
               "_token": "{{ csrf_token() }}",
                "id":sid,
               },
           success: function(data){
             var x=1;
             $(".squadlisthead").show();
             $("#probationerslist thead").empty();
             $("#probationerslist tbody").empty();
             $('#probationerslist thead').append('<tr><th>S.No</th><th>Roll Number</th><th>Name</th><th></th></tr>');
             if(data != '')
             {
             $.each(data, function(i) {
                var url = "{{ url ('/fitnessanalytics/:id')}}";
                url = url.replace(':id', data[i].id);

                   $('#probationerslist tbody').append('<tr id = "id'+data[i].RollNumber+'"><td>'+ x++ +'</td><td>' + data[i].RollNumber + '</td><td>' + data[i].Name + '</td><td><a href='+ url +'><img src="/images/view.png" /></a></td></tr>');
               });
             }
           }
       })
        }
        }


        /** -------------------------------------------------------------------
     * Get fitness latest data and graph
     * ----------------------------------------------------------------- */
     if (!window.fitness_analysis) {
        window.fitness_analysis = function () {
            var squad_id = $('#squad_id').val();
            var probationer_id = $('#probationer_id').val();
            $.ajax({
                type: "POST",
                url: appUrl +'/fitnessanalytics_prob',
                data: {
                    "_token": "{{ csrf_token() }}",
                    "squad_id": squad_id,
                    "id": probationer_id,
                },

                beforeSend: function (xhr) {
                    window.loadingScreen("show");
                },
                success: function (rData) {
                    window.loadingScreen("hide");
                    var temp_title = "Fitness";
                   // console.log(rData['gData'].length);

                    if(rData['gData'].length !== 0)
                    {
                        $("#chart_area").css("display","");
                        $('#error').empty();
                    google.charts.load('current', {
                        'callback': function () {
                            drawsingleMonthwiseChart(rData['gData'], temp_title);
                        },
                        'packages': ['corechart','line'],
                        'language': 'es'
                    });
                    google.charts.setOnLoadCallback();

                    $(".fitness_data").html(rData['fData']);
                    $(".fitnessdrp").css({"visibility":"visible"});

                    var date = new Date();
                    $("#month_datepicker").datepicker ({
                        dateFormat: 'dd-mm-yy',
                        beforeShowDay: function(date) {
                            var m = date.getMonth() + 1, d = (date.getDate() < 10 ? '0' : '') + date.getDate(), y = date.getFullYear();
                            var month = m < 10 ? '0' + m : '' + m;
                            for (i = 0; i < rData['dDates'].length; i++) {
                                if($.inArray(y + '-' + (month) + '-' + d,rData['dDates']) != -1) {
                                    //return [false];
                                    return [true, 'ui-state-active', ''];
                                }
                            }
                            return [true];
                        }
                    });
                }
                else
                {
                   // location.reload();
                   $(".fitness_data").empty();
                   $(".fitness_data").empty();
                   $("#chart_area").css("display","none");
                    $('#error').empty();
                    var e = $('<div class="alert alert-danger"><p>No data available</p></div>');
                    $('#error').append(e);
                }
                    }
            });
        }
    };


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
                    title: '',
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

        function fitnessanalytics_change(name)
        {
          var temp_title = 'Charts';
          var id = $('#probationer_id').val();
            $.ajax({
            url: '/fitnesswisechart',
            type: "POST",
            data:{
               "_token": "{{ csrf_token() }}",
                "name":name,
                "id":id,
            },
            beforeSend: function beforeSend(xhr) {
            // window.loadingScreen("show");
            },
            success: function(results){
              window.loadingScreen("hide");
              if(results == '1')
               {
                var chart = new google.visualization.ColumnChart(document.getElementById('chart_area'));
                chart.clear();
              //  $('#chart_area').clear();
                var e = $('<div class="alert alert-danger"><p>No data avaliable</p></div>');
                $('#error').append(e);
                $("div.alert").fadeTo(2000, 500).slideUp(500, function(){
                $("div.alert").slideUp(500);
             });
            }
            else
            {
               $('#error').empty();

               var temp_title = "Fitness";
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

   function getfitnessdata()
    {
        //
        var currentValue = $('#month_datepicker').val();
        var pid = $('#probationer_id').val();
        $.ajax({
            url: '/prob_month_fitness',
            type: "POST",
            data:{
                "_token": "{{ csrf_token() }}",
                "date":currentValue,
                "pid":pid
            },
            beforeSend: function (xhs) {
                window.loadingScreen("show");
            },
            success: function(rdata){
                window.loadingScreen("hide");

                console.log(rdata);

               // let rObj = JSON.parse(rdata);
               // if(rObj.status == "success") {
                    {{--  $("#weight").text(rObj.data.fitness_value + " Kgs");
                    $("#bmi").text(rObj.data.fitness_value + " Kg/m2");
                    $("#bodyfat").text(rObj.data.fitness_value);
                    $("#fitnessscore").text(rObj.data.fitness_value+ " Total");
                    $("#egrade").text(rObj.data.fitness_value + " Grade");
                    $("#sgrade").text(rObj.data.fitness_value + " Grade");
                    $("#fgrade").text(rObj.data.fitness_value);  --}}

                    $(".fitness_child").remove();
                    $(".fitness_data").html(rdata);

                {{--  } else {
                    alert(rObj.message);
                }  --}}
            }
        });
    }






</script>
@endsection
