{{-- Extends layout --}}
@extends('layouts.doctor.template')

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

<p id="messageContent"></p>


<section id="viewdata" class="content-wrapper_sub tab-content">
 <div class="row">
        <div class="col-12">
            <h4 class="page-title">View Data</h4>
        </div>
    </div>
    <form id="medicines_form" name="medicines_form" method="post" autocomplete="off">
      <div class="row mt-4">
        <div class="col-md-12">
            <div class="view_data_card">
                <div class="form-group row mb-0">
                    <div class="col-sm-3">
                        <input type="text" class="form-control" placeholder="Search" id="drug" name="drug">
                    </div>
                    <div class="col-sm-3">
                        <select name="medicines" id="medicines" class="form-control">
                            <option value=''>Select</option>
                            <option value="Tablet">Tablet</option>
                            <option value="Injection">Injection</option>
                            <option value="Surgicals">Surgicals</option>
                            <option value="Syrup">Syrup</option>
                            <option value="Others">Others</option>
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <button type="button" class="btn md-btn-success" id="medicinesdata" onclick="medicineslist()">Get Data</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </form>
        <div class="row mt-5">
        <div class="col-md-12">
            <div class="table-responsive view_data_table">
                <table class="table table-hover" id="medicines_list">
                    <thead></thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <!--Edit Modal Popup-->

      <!-- The Modal -->
  <div class="modal" id="editModal">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">

        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">Edit</h4>
        </div>

        <!-- Modal body -->
        <div class="modal-body">
    <form name="custForm"  method="POST">
    @csrf
     @method('POST')
        <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label>Name</label>
                <input class="form-control" type="text" class="from-control" id="m_name" name="m_name" />
                <input class="form-control" type="hidden" class="from-control" id="m_id" name="m_id" />
            </div>
            <div class="form-group">
                <label>Content</label>
                <input class="form-control" type="text" class="from-control" id="m_content" name="m_content" />
            </div>
            <div class="form-group">
                <label>Type</label>
                <select class="form-control" id="m_type" name="m_type">
                    <option value="Tablet">Tablet</option>
                    <option value="Injection">Injection</option>
                    <option value="Surgicals">Surgicals</option>
                    <option value="Syrup">Syrup</option>
                    <option value="Others">Others</option>
                </select>
            </div>
            <div class="form-group">
                <label>Manufacture</label>
                <input class="form-control" type="text" class="from-control" id="m_manufacture" name="m_manufacture" />
            </div>
            <div class="form-group">
                <label>Dosage (mg)</label>
                <input class="form-control" type="text" class="from-control" id="m_dosage" name="m_dosage" style="width:40%"/>
            </div>
        </div>
    </div>
        </div>
        <!-- Modal footer -->
        <div class="modal-footer">
            <button type="button" class="btn sub-btn-success" onclick="medicines_data()">Update</button>
          <button type="button" class="btn md-btn-success" data-dismiss="modal">Close</button>
        </div>
      </div>
      </form>
    </div>
  </div>

</section>
@endsection


@section('scripts')
<script>

$(document).on('keydown.autocomplete', '#drug', function() {
    $(this).autocomplete({
        source: "{{ route('autocomplete') }}",
      minLength: 1,
      select:function(event,ui) {
      }
    });
});

 function medicines_data()
{
    var id = $('#m_id').val();
    var m_name = $('#m_name').val();
    var m_content = $('#m_content').val();
    var m_type = $('#m_type').val();
    var m_manufacture = $('#m_manufacture').val();
    var m_dosage = $('#m_dosage').val();

     $.ajax({
           url: '/update_medicines',
           type: "POST",
           data:{
               "_token": "{{ csrf_token() }}",
                "m_id":id,
                "m_name":m_name,
                "m_content":m_content,
                "m_type":m_type,
                "m_manufacture":m_manufacture,
                "m_dosage":m_dosage
               },
           success: function(data){
               $('#editModal').modal('hide');
               $("#medicinesdata").trigger("click");
       }
     });
}

function medicineslist()
{
    var validate =  $("#medicines_form").validate({
            rules: {
                drug: {
                required: true,
            },
            // medicines: {
            //     required: true,
            // }
            },
            messages: {
                drug: {
                required: "Please enter medicine name",
                },
                // medicines: {
                // required: "Please Select medicine type",
                // },
            }
        }).form();
    if(validate == true)
        {
            var drugs = $('#drug').val();
            var medicines = $('#medicines').val();
          $.ajax({
            url: '/get_medicines_data',
            type: "POST",
            data:{
                "_token": "{{ csrf_token() }}",
                 "drug":drugs,
                 "medicine":medicines,
                },
             success: function(data){
                if(data == '1')
                {
                    $("#medicines_list thead").empty();
                    $("#medicines_list tbody").empty();
                    $('#medicines_list thead').append('<tr><th>S.No</th><th>Medicine Name</th><th>Dosage</th><th>Content</th><th>Type</th><th>Manufacturer</th><th></th></tr>');
                    $('#medicines_list tbody').append('<tr><td rowspan=></td></tr>');
                }
                else {
                   var x=1;
                    $("#medicines_list thead").empty();
                    $("#medicines_list tbody").empty();
                    $('#medicines_list thead').append('<tr><th>S.No</th><th>Medicine Name</th><th>Dosage</th><th>Content</th><th>Type</th><th>Manufacturer</th><th></th></tr>');
                    $.each(data, function(i) {
                        var url = "{{ url ('/delete_medicine/:id')}}";
                        url = url.replace(':id', data[i].id);
                        $('#medicines_list tbody').append('<tr><td>'+ x++ +'</td><td>' + data[i].MedicineName + '</td><td>' + data[i].MedicineDosage + '</td><td>' + data[i].MedicineContent + '</td><td>' + data[i].MedicineType + '</td><td>' + data[i].MedicineManufacturer + '</td><td width="2%"><a id="edit-medicine" data-toggle="modal" data-target="#editModal" data-id=' + data[i].id + '><img src="{{ asset('images/edit.png') }}"></a></td><td><a onclick="deletemedicines('+ data[i].id +')" ><img src="{{ asset('images/trash.png') }}" /><span></span></a></td></tr>');
                    });
                }
            }
        })
        }
}

function deletemedicines(id)
{
if (confirm("Are you sure?")) {
    var m_id =id;
$.ajax({
           url: '/delete_medicine',
           type: "POST",
           data:{
               "_token": "{{ csrf_token() }}",
                "id":id,
               },
           success: function(data){
           if(data == '1')
           {
              $("#successModalContent").html(
                   '<div class="msg msg-success msg-full">' + "Deleted Succesfully" + "</div>"
                );
            $("#successModal").modal("show");
            setTimeout(function() {
                location.reload();
            }, 3000);
           }

           }
       })
}
else{
    return false;
}
}

$('body').on('click', '#edit-medicine', function () {
var medicine_id = $(this).data('id');
$.get('medicines/'+medicine_id+'/edit', function (data) {

$('#editModal').modal('show');
$('#m_id').val(data.id);
$('#m_name').val(data.MedicineName);
$('#m_content').val(data.MedicineContent);
$('#m_type').val(data.MedicineType);
$('#m_manufacture').val(data.MedicineManufacturer);
$('#m_dosage').val(data.MedicineDosage);
})
});

</script>
@endsection
