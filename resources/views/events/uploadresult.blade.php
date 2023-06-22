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
    $('input[type="file"]').on("change", function() {
        let filenames = [];
        let files = this.files;
        if (files.length > 1) {
        filenames.push("Total Files (" + files.length + ")");
        } else {
        for (let i in files) {
            if (files.hasOwnProperty(i)) {
            filenames.push(files[i].name);
            }
        }
        }
        $(this)
        .next(".custom-file-label")
        .html(filenames.join(","));
  });
} );


</script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap4.min.js"></script>

    <section id="event" class="content-wrapper_sub">
        <div class="row">
            <div class="col-md-10">
                <h4>Upload Results</h4>

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
                    <div class="d-flex align-items-center time_Sec">
                        <div class="col-sm-3">
                            <label>Select Round</label>
                            <div><small>{{ $event_scheduled_data->roundno }}</small></div>

                        </div>
                        <div class="col-sm-2"></div>
                        <div class="col-sm-3 d-flex align-items-center">
                        <div class="">
                            <label>Date/Time</label>
                            <div class="form-group">
                                <div><small>{{ date('d/m/Y h:i a', $event_scheduled_data->date) }}</small></div>
                                <input type="hidden" class="form-control form-control-sm" id = "scheduled_id" name="scheduled_id"  value= "{{ $event_scheduled_data->event_scheduled_id }}"/>
                            </div>
                        </div>

                    </div>
                      <div class="col-sm-2"></div>
                        <div class="col-sm-2">
                            <label></label>
                            <div class="form-group">
                               <div></div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 upload_sec my-3">
                        <div class="form-group">
                        <label><strong>Upload Files Here</strong></label>
                        <div class="custom-file">
                            <form name="importDataSheet_form" id="importDataSheet_form" action="#" method="post" class="text-center mt-3" enctype="multipart/form-data" accept-charset="utf-8">
                            <input type="file"  multiple class="custom-file-input form-control" name="data_csv" id="customFile" accept=".csv">
                            <label class="custom-file-label" for="customFile">Choose file</label>
                            </form>
                        </div>
                        </div>
                        <div class="form-group">
                        <button type="button" name="upload" value="upload" id="upload" onclick="window.uploadDataSheet_Scheduled_sample();"class="btn-sm btn btn btn-success btn-block"><i class="fa fa-fw fa-upload"></i> Upload</button>
                        </div>
                        <div id="importDataSheet_form_status" class="mt-3"></div>
            </div>
        </div>
          <div class="row my-3">
            <div class="col-md-12 text-center">
                <button type="button" class="btn btn-dark px-3" onclick="window.downloadDataSheet_Scheduled_sample();">Download Sample &nbsp;<i class="fas fa-download"></i></button>
            </div>
        </div>
    </section>

@endsection


@section('scripts')
<script>

 /** -------------------------------------------------------------------
     * Sample event scheduled data
     * ----------------------------------------------------------------- */
     if (!window.downloadDataSheet_Scheduled_sample) {
        window.downloadDataSheet_Scheduled_sample = function() {
            var statusDiv = $("#schedulerSubmit_status");
            var token = $('meta[name="csrf-token"]').attr('content');
            var id = $("#scheduled_id").val();
            var FrmData = {
                'scheduled_id' : id,
                'request_name' : 'sample_scheduled_data',
                'token' : token
            }

            var isValid = true;
            var actionUrl = appUrl + "/events-ajax";
            if (isValid == true) {

            $.ajax({
                url: actionUrl,
                data: FrmData,
                type: "POST",
                beforeSend: function(xhr) {
                   // window.loadingScreen("show");
                    statusDiv.html("");
                },
                success: function(data) {
                    var rData = data.replace( /[\r\n]+/gm, "" );
                    window.loadingScreen("hide");
                        let rObj = JSON.parse(rData);

                        if (rObj.status == "success") {
                            // statusDiv.html(rObj.data);

                            // window.saveCsvData(window.jsonObjectToCSV(rObj.data), rObj.file_name);
                            let newTab = window.open(rObj.datasheet_url);

                        } else {
                            statusDiv.html('<div class="msg msg-danger msg-full">'+rObj.message+'</div>');
                        }
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

     /** -------------------------------------------------------------------
     * Import Data Sheet Submit
     * ----------------------------------------------------------------- */
     if (!window.uploadDataSheet_Scheduled_sample) {
        window.uploadDataSheet_Scheduled_sample = function () {
            var formEl = $("form#importDataSheet_form");
            var statusDiv = $("#importDataSheet_form_status");

            var FrmData = new FormData(formEl[0]);

            FrmData.append('request_name', 'import_DataSheet');

            $.ajax({
                url: appUrl + "/events-ajax",
                data: FrmData,
                type: "POST",
                processData: false,
                contentType: false,
                beforeSend: function (xhr) {
                    window.loadingScreen("show");

                    statusDiv.html("");
                },
                success: function (data) {
                    var rData = data.replace( /[\r\n]+/gm, "" );
                    window.loadingScreen("hide");
                    formEl[0].reset();
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
                }
            });
        };
    }
</script>

