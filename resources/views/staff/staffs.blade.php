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

<section id="addstaff" class="content-wrapper_sub">
      <div class="user_manage">
        <div class="row">
        <div class="col-md-10">
              <h4>Add Staff</h4>
            </div>
            <div class="col-md-2">
                <div class="userBtns">
                <!-- <a href="#" data-toggle="tooltip" title="import"> <img src="./images/import.png" /></a>
                    <a href="#" data-toggle="tooltip" title="excel"> <img style="width:29px !important"  src="./images/excel.png" /></a> -->
                </div>
            </div>
      </div>
      <form class="userform" action="{{ route('staffs.store') }}" method="POST" autocomplete="off">
      @csrf
        <div class="row">
            <div class="col">
                <label>Name</label>
              <input type="text" class="form-control" id = "Name" name = "Name" required>
              @error('Name') <span class="text-red-500">{{ $message }}</span>@enderror
            </div>
            <div class="col">
                <label>Email</label>
              <input type="text" class="form-control" id ="Email" name = "Email" required>
              @error('Email') <span class="text-red-500">{{ $message }}</span>@enderror
            </div>
          </div>
          <div class="row">
          <div class="col">
          <label>Date of Birth</label>
            <div class="input-group" id="datetimepicker3" data-target-input="nearest" name="Dob">
                    <input type="text" class="form-control datetimepicker-input"
                   data-target="#datetimepicker3"  data-toggle="datetimepicker" name="Dob" id="Dob" required/>

            </div>
              </div>
            <div class="col">
                <label>Mobile Number</label>
                <input oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                type = "tel" maxlength="10"  class="form-control" id = "MobileNumber" name = "MobileNumber" pattern="[0-9]{10}" required>
                @error('MobileNumber') <span class="text-red-500">{{ $message }}</span>@enderror
            </div>
          </div>
          <div class="row">

                    <div class="col">
                        <label>Role</label>
                        <select class="form-control" id="Role" name="Role" required>
                            <option value=''>Select Role</option>
                            @if ($role === 'admin')
                            {
                               <option value="superadmin">Admin</option>
                            }
                            @endif
                            <option value="Drillinspector">Drill Instructor</option>
                            <option value="Receptionist">Receptionist</option>
                            <option value="Doctor">Doctor</option>
                            <option value="faculty">Faculty</option>
                            <option value="si">SI</option>
                            <option value="adi">ADI</option>
                          </select>
                    </div>

                <div class="col">
            </div>
               </div>

        <div class="usersubmitBtns mt-5">
            <div class="mr-4">
                <button class="btn formBtn submitBtn">Submit</button>
            </div>
        </div>
        </form>

</section>
@endsection

@section('scripts')
  <script>
     $("#datetimepicker3").datetimepicker (
        {
            format: 'DD-MM-YYYY',
            maxDate: new Date(),
        }
    );

    $("#Dob").val('');

$('#Addedprob').DataTable({
  "bLengthChange": false,
})
  </script>
@endsection
