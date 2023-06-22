{{-- Extends layout --}}
<?php
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

<section id="fitnessanalytics" class="content-wrapper_sub tab-content">
    <div class="user_manage">
        <div class="row">
            <div class="col-md-9">
                <h4>General Assessment</h4>
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
                <div class="img_center_align ">
                   <img class="rounded-circle" width="110" src={{ isset($Probationer->profile_url) ?  "data:image/png;base64,".$Probationer->profile_url : asset('images/photo_upload.png') }}  />
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
                            <th><label>Date :</label></th>
                            <td style="display: flex">
                                {{-- <div class="input-group" id="datetimepicker1" data-target-input="nearest">
                                    <input type="text" class="form-control datetimepicker-input" data-target="#datetimepicker1" data-toggle="datetimepicker1" id="month_datepicker" onchange="getAssesmentData()" required />
                                </div> --}}
                                {{--  <a href="#" class="pl-2 pt-1" onclick="getAssesmentData()"><img src="{{ asset('images/submit.png') }}" width="25" /></a>  --}}

                                <div class="input-group" id="staticticsDatetimepicker" data-target-input="nearest" name="date">
                                    <input type="text" class="form-control"
                                        data-target="#staticticsDatetimepicker"  data-toggle="datetimepicker" name="date" id="month_datepickers" />
                                </div>
                                <a href="#" class="col-md-6 pl-3" id="get_general_data"><img src="{{ asset('images/submit.png') }}" /></a>
                            </td>
                        </tr>
                    </table>
            </div>
        </div>

        <div class="col-md-8">
            <div class="fitnesseval">
                <div class="fitnessmonthinfo">
                    <label>date:</label> <span id="dsmonth"></span>
                </div>
                <div class="row p-0" style="background: #9AA4CC; align-items: center">
                    <h5 class="col-md-11 mb-0"></h5>
                    <div class="col-md-1">
                        <a class="editvalue" data-toggle="tooltip" title="Edit"><i class="far fa-edit"></i></a>
                        <a class="cancel" style="display: none" data-toggle="tooltip" title="cancel"><i class="far fa-times-circle"></i></a>
                    </div>
                </div>

                <div class="row">

                    <div class="col-md-8">
                        <label>Punctuality</label>
                    </div>
                    <div class="col-md-4">
                        <span id="punctuality_txt">--</span>
                        <div class="fitnessinputValue" style="display: none">
                            <input type="text" name="punctuality" id="punctuality" class="form-control value" value="" />
                            {{-- <select name="punctuality" id="punctuality" class="form-control value">
                                <option value="">-- Select --</option>
                                <option value="A">OUTSTANDING</option>
                                <option value="B">VERYGOOD</option>
                                <option value="C">GOOD</option>
                                <option value="D">AVERAGE</option>
                                <option value="E">POOR</option>
                            </select> --}}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <label>Behaviour</label>
                    </div>
                    <div class="col-md-4">
                        <span id="behaviour_txt">--</span>
                        <div class="fitnessinputValue" style="display: none">
                            <input type="text" name="behaviour" id="behaviour" class="form-control value" value="" />
                            {{-- <select name="behaviour" id="behaviour" class="form-control value">
                                <option value="">-- Select --</option>
                                <option value="A">OUTSTANDING</option>
                                <option value="B">VERYGOOD</option>
                                <option value="C">GOOD</option>
                                <option value="D">AVERAGE</option>
                                <option value="E">POOR</option>
                            </select> --}}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <label>Team spirit</label>
                    </div>
                    <div class="col-md-4">
                        <span id="teamspirit_txt">--</span>
                        <div class="fitnessinputValue" style="display: none">
                            <input type="text" name="teamspirit" id="teamspirit" class="form-control value" value="" />
                            {{-- <select name="teamspirit" id="teamspirit" class="form-control value">
                                <option value="">-- Select --</option>
                                <option value="A">OUTSTANDING</option>
                                <option value="B">VERYGOOD</option>
                                <option value="C">GOOD</option>
                                <option value="D">AVERAGE</option>
                                <option value="E">POOR</option>
                            </select> --}}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <label>Learning Efforts</label>
                    </div>
                    <div class="col-md-4">
                        <span id="learningefforts_txt">--</span>
                        <div class="fitnessinputValue" style="display: none">
                            <input type="text" name="learningefforts" id="learningefforts" class="form-control value" value="" />
                            {{-- <select name="learningefforts" id="learningefforts" class="form-control value">
                                <option value="">-- Select --</option>
                                <option value="A">OUTSTANDING</option>
                                <option value="B">VERYGOOD</option>
                                <option value="C">GOOD</option>
                                <option value="D">AVERAGE</option>
                                <option value="E">POOR</option>
                            </select> --}}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <label>Sense of Responsibility</label>
                    </div>
                    <div class="col-md-4">
                        <span id="responsibility_txt">--</span>
                        <div class="fitnessinputValue" style="display: none">
                            <input type="text" name="responsibility" id="responsibility" class="form-control value" value="" />
                            {{-- <select name="responsibility" id="responsibility" class="form-control value">
                                <option value="">-- Select --</option>
                                <option value="A">OUTSTANDING</option>
                                <option value="B">VERYGOOD</option>
                                <option value="C">GOOD</option>
                                <option value="D">AVERAGE</option>
                                <option value="E">POOR</option>
                            </select> --}}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <label>Leadership Qualities</label>
                    </div>
                    <div class="col-md-4">
                        <span id="leadership_txt">--</span>
                        <div class="fitnessinputValue" style="display: none">
                            <input type="text" name="leadership" id="leadership" class="form-control value" value="" />
                            {{-- <select name="leadership" id="leadership" class="form-control value">
                                <option value="">-- Select --</option>
                                <option value="A">OUTSTANDING</option>
                                <option value="B">VERYGOOD</option>
                                <option value="C">GOOD</option>
                                <option value="D">AVERAGE</option>
                                <option value="E">POOR</option>
                            </select> --}}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <label>Command & Control</label>
                    </div>
                    <div class="col-md-4">
                        <span id="commandcontrol_txt">--</span>
                        <div class="fitnessinputValue" style="display: none">
                            <input type="text" name="commandcontrol" id="commandcontrol" class="form-control value" value="" />
                            {{-- <select name="commandcontrol" id="commandcontrol" class="form-control value">
                                <option value="">-- Select --</option>
                                <option value="A">OUTSTANDING</option>
                                <option value="B">VERYGOOD</option>
                                <option value="C">GOOD</option>
                                <option value="D">AVERAGE</option>
                                <option value="E">POOR</option>
                            </select> --}}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <label>Sportsmanship</label>
                    </div>
                    <div class="col-md-4">
                        <span id="sportsmanship_txt">--</span>
                        <div class="fitnessinputValue" style="display: none">
                            <input type="text" name="sportsmanship" id="sportsmanship" class="form-control value" value="" />
                            {{-- <select name="sportsmanship" id="sportsmanship" class="form-control value">
                                <option value="">-- Select --</option>
                                <option value="A">OUTSTANDING</option>
                                <option value="B">VERYGOOD</option>
                                <option value="C">GOOD</option>
                                <option value="D">AVERAGE</option>
                                <option value="E">POOR</option>
                            </select> --}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="usersubmitBtns mt-3" style="display: none">
                <div class="mr-4">
                    <input type="hidden" class="form-control value" name="probationer_id" id="probationer_id" value="{{ $Probationer->id }}" />
                    <input type="hidden" name="month" id="month" class="form-control value" value="{{ date('m') }}" />
                    <input type="hidden" name="year" id="year" class="form-control value" value="{{ date('Y') }}" />

                    <button type="button" onclick="submitAssesment()" class="btn formBtn submitBtn">Submit</button>
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

$("#staticticsDatetimepicker").datetimepicker ({
        viewMode: 'months',
        format: 'MM-YYYY',
    });

    $(document).ready(function () {
        $(document).on("click", ".fitnesseval a.editvalue", function () {
            $(".fitnessinputValue").show();
            $(".fitnesseval span").hide();
            $(this).hide();
            $(".fitnesseval a.cancel").show();
            $("#assesmentForm .usersubmitBtns").show();
        });



        $(document).on("click", ".fitnesseval a.cancel", function () {
            //
            $(".fitnessinputValue").hide();
            $(".fitnesseval span").show();
            $(".fitnesseval a.editvalue").show();
            $(".fitnesseval a.cancel").hide();
            $("#assesmentForm .usersubmitBtns").hide();
        });
    });

    function GetMonthName(monthNumber) {
        var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        return months[monthNumber - 1];
    }

    /* *************** Submit Assesment Form *************** */
    function submitAssesment() {
        var formEl = $('#assesmentForm');
        var FrmData = new FormData(formEl[0]);
        var date = $('#dsmonth').html();
        FrmData.append('requestName', 'submit_assesment_form');

        $.ajax({
            url: appUrl +'/fitness/ajax',
            data: FrmData,
            date:date,
            type: "POST",
            processData: false,
            contentType: false,
            beforeSend: function (xhs) {
                window.loadingScreen("show");
            },
            success: function (rData) {
                window.loadingScreen("hide");

                let rObj = JSON.parse(rData);
                if(rObj.status == "success") {
                    alert("updated Suceesfully");

                    window.location.reload();
                } else {
                    alert(rObj.message);
                }
            }
        });
    }

    $("#get_general_data").click(function()
    {
        var month_year = $('#month_datepickers').val();
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
                   $("#dsmonth").html(rObj.data['month'] + "/" + rObj.data['year']);
                } else {
                    alert(rObj.message);
                }
            }
        });
    });




    // $(document).ready (function (){
    //     var jDates = {!! $dt !!};
    //       var date = new Date();
    //           $("#month_datepicker").datepicker ({
    //               format: 'DD/MM/YYYY',
    //               beforeShowDay: function(date) {
    //                   var m = date.getMonth() + 1, d = (date.getDate() < 10 ? '0' : '') + date.getDate(), y = date.getFullYear();
    //                   var month = m < 10 ? '0' + m : '' + m;
    //                   for (i = 0; i < jDates.length; i++) {
    //                       if($.inArray(y + '-' + (month) + '-' + d,jDates) != -1) {
    //                           //return [false];
    //                           return [true, 'ui-state-active', ''];
    //                       }
    //                   }
    //                   return [true];

    //               }
    //           });
    //   });

    // var previousValue = $("#month_datepicker").val();

    /* *************** Get Assesment Data for selected month *************** */
    // function getAssesmentData() {
    //     var month_year    = $('#month_datepicker').val();
    //     var probationer_id  = $('#probationer_id').val();

    //     $.ajax({
    //         url: appUrl +'/fitness/ajax',
    //         data: {
    //             "month_year": month_year,
    //             "probationer_id": probationer_id,
    //             "requestName": 'get_assesment_data'
    //         },
    //         type: "POST",
    //         beforeSend: function (xhs) {
    //             window.loadingScreen("show");
    //         },
    //         success: function (rData) {
    //             window.loadingScreen("hide");

    //             let rObj = JSON.parse(rData);
    //             if(rObj.status == "success") {
    //                 $.each(rObj.data, function (name, val) {
    //                     $('#'+name).val(val);
    //                     $('#'+name+'_txt').html(val);
    //                 });

    //                 var monthYear   = month_year.split('/');
    //                 var monthName   = GetMonthName(monthYear[0]);
    //                $("#dsmonth").html(rObj.data['date']);
    //             } else {
    //                 alert(rObj.message);
    //             }
    //         }
    //     });
    // }

</script>
@endsection
