{{-- Extends layout --}}
@extends('layouts.default')

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

 @php
  function staffrole($template)
    {
        switch ($template) {
            case "admin":
              echo "Super Admin";
              break;
            case "superadmin":
              echo "Admin";
              break;
            case "drillinspector":
              echo "Drill Inspector";
              break;
            case "faculty":
              echo "Faculty";
              break;
            case "doctor":
              echo "Doctor";
              break;
            case "receptionist":
              echo "Receptionist";
              break;
            case "si":
              echo "SI";
              break;
            case "adi":
              echo "ADI";
              break;
          }
    }
@endphp


<section id="addstaff" class="content-wrapper_sub datatablelist">
    <div class="user_manage">
        <div class="row">
        <div class="col-md-10">
              <h4>Staff List</h4>
            </div>
            <div class="col-md-2">
                <div class="userBtns d-flex justify-content-end">
                    <!-- <a href="#" data-toggle="tooltip" title="import"> <img src="./images/import.png" /></a>
                    <a href="#" data-toggle="tooltip" title="excel"> <img style="width:29px !important"  src="./images/excel.png" /></a> -->

                    <a href="{{ url('staffs') }}" class="text-center ml-3" data-toggle="tooltip" title="Add Staff">
                        <img src="{{ asset('images/plus-icon-rounded.png') }}">
                        <p class="my-0">Add</p>
                    </a>
                </div>
            </div>
        </div>
        <div class="listdetails mt-4">
            <div class="squadlisthead">
                <div class="row">
                    <div class="col-md-6">
                        <div class="group">
                            <img src="/images/staff.png" />
                        </div>
                    </div>

                </div>
            </div>
            <div>
              
                <table class="table" id="stafflist" style="width: 100% !important">
                    <thead>
                        <tr>
                            <th class="text-left">Name</th>
                            <th class="text-left">Email</th>
                            <th>Date Of Birth</th>
                            <th>Mobile number</th>
                            <th>Role</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($staffs as $staff)
                        <tr>
                            <td class="text-left">{{ $staff->name }}</td>
                            <td class="text-left">{{ $staff->email }}</td>
                            <td>{{date('d-m-Y', strtotime($staff->Dob))}}</td>
                            <td>{{ $staff->MobileNumber }}</td>
                            <td>{{ staffrole($staff->role) }}</td>
                            <td>
                                @if($staff->role !== "admin")
                                <a href="{{ route('staffs.edit',$staff->id) }}" data-toggle="tooltip" title="Edit"> <img
                                        src="{{ asset('images/edit.png') }}" /></a>

                                <a href=""
                                    onclick="if(confirm('Do you want to delete this Staff?'))event.preventDefault(); document.getElementById('delete-{{$staff->id}}').submit();"><img
                                        src="{{ asset('images/trash.png') }}" /></a>
                                <form id="delete-{{$staff->id}}" method="post"
                                    action="{{ route('staffs.destroy',$staff->id) }}" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
</section>
@endsection

<style>
#stafflist_wrapper .row:nth-child(1){
  flex-direction:row;
}
#stafflist_wrapper .row:nth-child(1) .col-sm-12.col-md-6:nth-child(1){
  display: none;
}
#stafflist_wrapper .row:nth-child(1) .col-sm-12.col-md-6:nth-child(2){
  flex: 0 0 100%;
    max-width: 100%;
}
#stafflist_wrapper .row:nth-child(1) .col-sm-12.col-md-6:nth-child(2) div.dataTables_filter{
     position: absolute !important;
    right: 30px !important;
    bottom: 10px !important;
    left: auto !important;
  margin:5px;
}
</style>
@section('scripts')
  <script>

$('#stafflist').DataTable({
  "bLengthChange": false,
  language: { search: "", searchPlaceholder: "Search..." },
})
  </script>
@endsection
