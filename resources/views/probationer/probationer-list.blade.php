{{-- Extends layout --}}
@extends('layouts.default')

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

<section id="addstaff" class="content-wrapper_sub datatablelist">
    <div class="user_manage">
        <div class="row">
        <div class="col-md-10">
              <h4>Probationer List</h4>
            </div>
            <div class="col-md-2">
                <div class="userBtns d-flex justify-content-end">
                    <a href="#" onclick="window.get_probationerImport_modal()" data-toggle="tooltip" title="Import and Export Probationers"> <img src="{{ asset('images/import.png') }}" /></a>

                    <a href="{{ url('probationers') }}" class="text-center ml-3" data-toggle="tooltip" title="Add Probationer">
                        <img src="{{ asset('images/plus-icon-rounded.png') }}">
                        <p class="my-0">Add</p>
                    </a>
                </div>
            </div>
        </div>

        <div class="listdetails mt-4">
            <div class="squadlisthead">
            <div class="row">
                    <div class="col-md-1">
                        <div class="group">
                            <img src="/images/staff.png" />
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="row group">
                        <p id="selectTriggerFilter" class="mb-0"><label class="mr-3">Select Batch :</label></p>
                     <select class="form-control col-md-5" id="batch_id" name="batch_id">
                        <option value=''>Select Batch</option>
                        @foreach($batches as $batch)
                        <option value="{{ $batch->id }}" @if($batch->id == Session::get('current_batch')) selected @endif>{{ $batch->BatchName }}</option>
                        @endforeach
                    </select>
                        </div>
                    </div>
                </div>
            </div>

            <div >
                <table class="table" id ="ptable" style="width: 100% !important">
                    <thead>
                        <tr>
                            <!-- <th width=10%;></th>
                            <th>Cadre</th> -->
                            {{-- <th style="display:none;">Batch Number</th> --}}
                            <th>Name</th>
                            <th>Date Of Birth</th>
                            <th>Mobile Number</th>
                            <th>Email</th>
                            <th>Squad</th>
                            <th style="display: none">Squad Number</th>
                            <th style="display: none">Drill Inspector</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="dropdowndata">
                        @foreach($probationers as $probationer)
                        <tr>
                            <!-- <td><input type="checkbox" class="form-control" /></td>
                            <td>{{ $probationer->Cadre }}</td> -->

                            {{-- <td style="display:none;">{{ $probationer->BatchName }}</td> --}}
                            <td>{{ $probationer->Name }}</td>
                            <td>{{date('d-m-Y', strtotime($probationer->Dob))}}</td>
                            <td>{{ $probationer->MobileNumber }}</td>
                            <td>{{ $probationer->Email }}</td>
                            <td>{{$probationer->SquadNumber}}</td>
                            {{-- <td>{{$probationer->name}}</td> --}}
                            <td>
                                <a href="/probationerprofile/{{$probationer->id}}"><img src="/images/view.png" /></a>

                                <a href="{{ route('probationers.edit',$probationer->id) }}" data-toggle="tooltip"
                                    title="Edit"> <img src="/images/edit.png" /></a>
                                <a href=""
                                    onclick="if(confirm('Do you want to delete this Probationer?'))event.preventDefault(); document.getElementById('delete-{{$probationer->id}}').submit();"><img
                                        src="{{ asset('images/trash.png') }}" /></a>
                                <form id="delete-{{$probationer->id}}" method="post"
                                    action="{{ route('probationers.destroy',$probationer->id) }}"
                                    style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
</section>
@endsection


@section('scripts')
<script>
//  $(function() {
//   $('#ptable').DataTable({
//     "bLengthChange": false,
//     language: { search: "", searchPlaceholder: "Search..." },
//     initComplete: function() {
//       var column = this.api().column(0);
//       var select = $('<select class="filter"><option value="">ALL</option></select>')
//         .appendTo('#selectTriggerFilter')
//         .on('change', function() {
//           var val = $(this).val();
//           column.search(val ? '^' + $(this).val() + '$' : val, true, false).draw();
//         });
//       column.data().unique().sort().each(function(d, j) {
//         select.append('<option value="' + d + '">' + d + '</option>');
//       });
//     }
//   });
// });

// $(function() {
//     $('#ptable').DataTable({
//       "bLengthChange": false,
//       "searching": false,
//       "pageLength": 20
//     });
//   });

    /** -------------------------------------------------------------------
     * Get Import and download Data Modal
     * ----------------------------------------------------------------- */
     if (!window.get_probationerImport_modal) {
        window.get_probationerImport_modal = function () {

            $.ajax({
                url: appUrl +'/probationer/ajax',
                data: {
                    requestName: "get_probationerImport_modal"
                },
                type: "POST",
                beforeSend: function (xhr) {
                    window.loadingScreen("show");
                },
                success: function (rData) {
                    window.loadingScreen("hide");
                    $("#dataImportModalTitle").html("Import and Download of probationer data");
                    $("#dataImportModalContent").html(rData);
                    $("#dataImportModalBtns").hide();
                    $("#dataImportModal .modal-dialog").addClass("modal-lg");
                    $("#dataImportModal").modal("show");

                    window.getDatepicker();
                }
            });
        }
    };


        /** -------------------------------------------------------------------
     * Export probationer Details Datasheet
     * ----------------------------------------------------------------- */
     if (!window.export_probationer_submit) {
        window.export_probationer_submit = function () {

            var formEl = $("form#download_probationerDatasheet_form");
            var statusDiv = $("#download_probationerDatasheet_status");

            formEl.find(".form-control").each(function () {
                $(this).removeClass("input-error");
            });

            var isValid = true;
            var firstError = "";
            var ErrorCount = 0;

            formEl.find(".reqField").each(function () {
                if ($(this).val().trim() == "") {
                    $(this).addClass("input-error");

                    isValid = false;
                    // return isValid;
                    if (ErrorCount == 0) {
                        firstError = $(this);
                    }
                    ErrorCount++;
                } else {
                    $(this).removeClass("input-error");
                }
            });

            if (isValid == true) {
                var FrmData = new FormData(formEl[0]);
                FrmData.append('requestName', 'download_probationerDatasheet');

                $.ajax({
                    url: appUrl +'/probationer/ajax',
                    data: FrmData,
                    type: "POST",
                    processData: false,
                    contentType: false,
                    beforeSend: function (xhr) {
                        window.loadingScreen("show");
                        statusDiv.html("");
                    },
                    success: function (rData) {
                        window.loadingScreen("hide");
                        console.log(rData);
                        let rObj = JSON.parse(rData);
                        if (rObj.status == "success") {
                            let newTab = window.open(rObj.datasheet_url);
                        } else {
                           statusDiv.html('<div class="msg msg-danger msg-full">'+rObj.message+'</div>');
                        }
                    }
                });
            } else {
                firstError.focus();
                statusDiv.html('<div class="msg msg-danger msg-full">Fill all the required fields</div>');
            }
        }
    };

        /** -------------------------------------------------------------------
     * Get Probationer sample Import
     * ----------------------------------------------------------------- */
     if (!window.window.get_probationerImport_sample) {
        window.window.get_probationerImport_sample = function () {
            var batch_id = $('#data_batch_id').val();

            $.ajax({
                url: appUrl +'/probationer/ajax',
                data: {
                    requestName: "get_probationerImport_sample",
                    data_batch_id:batch_id
                },
                type: "POST",
                beforeSend: function (xhr) {
                    window.loadingScreen("show");
                },
                success: function (rData) {
                    window.loadingScreen("hide");
                    console.log(rData);
                    let rObj = JSON.parse(rData);
                    if (rObj.status == "success") {
                        let newTab = window.open(rObj.datasheet_url);
                    } else {
                       statusDiv.html('<div class="msg msg-danger msg-full">'+rObj.message+'</div>');
                    }

                }
            });
        }
    };

        /** -------------------------------------------------------------------
     * Import Data Sheet Submit
     * ----------------------------------------------------------------- */
     if (!window.import_probationerDataSheet_submit) {
        window.import_probationerDataSheet_submit = function () {
            var formEl = $("form#importProbationer_form");
            var statusDiv = $("#importProbationer_form_status");

            var FrmData = new FormData(formEl[0]);

            FrmData.append('requestName', 'import_Probationer_DataSheet');

            $.ajax({
                url: appUrl + "/probationer/ajax",
                data: FrmData,
                type: "POST",
                processData: false,
                contentType: false,
                beforeSend: function (xhr) {
                   // window.loadingScreen("show");

                    statusDiv.html("");
                },
                success: function (rData) {
                    window.loadingScreen("hide");
                    formEl[0].reset();
                    var sData = rData.replace( /[\r\n]+/gm, "" );

                    let rObj = JSON.parse(sData);

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

    /*
    batch wise probationers
    */


    $(document).ready(function(){
        $('#batch_id').on('change',function(){
             $('#ptable #dropdowndata').empty();

        var id = $(this).val();
        console.log(id);
        $.ajax({
             url:"/batchwiseprob",
            method: "POST",
            data: {
                "_token": "{{ csrf_token() }}",
                        "id":id,
                        "requestname":"probationerlist"
            },
            success: function(data){
                $.each(data, function(i)
                {
                    var url = "{{ url ('/probationerprofile/:id')}}";
                    var route = "{{ route('probationers.edit',':id')}}";
                    var route_delete = "{{ url ('/probationers/deleteprobationers/:id')}}";
                    url = url.replace(':id', data[i].id);
                    route = route.replace(':id', data[i].id);
                    route_delete = route_delete.replace(':id', data[i].id);

                    $('#ptable #dropdowndata').append('<tr><td>' + data[i].Name + '</td><td>' + data[i].Dob + '</td><td>' + data[i].MobileNumber + '</td><td>' + data[i].Email + '</td><td>' + data[i].SquadNumber + '</td><td><a href=' + url + '><img src="/images/view.png" /><span></span></a><a href='+ route +'><img src="/images/edit.png" /><span></span></a><a onclick = "deleteprobationer('+ data[i].id +');"><img src="/images/trash.png" /><span></span></a></td></tr>');
                });

        }
    });
    });
});

function deleteprobationer(id)

    {
    var result = confirm("Do you want to delete this Probationer?");
    if (result) {
    var id = id;
      $.ajax({
            url: '/probationers/deleteprobationers/id',
            type: "POST",
            data:{
                "_token": "{{ csrf_token() }}",
                 "id":id,
                },
            success: function(data){
                var sData = data.replace( /[\r\n]+/gm, "" );
              if(sData == '1')
              {
                $('#error').empty();
                var e = $('<div class="alert alert-danger"><p>Probationer deleted successfully</p></div>');
                 $('#error').append(e);
                    $("div.alert").fadeTo(2000, 500).slideUp(500, function () {
                    $("div.alert").slideUp(500);
                });
              }
            }
        })
    }
    }

</script>
@endsection
