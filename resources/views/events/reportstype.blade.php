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
                    <table id="dtBasicExample" class="table table-bordered">
                        <thead class="bg">
                            <tr>
                                <th>Competition Type</th>
                                <th>Event Name</th>
                                <th></th>
                                
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Individual Events</td>
                                <td>Indual</td>
                                <td></td>
                            </tr>
                             <tr>
                               <td>Individual Events</td>
                                <td>Indual</td>
                                <td></td>
                                
                            </tr>
                             <tr>
                               <td>Individual Events</td>
                                <td>Team</td>
                                <td></td>
                            </tr>
                             <tr>
                                <td>Individual Events</td>
                                <td>Indual</td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
      
    </section>
    
@endsection

