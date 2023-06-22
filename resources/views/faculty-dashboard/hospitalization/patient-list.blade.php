{{-- Extends layout --}}
@extends('layouts.faculty.template')

{{-- Content --}}
@section('content')

<section id="activitieslist" class="content-wrapper_sub tab-content">
    <div class="user_manage">
        <div class="squadlisthead">
            <div class="row">
                <div class="col-md-6">
                    <div class="activityhead">
                        <img src="{{ asset('images/indooractivities.png') }}" />
                        <h4 class="mb-0 ml-4">Patient List</h4>
                    </div>
                </div>
                <div class="col-md-6">
                </div>
            </div>
        </div>
        <div id="activity-list" class="listdetails">
            <table class="table text-left">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th class="text-left">Batch Number</th>
                        <th class="text-left">Name</th>
                        <th class="text-left">Doctor Name</th>
                        <th class="text-left">Admitted Date</th>
                        <th class="text-left">Emergency Number</th>
                        <th class="text-left">Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="activity-list-tbody">
                    @foreach($patientslist as $patientslists)
                    <?php
                        $doctor_id      = $patientslists->Doctor_Id;
                        $doctor_name    = user_name($doctor_id);

                        $EmergencyPhone = $patientslists->EmergencyPhone;
                        if(empty($EmergencyPhone)) {
                            $EmergencyPhone = '-';
                        }
                    ?>
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{$patientslists->batch_id}}</td>
                        <td>{{$patientslists->Name}}</td>
                        <td>{{ $doctor_name }}</td>
                        <td>{{date('d-m-Y', strtotime($patientslists->admitted_date))}}</td>
                        <td>{{ $EmergencyPhone }}</td>
                        <td>{{$patientslists->status}}</td>
                        <td>
                            <a href="{{ route('inpatientprescription', $patientslists->appoinmentid) }}">
                                <img src="{{ asset('/images/edit.png') }}">
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>

@endsection
