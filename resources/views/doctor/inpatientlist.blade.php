{{-- Extends layout --}}
@extends('layouts.doctor.template')

{{-- Content --}}
@section('content')

<section id="appointments" class="content-wrapper_sub tab-content">
    <div class="user_manage">
        <div class="row">
            <div class="col-md-12">
                <h5>In Patients List</h5>
            </div>
        </div>
        <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered table-hover inpatient_list_table">
                        <thead class="thead-dark">
                            <tr>
                                <th width="2%">S.No</th>
                                <th>Probationer Name</th>
                                <th>Gender</th>
                                <th>Admitted Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($patientslist as $patientslists)
                        @if($patientslists->status == 'Open')
                            <tr onclick="window.location='{{ route('inpatientprescription', $patientslists->appoinmentid) }}';">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{$patientslists->Name}}</td>
                                <td>{{$patientslists->gender}}</td>
                                <td>{{date('d-m-Y', strtotime($patientslists->admitted_date))}}</td>
                                <td>{{$patientslists->status}}</td>

                            </tr>
                        @endif
                         @if($patientslists->status == 'Discharge')
                            <tr class="no_cursor">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{$patientslists->Name}}</td>
                                <td>{{$patientslists->gender}}</td>
                                <td>{{date('d-m-Y', strtotime($patientslists->admitted_date))}}</td>
                                <td>{{$patientslists->status}}</td>
                            </tr>
                              @endif
                           @endforeach
                        </tbody>
                    </table>
        </div>
</div>
            </div>


</section>
@endsection
