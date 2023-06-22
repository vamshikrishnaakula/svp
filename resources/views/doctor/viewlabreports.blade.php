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

    <?php
         $labreports = App\Models\Lab::get();
    ?>
<section id="viewdata" class="content-wrapper_sub tab-content">
 <div class="row">
        <div class="col-12">
            <h4 class="page-title">View LabTest Data</h4>
        </div>
    </div>
        <div class="row mt-5">
        <div class="col-md-12">
            <div class="table-responsive view_data_table">
                <table class="table table-hover" id="medicines_list">
                    <thead>
                    <tr>
                    <th>S.NO</th>
                    <th>LabTest Name</th>
                    <th></th>
                    <th></th>
                    </thead>
                    <tbody>
                   @foreach($labreports as $lab)
                   <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $lab->LabTestName}}</td>
                    <td width="2%"><a href="javascript:void(0)" id="edit-lab" data-toggle="modal" data-target="#editModal" data-id={{$lab->id }}><img src="{{ asset('images/edit.png') }}"></a></td>
                    <td><a onclick="deletelab({{ $lab->id }})"><img src="{{ asset('images/trash.png') }}" /><span></span></a></td>
                   </tr>
                    @endforeach
                    </tbody>
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
                <label>Lab Test</label>
                <input class="form-control" type="text" class="from-control" id="l_name" name="l_name" />
                <input class="form-control" type="hidden" class="from-control" id="m_id" name="m_id" />
            </div>
        </div>
    </div>
        </div>
        <!-- Modal footer -->
        <div class="modal-footer">
            <button type="button" class="btn sub-btn-success" onclick="labs_data()">Update</button>
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

function deletelab(id)
{
if (confirm("Are you sure?")) {
         var m_id =id;
        $.ajax({
                url: '/delete_lab',
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
    else
    {
        return false;
    }

}

function labs_data()
{
    var id = $('#m_id').val();
    var l_name = $('#l_name').val();

     $.ajax({
           url: '/update_labs',
           type: "POST",
           data:{
               "_token": "{{ csrf_token() }}",
                "m_id":id,
                "m_name":l_name
               },
           success: function(data){
               $('#editModal').modal('hide');
               window.location.reload();
       }
     });
}

$('body').on('click', '#edit-lab', function () {
var lab_id = $(this).data('id');
$.get('labs/'+lab_id+'/edit', function (data) {

$('#editModal').modal('show');
$('#m_id').val(data.id);
$('#l_name').val(data.LabTestName);
})
});

</script>
@endsection
