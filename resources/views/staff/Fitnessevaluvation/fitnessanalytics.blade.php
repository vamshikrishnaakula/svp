{{-- Extends layout --}}
@extends('layouts.default')

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
              <a href="#" data-toggle="tooltip" title="import"> <img src="{{ asset('images/import.png') }}" /></a>
              <a href="#" data-toggle="tooltip" title="excel"> <img src="{{ asset('images/excel.png') }}" /></a>
              <a href="#" data-toggle="tooltip" title="download"> <img src="{{ asset('images/download1.png') }}" /></a>
              <a href="#" data-toggle="tooltip" title="print"> <img src="{{ asset('images/print1.png') }}" /></a>



          </div>
        </div>
        {{-- <div class="col-md-2 profileimg">
            <img src="{{ asset('images/probationerprofile.png') }}" />
        </div> --}}
      </div>
      <form class="fitnessevalform" id="fitness_evaluvation" autocomplete="off">
      <div class="row mt-5">

        <div class="col-md-4 card text-center">
            <div class="img_center_align">
                <img  class="rounded-circle" width="110" src="{{ asset('images/probationerprofile.png') }}" />
            </div>
            <div  style="text-align: justify">
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
                        <td>   <div class="input-group" id="datetimepicker33" data-target-input="nearest" name="Dob">
                            <input type="text" class="form-control datetimepicker-input"
                                data-target="#datetimepicker33"  data-toggle="datetimepicker" name="Dob" id="month_datepicker" required />

        </div></td>
                    </tr>
                </table>
          {{-- <div class="form-group row">
            <label class="col-md-6">Name :</label>
            <span class="col-md-6">{{ $get_probationer->Name }}</span>
          </div>
          <div class="form-group row">
            <label class="col-md-6">Dob :</label>
            <span class="col-md-6">{{ $get_probationer->Dob }}</span>
          </div>
          <div class="form-group row">
            <label class="col-md-6">Gender :</label>
            <span class="col-md-6">{{ $get_probationer->gender }}</span>
          </div>
          <div class="form-group row">
            <label class="col-md-6">Month :</label>
            <span class="col-md-6"></span>
          </div> --}}
            </div>
        </div>

        <div class="col-md-8">
            <div class="fitnesseval">
                <div class="fitnessmonthinfo">
            <label>Month:</label> <span id="dsmonth">{{(isset($fitness->month) ? date('F', mktime(0, 0, 0, $fitness->month, 10)):'-')}}</span>
            <label class="ml-5">Year:</label> <span id="dsyear">{{(isset($fitness->year) ? $fitness->year:'-')}}</span>
                </div>
                <div class="row p-0" style="background: #9AA4CC; align-items: center">
                    <h5 class="col-md-11 mb-0">Fitness</h5>
                    <div class="col-md-1">
                    <a class="editvalue" data-toggle="tooltip" title="Edit"><i class="far fa-edit"></i></a>
                    <a class="cancel" style="display: none" data-toggle="tooltip" title="cancel"><i class="far fa-times-circle"></i></a>
                    </div>
                </div>

                <div class="row">

                    <div class="col-md-8">
                        <label>Weight</label>
                    </div>
                    <div class="col-md-4">
                        <span id="sweight">{{(isset($fitness->weight) ? $fitness->weight:'-')}}</span>
                        <div class="fitnessinputValue" style="display: none"><input type="text" class="form-control value" name="weight"  value="{{(isset($fitness->weight) ? $fitness->weight:'')}}" /><input type="hidden" class="form-control value" name="pid" id="pid" value="{{(isset($get_probationer->id) ? $get_probationer->id:'')}}" /><input type="hidden" class="form-control value" name="month"  value="{{(isset($fitness->month) ? $fitness->month:'')}}" /><input type="hidden" class="form-control value" name="year"  value="{{(isset($fitness->year) ? $fitness->year:'')}}" /></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <label>BMI</label>
                    </div>
                    <div class="col-md-4">
                        <span id="sbmi">{{(isset($fitness->bmi) ? $fitness->bmi:'-')}}</span>
                        <div class="fitnessinputValue" style="display: none"><input type="text" class="form-control value" name="bmi" value="{{(isset($fitness->bmi) ? $fitness->bmi:'')}}"/></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <label>Body Fat</label>
                    </div>
                    <div class="col-md-4">
                        <span id="sbodyfat">{{(isset($fitness->bodyfat) ? $fitness->bodyfat:'-')}}</span>
                        <div class="fitnessinputValue" style="display: none"><input type="text" class="form-control value" name="bodyfat" value="{{(isset($fitness->bodyfat) ? $fitness->bodyfat:'')}}" /></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <label>Fitness Score</label>
                    </div>
                    <div class="col-md-4">
                        <span id="sfitnessscore">{{(isset($fitness->fitnessscore) ? $fitness->fitnessscore:'-')}}</span>
                        <div class="fitnessinputValue" style="display: none"><input type="text" class="form-control value" name="fitnessscore" value="{{(isset($fitness->fitnessscore) ? $fitness->fitnessscore:'')}}" /></div>
                    </div>
                </div>
                <h5>Endurance</h5>
                <div class="row">
                    <div class="col-md-8">
                        <label>Grade</label>
                    </div>
                    <div class="col-md-4">
                        <span id="segrade">{{(isset($fitness->endurancegrade) ? $fitness->endurancegrade:'-')}}</span>
                        <div class="fitnessinputValue" style="display: none"><input type="text" class="form-control value" name="egrade" value="{{(isset($fitness->endurancegrade) ? $fitness->endurancegrade:'')}}" /></div>
                    </div>
                </div>
                <h5>Strength</h5>
                <div class="row">
                    <div class="col-md-8">
                        <label>Grade</label>
                    </div>
                    <div class="col-md-4">
                        <span id="ssgrade">{{(isset($fitness->strengthgrade) ? $fitness->strengthgrade:'-')}}</span>
                        <div class="fitnessinputValue" style="display: none"><input type="text" class="form-control value" name="sgrade" value="{{(isset($fitness->strengthgrade) ? $fitness->strengthgrade:'')}}" /></div>
                    </div>
                </div>
                <h5>Flexibility</h5>
                <div class="row">
                    <div class="col-md-8">
                        <label>Grade</label>
                    </div>
                    <div class="col-md-4">
                        <span id="sfgrade">{{(isset($fitness->flexibilitygrade) ? $fitness->flexibilitygrade:'-')}}</span>
                        <div class="fitnessinputValue" style="display: none"><input type="text" class="form-control value" name="fgrade" value="{{(isset($fitness->flexibilitygrade) ? $fitness->flexibilitygrade:'-')}}" /></div>
                    </div>
                </div>
            </div>
            <div class="usersubmitBtns mt-3" style="display: none">
                <div class="mr-4">
                    <button type="button" onclick = "submitfitness()" class="btn formBtn submitBtn">Submit</button>
                </div>
            </div>
        </div>
      </div>
    </form>

  {{-- <div class="listdetails evaluvation mt-5">
    <div class="table-responsive">
        <table class="table">
            <thead>
                    <th colspan="3">Evaluvation Table</th>
            </thead>
            <tbody>
                <tr>
                    <td>Weight</td>
                    <td>
                        <label>85kgs</label>
                        <div style="display: none"><input type="text" class="form-control value" /><a class="update">update</a></div>
                    </td>
                    <td> <a class="editvalue" data-toggle="tooltip" title="Edit"><img src="./images/edit.png" /></a></td>
                </tr>
                <tr>
                  <td>BMI</td>
                  <td>
                      <label>value</label>
                    <div style="display: none"><input type="text" class="form-control value" /><a class="update">update</a></div>
                </td>
                  <td> <a class="editvalue" href="#" data-toggle="tooltip" title="Edit"><img src="./images/edit.png" /></a></td>
              </tr>
              <tr>
                  <td>Body Fat</td>
                  <td>
                    <label>value</label>
                    <div style="display: none"><input type="text" class="form-control value" /><a class="update">update</a></div>
                  </td>
                  <td> <a class="editvalue" href="#" data-toggle="tooltip" title="Edit"><img src="./images/edit.png" /></a></td>
              </tr>
              <tr>
                  <td>Fitness Score</td>
                  <td>
                    <label>value</label>
                    <div style="display: none"><input type="text" class="form-control value" /><a class="update">update</a></div>
                  </td>
                  <td> <a class="editvalue" href="#" data-toggle="tooltip" title="Edit"><img src="./images/edit.png" /></a></td>
              </tr>
            </tbody>
        </table>
    </div>
    </div> --}}
  </div>

</section>

@endsection

@section('scripts')
    <script>
$(document).ready(function() {
    $(document).on("click", ".fitnesseval a.editvalue", function() {

        $(".fitnessinputValue").show();
        $(".fitnesseval span").hide();
        $(this).hide();
        $(".fitnesseval a.cancel").show();
        $(".fitnessevalform .usersubmitBtns").show();
    });



    $(document).on("click", ".fitnesseval a.cancel", function() {
        //
        $(".fitnessinputValue").hide();
        $(".fitnesseval span").show();
        $(".fitnesseval a.editvalue").show();
        $(".fitnesseval a.cancel").hide();
        $(".fitnessevalform .usersubmitBtns").hide();
    });
});
function GetMonthName(monthNumber) {
      var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
      return months[monthNumber - 1];
}
function submitfitness()
        {
          //  var bid = $('#batch_id').val();
            var data = $('#fitness_evaluvation').serializeArray();
            $.ajax({
           url: '/insertfitnessevaluvation',
           type: "POST",
           data:{
                "_token": "{{ csrf_token() }}",
                 "data":data,
                },
           success: function(data){
           alert("updated Suceesfully")
            window.location.reload();
           }
       })
        }
        $(document).ready (function (){
    $("#datetimepicker33").datetimepicker ({
            viewMode: 'months',
            format: 'MM/YYYY'
        });
});
var previousValue = $("#month_datepicker").val();

// $('#month_datepicker').on('change input',function(e){
//     e.preventDefault();
//     var currentValue = $(this).val();
//     console.log(previousValue)
//     console.log(currentValue)
//     if(currentValue != previousValue) {
//          previousValue = currentValue;
//          alert("Value changed!");
//     }
// });
//var previousValue = $("#month_datepicker").val();
    $("#datetimepicker33").on("change.datetimepicker", ({date, oldDate}) => {
        if(oldDate != null)
        {
            if(date != oldDate)
            {
                var currentValue = $('#month_datepicker').val();
                var pid = $('#pid').val();
                $.ajax({
                url: '/prob_month_fitness',
                type: "POST",
                data:{
                    "_token": "{{ csrf_token() }}",
                        "id":currentValue,
                        "pid":pid
                    },
                    beforeSend: function (xhs) {
                        window.loadingScreen("show");

            },

                success: function(data){
                    window.loadingScreen("hide");
                  if(data != '')
                  {
                    $(':input').not(':button, :submit, :reset, :hidden, :checkbox, :radio').val('');

                    $('input[name="weight"]').val(data[0].weight);
                    $('input[name="pid"]').val(data[0].Probationer_Id);
                    $('input[name="month"]').val(data[0].month);
                    $('input[name="year"]').val(data[0].year);
                    $('input[name="bmi"]').val(data[0].bmi);
                    $('input[name="bodyfat"]').val(data[0].bodyfat);
                    $('input[name="fitnessscore"]').val(data[0].fitnessscore);
                    $('input[name="egrade"]').val(data[0].endurancegrade);
                    $('input[name="sgrade"]').val(data[0].strengthgrade);
                    $('input[name="fgrade"]').val(data[0].flexibilitygrade);
                    $("#sweight").text(data[0].weight);
                    $("#sbmi").text(data[0].bmi);
                    $("#sbodyfat").text(data[0].bodyfat);
                    $("#sfitnessscore").text(data[0].fitnessscore);
                    $("#segrade").text(data[0].endurancegrade);
                    $("#ssgrade").text(data[0].strengthgrade);
                    $("#sfgrade").text(data[0].flexibilitygrade);
                    $("#dsmonth").text(GetMonthName(data[0].month));
                    $("#dsyear").text(data[0].year);
                    }
                    else{
                        alert("No records exits")
                    }
               }
       })
            }
        }
 })



</script>
@endsection
