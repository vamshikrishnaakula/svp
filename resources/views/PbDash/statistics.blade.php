{{-- Extends Pb Dashboard Template --}}
<?php
$app_view = session('app_view');
?>
@extends(($app_view) ? 'layouts.pbdash.mobile-template' : 'layouts.pbdash.template')

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

<section id="user-statistics" class="content-wrapper_sub tab-content">
    <div class="user_manage">
        <div class="row">
            <div class="col-md-6">
                <h4 style="text-align: center;">Reports</h4>
            </div>
        </div>

        <?php

            // echo '<p style="text-align: center;">This page is under development</p>';
            // exit;

            $user = Auth::user();
            // {{--  if($user->role !== "probationer") {
            //     echo '<p style="text-align: center;">This page is under development</p>';
            //     exit;
            // }  --}}

            $user_id = $user->id;
            $batch_id = App\Models\probationer::where('user_id', $user_id)->value('batch_id');
            $activities = App\Models\Activity::where('batch_id', $batch_id)->where('type', 'activity')->get();
            ?>

        <div class="row mt-3">
            <div class="col">
                <form method="POST" id="reports" name="reports" autocomplete="off">
                @csrf
                    <div class="row">
                        <div class="col-md-3 ct-col-6">
                            <div class="form-group">
                                <label>Activity</label>
                                <select name="p_activity_id" id="p_activity_id" class="form-control" onchange="window.get_pbSubactivityOptions(this, 'sub_activity_id'); reset_component(); check_subactivites(this);" required>
                                <option value="">Select Activity</option>
                                @foreach ($activities as $activity)
                                <option value="{{ $activity->id }}" @if(isset($p_activity_id) && $p_activity_id=="{{ $activity->id }}"){{"selected"}} @endif>{{ $activity->name }}</option>
                            @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 ct-col-6">
                            <div class="form-group">
                                <label>Sub Activity</label>
                                <select name="sub_activity_id" id="sub_activity_id" class="form-control" onchange="window.select_sub_activityId_changed(this, 'component');">
                                <option value="">Select Sub  Activity</option>
                            </select>
                            </div>
                        </div>

                        <div class="col-md-3 ct-col-6">
                            <div class="form-group">
                                <label>Component</label>
                                <select name="component" id="component" class="form-control">
                                <option value="">Select Component</option>
                            </select>
                            <input type="hidden" class="form-control" id="probationer_id" name='probationer_id' value={{ $probationer_id }}>
                            </div>
                        </div>

                        <div class="dateFieldWidth ct-col-6">
                            <label>Date</label>
                                <input type="text" class="form-control" id="datetimerange-input" name="date" size="24" style="text-align:center" required>
                            </div>

                        <div class="col-md-1 ct-col-6 report_submit_btn">
                            {{-- <button  class="btn formBtn submitBtn"><img src="{{ asset('images/submit.png') }}" width="25" /></button> --}}
                            <button class="pt-1" type="button" onclick = "reports_submit()" id="check_button"><img src="{{ asset('images/submit.png') }}" width="30" /></button>
                            <a onclick="problist()"><img src="{{ asset('images/download1.png') }}" width="25" /></a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div id="error"></div>

        <div class="row mt-3 heading_stat p-3 text-center">
            <div class="col-md-4">
            </div>
            <div class="col-md-4">
                <p>Activities</p>
            </div>
        </div>
<div class="table-responsive">
        <table class="table table-striped statistictable table-bordered" id="statistics" style="width: 100% !important">
            <thead>

                <tr>
                    <th></th>
                    @if (!empty($dt))
                    @foreach($dt as $date)
                    <th colspan="2">{{ $date }}</th>
                    @endforeach
                    @endif
                </tr>
                <tr>

                    @if (!empty($dt))
                    <th>Probationers Name</th>
                    @foreach($dt as $date)
                    @if($activity_unit->unit == '')
                    <th>No Units</th>
                    @else
                    <th>{{$activity_unit->unit}}</th>
                    @endif
                    <th>GRADES</th>
                    @endforeach
                    @endif
                </tr>
            </thead>
            <tbody>
                @if (!empty($data))
                <tr>
                <td>{{ $probationer_name }}</td>
                @foreach ($data as $date)
                    @foreach ($date as $item)
                      <td>{{ $item }}</td>
                    @endforeach

                @endforeach
                </tr>
                @endif


            </tbody>
        </table>
</div>
    </div>

</section>

<script>

// $(document).ready (function (){
//     $("#staticticsDatetimepicker").datetimepicker ({
//         viewMode: 'months',
//         format: 'MM-YYYY'
//     });
// });

$(document).ready(function () {

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

function reset_component() {

        $("select#component").html('<option value="">Select Component</option>');
        $('#component').attr('required', false);
    }

    function check_subactivites(el) {
        $('#sub_activity_id').attr('required', false);
        var p_activity_id = $(el).val();
        $.ajax({
            url: '/sub_activity_count',
            type: "POST",
            data: {
                "_token": "{{ csrf_token() }}",
                "activity_id": p_activity_id,
            },
            success: function (data) {
                var sData = data.replace( /[\r\n]+/gm, "" );
                if (sData != '0') {
                    $('#sub_activity_id').attr('required', true);
                }
            }
        })
    }

    function check_components(el) {
        $('#component').attr('required', false);
        var sub_activity_id = $(el).val();
        $.ajax({
            url: '/component_count',
            type: "POST",
            data: {
                "_token": "{{ csrf_token() }}",
                "sub_activity_id": sub_activity_id,
            },
            success: function (data) {
                var sData = data.replace( /[\r\n]+/gm, "" );
                if (sData != '0') {
                    $('#component').attr('required', true);
                }
            }
        })
    }

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
            $('#sub_activity_id').attr('required', false);
            $('#component').attr('required', false);
            var p_activity_id = $('#p_activity_id').val();
            var sub_activity_id = $('#sub_activity_id').val();
            var component = $('#component').val();
            var date = $('#datetimerange-input').val();
            $.ajax({
           url: '/single_activity_reports_export',
           type: "POST",
           data:{
               "_token": "{{ csrf_token() }}",
                "activity_id":p_activity_id,
                "sub_activity_id":sub_activity_id,
                "component":component,
                "date":date,
               },
           success: function(data){
            if(data == '1')
            {
                alert("No data Exits");
            }
            else{
                window.location=data;
              // console.log(data);
            }

           }
       })
        }

        }

        function reports_submit() {
        //  e.preventDefault();
        var validate = $("#reports").validate({
            rules: {
                squad_id: {
                    required: true,
                },
                activity_id: {
                    required: true,
                }
            }
        }).form();
        if (validate == true) {
            $('#sub_activity_id').attr('required', false);
            $('#component').attr('required', false);
              var p_activity_id = $('#p_activity_id').val();
              var sub_activity_id = $('#sub_activity_id').val();
              var component = $('#component').val();
              var date = $('#datetimerange-input').val();

              $.ajax({
                  url: appUrl +'/single_activity_reports',
                  type: "POST",
                  data: {
                      "_token": "{{ csrf_token() }}",
                      "activity_id": p_activity_id,
                      "sub_activity_id": sub_activity_id,
                      "component": component,
                      "date": date,
                  },
                  beforeSend: function (xhr) {
                      window.loadingScreen("show");
                  },
                  success: function (data) {
                      window.loadingScreen("hide");
                      var sData = data.replace( /[\r\n]+/gm, "" );
                      if (sData == '1') {
                            $('#error').empty();
                            $('#statistics thead').remove();
                            $('#statistics tbody').remove();
                            var e = $('<div class="alert alert-danger"><p>No data available</p></div>');
                            $('#error').append(e);
                            $("div.alert").fadeTo(2000, 500).slideUp(500, function () {
                            $("div.alert").slideUp(500);
                          });
                      }
                      else {
                          $('#statistics thead tbody').remove();
                          $("#statistics").html(sData);
                      }
                  }
              })
      }
      else
      {
       // $('#sub_activity_id').attr('required', true);
        //$('#component').attr('required', true);
      }
    }


</script>

@endsection
