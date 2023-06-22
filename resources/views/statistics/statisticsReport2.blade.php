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
<div id="error"></div>
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
        <div class="col-md-10">
          <h4>Monthly Reports</h4>
        </div>
        <div class="col-md-2">
            <!-- <div class="userBtns">
                <a href="#" data-toggle="tooltip" title="import"> <img src="{{ asset('images/download1.png') }}" /></a>
                    <a href="#" data-toggle="tooltip" title="excel"> <img style="width:29px !important"  src="{{ asset('images/print1.png') }}" /></a>
                </div> -->
        </div>
  </div>
  <div class="row mt-3">
    <div class="col">
        <form action="{{ url('report_monthly_activity_view') }}" method="POST" id="reports" name="reports" autocomplete="off">
        @csrf
            <div class="row">
                <div class="col-md-2 ct-col-6">
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
                          <select name="activity_id" id="activity_id" class="form-control" onchange="window.select_activityId_changed(this, 'sub_activity_id');" >
                            <option value="">Select Activity</option>
                        </select>
                        </div>
                      </div>
                      <div class="col-md-2 ct-col-6">
                        <div class="form-group">
                          <label>Sub Activity</label>
                          <select name="sub_activity_id" id="sub_activity_id" class="form-control">
                            <option value="">Select Sub  Activity</option>
                        </select>
                        </div>
                      </div>

                      <!-- <div class="col">
                        <div class="form-group">
                          <label>Component</label>
                          <select name="component" id="component" class="form-control">
                            <option value="">Select Component</option>
                        </select>
                        </div>
                      </div> -->

                      <div class="col-md-2 ct-col-6">
                          <label>Date</label>
                        {{--  <div class="input-group" id="datetimepicker44" data-target-input="nearest" name="date">
                            <input type="text" class="form-control datetimepicker-input"
                                data-target="#datetimepicker44"  data-toggle="datetimepicker" name="date" id="month_datepicker" required/>  --}}

                                <input type="text" id="datetimerange-input" name="date" size="24" style="text-align:center" required>

                        </div>
                      </div>
                      <div class="col-md-2 ct-col-6 report_submit_btn" style="justify-content: flex-start;">
                        <button class="btn formBtn submitBtn" style="width:auto;"><img src="{{ asset('images/submit.png') }}" width="25"/></button>
                        <a onclick="exportdata()"><img src="{{ asset('images/download1.png') }}" width="25" /></a>
                      </div>
            </div>
        </form>
    </div>

  </div>

  @php
  function replacewithgraade($template)
    {
        switch ($template) {
            case "5":
              echo "A";
              break;
            case "4":
              echo "B";
              break;
            case "3":
              echo "C";
              break;
            case "2":
              echo "D";
              break;
            case "1":
              echo "E";
              break;
            default:
              echo "-";
          }
    }
@endphp
    <div class="row mt-3 heading_stat p-3 text-center statistics-header">
        <div class="col-md-4">
        </div>
        <div class="col-md-4">
            <p>Activities Monthly Reports </p>
        </div>
    </div>
    <div class="table-responsive">
    <table class="table table-striped statistictable table-bordered" id="statistics" style="width: 100% !important">
        <thead>
            <tr>
              @if (!empty($dt))
              <th>Probationer Name</th>
              @foreach($dt as $date)
              <th>{{ $date }}</th>
              @endforeach
              @endif
          </tr>
        </thead>
        <tbody>
        @if (!empty($data1))
        @foreach ($data1 as $date)
        <tr>
        <td>{{ $date[0]->Name }}</td>
            @foreach ($date as $item)
                @if(!empty($data1))
                <td>{{ replacewithgraade($item->avg_gpa) }}</td>
                @else
                <td>{{ ($item->count) }}</td>
                @endif
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
$(document).ready (function (){
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

$('#statistics').DataTable({
  "bLengthChange": false,
  "ordering": false,
  "bAutoWidth": false,
  language: { search: "" },

});

function exportdata()
        {
           var validate =  $("#reports").validate({
            rules: {
                squad_id: {
                required: true,
            },

            }
        }).form();
        if(validate == true)
        {
            var batch = $('#batch_id').val();
            var squad = $('#squad_id').val();
            var activity_id = $('#activity_id').val();
            var sub_activity_id = $('#sub_activity_id').val();
            var date = $('#datetimerange-input').val();
            $.ajax({
           url: '/monthlyexport',
           type: "POST",
           data:{
               "_token": "{{ csrf_token() }}",
               "batch_id": batch,
               "squad_id": squad,
                "activity_id":activity_id,
                "sub_activity_id":sub_activity_id,
                "date":date,
               },
           success: function(data){
          if(data == '1')
            {
                $('#error').empty();
                var e = $('<div class="alert alert-danger"><p>No data available</p></div>');
                $('#error').append(e);
                $("div.alert").fadeTo(2000, 500).slideUp(500, function(){
                $("div.alert").slideUp(500);
                });
            }
            else{
                window.location=data;
            }
           }
       })
        }

        }
</script>
@endsection
