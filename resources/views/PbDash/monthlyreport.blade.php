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
    <?php
            $user_id = Auth::id();
            $batch_id = App\Models\probationer::where('user_id', $user_id)->value('batch_id');
            $activities = App\Models\Activity::where('batch_id', $batch_id)->where('type', 'activity')->get();
            ?>
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
        <form action="{{ url('monthly_activity_reports') }}" method="POST" id="reports" name="reports" autocomplete="off">
        @csrf
            <div class="row">
                    <div class="col-md-3 ct-col-6">
                        <div class="form-group">
                          <label>Activity</label>
                          <select name="activity_id" id="activity_id" class="form-control" onchange="window.select_activityId_changed(this, 'sub_activity_id'); reset_component(); check_subactivites(this);" required>
                            <option value="">Select Activity</option>
                            @foreach ($activities as $activity)
                                <option value="{{ $activity->id }}">{{ $activity->name }}</option>
                            @endforeach
                        </select>
                        </div>
                      </div>
                      <div class="col-md-3 ct-col-6">
                        <div class="form-group">
                          <label>Sub Activity</label>
                          <select name="sub_activity_id" id="sub_activity_id" class="form-control">
                            <option value="">Select Sub Activity</option>
                        </select>

                        </div>
                      </div>

                      <div class="col-md-3 ct-col-6">
                          <label>Month</label>
                        <div class="input-group" id="datetimepicker44" data-target-input="nearest" name="date">
                            <input type="text" class="form-control datetimepicker-input"
                                data-target="#datetimepicker44"  data-toggle="datetimepicker" name="date" id="month_datepicker" required/>

                        </div>
                      </div>
                      <div class="col-md-1 ct-col-6 report_submit_btn">
                        <button class="pl-2 pt-1" class="btn formBtn submitBtn"><img src="{{ asset('images/submit.png') }}" width="25" /></button>
                        <a class="pl-2 pt-1" onclick="exportdata()"><img src="{{ asset('images/download1.png') }}" width="25" /></a>
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
            default:
              echo "E";
          }
    }
@endphp
    <div class="row mt-3 text-center heading_stat p-3 statistics-header">
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
        @if (!empty($data))
        <tr>
        <td>{{ $probationer_name }}</td>
        @foreach ($data as $item)
                    <td>{{ replacewithgraade($item->avg_gpa) }}</td>
         @endforeach

        </tr>
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
    $("#datetimepicker44").datetimepicker ({
            viewMode: 'months',
            format: 'MM/YYYY'
        });
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

                if (data != '0') {
                    $('#sub_activity_id').attr('required', true);
                }
            }
        })
    }

function exportdata()
        {
           var validate =  $("#reports").validate({
            rules: {
                squad_id: {
                required: true,
            },
            activity_id: {
                required: true,
            },
            // sub_activity_id: {
            //     required: true,
            // }
            }
        }).form();
        if(validate == true)
        {
            var bid = $('#batch_id').val();
            var sid = $('#squad_id').val();
            var activity_id = $('#activity_id').val();
            var sub_activity_id = $('#sub_activity_id').val();
           // var component = $('#component').val();
            var date = $('#month_datepicker').val();
            $.ajax({
           url: '/monthly_activity_reports_export',
           type: "POST",
           data:{
               "_token": "{{ csrf_token() }}",
                "batch_id":bid,
                "squad_id":sid,
                "activity_id":activity_id,
                "sub_activity_id":sub_activity_id,
                "date":date,
               },
           success: function(data){
           window.location=data;
           }
       })
        }

        }
</script>

@endsection
