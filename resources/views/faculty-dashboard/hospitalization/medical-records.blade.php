{{-- Extends layout --}}
@extends('layouts.faculty.template')

{{-- Content --}}
@section('content')

@if ($message = Session::get('success'))
<div class="alert alert-success">
    <p>{{ $message }}</p>
</div>
@elseif ($message = Session::get('delete'))
<div class="alert alert-danger">
    <p>{{ $message }}</p>
</div>

@endif

<section id="healthprofile" class="content-wrapper_sub tab-content">
    <div class="user_manage">
        <div class="row">
            <div class="col-md-9">
                <h4>Medical Records</h4>
            </div>
            <div class="col-md-3">
                <div class="useractionBtns"></div>
            </div>
        </div>

        <div class="mt-5 mx-auto" style="max-width:405px;">
            <label class="text-center">Roll Number :</label>
            <div class="d-flex">
                <div class="">
                    <input class="form-control" type="number" id="roll_no" name="roll_no">
                </div>
                <div class="rollnosubmit">
                    <a href="#" class="pl-3" id="get_medical_records"><img src="{{ asset('images/submit.png') }}" /></a>
                </div>
            </div>
        </div>

        <div id="medical_records_data" class="mt-5"></div>

</section>
@endsection

@section('scripts')

<script>
    $('#get_medical_records').click(function (e) {
        e.preventDefault();

        var roll_no = $('input#roll_no').val();
        $.ajax({
            url: appUrl+'/faculty-ajax',
            type: "POST",
            data: {
                requestName: "get_medical_records",
                roll_no: roll_no
            },
            beforeSend: function (xhs) {
                window.loadingScreen("show");
            },
            success: function (rData) {
                window.loadingScreen("hide");
                $("#medical_records_data").html(rData);
            }
        });
    });
</script>

@endsection
