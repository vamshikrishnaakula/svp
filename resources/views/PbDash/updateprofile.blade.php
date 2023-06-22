
{{-- Extends layout --}}
@extends('layouts.pbdash.template')

{{-- Content --}}
@section('content')

<div id="error"></div>
@if ($message = Session::get('success'))
<div class="alert alert-success">
    <p>{{ $message }}</p>
</div>
@elseif ($message = Session::get('delete'))
<div class="alert alert-danger">
    <p>{{ $message }}</p>
</div>

@endif

<section id="probationerprofile" class="content-wrapper_sub tab-content">
    <form class="userform" action="{{ route('pbdash.update',$get_probationer->user_id)}}" method="POST">
        @csrf
        @method('PUT')
<div class="user_manage">
<div class="row">
      <div class="col-md-6">
        <h4>Update Profile</h4>
      </div>
      <div class="col-md-6 text-right">
        <img src={{ isset($get_probationer->profile_url) ?  "data:image/png;base64,".$get_probationer->profile_url : asset('images/photo_upload.png') }}  class="rounded-circle" />
      </div>
</div>
  <table class="mt-4 table-striped">
      <tbody>
        <input type="hidden" class="form-control" id = "pid" name="pid" value = "{{ $get_probationer->user_id }}">
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
              <td>RELIGION</td>
              <td><input type="text" name="religion" id="religion" value = "{{ $get_probationer->Religion }}"></td>
          </tr>
          <tr>
              <td>CATEGORY</td>
              <td><input type="text" name="category" id="category" value = "{{ $get_probationer->Category }}"></td>
          </tr>
          <tr>
              <td>MARITAL STATUS</td>
              <td><input type="text" name="maritalstatus" id="maritalstatus" value = "{{ $get_probationer->MartialStatus }}"></td>

          </tr>
          <tr>
              <td>MOTHER'S NAME</td>
              <td><input type="text" name="mothersname" id="mothersname" value = "{{ $get_probationer->MotherName }}"></td>

          <tr>
              <td>OCCUPATION</td>
              <td><input type="text" name="m_occupation" id="m_occupation" value = "{{ $get_probationer->Moccupation }}"></td>

          </tr>
          <tr>
              <td>FATHER'S NAME</td>
              <td><input type="text" name="fathersname" id="fathersname" value = "{{ $get_probationer->FatherName }}"></td>

          </tr>
          <tr>
              <td>OCCUPATION</td>
              <td><input type="text" name="f_occupation" id="f_occupation" value = "{{ $get_probationer->Foccupation }}"></td>

          </tr>
          <tr>
              <td>STATE OF DOMICILE</td>
              <td><input type="text" name="stateofdomicile" id="stateofdomicile" value = "{{ $get_probationer->Stateofdomicile }}"></td>

          </tr>
          <tr>
              <td>HOME ADDRESS</td>
              <td><input type="text" name="homeaddress" id="homeaddress" value = "{{ $get_probationer->HomeAddress }}"></td>

          </tr>
          <tr>
              <td>HOME TOWN</td>
              <td><input type="text" name="hometown" id="hometown" value = "{{ $get_probationer->Hometown }}"></td>

          </tr>
          <tr>
              <td>DISTRICT</td>
              <td><input type="text" name="district" id="district" value = "{{ $get_probationer->District }}"></td>

          </tr>
          <tr>
              <td>STATE</td>
              <td><input type="text" name="state" id="state" value = "{{ $get_probationer->State }}"></td>

          </tr>
          <tr>
              <td>PINCODE</td>
              <td><input type="text" name="pincode" id="pincode" value = "{{ $get_probationer->Pincode }}"></td>

          </tr>
          <tr>
              <td>PHONE NO WITH STD CODE</td>
              <td><input type="text" name="phonenowithstdcode" id="phonenowithstdcode" value = "{{ $get_probationer->phoneNumberStd }}"></td>

          <tr>
              <td>Which State in India, Other than your home state, have you lived or worked in?</td>
              <td><input type="text" name="whichstateinindia" id="whichstateinindia" value = "{{ $get_probationer->OtherState }}"></td>

          </tr>
          <tr>
              <td colspan="2"><p class="mb-0"><b>Name & contact details of next kin who should be notified in case of any emergency</b></p></td>
          </tr>
          <tr>
              <td>NAME</td>
              <td><input type="text" name="ename" id="ename" value = "{{ $get_probationer->EmergencyName }}"></td>

          </tr>
          <tr>
              <td>ADDRESS</td>
              <td><input type="text" name="eaddress" id="eaddress" value = "{{ $get_probationer->EmergencyAddress }}"></td>

          </tr>
          <tr>
              <td>Number</td>
              <td><input type="text" name="epnostd" id="epnostd" value = "{{ $get_probationer->EmergencyPhone }}"></td>

          </tr>
          <tr>
              <td>Email ID</td>
              <td><input type="text" name="eemailid" id="eemailid" value = "{{ $get_probationer->EmergencyEmailId }}"></td>
          </tr>
      </tbody>
  </table>
  <div class="usersubmitBtns mt-5">
    <div class="mr-4">
        <button class="btn formBtn submitBtn">Submit</button>
    </div>
</div>
</div>
</div>
    </form>
</section>
@endsection
