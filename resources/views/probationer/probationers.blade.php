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

<section id="addprobationer" class="content-wrapper_sub">
      <div class="user_manage">
        <div class="row">
        <div class="col-md-10">
              <h4>Add Probationer</h4>
            </div>
            <div class="col-md-2">
                <div class="userBtns">
                <!-- <a href="#" data-toggle="tooltip" title="import"> <img src="./images/import.png" /></a>
                <a href="#" data-toggle="tooltip" title="excel"> <img style="width:29px !important"  src="./images/excel.png" /></a> -->
                </div>
            </div>
      </div>
      <form class="userform"  action="{{ route('probationers.store') }}" method="POST" autocomplete="off">
      @csrf
                <div class="row">
                    <div class="col">
                    <label>Select Batch</label>
                        <select class="form-control" id="batch_id" name='batch_id' required>
                          <option value="">Select  Batch Number</option>
                            @foreach($batch as $batches)
                                <option value="{{ $batches->id }}" @if($batches->id == Session::get('current_batch')) selected @endif>{{ $batches->BatchName }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col">
                        <label>Roll Number</label>
                      <input type="text" class="form-control" id = "Rollnumber" name="Rollnumber" required>
                    </div>
                  </div>
                    <div class="row">
                    <div class="col">
                        <label>Cadre</label>
                      <input type="text" class="form-control" id = "Cadre" name="Cadre">
                    </div>
                    <div class="col">
                        <label>Probationer Name</label>
                      <input type="text" class="form-control" id = "Name" name="Name" required>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col">
                        <label>Email</label>
                      <input type="Email" class="form-control" id = "Email" name="Email" required>
                    </div>
                    <div class="col">
                        <label>Date of Birth</label>
                        <div class="input-group" id="Dob" data-target-input="nearest" name="Dob">
                                <input type="text" class="form-control datetimepicker-input"
                                    data-target="#Dob" data-toggle="datetimepicker" name="Dob" id="Dob1" required/>
                         </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col">
                        <label>Gender</label>
                        <select class="form-control" id = "Gender" name="Gender" required>
                            <option value="">Select</option>
                            <option>Male</option>
                            <option>Female</option>
                            <option>Others</option>
                          </select>
                    </div>
                    <div class="col-md-6">
                        <label>Mobile Number</label>
                      <input oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                type = "tel" maxlength="10" class="form-control" id = "MobileContactNumber" name="MobileContactNumber" pattern="[0-9]{10}" required>
                    </div>
                  </div>
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
            maxDate: new Date(),
        }
    );
        $("#Dob1").val('');
  </script>
@endsection
