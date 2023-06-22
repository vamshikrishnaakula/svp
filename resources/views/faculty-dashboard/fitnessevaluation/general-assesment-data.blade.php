{{-- Extends layout --}}
@extends('layouts.faculty.template')

{{-- Content --}}
@section('content')

<section id="fitnessanalytics" class="content-wrapper_sub tab-content">
    <div class="user_manage">
        <div class="row">
            <div class="col-md-9">
                <h4>General Assesment</h4>
            </div>
            <div class="col-md-3">

            </div>
            {{-- <div class="col-md-2 profileimg">
            <img src="{{ asset('images/probationerprofile.png') }}" />
        </div> --}}
    </div>

    <form class="assesment-form" id="assesmentForm" autocomplete="off">
        <div class="row mt-5">

            <div class="col-md-4 card text-center">
                <div class="mb-4">
                    <img class="rounded-circle" width="110" src={{ isset($Probationer->profile_url) ?  $Probationer->profile_url : asset('images/photo_upload.png') }}  />
                </div>
                <div style="text-align: justify">
                    <table class="table mb-0">
                        <tr>
                            <th><label>Name :</label></th>
                            <td>{{ $Probationer->Name }}</td>
                        </tr>
                        <tr>
                            <th><label>Dob :</label></th>
                            <td>{{date('d-m-Y', strtotime($Probationer->Dob))}}</td>
                        </tr>
                        <tr>
                            <th><label>Gender :</label></th>
                            <td>{{ $Probationer->gender }}</td>
                        </tr>
                        <tr>
                            <th><label>Month :</label></th>
                            <td style="display: flex">
                                <div class="input-group" id="datetimepicker33" data-target-input="nearest">
                                    <input type="text" class="form-control datetimepicker-input" data-target="#datetimepicker33" data-toggle="datetimepicker" id="month_datepicker" required />

                                </div>
                                <a href="#" class="pl-2 pt-1" onclick="getAssesmentData()"><img src="{{ asset('images/submit.png') }}" width="25" /></a>
                            </td>
                        </tr>
                    </table>
            </div>
        </div>

        <div class="col-md-8">
            <div class="fitnesseval">
                <div class="fitnessmonthinfo">
                    <label>Month:</label> <span id="dsmonth"></span>
                    <label class="ml-5">Year:</label> <span id="dsyear"></span>
                </div>
                <div class="row p-0" style="background: #9AA4CC; align-items: center">
                    <h5 class="col-md-11 mb-0"></h5>
                    <div class="col-md-1">

                    </div>
                </div>

                <div class="row">

                    <div class="col-md-8">
                        <label>Punctuality</label>
                    </div>
                    <div class="col-md-4">
                        <span id="punctuality_txt">--</span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <label>Behaviour</label>
                    </div>
                    <div class="col-md-4">
                        <span id="behaviour_txt">--</span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <label>Team spirit</label>
                    </div>
                    <div class="col-md-4">
                        <span id="teamspirit_txt">--</span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <label>Learning Efforts</label>
                    </div>
                    <div class="col-md-4">
                        <span id="learningefforts_txt">--</span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <label>Sense of Responsibility</label>
                    </div>
                    <div class="col-md-4">
                        <span id="responsibility_txt">--</span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <label>Leadership Qualities</label>
                    </div>
                    <div class="col-md-4">
                        <span id="leadership_txt">--</span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <label>Command & Control</label>
                    </div>
                    <div class="col-md-4">
                        <span id="commandcontrol_txt">--</span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <label>Sportsmanship</label>
                    </div>
                    <div class="col-md-4">
                        <span id="sportsmanship_txt">--</span>
                    </div>
                </div>
            </div>
            <div class="usersubmitBtns mt-3" style="display: none">
                <div class="mr-4">
                    <input type="hidden" class="form-control value" name="probationer_id" id="probationer_id" value="{{ $Probationer->id }}" />
                </div>
            </div>
        </div>
        </div>
    </form>

    </div>

</section>

@endsection

@section('scripts')
<script>

    function GetMonthName(monthNumber) {
        var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        return months[monthNumber - 1];
    }

    $(document).ready(function () {
        $("#datetimepicker33").datetimepicker({
            viewMode: 'months',
            format: 'MM/YYYY'
        });

        // Get Assesment Data on page load
        var d = new Date();
        var month   = d.getMonth()+1;
        var year    = d.getFullYear();

        var month_year = month+'/'+year;
        getAssesmentData(month_year);
    });

    var previousValue = $("#month_datepicker").val();

    /* *************** Get Assesment Data for selected month *************** */
    function getAssesmentData(month_year = '') {
        if(month_year.length == 0) {
            var month_year    = $('#month_datepicker').val();
        }
        var probationer_id  = $('#probationer_id').val();

        $.ajax({
            url: appUrl +'/fitness/ajax',
            data: {
                "month_year": month_year,
                "probationer_id": probationer_id,
                "requestName": 'get_assesment_data'
            },
            type: "POST",
            beforeSend: function (xhs) {
                window.loadingScreen("show");
            },
            success: function (rData) {
                window.loadingScreen("hide");

                let rObj = JSON.parse(rData);
                if(rObj.status == "success") {
                    $.each(rObj.data, function (name, val) {
                        $('#'+name).val(val);
                        $('#'+name+'_txt').html(val);
                    });

                    var monthYear   = month_year.split('/');
                    var monthName   = GetMonthName(monthYear[0]);
                    $("#dsmonth").html(monthName);
                    $("#dsyear").html(monthYear[1]);
                } else {
                    alert(rObj.message);
                }

            }
        });
    }

</script>
@endsection
