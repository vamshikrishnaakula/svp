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
} );
</script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap4.min.js"></script>

    <section id="event" class="content-wrapper_sub">
        <div class="row">
            <div class="col-md-10">
                <h4>Schedule Event</h4>

            </div>
            <div class="col-md-2 text-right">

            </div>
        </div>
      <!--   <div class="row">
            <div class="col-md-3">
                <div class="form-group has-search mb-0" id="dtBasicExample_filter">
                    <span class="fa fa-search form-control-feedback"></span>
                        <input type="text" class="form-control form-control-sm" placeholder="Search"/>
                </div>
            </div>
        </div> -->
        <?php
        $events = App\Models\Event::get();
        ?>


        <div class="row my-3">
            <div class="col-md-12">
                <div class="auth_event_sec bg-white px-2 py-2">
                    <table id="dtBasicExample" class="table table-bordered schedule_sec">
                        <thead class="bg">
                            <tr>
                                <th>Competition</th>
                                <th>Category</th>
                                <th>Event Name</th>
                                <th class="text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($events as $event )
                            <?php
                            $events_scheduler = App\Models\EventScheduler::where('event_id', $event->id)->first();
                            $action =  (empty($events_scheduler)) ? "Add" : "Edit";
                            $url =  (empty($events_scheduler)) ? "addSchedule" : "editSchedule";
                            ?>

                            <tr>
                                <td>{{ $event->competition}}</td>
                                <td>{{ $event->category}}</td>
                                <td>{{ $event->event_name}}</td>
                                <td>
                                    <div class="d-flex align-items-center justify-content-end">
                                        <a href="{{ $url }}/{{ $event->id }}">{{ $action }}</a>
                                        @if(!empty($events_scheduler))
                                          |
                                         <a href="viewscheduler/{{ $event->id }}">View</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </section>

@endsection

