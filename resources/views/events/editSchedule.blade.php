{{-- Extends layout --}}
@extends('layouts.default')

{{-- Content --}}
@section('content')

<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap4.min.js"></script>

    <section id="event" class="content-wrapper_sub">
        <div class="row">
            <div class="col-md-10">
                <h4>Scheduler / Add Probationers</h4>

            </div>
            <div class="col-md-2 text-right">

            </div>
        </div>
      <!--   <div class="row">
            <div class="col-md-3">
                <div class="form-group has-search mb-0" id="probationers_table_filter">
                    <span class="fa fa-search form-control-feedback"></span>
                        <input type="text" class="form-control form-control-sm" placeholder="Search"/>
                </div>
            </div>
        </div> -->
        <?php

        $date = date('m/d/Y h:i a', $check_event_scheduler->date);
         ?>
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
                                <td>{{ batch_name($event->batch_id) }}</td>
                                <td>Annual {{ $event->competition }}</td>
                                <td>{{ $event->category }}</td>
                                <td>{{ $event->event_name }}</td>
                            </tr>
                        </tbody>
                    </table>


             <form id="scheduler_submit_form" name="scheduler_submit_form" autocomplete="off" method="POST">
                    <div class="d-flex align-items-center time_Sec">
                        <div class="col-sm-3">
                            <label>Round Number</label>
                            <input type="text" class="form-control form-control-sm" name="round" value="{{ $check_event_scheduler->roundno }}" />
                            <input type="hidden" class="form-control form-control-sm" id="event_id" name="event_id"  value= "{{ $event->id }}"/>
                            <input type="hidden" class="form-control form-control-sm" id="event_scheduler_id" name="event_scheduler_id"  value= "{{ $check_event_scheduler->id }}"/>
                        </div>
                        <div class="col-sm-1"></div>
                        <div class="col-sm-3 d-flex align-items-center">
                        <div class="">
                            <label> Date / Time </label>
                            <div class="input-group" id="datetimepicker_schedule" data-target-input="nearest">
                                <input type="text" class="form-control reqField"
                                    data-target="#datetimepicker_schedule"  data-toggle="datetimepicker" id="datetimepicker_schedule" name="venue_Time" autocomplete="off" value="{{ $date }}" required/>
                            </div>
                        </div>
                    </div>
                      <div class="col-sm-2"></div>
                        <div class="col-sm-3">
                            <label>Enter Venue Details</label>
                            <input type="text" class="form-control form-control-sm" name ="venue" value= "{{ $check_event_scheduler->venue }}"/>
                        </div>
                    </div>
                    <p id="selectTriggerFilter" class="mb-0"><label class="mr-3">Select Squad :</label></p>
                    <table id="probationers_table" class="table table-bordered">
                        <thead class="bg">
                            <tr>
                                <th class="text-center" >
                                    <div class="custom-control custom-checkbox">
                                      <input type="checkbox" class="custom-control-input" id="bulkActionCb_master">
                                      <label class="custom-control-label" for="bulkActionCb_master"></label>
                                  </div>
                                <th>Roll Number</th>
                                <th>Squad</th>
                                <th>Probationer Name</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($probationers as $probationer )
                            <tr>
                                <?php
                                $checked = '';
                                      if (in_array($probationer->id, $scheduled_probationers))
                                      {
                                          $checked = "checked";
                                      }

                                ?>
                                <td>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input bulk-action-cb" name="probationer_name" id="bulkActionCb-{{$probationer->id}}" data-probationer-id="{{$probationer->id}}" {{ $checked }}>
                                            <label class="custom-control-label" for="bulkActionCb-{{$probationer->id}}"></label>
                                        </td>

                                <td>{{$probationer->RollNumber}}</td>
                                <td>{{squad_number((int)$probationer->squad_id)}}</td>
                                <td>{{$probationer->Name}}</td>
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
                        <input type="checkbox" class="form-check-input" value=""> Notify all the users
                    </label>
                </div>
                <div id="schedulerSubmit_status" class="mt-3"></div>
            </div>
        </div>
          <div class="row my-3">
            <div class="col-md-12 text-center">
                <button type="button" class="btn btn-success btn-sm px-4" onclick="window.scheduler_formsubmit();">Submit</button>
            </div>
        </div>
    </form>
    </section>

@endsection

@section('scripts')
<script>
    $('#datetimepicker_schedule').datetimepicker({
                // dateFormat: 'dd-mm-yy',
                format:'DD/MM/Y hh:mm a',
                setDate:'today',
             });
    $(document).ready(function() {
       var probationers_table =  $('#probationers_table').DataTable({
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
            initComplete: function() {
                var column = this.api().column(2);
                var select = $('<select class="filter"><option value="">ALL</option></select>')
                  .appendTo('#selectTriggerFilter')
                  .on('change', function() {
                    var val = $(this).val();
                    column.search(val ? '^' + $(this).val() + '$' : val, true, false).draw();
                  });
                column.data().unique().sort().each(function(d, j) {
                  select.append('<option value="' + d + '">' + d + '</option>');
                });
              }
        });
        $(".dataTables_filter label").addClass("input-group")
          $('.datePicker').datetimepicker({
                dateFormat: "yy-mm-dd",
                timeFormat:  "hh:mm:ss"
});

/** --------------------------------
 * Bulk Action
 */

 $(document).on("change", "#bulkActionCb_master", function () {

        if ($(this).is(':checked')) {
            probationers_table.rows().nodes().to$().find('.bulk-action-cb').each(function () {
            if ($(this).is(':disabled') == false) {
            $(this).prop('checked', true);
            }
        });
        $(".bulk_action_btn").prop('disabled', false);
        } else {
        probationers_table.rows().nodes().to$().find('.bulk-action-cb').prop('checked', false);
        $(".bulk_action_btn").prop('disabled', true);
        }
    });

    $(document).on("change", "tbody .bulk-action-cb", function () {
        if ($(this).closest('table').find('tbody .bulk-action-cb:checked').length > 0) {
        $(".bulk_action_btn").prop('disabled', false);
        } else {
        $(".bulk_action_btn").prop('disabled', true);
        }
    });

            /** -------------------------------------------------------------------
     * Submit event Form Data
     * ----------------------------------------------------------------- */
     if (!window.scheduler_formsubmit) {
        window.scheduler_formsubmit = function() {
            var statusDiv = $("#schedulerSubmit_status");
            var eventForm = $("form#scheduler_submit_form");
            var probationersIds = [];
            var unchecked_probationersIds = [];
            probationers_table.rows().nodes().to$().find('.bulk-action-cb:checked').each(function(){
                probationersIds.push($(this).attr('data-probationer-id'));
            });

            probationers_table.rows().nodes().to$().find('.bulk-action-cb:not(:checked)').each(function(){
                unchecked_probationersIds.push($(this).attr('data-probationer-id'));
            });

            var count = probationersIds.length;

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

            var actionUrl = appUrl + "/events/updateSchedule";
            if (isValid == true) {
                var FrmData = new FormData(eventForm[0]);
                FrmData.append('probationers', probationersIds);
                FrmData.append('unchecked_probationers', unchecked_probationersIds);
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
                    // eventForm[0].reset();
                    // $("#timetableUpdate_form_status").html(rData);

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
});
    </script>
    @endsection
tion
