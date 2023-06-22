{{-- Extends layout --}}
@extends('layouts-Receptionist.default')

{{-- Content --}}
@section('content')

<div id="error"></div>
@if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible">
            <p>{{ $message }}</p>
        </div>
  @elseif ($message = Session::get('delete'))
        <div class="alert alert-danger  alert-dismissible">
            <p>{{ $message }}</p>
        </div>

    @endif
<section id="reception" class="content-wrapper_sub p-3">
    <div class="row">
        <div class="uploadreports col">
            {{-- <button type="button" class="btn mr-2"  onclick="window.location='{{ url ('patient-discharge-summary') }}'">Discharge Summary</button> --}}
            <button type="button" class="btn mr-2"  onclick="window.location='{{ url ('health-profiles') }}'">Health Profiles</button>
            <button type="button" class="btn" onclick="window.location='{{ url ('labreports') }}'">Upload Patient Documents</button>
        </div>
    </div>

        <div class="row mt-4">
            <div class="col-md-6">
            <div class="newappointment p-3">
                    <h5>New Appointment</h5>
                    <form action="{{ route('receptionist.store') }}" method="POST" autocomplete="off">
                    @csrf
                    <div class="form-group row">

                        <div class="col-md-6">
                            <label class="text-center">Name / Roll No :</label>
                            <div class="row no-gutters" style="display:flex; align-items: center;">
                              <div class="col-md-12">
                            <input class="form-control" type="text" id="roll_no" name="roll_no">
                          </div>
                          <input class="form-control" type="hidden" id="prob_id" name="prob_id">
                            </div>
                          </div>


                        <div class="col-md-6 getdata">
                        <button type="button" class="btn" onclick="window.getData();">Get Data</button>
                    </div>
                    </div>
                    <div class="row">
                        <div class="form-group col">
                            <label>Patient Name :</label>
                            <span class="ml-3" name = "Pname" id="Pname"/>

                        </div>
                        <div class="form-group col">
                            <label>Gender :</label>
                            <span class="ml-3"id="gender"></span>
                            <input type="hidden" class="form-control" name = "Probationer_Id" id="Probationer_Id" required />
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col">
                            <label>Doctor Name</label>
                            <select class="form-control col" id="Doctor_Id" name='Doctor_Id' required>
                            <option value="">Select Doctor</option>
                            @foreach($staffs as $staff)
                                <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                             @endforeach
                              </select>
                        </div>
                        <div class="form-group col">
                            <label>Symptoms</label>
                            <input type="text" class="form-control" name="Symptoms" required />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                        <label>Date & Time:</label>
                        <div class="form-group">
                            <div class="input-group date reception_date" id="datetimepicker3" data-target-input="nearest" name="Appoinment_Time">
                                <input type="text" class="form-control datetimepicker-input"
                                    data-target="#datetimepicker3"  data-toggle="datetimepicker" name="Appoinment_Time" required/>
                            </div>
                        </div>
                        </div>
                    </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="usersubmitBtns text-center mt-4">
                                    <div class="mr-4">
                                        <button type="submit" class="btn formBtn submitBtn">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-6">
                <div class="todayappointments p-3">
                    <h5>Todays Appointments</h5>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Patient ID</th>
                                <th>Patient Name</th>
                                <th>Doctor Name</th>
                                <th>Time</th>
                                <th>Staus</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($today_appoinment as $today_appoinments)
                            <tr>
                                <td>{{$today_appoinments->RollNumber}}</td>
                                <td>{{$today_appoinments->Name}}</td>
                                <td>{{$today_appoinments->name}}</td>
                                <td>{{date('h:i a', strtotime($today_appoinments->Appoinment_Time))}}</td>
                                <td>{{$today_appoinments->status}}</td>
                            </tr>
                           @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="doctorlist  p-3">
                    <h5>Doctors</h5>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Doctor Name</th>
                                {{--  <th>Specialization</th>  --}}
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($staffs as $staff)
                            <tr>
                                <td>{{ $staff->name }}</td>
                                {{--  <td></td>  --}}
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
                <div class="col-md-6">
                    <div class=" inpatientlist p-3">
                    <h5>InPatient List</h5>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Patient ID</th>
                                <th>Patient Name</th>
                                <th>Date of Joining</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($Inpatientslist as $Inpatientslists)
                            <tr>
                                <td>{{$Inpatientslists->id}}</td>
                                <td>{{$Inpatientslists->Name}}</td>
                                <td>{{$Inpatientslists->admitted_date}}</td>
                                <td>{{$Inpatientslists->status}}</td>

                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')

<script src="{{ asset('js/receptionist.js') }}" type="text/javascript"></script>

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
    </script>
@endsection
