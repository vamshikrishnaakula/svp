{{-- Extends layout --}}
@extends('layouts.faculty.template')

{{-- Content --}}
@section('content')

<div id="error"></div>
<section id="medicalexamination" class="content-wrapper_sub">
    <div class="user_manage">
        <div class="row">
            <div class="col-md-5">
                <h4>Medical Examination</h4>
                <div id="timetableUpdate_form_status" class="mt-5"></div>
            </div>
        </div>

        <div class="mt-4">
            <ul class="nav nav-tabs">
                <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#addnew">Add New</a></li>
                <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#viewrecords">View Records</a></li>
            </ul>
        </div>

        <div class="tab-content p-5">
            <div id="addnew" class="tab-pane fade in active show">
                <form  id="get_prob_details" name="get_prob_details" style="width: 70%; margin: 0 auto">
                    <div class="row">
                        <div class="col-md-2"></div>
                        <label class="col-md-3 text-center">Name / Roll No :</label>
                        <input class="col-md-3 form-control" type="text" id="roll_no" name="roll_no">
                        <input class="form-control" type="hidden" id="prob_id" name="prob_id">


                        <div class="rollnosubmit col-md-3">
                            <button type="submit" class ="btn btn-img" ><img src="{{ asset('images/submit.png') }}" /></button>
                        </div>
                    </div>
                </form>


                <form id="medical_examination">
                <div class="row profileInfo mt-5">
                    <div class="col-md-4">
                        <label>Name :</label>
                        <span id="prob_name"></span>
                    </div>
                    <div class="col-md-2">
                        <label>Gender :</label>
                        <span id="prob_gender"></span>
                    </div>

                    <div class="col-md-2.5">
                        <label>Medical Examination Test Date :</label>
                    </div>

                    <div class="col-md-3">
                        <div class="input-group" id="insert_datetimepicker" data-target-input="nearest" name="Dob">
                        <input type="text" class="form-control datetimepicker-input" data-target="#insert_datetimepicker" data-toggle="datetimepicker" name="date" id="month_datepicker" autocomplete="off" required /></div>
                    </div>
                </div>


                    <div class="row p-5">
                        <div class="col-md-5">
                            <div class="form-group row">
                                <label class="col-md-5">Temperature :</label>
                                <input type="text" class="form-control col-md-5" name="temperature">
                                <input type="hidden" class="form-control col-md-5" id="pid" name="pid_medical">
                            </div>
                            <div class="form-group row">
                                <label class="col-md-5">Antigen Test :</label>
                                <input type="text" class="form-control col-md-5" name="antigentest">
                            </div>
                            <div class="form-group row">
                                <label class="col-md-5">RTPCR :</label>
                                <input type="text" class="form-control col-md-5" name="rtpcr">
                            </div>
                            <div class="form-group row">
                                <label class="col-md-5">Hemoglobin :</label>
                                <input type="text" class="form-control col-md-5" name="haemoglobin">
                            </div>
                            <div class="form-group row">
                                <label class="col-md-5">Calcium :</label>
                                <input type="text" class="form-control col-md-5" name="calcium">
                            </div>
                            <div class="form-group row">
                                <label class="col-md-5">Vitamin D :</label>
                                <input type="text" class="form-control col-md-5" name="vitamind">
                            </div>
                            <div class="form-group row">
                                <label class="col-md-5">Vitamin B12 :</label>
                                <input type="text" class="form-control col-md-5" name="vitaminb12">
                            </div>
                        </div>
                        <div class="col-md-2"></div>
                        <div class="col-md-5 p-4" style="background: #F7FCFF;">
                            <div class="form-group">
                                <label>Pre-existing Injury :</label>
                                <textarea class="form-control" name="preexistinginjury"></textarea>
                            </div>
                            <div class="form-group">
                                <label>Family members ever tested Covid +ve :</label>
                                <input type="text" class="form-control" name="covid">
                            </div>
                        </div>
                    </div>
                    <div class="usersubmitBtns mt-4">
                        <button type="button" onclick="getdata()" class="btn formBtn submitBtn">Submit</button>
                    </div>
                </form>
            </div>
            <div id="viewrecords" class="tab-pane fade in">
                <form style="width: 70%; margin: 0 auto" autocomplete="off" id="viewmedicalrecords" name="viewmedicalrecords">
                    <div class="row">
                        <div class="col-md-1"></div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Month :</label>
                                <div class="input-group" id="medical_datetimepicker" data-target-input="nearest" name="Dob">
                                    <input type="text" class="form-control datetimepicker-input" data-target="#medical_datetimepicker" data-toggle="datetimepicker" name="Dob" id="month_datepicker1" required /></div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label>Name / Roll No :</label>
                            <input type="text" class="form-control" id="roll_no" required>
                            <input class="form-control" type="hidden" id="prob_id" name="prob_id">


                        </div>
                        <div class="col-md-2" style="align-self: center; margin-top: 15px;">
                            <div class="usersubmitBtns">
                                <!-- <button type="submit" onclick="getmedicaldata()" class="btn formBtn submitBtn">Get Data</button> -->
                                <button type="submit" class="btn formBtn submitBtn">Get Data</button>

                                 <a class="pl-2 pt-1" onclick="downloadmedical()"><img src="{{ asset('images/download1.png') }}" width="25"></a>
                            </div>
                        </div>
                    </div>

                </form>
                <div id="no_data_alert"></div>

                <div class="row profileInfo mt-5">
                    <div class="col-md-4">
                        <label>Name :</label>
                        <span id="pname"></span>
                    </div>
                    <div class="col-md-2">
                        <label>Gender :</label>
                        <span id="pgender"></span>
                    </div>
                </div>
                <div class="medicalexam">

                    <div class="row">
                        <div class="col-md-12 text-right mr-5">
                            <!-- <img class="editprofiles" src="./images/edithealthprofile.png" />
            <img class="cancelprofiles" style="display: none" src="./images/wrong.png" /> -->
                        </div>
                    </div>
                    <form>

                        <div class="row p-4">
                            <div class="col-md-5">
                                <div class="form-group row">
                                    <label class="col-md-3">Temperature :</label>
                                    <input type="text" class="form-control col-md-5">

                                    <span id="temperature"></span>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3">Antigen Test :</label>
                                    <span id="antigen"></span>
                                    <input type="text" class="form-control col-md-5">
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3">RTPCR :</label>
                                    <span id="rtpcr"></span>
                                    <input type="text" class="form-control col-md-5">
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3">Hemoglobin :</label>
                                    <span id="hemoglobin"></span>
                                    <input type="text" class="form-control col-md-5">
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3">Calcium :</label>
                                    <span id="calcium"></span>
                                    <input type="text" class="form-control col-md-5">
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3">Vitamin D :</label>
                                    <span id="vitamind"></span>
                                    <input type="text" class="form-control col-md-5">
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3">Vitamin B12 :</label>
                                    <span id="vitaminb12"></span>
                                    <input type="text" class="form-control col-md-5">
                                </div>
                            </div>
                            <div class="col-md-2"></div>
                            <div class="col-md-5 p-4" style="background: #F7FCFF;">

                                <div class="form-group">
                                    <div class="d-flex">
                                        <label>Pre-existing Injury :</label>
                                        <p id="pre_existing"></p>
                                    </div>
                                    <textarea class="form-control"></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Family members ever tested Covid +ve :</label>
                                    <p id="covid"></p>
                                    <input type="text" class="form-control">
                                </div>

                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
    $(document).on('keydown.autocomplete', '#roll_no', function() {
        $(this).autocomplete({
            source: "{{ route('prob_autosuggestion') }}",
            minLength: 1,
            select:function(event,ui) {
              $("#prob_id").val(ui.item['id']);

          }
        });
    });


    $('#medicalexamination .editprofiles').click(function() {
      debugger
        $('.medicalexam span, .medicalexam p').hide();
        $('.medicalexam input, .medicalexam textarea, .cancelprofiles').css('display', 'inline-block');
        $(this).hide();
    })

    $('#medicalexamination .cancelprofiles').click(function() {
      debugger
        $('.medicalexam span, .medicalexam p, .editprofiles').show();
        $('.medicalexam input, .medicalexam textarea, .cancelprofiles').hide();
        $(this).hide();
    })


     $('#get_prob_details').submit(function(e) {debugger;
        e.preventDefault();
      var id = $('#prob_id').val();
      $.ajax({
                url: '/prob_medical_exam',
                type: "POST",
                data:{
                    "_token": "{{ csrf_token() }}",
                        "id":id
                    },
                    beforeSend: function (xhs) {
                        window.loadingScreen("show");
                  },
                success: function(data){debugger;
                  window.loadingScreen("hide");
                  if(data != '')
                  {
                    $('#prob_name').text(data.Name);
                    $('#prob_gender').text(data.gender);
                    $('#pid').val(data.pid);
                  }
                  else
                  {
                     $('#error').empty();
                    var e = $('<div class="alert alert-danger"><p>Please Enter valid Probationer RollNumber</p></div>');
                  $('#error').append(e);
                    $("div.alert").fadeTo(2000, 500).slideUp(500, function(){
                    $("div.alert").slideUp(500);
                    });
                  }
               }
       })
    });


    function getdata()
    {
      var validate =  $("#medical_examination").validate({
            rules: {
                Dob: {
                required: true,
            }
            }
        }).form();
        if(validate == true)
        {
      var input =  $("form#medical_examination");
      var inputs =  new FormData(input[0]);
      var statusDiv = $("#timetableUpdate_form_status");
      $.ajax({
            url: '/insert_medical_exam',
            type: "POST",
            data:inputs,
            processData: false,
            contentType: false,
            beforeSend: function(xhr) {
                window.loadingScreen("show");

                statusDiv.html("");
            },
            success: function(rData){
                let rObj = JSON.parse(rData);
                window.loadingScreen("hide");
                if (rObj.status == "success") {
                    $("#successModalContent").html(
                        '<div class="msg msg-success msg-full">' + rObj.message + "</div>"
                    );
                    $("#successModal").modal("show");
                } else {
                    statusDiv.html(
                        '<div class="msg msg-danger msg-full">' + rObj.message + "</div>"
                    );
                }
              $(':input').not(':button, :submit, :reset, :hidden, :checkbox, :radio').val('');
            }
        })
        }
    }
    $('#viewmedicalrecords').submit(function(e) {
        e.preventDefault();
      var validate =  $("#viewmedicalrecords").validate({
            rules: {
              month_datepicker1: {
                required: true,
            },
            prob_rollnumber1: {
                required: true,
            }
            }
        }).form();
        if(validate == true)
        {
      var rollnumber = $('#prob_id').val();
      var date = $('#month_datepicker1').val();

      $.ajax({
            url: '/view_medical_exam',
            type: "POST",
            data:{
                "_token": "{{ csrf_token() }}",
                "date":date,
                "id":rollnumber,
            },
            beforeSend: function (xhs) {
                window.loadingScreen("show");
                $("#no_data_alert").html('');
            },
            success: function(rData){
                window.loadingScreen("hide");
                let rObj = JSON.parse(rData);
                if(rObj.status !== 'success')
                {

                    $('#pre_existing, #covid, #temperature, #antigen, #rtpcr, #hemoglobin, #calcium, #vitamind, #vitaminb12').text('');
                    $("#no_data_alert").html('<p class="text-danger mb-0" style="margin:30px 0 -30px !important;">'+rObj.message+'.</p>')
                }
                else {
                    $("#pname").text(rObj.data.Name);
                    $("#pgender").text(rObj.data.gender);
                    $("#temperature").text(rObj.data.temperature);
                    $("#antigen").text(rObj.data.antigentest);
                    $("#rtpcr").text(rObj.data.rtpcr);
                    $("#hemoglobin").text(rObj.data.haemoglobin);
                    $("#calcium").text(rObj.data.calcium);
                    $("#vitamind").text(rObj.data.vitamind);
                    $("#vitaminb12").text(rObj.data.vitaminb12);
                    $("#pre_existing").text(rObj.data.preexistinginjury);
                    $("#covid").text(rObj.data.covid);
                }
            }
        })
      }
    });


    function downloadmedical()
    {
            var validate = $("#viewmedicalrecords").validate({
                        rules : {
                            month_datepicker1 : {
                                required : true,
                            },
                            roll_no : {
                                required : true,
                            }
                            }
                }).form()
                if(validate == true)
                {
                    var month_datepicker1 = $("#month_datepicker1").val();
                    var roll_no = $("#prob_id").val();

                    $.ajax({
                        url : '/download_medical_testdata',
                        type : "POST",
                        data : {
                            "_token" : "{{ csrf_token() }}",
                            'date' : month_datepicker1,
                            'roll_no' : roll_no,
                        },

                        beforeSend: function (xhs) {
                   window.loadingScreen("show");

            },
            success: function(rData){

                window.loadingScreen("hide");

            var sData = rData.replace( /[\r\n]+/gm, "" );
            console.log(sData);
            let rObj = JSON.parse(sData);

            if (rObj.status == "success") {
                // statusDiv.html(rObj.data);

                // window.saveCsvData(window.jsonObjectToCSV(rObj.data), rObj.file_name);
                let newTab = window.open(rObj.datasheet_url);
            //  $('#sub_activity_id').attr('required', true);
            // $('#component').attr('required', true);

            } else {
                $('#error').empty();
                var e = $('<div class="alert alert-danger"><p>No data available</p></div>');
                $('#error').append(e);
                    $("div.alert").fadeTo(2000, 500).slideUp(500, function () {
                    $("div.alert").slideUp(500);
                });
            }


            }

                    })
                }
    }


    $(document).ready (function (){
    $("#medical_datetimepicker").datetimepicker ({
            viewMode: 'months',
            format: 'MM/YYYY',
            maxDate: new Date()
        });
});

$("#insert_datetimepicker").datetimepicker ({
            format: 'YYYY-MM-DD',
            maxDate: new Date()
});
$('#month_datepicker1').val('');
$('#month_datepicker').val('');

</script>
@endsection
