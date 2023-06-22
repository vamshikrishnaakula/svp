{{-- Extends layout --}}
<?php
$app_view = session('app_view');
?>
@extends(($app_view) ? 'layouts.pbdash.mobile-template' : 'layouts.default')

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
<section id="addstaff" class="content-wrapper_sub">
    <div class="row">
        <div class="col-md-12">
            <h3>Compare Probationers</h3>
        </div>
    </div>
    <div class="row mt-3 compare_header align-items-center">
        <div class="col-md-6">
            <div class="row align-items-center">
                <label class="mb-0">Batch</label>
                <div class="col-sm-4">
                    <select class="form-control mb-0" id="batch_id" name="batch_id">
                        <option value="">Select Batch</option>
                        @if( !empty($batches) )
                        @foreach($batches as $batch)
                        <option value="{{ $batch->id }}" @if($batch->id == Session::get('current_batch')) selected @endif>{{ $batch->BatchName }}</option>
                        @endforeach
                        @endif
                    </select>
                </div>

                {{-- <a class="pl-2" class="btn formBtn submitBtn"><img src="{{ asset('images/submit.png') }}" width="25" /></a> --}}
            </div>
        </div>
        <div class="col-md-6 text-right">
            <div class="download_section ">
                {{-- <a class="mr-4"><img src="{{ asset('images/download1.png') }}" width="25" /></a> --}}
                <a onclick="window.print(); return false;"><img src="{{ asset('images/print_view_icon.svg') }}" width="25" /></a>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table id="pb_compare_table" class="table table-borderless table-nowrap">
            <tr class="user_card_row" data-user-count="2">
                <td>
                    <div class="user_card_blank"></div>
                </td>
                <td>
                    <div class="user_card_container">
                        <div class="card">
                            <div class="card-body p-3">
                                <div class="text-center">
                                    <img src="{{ asset('images/user_icon.png') }}" alt="user icon" class="rounded-circle" width="100">
                                </div>
                                <div class="mt-5">
                                    <div class="form-group">
                                        <select class="form-control squad_id">
                                            <option value="">Select Squad</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-0">
                                        <select class="form-control probationer_id">
                                            <option value="">Select Probationer</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="user_card_container">
                        <div class="card">
                            <div class="card-body p-3">
                                <div class="text-center">
                                    <img src="{{ asset('images/user_icon.png') }}" alt="user icon" class="rounded-circle" width="100">
                                </div>
                                <div class="mt-5">
                                    <div class="form-group">
                                        <select class="form-control squad_id">
                                            <option value="">Select Squad</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-0">
                                        <select class="form-control probationer_id">
                                            <option value="">Select Probationer</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="user_card_container user_card_add">
                        <div class="card">
                            <div class="plus-container">
                                <span class="plus user_card_add_btn">+</span>
                                <span class="text">Add</span>
                            </div>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="user_card_container"></div>
                </td>
            </tr><!-- user_card_row -->

            <tr class="compare_btn_row">
                <td colspan="5">
                    <div class="text-center">
                        <button type="button" id="compare_btn" class="btn btn-primary compare_btn" style="width:140px;">Compare</button>
                    </div>
                </td>
            </tr>
            <tr class="filter_btns_row hidden">
                <td colspan="5">
                    <div class="filter_title">
                        <label>Filters :</label>
                    </div>
                    <div id="fliter_section" class="fliter_section">
                        <div class="activity-filter-btns"></div>

                        <div class="other-filter-btns hidden">
                            {{-- <button type="button" data-toggle="collapse" class="col-sm-2 filter-btn">Attendence
                                <span class="hidden close_icon" aria-hidden="true"><i class="fas fa-times"></i></span>
                            </button>
                            <button type="button" data-toggle="collapse" class="col-sm-2 filter-btn">UAC
                                <span class="hidden close_icon" aria-hidden="true"><i class="fas fa-times"></i></span>
                            </button>
                            <button type="button" data-toggle="collapse" class="col-sm-2 filter-btn">Health Profile
                                <span class="hidden close_icon" aria-hidden="true"><i class="fas fa-times"></i></span>
                            </button>
                            <button type="button" data-toggle="collapse" class="col-sm-2 filter-btn">Medicall Records
                                <span class="hidden close_icon" aria-hidden="true"><i class="fas fa-times"></i></span>
                            </button>
                            <button type="button" data-toggle="collapse" class="col-sm-2 filter-btn">Fitness Evalution
                                <span class="hidden close_icon" aria-hidden="true"><i class="fas fa-times"></i></span>
                            </button>
                            <button type="button" data-toggle="collapse" class="col-sm-2 filter-btn filter-selectAll">Select all
                                <span class="hidden close_icon close_icon_all" aria-hidden="true"><i class="fas fa-times"></i></span>
                            </button> --}}
                        </div>
                    </div>
                </td>
            </tr><!-- filter_btns_row -->

            <tr id="data_result_row">
                <td colspan="5"></td>
            </tr><!-- data_result_row -->

        </table>
    </div>

    <hr>
</section>
@endsection



@section('scripts')
<script>
    // Get squads on page load (if batch selected)
    $(function () {
        var batchId = $("select#batch_id").val();
        if(batchId && batchId.length > 0) {
            getCPsquads (batchId);
        }
    });

    // Get squads on batch select
    $(document).on("change", "select#batch_id", function () {
        var batchId = $(this).val();
        getCPsquads (batchId);
    });

    function getCPsquads (batchId) {
        $.ajax({
            url: appUrl+"/squadDropdownOptions",
            data: {
                batch_id: batchId
            },
            type: "POST",
            beforeSend: function (xhr) {
                window.loadingScreen("show");
            },
            success: function (rData) {
                window.loadingScreen("hide");

                $("#pb_compare_table .squad_id").each(function(){
                    $(this).html(rData);
                });
                $("#pb_compare_table .probationer_id").each(function(){
                    $(this).html(`<option value="">Select Probationer</option>`);
                });
            }
        });
    }

    // Get probationers on squad select
    $(document).on("change", "table select.squad_id", function () {
        var squadId = $(this).val();
        var pbItem  = $(this).closest(".user_card_container").find("select.probationer_id");

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
