{{-- Extends layout --}}
@extends('layouts.default')

{{-- Content --}}
@section('content')

  <section id="fitnessanalytics" class="content-wrapper_sub tab-content">
            <div class="user_manage mt-0 pt-3">
                <div class="row">
                    <div class="col-md-12">
                        <h4>Fitness Analytics</h4>
                    </div>
                </div>
                <div class="row fitness_section">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="w-25">
                                        <div class="img_center_align">
                                            <img class="rounded-circle" width="110px" src="{{ isset($get_probationer->profile_url) ?  $get_probationer->profile_url : asset('images/profileimage.png') }}">
                                        </div>

                                    </div>
                                    <div class="w-75">
                                        <div class="probationer-details ">
                                              <div class="row">

                                                <label class="col-sm-3">Name :</label>
                                                <div class="col-sm-9">
                                                    <p>{{ $get_probationer->Name }}</p>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                <p >DOB : {{date('d-m-Y', strtotime($get_probationer->Dob))}}</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p >Gender : {{ $get_probationer->gender }}</p>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <label class="col-sm-3">Date :</label>
                                                <div class="col-sm-9"> <div class="input-group" id="datetimepicker" data-target-input="nearest" name="Dob">
                                                <input type="text" class="form-control datetimepicker-input" data-target="#datetimepicker33" data-toggle="datetimepicker" name="Dob" id="month_datepicker" required="" autocomplete="off" onchange="getfitnessdata()">
                                            </div>
                                            <input type="hidden" name="probationer_id" id="probationer_id" value="{{$get_probationer->id}}"></input></div>
                                            </div>


                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Change Probationer</h5>
                                <div class="form-group">
                                    <label for="SquadProbationers"></label>
                                    <select class="form-control" id="SquadProbationers" name="SquadProbationers" onchange="probationerchange(this.value)">
                                        @foreach ($squad_probationers as $squad_probationers)
                                              <option value="{{ $squad_probationers->id }}" {{$squad_probationers->id == $get_probationer->id  ? 'selected' : ''}}>{{ $squad_probationers->Name }}</option>
                                        @endforeach
                                    </select>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="container">
                    <div class="row">
                        <div class="col-md-10"></div>
                        <div class="col-md-2">
                            <div class="form-group mb-0">
                                <select name="fitness_id" id="fitness_id" class="form-control" onchange="fitnessanalytics_change(this.value)">
                                    <option value="weight">Weight</option>
                                    <option value="bmi">BMI</option>
                                    <option value="bodyfat">Body Fat</option>
                                    <option value="fitnessscore">Fitness Score</option>
                                </select>
                            </div>
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
                </div>
                 <hr />
                 <div class="row">
                    <div class="col-md-12">
                    <div class="fitness_data">
                    </div>
                </div>
                </div>
        </section>

@endsection

@section('scripts')
    <script>
    $(document).ready(function() {
        $(document).on("click", ".fitnesseval a.editvalue", function() {
            //
            $(".fitnessinputValue").show();
            $(".fitnesseval span").hide();
            $(this).hide();
            $(".fitnesseval a.cancel").show();
            $(".fitnessevalform .usersubmitBtns").show();
        });



        $(document).on("click", ".fitnesseval a.cancel", function() {
            //
            $(".fitnessinputValue").hide();
            $(".fitnesseval span").show();
            $(".fitnesseval a.editvalue").show();
            $(".fitnesseval a.cancel").hide();
            $(".fitnessevalform .usersubmitBtns").hide();
        });
    });
    function GetMonthName(monthNumber) {
        var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        return months[monthNumber - 1];
    }

    function submitfitness()
    {
        //
        //  var bid = $('#batch_id').val();
        var data = $('#fitness_evaluvation').serializeArray();
        $.ajax({
            url: '/insertfitnessevaluvation',
            type: "POST",
            data:{
                "_token": "{{ csrf_token() }}",
                "data":data,
            },
            success: function(data){
                alert("updated Suceesfully")
                window.location.reload();
            }
        });
    }

    $(document).ready (function (){
      //  var jDates = ["2021-9-21","2021-9-24","2021-9-27","2021-9-28"];
      var jDates = {!! $dt !!};

        var date = new Date();


        $("#month_datepicker").datepicker ({
            format: 'DD/MM/YYYY',
            beforeShowDay: function(date) {
                var m = date.getMonth() + 1, d = (date.getDate() < 10 ? '0' : '') + date.getDate(), y = date.getFullYear();
                var month = m < 10 ? '0' + m : '' + m;
                console.log(y + '-' + (month) + '-' + d);
                for (i = 0; i < jDates.length; i++) {
                    if($.inArray(y + '-' + (month) + '-' + d,jDates) != -1) {
                        //return [false];
                        return [true, 'ui-state-active', ''];
                    }
                }
                return [true];

            }
        });
    });
    var previousValue = $("#month_datepicker").val();

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
                "id":currentValue,
                "pid":pid
            },
            beforeSend: function (xhs) {
                window.loadingScreen("show");
            },
            success: function(rdata){
                window.loadingScreen("hide");

                // console.log(rObj);
                let rObj = JSON.parse(rdata);

                if(rObj.status == "success") {
                    $(':input').not(':button, :submit, :reset, :hidden, :checkbox, :radio').val('');
                    $("#weight").text(rObj.data.weight + " Kgs");
                    $("#bmi").text(rObj.data.bmi + " Kg/m2");
                    $("#bodyfat").text(rObj.data.bodyfat);
                    $("#fitnessscore").text(rObj.data.fitnessscore+ " Total");
                    $("#egrade").text(rObj.data.endurancegrade + " Grade");
                    $("#sgrade").text(rObj.data.strengthgrade + " Grade");
                    $("#fgrade").text(rObj.data.flexibilitygrade + " Grade");
                    {{-- $("#sweight").text(rObj.data.weight);
                    $("#sbmi").text(rObj.data.bmi);
                    $("#sbodyfat").text(rObj.data.bodyfat);
                    $("#sfitnessscore").text(rObj.data.fitnessscore);
                    $("#segrade").text(rObj.data.endurancegrade);
                    $("#ssgrade").text(rObj.data.strengthgrade);
                    $("#sfgrade").text(rObj.data.flexibilitygrade);
                    $("#dsmonth").text(GetMonthName(rObj.data.month));
                    $("#dsyear").text(rObj.data.year); --}}
                } else {
                    alert(rObj.message);
                }
            }
        });
    }


    var results = {!! $output !!};
    var temp_title = "Fitness";

    google.charts.load('current', {
        'callback': function () {
            drawsingleMonthwiseChart(results, temp_title);
        },
        'packages': ['corechart','line'],
        'language': 'es'
    });

    google.charts.setOnLoadCallback();



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

        function probationerchange(id) {
            var url = "{{ url ('/fitnessanalytics/:id')}}";
                url = url.replace(':id', id);
            window.location.href = url;
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
               drawsingleMonthwiseChart(results, temp_title);
            }
           }
       })
   }

    </script>
@endsection
