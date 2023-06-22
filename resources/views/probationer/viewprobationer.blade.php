
{{-- Extends layout --}}
@extends('layouts.default')

{{-- Content --}}
@section('content')

<section id="probationerprofile" class="content-wrapper_sub tab-content">

<div class="user_manage">
<div class="row">
      <div class="col-md-6">
        <h4>Probationer Profile</h4>
      </div>
      <div class="col-md-6 text-right">
        <img src={{ isset($get_probationer->profile_url) ?  "data:image/png;base64,".$get_probationer->profile_url : asset('images/photo_upload.png') }}  class="rounded-circle" />
      </div>
</div>
  <table class="mt-4 table-striped">
      <tbody>
          <tr>
              <td>CADRE</td>
              <td>{{ $get_probationer->Cadre }}</td>
          </tr>
          <tr>
              <td>NAME</td>
              <td>{{ $get_probationer->Name }}</td>
          </tr>
          <tr>
              <td>Date of Birth</td>
              <td>{{date('d-m-Y', strtotime($get_probationer->Dob))}}</td>
          </tr>
          <tr>
              <td>ROLL NO</td>
              <td>{{ $get_probationer->RollNumber }}</td>
          </tr>
          <tr>
              <td>GENDER</td>
              <td>{{ $get_probationer->gender }}</td>
          </tr>
          <tr>
              <td>Email</td>
              <td>{{ $get_probationer->Email }}</td>
          </tr>
          <tr>
              <td>Mobile Number</td>
              <td>{{ $get_probationer->MobileNumber }}</td>
          </tr>
          <tr>
            <td>Squad Number</td>
            <td>{{ squad_number($get_probationer->squad_id) }}</td>
        </tr>
        <tr>
            <td>Drill Instructor</td>
            <td>{{ $get_probationer->name }}</td>
        </tr>
          <tr>
              <td>RELIGION</td>
              <td>{{ $get_probationer->Religion }}</td>
          </tr>
          <tr>
              <td>CATEGORY</td>
              <td>{{ $get_probationer->Category }}</td>
          </tr>
          <tr>
              <td>MARITAL STATUS</td>
              <td>{{ $get_probationer->MartialStatus }}</td>
          </tr>
          <tr>
              <td>MOTHER'S NAME</td>
              <td>{{ $get_probationer->MotherName }}</td>
          </tr>
          <tr>
              <td>OCCUPATION</td>
              <td>{{ $get_probationer->Moccupation }}</td>
          </tr>
          <tr>
              <td>FATHER'S NAME</td>
              <td>{{ $get_probationer->FatherName }}</td>
          </tr>
          <tr>
              <td>OCCUPATION</td>
              <td>{{ $get_probationer->Foccupation }}</td>
          </tr>
          <tr>
              <td>STATE OF DOMICILE</td>
              <td>{{ $get_probationer->Stateofdomicile }}</td>
          </tr>
          <tr>
              <td>HOME ADDRESS</td>
              <td>{{ $get_probationer->HomeAddress }}</td>
          </tr>
          <tr>
              <td>HOME TOWN</td>
              <td>{{ $get_probationer->Hometown }}</td>
          </tr>
          <tr>
              <td>DISTRICT</td>
              <td>{{ $get_probationer->District }}</td>
          </tr>
          <tr>
              <td>STATE</td>
              <td>{{ $get_probationer->State }}</td>
          </tr>
          <tr>
              <td>PINCODE</td>
              <td>{{ $get_probationer->Pincode }}</td>
          </tr>
          <tr>
              <td>PHONE NO WITH STD CODE</td>
              <td>{{ $get_probationer->phoneNumberStd }}</td>
          </tr>
          <tr>
              <td>Which State in India, Other than your home state, have you lived or worked in?</td>
              <td>{{ $get_probationer->OtherState }}</td>
          </tr>
          <tr>
              <td colspan="2"><p class="mb-0"><b>Name & contact details of next kin who should be notified in case of any emergency</b></p></td>
          </tr>
          <tr>
              <td>NAME</td>
              <td>{{ $get_probationer->EmergencyName }}</td>
          </tr>
          <tr>
              <td>ADDRESS</td>
              <td>{{ $get_probationer->EmergencyAddress }}</td>
          </tr>
          <tr>
              <td>Number</td>
              <td>{{ $get_probationer->EmergencyPhone }}</td>
          </tr>
          <tr>
              <td>Email ID</td>
              <td>{{ $get_probationer->EmergencyEmailId }}</td>
          </tr>
      </tbody>
  </table>
</div>
</div>
</section>
@endsection
