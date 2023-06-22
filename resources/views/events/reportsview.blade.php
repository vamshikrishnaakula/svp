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
            "search": false,
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
            <div class="col-md-12">
              <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Reports -</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Individual</li>
                        <li class="breadcrumb-item active" aria-current="page">Winnerlist</li>
                    </ol>
                </nav>

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
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="d-flex align-items-center time_Sec justify-content-center">
                        <div><small>Round 1</small></div>
                    </div>
                    <table id="dtBasicExample" class="table table-bordered">
                        <thead class="bg-white">
                            <tr class="text-center">
                                <th>Roll Number</th>
                                <th>Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="text-center">
                                <td>Annual Athletic</td>
                                <td>Indual</td>
                            </tr>
                             <tr class="text-center">
                               <td>Annual Athletic</td>
                                <td>Indual</td>
                            </tr>
                             <tr class="text-center">
                               <td>Annual Athletic</td>
                                <td>Team</td>
                            </tr>
                             <tr class="text-center">
                                <td>Annual Athletic</td>
                                <td>Indual</td>
                            </tr>
                        </tbody>
                    </table>
                     <div class="d-flex align-items-center time_Sec justify-content-center">
                        <div><small>Round 2</small></div>
                    </div>
                     <table id="dtBasicExample" class="table table-bordered">

                        <tbody>
                            <tr class="text-center">
                                <td>Annual Athletic</td>
                                <td>Indual</td>
                            </tr>
                             <tr class="text-center">
                               <td>Annual Athletic</td>
                                <td>Indual</td>
                            </tr>
                             <tr class="text-center">
                               <td>Annual Athletic</td>
                                <td>Team</td>
                            </tr>
                             <tr class="text-center">
                                <td>Annual Athletic</td>
                                <td>Indual</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

          <div class="row my-3">
            <div class="col-md-12 text-center">

                 <button type="button" class="btn btn-dark btn-sm px-4">Download</button>
            </div>
        </div>
    </section>

@endsection

