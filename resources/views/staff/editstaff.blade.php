
{{-- Extends layout --}}
@extends('layouts.default')

{{-- Content --}}
@section('content')

<section id="editstaff" class="content-wrapper_sub">
      <div class="user_manage">
        <div class="row">
            <div class="col-md-10">
              <h4>Edit Staff</h4>
            </div>
            <div class="col-md-2">
                <div class="userBtns">
                    <!-- <a href="#" data-toggle="tooltip" title="import"> <img src="{{ asset('images/import.png') }}" /></a>
                        <a href="#" data-toggle="tooltip" title="excel"> <img style="width:29px !important"  src="{{ asset('images/excel.png') }}" /></a> -->
                    </div>
            </div>
      </div>
      <form class="userform" action="{{ route('staffs.update',$staff->id) }}" method="POST">
      @csrf
      @method('PUT')
        <div class="row">
            <div class="col">
                <label>First Name</label>
              <input type="text" class="form-control" id = "Name" name = "Name" value = "{{ $staff->name }}">
              @error('Name') <span class="text-red-500">{{ $message }}</span>@enderror
            </div>
            <div class="col">
                <label>Email</label>
              <input type="text" class="form-control" id ="Email" name = "Email" value = "{{ $staff->email }}">
              @error('Email') <span class="text-red-500">{{ $message }}</span>@enderror
            </div>
          </div>
          <div class="row">
          <div class="col">
          <label>Date of Birth</label>
            <div class="input-group" id="datetimepicker3" data-target-input="nearest" name="Dob">
                                <input type="text" class="form-control datetimepicker-input"
                                    data-target="#datetimepicker3" data-toggle="datetimepicker" name="Dob" value = "{{date('d-m-Y', strtotime($staff->Dob))}}" required/>
            </div>
              </div>
            <div class="col">
                        <label>Mobile Number</label>
                      <input oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                type = "tel" maxlength="10" class="form-control" id = "MobileNumber" name = "MobileNumber" value = "{{ $staff->MobileNumber }}" pattern="[0-9]{10}" required>
                      @error('MobileNumber') <span class="text-red-500">{{ $message }}</span>@enderror
                    </div>
          </div>


             @if (Auth::user()->isAdmin())
          <div class="row">
            <div class="col">
                <label>Password</label>
              <input type="password" class="form-control" id = "password" name = "password" >
              @error('password') <span class="text-red-500">{{ $message }}</span>@enderror
            </div>
            <div class="col">
                <label>Confirm Password</label>
              <input type="password" class="form-control" id ="confirm_password" name = "confirm_password">
              @error('confirm_password') <span class="text-red-500">{{ $message }}</span>@enderror
            </div>
          </div>
          @endif

          <div class="row">
                    <div class="col-md-6">
                        <label>Role</label>
                        <select class="form-control" id="Role" name="Role" value = "{{ $staff->role }}" required>
                        <option value=''>Select Role</option>
                            @if ($role === 'admin')
                            {
                                <option value="superadmin" {{$staff->role == 'admin' ? 'selected' : ''}}>Admin</option>
                            }
                            @endif

                            <option value="Drillinspector" {{$staff->role == 'drillinspector' ? 'selected' : ''}}>Drill Instructor</option>
                            <option value="receptionist" {{$staff->role == 'receptionist' ? 'selected' : ''}}>Receptionist</option>
                            <option value="doctor" {{$staff->role == 'doctor' ? 'selected' : ''}}>Doctor</option>
                            <option value="faculty" {{$staff->role == 'faculty' ? 'selected' : ''}}>Faculty</option>
                            <option value="si" {{$staff->role == 'si' ? 'selected' : ''}}>SI</option>
                            <option value="adi" {{$staff->role == 'adi' ? 'selected' : ''}}>ADI</option>
                          </select>
                    </div>
               </div>
               <div id="importProbationer_password_status" class="mt-3"></div>
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
        }
    );

    $('#password, #confirm_password').on('keyup', function () {
        if ($('#password').val() == $('#confirm_password').val()) {
                $('#importProbationer_password_status').html('<div class="msg msg-success msg-full">Password Matched</div>');
                $('.submitBtn').prop('disabled', false);

        } else
        {
            if($('#confirm_password').val() != ''){
                $('#importProbationer_password_status').html('<div class="msg msg-danger msg-full">Password Not Matching</div>');
                 $('.submitBtn').prop('disabled', true);
            }
        }

      });
  </script>
@endsection
