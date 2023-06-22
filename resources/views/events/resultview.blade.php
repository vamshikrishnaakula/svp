{{-- Extends layout --}}
@extends('layouts.default')

{{-- Content --}}
@section('content')

<script>

$(document).ready(function() {
    $('#dtBasicExample').DataTable({
       "bSort":false,
        "bInfo": false,
         "bLengthChange": false,
        "iDisplayLength": 10,
        "language": {
            "search": '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>',
            "searchPlaceholder": "search",
            "paginate": {
                "previous": '<i class="fa fa-angle-left"></i>',
                "next": '<i class="fa fa-angle-right"></i>'
            }
        },
    });
    $(".dataTables_filter label").addClass("input-group")


      $('.datePicker').datetimepicker({
            dateFormat: "yy-mm-dd",
            timeFormat:  "hh:mm:ss"
        });
} );
</script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap4.min.js"></script>

    <section id="event" class="content-wrapper_sub">
        <div class="row">
            <div class="col-md-10">
                <h4>Results - View</h4>

            </div>
            <div class="col-md-2 text-right">

            </div>
        </div>

        <div class="row my-3">
            <div class="col-md-12">
                <div class="auth_event_sec bg-white px-2 py-2">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Batch</th>
                                <th>Competition</th>
                                <th>Category</th>
                                <th>Event Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{batch_name($event_scheduled_data->batch_id)}}</td>
                                <td>{{$event_scheduled_data->competition}}</td>
                                <td>{{$event_scheduled_data->category}}</td>
                                <td>{{$event_scheduled_data->event_name}}</td>
                            </tr>
                        </tbody>
                    </table>

                    <table id="dtBasicExample" class="table table-bordered">
                        <thead class="bg">
                            <tr>
                                <th>Roll Number</th>
                                <th>Squad</th>
                                <th>Probationer Name</th>
                                <th>Results</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($event_probationers_data as $event_probationer_data)
                            <tr>
                            <?php
                                 $squad_id = squad_id($event_probationer_data->probationers_id);
                            ?>
                                 <td>{{ probationer_rollnumber($event_probationer_data->probationers_id) }}</td>
                                 <td>{{ squad_number($squad_id) }}</td>
                                 <td>{{ probationer_name($event_probationer_data->probationers_id) }}</td>
                                 <td>{{ $event_probationer_data->result}}</td>
                                 <td>{{ $event_probationer_data->status}}</td>
                            </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row my-2">
            <div class="col-md-12">
                <div class="notify_sec py-3">
                    <label class="form-check-label">
                        {{--  <input type="checkbox" class="form-check-input" value=""> Notify all the users  --}}
                    </label>
                </div>
            </div>
        </div>
          <div class="row my-3">
            <div class="col-md-12 text-center">
                {{--  <button type="button" class="btn btn-success btn-sm px-4">Submit</button>
                <button type="button" class="btn btn-dark btn-sm px-4">Download</button>  --}}
            </div>
        </div>
    </section>

@endsection

