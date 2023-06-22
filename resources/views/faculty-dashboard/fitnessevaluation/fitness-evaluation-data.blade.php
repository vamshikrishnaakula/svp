{{-- Extends layout --}}
@extends('layouts.faculty.template')

{{-- Content --}}
@section('content')

<section id="fitnessanalytics" class="content-wrapper_sub tab-content">
    <div class="user_manage">
        <div class="row">
            <div class="col-md-9">
                <h4>Fitness Analytics</h4>
            </div>
            <div class="col-md-3">
                <div class="useractionBtns">
                </div>
            </div>
        </div>
        <form class="fitnessevalform" id="fitness_evaluvation" autocomplete="off">
            <div class="row mt-5">

                <div class="col-md-3 card text-center">
                    <div class="img_center_align">
                        <img class="rounded-circle" width="110" src={{ isset($get_probationer->profile_url) ?  $get_probationer->profile_url : asset('images/photo_upload.png') }}  />
                    </div>
                    <div style="text-align: justify">
                        <table class="table mb-0">
                            <tr>
                                <th><label>Name :</label></th>
                                <td>{{ $get_probationer->Name }}</td>
                            </tr>
                            <tr>
                                <th><label>Dob :</label></th>
                                <td>{{date('d-m-Y', strtotime($get_probationer->Dob))}}</td>
                            </tr>
                            <tr>
                                <th><label>Gender :</label></th>
                                <td>{{ $get_probationer->gender }}</td>
                            </tr>
                            <tr>
                                <th><label>Month :</label></th>
                                <td style="display: flex">
                                    <div class="input-group" id="datetimepicker33" data-target-input="nearest">
                                        <input type="text" class="form-control datetimepicker-input" data-target="#datetimepicker33" data-toggle="datetimepicker" id="month_datepicker" required />

                                    </div>
                                    <a href="#" class="pl-2 pt-1" onclick="getfitnessdata()"><img src="{{ asset('images/submit.png') }}" width="25" /></a>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="col-md-9">
                    <div class="fitnesseval" style="max-height: unset;">
                        <div class="fitnessmonthinfo">
                            <label>Month:</label> <span id="dsmonth"></span>
                            <label class="ml-5">Year:</label>
                            <span id="dsyear"></span>
                        </div>
                        <div class="row p-0" style="background: #9AA4CC; align-items: center">
                            <h5 class="col-md-11 mb-0">Fitness</h5>
                            <div class="col-md-1"></div>
                        </div>

                        <div class="row">

                            <div class="col-md-8">
                                <label>Weight</label>
                            </div>
                            <div class="col-md-4">
                                <span id="sweight">{{(isset($fitness->weight) ? $fitness->weight:'-')}}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <label>BMI</label>
                            </div>
                            <div class="col-md-4">
                                <span id="sbmi">{{(isset($fitness->bmi) ? $fitness->bmi:'-')}}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <label>Body Fat</label>
                            </div>
                            <div class="col-md-4">
                                <span id="sbodyfat">{{(isset($fitness->bodyfat) ? $fitness->bodyfat:'-')}}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <label>Fitness Score</label>
                            </div>
                            <div class="col-md-4">
                                <span id="sfitnessscore">{{(isset($fitness->fitnessscore) ? $fitness->fitnessscore:'-')}}</span>
                            </div>
                        </div>
                        <h5>Endurance</h5>
                        <div class="row">
                            <div class="col-md-8">
                                <label>Grade</label>
                            </div>
                            <div class="col-md-4">
                                <span id="segrade">{{(isset($fitness->endurancegrade) ? $fitness->endurancegrade:'-')}}</span>
                            </div>
                        </div>
                        <h5>Strength</h5>
                        <div class="row">
                            <div class="col-md-8">
                                <label>Grade</label>
                            </div>
                            <div class="col-md-4">
                                <span id="ssgrade">{{(isset($fitness->strengthgrade) ? $fitness->strengthgrade:'-')}}</span>
                            </div>
                        </div>
                        <h5>Flexibility</h5>
                        <div class="row">
                            <div class="col-md-8">
                                <label>Grade</label>
                            </div>
                            <div class="col-md-4">
                                <span id="sfgrade">{{(isset($fitness->flexibilitygrade) ? $fitness->flexibilitygrade:'-')}}</span>
                            </div>
                        </div>
                    </div>
                    <div class="usersubmitBtns mt-3" style="display: none">
                        <div class="mr-4">
                            <input type="hidden" class="form-control value" name="probationer_id" id="probationer_id" value="{{ $get_probationer->id }}" />
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

        // Get Fitness Data on page load
        var d = new Date();
        var month   = d.getMonth()+1;
        var year    = d.getFullYear();

        var month_year = month+'/'+year;
        getfitnessdata(month_year);
    });
    var previousValue = $("#month_datepicker").val();

    function getfitnessdata(month_year = '') {
        if(month_year.length == 0) {
            var month_year    = $('#month_datepicker').val();
        }
        var probationer_id  = $('#probationer_id').val();

        // var currentValue = $('#month_datepicker').val();
        $.ajax({
            url: '/prob_month_fitness',
            type: "POST",
            data: {
                "_token": "{{ csrf_token() }}",
                "id": month_year,
                "pid": probationer_id
            },
            beforeSend: function (xhs) {
                window.loadingScreen("show");

            },

            success: function (rData) {
                window.loadingScreen("hide");

                let rObj = JSON.parse(rData);
                if(rObj.status == "success") {
                    $("#sweight").text(rObj.data.weight);
                    $("#sbmi").text(rObj.data.bmi);
                    $("#sbodyfat").text(rObj.data.bodyfat);
                    $("#sfitnessscore").text(rObj.data.fitnessscore);
                    $("#segrade").text(rObj.data.endurancegrade);
                    $("#ssgrade").text(rObj.data.strengthgrade);
                    $("#sfgrade").text(rObj.data.flexibilitygrade);

                    // $("#dsmonth").text(GetMonthName(rObj.data.month));
                    // $("#dsyear").text(rObj.data.year);
                    var monthYear   = month_year.split('/');
                    var monthName   = GetMonthName(monthYear[0]);
                    $("#dsmonth").html(monthName);
                    $("#dsyear").html(monthYear[1]);
                }
                else {
                    alert("No records exits")
                }
            }
        })

    }
</script>
@endsection
