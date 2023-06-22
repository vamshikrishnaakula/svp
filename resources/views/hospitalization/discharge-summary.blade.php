{{-- Extends layout --}}
@extends('layouts.default')

{{-- Content --}}
@section('content')

<section id="fitnessevaluvation" class="content-wrapper_sub">
    <div class="user_manage">
        <div class="row">
            <div class="col-md-9">
                <h4>Discharge Summary</h4>
            </div>
        </div>
    <form id="patients_medicines" name="patients_medicines" method="post" autocomplete="on">
      <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Patient ID</label>
                    <input type="text" class="form-control" id="rollnumber" name="rollnumber" required/>
                    <input type="hidden" class="form-control" id="prob_id" name="prob_id" required/>
                </div>
            </div>
            <div class="col-md-3 getdata">
                <button type="button" class="btn" onclick="get_patient_history();">Get Data</button>
            </div>
        </div>
    </form>
        <div class="squadlisthead mt-5" style="display: none">
            <div class="row">
                <div class="col-md-9">
                    <div class="activityhead">
                        <h5 class="mb-0 ml-4">Probationer List</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="listdetails">
            <div class="table-responsive">
                <table class="table" id="probationerslist">
                    <thead></thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

    </div>
</section>

@endsection

@section('scripts')
<script>

$(document).on('keydown.autocomplete', '#rollnumber', function(e) {
        $(this).autocomplete({
            source: "{{ route('prob_autosuggestion') }}",
            minLength: 1,
            select:function(event,ui) {
              $("#prob_id").val(ui.item['id']);

          }
        });

        var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                e.preventDefault();
                return false;
            }
    });

   function get_patient_history()
    {

    var validate =  $("#patients_medicines").validate({
                rules: {
                    rollnumber: {
                    required: true,
                }
                },
                messages: {
                    rollnumber: {
                    required: "Please enter RollNumber",
                    }
                }
            }).form();
        if(validate == true)
            {
                var id = $('#prob_id').val();
            $.ajax({
                url: '/get_inpatients_data',
                type: "POST",
                data:{
                    "_token": "{{ csrf_token() }}",
                    "id":id,
                    },
                    success: function(data){
                    //console.log(data);
                    if(data == '')
                    {
                        $("#probationerslist thead").empty();
                        $("#probationerslist tbody").empty();
                    // $('#probationerslist thead').append('<tr><th>S.NO</th><th>Patient Id</th><th>Patient Name</th><th>Date of joining</th><th>Discharge Date</th><th></th></tr>');
                    $('#probationerslist tbody').append('<tr><td>No Discharge Summary Found for this Probationer</td></tr>');
                    }
                    else {
                    var x=1;
                        $("#probationerslist thead").empty();
                        $("#probationerslist tbody").empty();
                        $('#probationerslist thead').append('<tr><th>S.NO</th><th>Patient Id</th><th>Patient Name</th><th>Date of joining</th><th>Discharge Date</th><th></th></tr>');
                        $.each(data, function(i) {
                            var url = "{{ url ('/discharge_summary/:id')}}";
                            url = url.replace(':id', data[i].in_pat_id);
                            $('#probationerslist tbody').append('<tr><td>'+ x++ +'</td><td>' + data[i].RollNumber + '</td><td>' + data[i].Name + '</td><td>' + data[i].admitted_date + '</td><td>' + data[i].discharge_date + '</td><td><a href="' + url + '" ><img src="{{ asset('images/download1.png') }}" /><span></span></a></td></tr>');
                        });
                    }
                }
            })
            }
    }
</script>
@endsection
