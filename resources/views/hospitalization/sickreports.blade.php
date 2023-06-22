{{-- Extends layout --}}
@extends('layouts.default')

{{-- Content --}}
@section('content')

<section id="notes" class="content-wrapper_sub">

    <div class="row">
        <div class="col-md-12">
            <div class="user_manage notes-wrapper">
                <div class="row mt-2 mb-3">
                    <div class="col-md-6">
                        <h4 class="notes-heading">Sick Reports</h4>
                    </div>
                    <div class="col-md-6">
                        <div class="text-right">
                        </div>
                    </div>
                </div>

                <form name="get_sick_form" id="get_sick_form" class="width-three-fourth rl-margin-auto" accept-charset="utf-8">
                    @csrf

                    @php
                    $batches = DB::table('batches')->get();
                    @endphp
                    <div class="row mt-5">


                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Select Batch:</label>
                                <select name="batch_id" id="batch_id" onchange="window.select_batchId_changed(this, 'squad_id');" class="form-control reqField">
                                    <option value="">Select batch...</option>
                                    @if( !empty($batches) )
                                    @foreach($batches as $batch)
                                    <option value="{{ $batch->id }}" @if($batch->id == Session::get('current_batch')) selected @endif>{{ $batch->BatchName }}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="squad_id">Select Squad:</label>
                                <select name="squad_id" id="squad_id" class="form-control reqField">
                                    <option value="">Select...</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group probationer_id_container">
                                <label for="probationer_id">Select Probationer:</label>
                                <select name="probationer_id" id="probationer_id" class="form-control">
                                    <option value="">Select...</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="usersubmitBtns mt-4">
                                <div class="mr-4">
                                    <button type="button" id="get_sick_btn" onclick="getsickreports();" class="btn formBtn submitBtn">Proceed</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <div id="get_notes_status" class="mt-5"></div>
                <div id="notes-container" class="mt-5"></div>
            </div>

        </div>
    </div>

    {{-- Create Note Modal --}}
    <div class="modal fade" id="createNoteModal" tabindex="-1" role="dialog" aria-labelledby="createNoteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">

                <div class="modal-body">
                    <h4 class="text-center">Create Note</h4>

                    <div id="createNoteModalContent"></div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection



@section('scripts')
<script>
    // Get probationers on squad select
    $(document).on("change", "select#squad_id", function () {
        var squadId = $(this).val();
        var pbItem  = $("select#probationer_id");

        $.ajax({
            url: appUrl+"/probationerDropdownOptions",
            data: {
                SquadId: squadId
            },
            type: "POST",
            beforeSend: function (xhr) {
                pbItem.html(
                    '<option value="">Please wait...</option>'
                );
            },
            success: function (rData) {
                pbItem.html(rData);
            }
        });
    });


function getsickreports()
{
        var formEl = $("form#get_sick_form");
        var statusDiv = $("#get_notes_status");

        $("#notes-container").html("");

        formEl.find(".form-control").each(function() {
            $(this).removeClass("input-error");
        });

        var isValid = true;
        var firstError = "";
        var ErrorCount = 0;

        formEl.find(".reqField").each(function() {
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
            FrmData.append('requestName', 'get_sick');
            $.ajax({
                url: appUrl + "/get_sick_reports",
                data: FrmData,
                type: "POST",
                processData: false,
                contentType: false,
                beforeSend: function(xhr) {
                    {{--  window.loadingScreen("show");
                    statusDiv.html(`Please wait...`);  --}}
                },
                success: function(rData) {
                    window.loadingScreen("hide");
                    statusDiv.html("");
                    $("#notes-container").html(rData);
                }
            });
        } else {
            firstError.focus();
            statusDiv.html('<div class="msg msg-danger">Fill all the required fields</div>');
        }
    }

</script>
@endsection
