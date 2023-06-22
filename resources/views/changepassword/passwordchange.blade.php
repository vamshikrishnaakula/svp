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
 @elseif ($message = Session::get('deletes'))
 <div class="alert alert-danger">
     <p>{{ $message }}</p>
 </div>
 @elseif ($message = Session::get('deletess'))
 <div class="alert alert-danger">
     <p>{{ $message }}</p>
 </div>
    @endif

    <section id="addprobationer" class="content-wrapper_sub">
        <div class="user_manage">
          <div class="row">
          <div class="col-md-10">
                <h4>Change Password</h4>
              </div>
              <div class="col-md-2">
                  <div class="userBtns">
                  <!-- <a href="#" data-toggle="tooltip" title="import"> <img src="./images/import.png" /></a>
                  <a href="#" data-toggle="tooltip" title="excel"> <img style="width:29px !important"  src="./images/excel.png" /></a> -->
                  </div>
              </div>
        </div>
     


  <form class="changepasswordform"  action="/changepassword" method="POST" autocomplete="off">
    @csrf
              <div class="row">
                  <div class="col">
                  <label>Old Password</label>
                  <input type="password" class="form-control" id = "oldpassword" name="oldpassword">
                  </div>
  
              </div>
  
               <div class="row">
                  <div class="col">
                      <label>New Password</label>
                    <input type="password" class="form-control" id = "newpassword" name="newpassword">
                  </div>
              </div>
  
              <div class="row">
                  <div class="col">
                      <label>Confirm Password</label>
                    <input type="password" class="form-control" id = "confirmpassword" name="confirmpassword">
                  </div>
              </div>
  
              
              <div class="usersubmitBtns mt-5">
                  <div class="mr-4">
                      <button class="btn formBtn submitBtn">Change Password</button>
                  </div>
  
              </div>
            </form>
        </div>
  </section>
  @endsection





























