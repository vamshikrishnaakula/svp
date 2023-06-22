{{-- Extends layout --}}
{{-- @extends('layouts.default') --}}
<?php
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
</div>
@elseif ($message = Session::get('delete'))
<div class="alert alert-danger">
    <p>{{ $message }}</p>
</div>

@endif
<section id="grades" class="content-wrapper_sub">
    <div class="user_manage">
        <div class="row">
            <div class="col-md-9">
                <h4>Reports</h4>
            </div>
            <div class="col-md-3">
                <div class="userBtns" style="justify-content: flex-end;">
                    {{-- <a href="#" data-toggle="tooltip" title="import"> <img src="{{ asset('images/download1.png') }}" /></a>
                    <a href="#" data-toggle="tooltip" title="excel"> <img style="width:29px !important" src="{{ asset('images/print1.png') }}" /></a> --}}
                    <?php
                        $user = Auth::user()->role;
                    ?>

                        @if($user !== "faculty")
                            <a href="#" onclick="window.getImportDataBtn(); return false;" data-toggle="tooltip" title="Import data"><img src="{{ asset('images/import.png') }}" /></a>
                        @endif

                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col">
                <form id="reports" name="reports" autocomplete="off">
                    <div class="row">
                        <div class="col-md-2 ct-col-6">
                            <div class="form-group">
                                <label>Batch</label>
                                <select class="form-control" id="batch_id" name="batch_id" onchange="window.select_batchId_changed(this, 'squad_id');" required>
                                    <option value="">Select Batch</option>
                                    @if( !empty($batches) )
                                    @foreach($batches as $batch)
                                    <option value="{{ $batch->id }}" {{$batch->id == Session::get('current_batch')  ? 'selected' : ''}}>{{ $batch->BatchName }}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 ct-col-6">
                            <div class="form-group">
                                <label>Squad</label>
                                <select name="squad_id" id="squad_id" class="form-control" required>
                                    <option value="">Select Squad</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 ct-col-6">
                            <div class="form-group">
                                <label>Activity</label>
                                <select name="activity_id" id="activity_id" class="form-control" onchange="window.select_activityId_changed(this, 'sub_activity_id'); reset_component();" required>
                                    <option value="">Select Activity</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2 ct-col-6">
                            <div class="form-group">
                                <label>Sub Activity</label>
                                <select name="sub_activity_id" id="sub_activity_id" class="form-control" onchange="window.select_sub_activityId_changed(this, 'component');">
                                    <option value="">Select Sub Activity</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2 ct-col-6">
                            <div class="form-group">
                                <label>Component</label>
                                <select name="component" id="component" class="form-control">
                                    <option value="">Select Component</option>
                                </select>
                            </div>
                        </div>

                        <div class="dateFieldWidth ct-col-6">
                            <label>Date</label>
                                <input type="text" class="form-control" id="datetimerange-input" name="date" size="24" style="text-align:center" required>
                            </div>
                        </div>
                        <div class="ct-col-6 report_submit_btn" style="justify-content:end;">
                            <button class="pt-1" type="button" onclick = "reports_submit()" id="check_button"><img src="{{ asset('images/submit.png') }}" width="30" /></button>
                            <a class="pt-1" onclick="problist()"><img src="{{ asset('images/download1.png') }}" width="25" /></a>
                        </div>
                    </div>
                </form>
            </div>
            <div class="row mt-2">
                <div class="col-md-6">

                <div id="error"></div>
                </div>
            </div>
        </div>

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
                    @foreach ($data as $date)
                    <tr>
                        <td>{{ $date[0]->Name }}</td>
                        @foreach ($date as $item)
                            <td>{{ $item->count }}</td>
                            <td>{{ $item->grade }}</td>
                        @endforeach
                    </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</section>

@endsection
@section('scripts')
<script>
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
        var activity_id = $(el).val();
        $.ajax({
            url: '/sub_activity_count',
            type: "POST",
            data: {
                "_token": "{{ csrf_token() }}",
                "activity_id": activity_id,
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
                   // $('#component').attr('required', true);
                }
            }
        })
    }
    function problist() {
        $('#sub_activity_id').attr('required', false);
        $('#component').attr('required', false);
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
            var bid = $('#batch_id').val();
            var sid = $('#squad_id').val();
            var activity_id = $('#activity_id').val();
            var sub_activity_id = $('#sub_activity_id').val();
            var component = $('#component').val();
            var date = $('#datetimerange-input').val();
            var requestName = "request_data_download";
            $.ajax({
                url: '/statistics/ajax',
                type: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "id": bid,
                    "sid": sid,
                    "activity_id": activity_id,
                    "sub_activity_id": sub_activity_id,
                    "component": component,
                    "date": date,
                    "requestName":requestName,
                },
                beforeSend: function (xhr) {
                    window.loadingScreen("show");
                },
                success: function (data) {
                    window.loadingScreen("hide");

                    var sData = data.replace( /[\r\n]+/gm, "" );
                    console.log(sData);
                    let rObj = JSON.parse(sData);

                    if (rObj.status == "success") {
                        // statusDiv.html(rObj.data);

                        // window.saveCsvData(window.jsonObjectToCSV(rObj.data), rObj.file_name);
                        let newTab = window.open(rObj.datasheet_url);
                      //  $('#sub_activity_id').attr('required', true);
                       // $('#component').attr('required', true);

                    } else {
                        $('#error').empty();
                        var e = $('<div class="alert alert-danger"><p>No data available</p></div>');
                         $('#error').append(e);
                            $("div.alert").fadeTo(2000, 500).slideUp(500, function () {
                            $("div.alert").slideUp(500);
                        });
                    }
                }
            })
        }

    }

    function reports_submit() {
        // preventDefault();
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
              var bid = $('#batch_id').val();
              var sid = $('#squad_id').val();
              var activity_id = $('#activity_id').val();
              var sub_activity_id = $('#sub_activity_id').val();
              var component = $('#component').val();
              var date = $('#datetimerange-input').val();

              $.ajax({
                  url: appUrl +'/report_single_activity_view',
                  type: "POST",
                  data: {
                      "_token": "{{ csrf_token() }}",
                      "batch_id": bid,
                      "squad_id": sid,
                      "activity_id": activity_id,
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

    // if($user === "faculty")
    // {
    //     $("#importbtnhide").hide();
    // }

</script>
<script src="{{ asset('/js/statistics.js') }}"></script>
@endsection
