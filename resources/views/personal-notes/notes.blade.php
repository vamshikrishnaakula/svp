
<?php
$role   = Auth::user()->role;
if($role === 'faculty') {
    $template   = 'layouts.faculty.template';
} else {
    $template   = 'layouts.default';
}
$app_view = session('app_view');
?>



@extends(($app_view) ? 'layouts.pbdash.mobile-template' : $template)

{{-- Content --}}
@section('content')



<section id="notes" class="content-wrapper_sub">

    <div class="row">
        <div class="col-md-12">
            <div class="user_manage notes-wrapper">
                <div class="row mt-2 mb-3">
                    <div class="col-md-6">
                        <h4 class="notes-heading">Notes</h4>
                    </div>
                    <div class="col-md-6">
                        <div class="text-right">

                        </div>
                    </div>
                </div>

                <form name="get_notes_form" id="get_notes_form" class="width-three-fourth rl-margin-auto" accept-charset="utf-8">
                    @csrf

                    @php
                    $batches = DB::table('batches')->get();
                    @endphp
                    <div class="row mt-5">
                        <div class="col-md-12 mb-3">
                            <!-- Default inline 1-->
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" name="reference" id="referenceRadio1" value="squad" class="custom-control-input" checked>
                                <label class="custom-control-label" for="referenceRadio1">Squad</label>
                            </div>

                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" name="reference" id="referenceRadio2" value="probationer" class="custom-control-input">
                                <label class="custom-control-label" for="referenceRadio2">Probationer</label>
                            </div>
                        </div>

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
                            <div class="form-group probationer_id_container hidden">
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
                                    <button type="button" id="get_notes_btn" class="btn formBtn submitBtn">Proceed</button>
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
    // Check whether squad or probationer is checked
    $('input:radio[name="reference"]').change(
    function(){
        if ($(this).is(':checked') && $(this).val() == 'probationer') {
            $(".probationer_id_container").removeClass("hidden");
            $("#probationer_id").addClass("reqField");
        } else {
            $(".probationer_id_container").addClass("hidden");
            $("#probationer_id").removeClass("reqField");
        }
    });

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
</script>
@endsection
