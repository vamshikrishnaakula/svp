
{{-- Extends layout --}}
@extends('layouts.default')

{{-- Content --}}
@section('content')

<section id="editprobationer" class="content-wrapper_sub">
      <div class="user_manage">
        <div class="row">
            <div class="col-md-10">
              <h4>Edit Probationer</h4>
            </div>
            <div class="col-md-2">
                <div class="userBtns">
                    <!-- <img src="{{ asset('images/import.png') }}" />
                    <img src="{{ asset('images/excel.png') }}" /> -->
             </div>
            </div>
      </div>


      <form class="userform"  action="{{ route('probationers.update',$probationer->id)}}" method="POST">
      @csrf
      @method('PUT')
                  <div class="row">
                    <div class="col">
                        <label>Select Batch</label>
                      <select class="form-control" id = "batch_id" name="batch_id" required>
                            <option value="">Select Batches</option>
                            @foreach($batches as $batch)
                                <option value="{{ $batch->id }}" {{$probationer->batch_id == $batch->id  ? 'selected' : ''}}>{{ $batch->BatchName }}</option>
                             @endforeach
                              </select>

                      <input type="hidden" class="form-control" id = "pid" name="pid" value = "{{ $probationer->user_id }}">
                    </div>
                    <div class="col">
                        <label>Roll Number</label>
                      <input type="text" class="form-control" id = "Rollnumber" name="Rollnumber" value = "{{ $probationer->RollNumber }}" required>
                    </div>
                  </div>
                <div class="row">
                    <div class="col">
                        <label>CADRE</label>
                      <input type="text" class="form-control" id = "Cadre" name="Cadre" value = "{{ $probationer->Cadre }}">
                    </div>
                    <div class="col">
                        <label>Probationer Name</label>
                      <input type="text" class="form-control" id = "Name" name="Name" value = "{{ $probationer->Name }}" required>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col">
                        <label>Email</label>
                      <input type="email" class="form-control" id = "Email" name="Email" value = "{{ $probationer->Email }}"  required>
                    </div>
                    <div class="col">
                        <label>Date of Birth</label>
                        <div class="input-group" id="Dob" data-target-input="nearest" name="Dob">
                                <input type="text" class="form-control datetimepicker-input"
                                    data-target="#Dob" name="Dob" data-toggle="datetimepicker" value = "{{date('d-m-Y', strtotime($probationer->Dob))}}" required/>

                         </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col">
                        <label>Gender</label>
                        <select class="form-control" id = "Gender" name="Gender" value = "{{ $probationer->gender }}" required>
                            <option value="">Select</option>
                            <option value="Male" {{$probationer->gender == 'Male' ? 'selected' : ''}}>Male</option>
                            <option value="Female" {{$probationer->gender == 'Female' ? 'selected' : ''}}>Female</option>
                            <option value="Others" {{$probationer->gender == 'Others' ? 'selected' : ''}}>Others</option>
                          </select>
                    </div>
                    <div class="col-md-6">
                        <label>Mobile Number</label>
                      <input oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                      type = "tel" maxlength="10" class="form-control" id = "MobileContactNumber" name="MobileContactNumber" value = "{{ $probationer->MobileNumber }}" pattern="[0-9]{10}" required>
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
                <div id="importProbationer_password_status" class="mt-3"></div>
                <div class="usersubmitBtns mt-5">
                    <div class="mr-4">
                        <button class="btn formBtn submitBtn">Submit</button>
                    </div>
                </div>
              </form>
      </div>
</section>
@endsection
@section('scripts')
  <script>
     $("#Dob").datetimepicker (
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
