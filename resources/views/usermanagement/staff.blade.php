@include('layouts.header')
@extends('layouts.footer')
<section id="addstaff" class="content-wrapper_sub tab-content">
    <div class="row">
        <div class="col-lg-7">
          <div class="input-group">
              <input type="text" class="form-control" placeholder="Search">
              <div class="input-group-append">
                <button class="btn btn-secondary" type="button">
                  <img src="./images/search.png" />
                </button>
              </div>
            </div>
        </div>
        <div class="col-lg-3">
          <ul class="nav_menu_list">
              <li><a href="#"><img src="./images/settings.png" /><h6>Settings</h6></a></li>
              <li><a href="#"><img src="./images/notification.png" /><h6>Notifications</h6></a></li>
          </ul>
        </div>
        <div class="col-lg-2 text-center username">
          <a href="#"><img src="./images/adminuser.png" /><span>Admin</span></a>
        </div>
      </div>
      <div class="user_manage">
        <div class="row">
            <div class="col-md-6">
              <h4>Add Staff</h4>
            </div>
            <div class="col-md-6">
                <div class="useractionBtns">
                  <button type="button" class="btn formBtn importBtn">Import</button>
                  <button type="button" class="btn formBtn excelBtn">Sample Excel</button>
             </div>
            </div>
      </div>
      <form class="userform">
        <div class="row">
            <div class="col">
                <label>First Name</label>
              <input type="text" class="form-control" id = "first_name">
            </div>
            <div class="col">
                <label>Last Name</label>
              <input type="text" class="form-control" id = "last_name">
            </div>
          </div>
          <div class="row">
            <div class="col">
                <label>Email</label>
              <input type="text" class="form-control" id ="email">
            </div>
            <div class="col">
                <label>DOB</label>
              <input type="text" class="form-control" id = "dob">
            </div>
          </div>
          <div class="row">
            <div class="col">
                <label>Mobile Number</label>
              <input type="text" class="form-control" id = "mobile_number">
            </div>
            <div class="col">
                <label>Role</label>
                <select class="form-control" id="role">
                    <option>1</option>
                    <option>2</option>
                    <option>3</option>
                    <option>4</option>
                  </select>
            </div>
          </div>

        <div class="usersubmitBtns mt-5">
            <div class="mr-4">
                <button wire:click.prevent="store()" type="submit" class="btn formBtn submitBtn">Submit</button>
            </div>
            <div>
                <button type="button" class="btn formBtn cancelBtn">Cancel</button>
            </div>
        </div>
      </form>
      </div>
</section>
