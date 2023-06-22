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
                <h4>Events List</h4>

            </div>
            <div class="col-md-2 text-right">
                <butto type="button" data-toggle="modal" data-target="#creatListModal" class="btn btn-primary btn-sm">
                    <i class="fa fa-plus" aria-hidden="true"></i> Create New
                </button>
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

        <div class="row my-3">
            <div class="col-md-12">
                <div class="auth_event_sec bg-white px-2 py-2">
                    <table id="dtBasicExample" class="table table-bordered">
                        <thead class="bg">
                            <tr>
                                <th>Competition</th>
                                <th>Category</th>
                                <th>Event Name</th>
                                <th>Round</th>
                            </tr>
                        </thead>
                        @if($events)
                        <tbody>
                        @foreach ($events as $event )
                        <tr>
                            <td>{{ $event->competition}}</td>
                            <td>{{ $event->category}}</td>
                            <td>{{ $event->event_name}}</td>
                            <td>{{ $event->events_rounds}}</td>
                        </tr>
                        @endforeach
                       </tbody>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <!--Create Modal --->

        <div class="modal fade event_modal" id="creatListModal">
            <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create Event</h6>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
            <form id="events_submit_form" name="events_submit_form" autocomplete="off" method="POST">
                @csrf()
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="mb-0">Select Batch</label>
                            <select class="form-control form-control-sm reqField" name="batch">
                                <option value="">Select Batch</option>
                                @if( !empty($batches) )
                                @foreach($batches as $batch)
                                <option value="{{ $batch->id }}" {{$batch->id == Session::get('current_batch')  ? 'selected' : ''}}>{{ $batch->BatchName }}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6">
                          <div class="form-group">
                            <label class="mb-0">Select Competition</label>
                            <select class="form-control form-control-sm reqField" name = "event_competition">
                                <option value="">Select Competition</option>
                                <option value="Athletic">Annual Athletic Events</option>
                                <option value="Aquatic">Annual Aquatic Events</option>
                                <option value="Squad">Inter - Squad Competitons</option>
                                <option value="Marathon">Marathon</option>
                                <option value="Triathlon">Triathlon</option>
                                <option value="Cycling">Cycling</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="sel1" class="mb-0">Category</label>
                            <select class="form-control form-control-sm reqField" name = "event_category" id= "event_category">
                                <option value="">Select Category</option>
                                <option value="individual">individual</option>
                                <option value="team">Team</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row mb-2" id="radiohide">
                    <div class="col-sm-12">
                           <div class="form-check-inline">
                                <label class="form-check-label ml-0">
                                    <input type="radio" class="form-check-input" name ="gender" value="1">Gentlemen Probationer
                                </label>
                            </div>
                            <div class="form-check-inline ml-0">
                                <label class="form-check-label">
                                    <input type="radio" class="form-check-input" name ="gender" value="2" >Lady Probationer
                                </label>
                            </div>
                            <div class="form-check-inline ml-0">
                                <label class="form-check-label">
                                    <input type="radio" class="form-check-input" name ="gender" value="3">Both
                                </label>
                            </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                         <div class="form-group">
                            <label for="sel1" class="mb-0">Event Name</label>
                            <input type="text" class="form-control form-control-sm reqField" name = "event_name"/>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                         <div class="form-group">
                            <label for="sel1" class="mb-0">No of Rounds</label>
                            <input type="text" class="form-control form-control-sm reqField" name = "event_"/>
                        </div>
                    </div>
                     <div class="col-sm-6">
                         <div class="form-group">
                            <label for="sel1" class="mb-0">Units</label>
                            <input type="text" class="form-control form-control-sm" name= "units"/>
                        </div>
                    </div>
                </div>
                <div class="row">
                    {{--  <div class="col-sm-12 text-right">
                        <button type="button" class="btn btn-primary add_btn"><i class="fa fa-plus"></i> add</button>
                    </div>  --}}
                </div>
                <div id="eventSubmit_status" class="mt-3"></div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" onclick="window.eventSubmit();" class="btn btn-success submit_btn">Submit</button>
                </div>

            </div>
            </div>
        </div>

    </section>

@endsection

@section('scripts')
<script>

/** -------------------------------------------------------------------
     * Submit event Form Data
     * ----------------------------------------------------------------- */
     if (!window.eventSubmit) {
        window.eventSubmit = function() {
            var statusDiv = $("#eventSubmit_status");

            var eventForm = $("form#events_submit_form");


            eventForm.find(".form-control").each(function () {
                $(this).removeClass("input-error");
            });

            var isValid = true;
            var firstError = "";
            var ErrorCount = 0;

            eventForm.find(".reqField").each(function () {
                if ($(this).val().trim() == "") {
                    $(this).addClass("input-error");
                    isValid = false;
                    if (ErrorCount == 0) {
                        firstError = $(this);
                    }

                    ErrorCount++;
                } else {
                    $(this).removeClass("input-error");
                }
            });

            var actionUrl = appUrl + "/events/store";
            if (isValid == true) {
                var FrmData = new FormData(eventForm[0]);
            $.ajax({
                url: actionUrl,
                data: FrmData,
                type: "POST",
                processData: false,
                contentType: false,
                beforeSend: function(xhr) {
                    //window.loadingScreen("show");

                    statusDiv.html("");
                },
                success: function(data) {
                    var rData = data.replace( /[\r\n]+/gm, "" );
                    window.loadingScreen("hide");
                    eventForm[0].reset();
                    let rObj = JSON.parse(rData);

                    if (rObj.status == "success") {
                        statusDiv.html(
                            '<div class="msg msg-success msg-full">' + rObj.message + "</div>"
                        );
                    } else {
                        statusDiv.html(
                            '<div class="msg msg-danger msg-full">' + rObj.message + "</div>"
                        );
                    }
                    window.setTimeout(function() {
                        window.location.reload();
                    }, 2000);
                }
            });
        }
        else
        {
            firstError.focus();
                statusDiv.html('<div class="msg msg-danger mx-auto">Fill all the required fields</div>');
        }
        };
    }
    $("#radiohide").hide();
    $("#event_category").on("change",function()
    {
        if($(this).val() == "" || $(this).val() == "team")
        {
            $("#radiohide").hide();
        }
        else
        {
            $("#radiohide").show();
        }
    });
</script>
    @endsection
