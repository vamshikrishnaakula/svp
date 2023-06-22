@include('layouts.header')
@extends('layouts.footer')
<section id="addprobationer" class="content-wrapper_sub tab-content">
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
                      <h4>Add Probationer</h4>
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
                        <label>CADRE</label>
                      <input type="text" class="form-control">
                    </div>
                    <div class="col">
                        <label>Name</label>
                      <input type="text" class="form-control">
                    </div>
                  </div>
                  <div class="row">
                    <div class="col">
                        <label>DOB</label>
                      <input type="text" class="form-control">
                    </div>
                    <div class="col">
                        <label>Religion</label>
                        <select class="form-control" id="sel1">
                            <option>1</option>
                            <option>2</option>
                            <option>3</option>
                            <option>4</option>
                          </select>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col">
                        <label>Category</label>
                        <select class="form-control" id="sel1">
                            <option>1</option>
                            <option>2</option>
                            <option>3</option>
                            <option>4</option>
                          </select>
                    </div>
                    <div class="col">
                        <label>Marital Status</label>
                        <select class="form-control" id="sel1">
                            <option>1</option>
                            <option>2</option>
                            <option>3</option>
                            <option>4</option>
                          </select>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col">
                        <label>Mother's Name</label>
                      <input type="text" class="form-control">
                    </div>
                    <div class="col">
                        <label>Occupation</label>
                        <select class="form-control" id="sel1">
                            <option>1</option>
                            <option>2</option>
                            <option>3</option>
                            <option>4</option>
                          </select>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col">
                        <label>Father's Name</label>
                      <input type="text" class="form-control">
                    </div>
                    <div class="col">
                        <label>Occupation</label>
                        <select class="form-control" id="sel1">
                            <option>1</option>
                            <option>2</option>
                            <option>3</option>
                            <option>4</option>
                          </select>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                        <label>State of Domicile</label>
                      <input type="text" class="form-control">
                      <label>Home town</label>
                      <input type="text" class="form-control">
                      <label>District</label>
                      <input type="text" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label>Home Address</label>
                      <textarea type="textarea" rows="5" class="form-control"></textarea>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                        <label>Sate</label>
                      <input type="text" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label>Pincode</label>
                      <input type="text" class="form-control">
                    </div>                 
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                        <label>Phone No with STD Code</label>
                      <input type="text" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label>Mobile Contact Number</label>
                      <input type="text" class="form-control">
                    </div>                 
                  </div>
                  <div class="row">
                    <div class="col-md-12">
                        <label>Which State in India, Other than your home state, have you lived or worked in?</label>
                      <input type="text" class="form-control">
                    </div>                               
                  </div>
                  <p><b>Name & contact details of next kin who should be notified in case of any emergency</b></p>
                  <div class="row">
                    <div class="col-md-6">
                        <label>Name</label>
                      <input type="text" class="form-control">
                      <label>Phone No with STD Code</label>
                      <input type="text" class="form-control">
                      <label>Email ID</label>
                      <input type="email" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label>Address</label>
                      <textarea type="textarea" rows="5" class="form-control"></textarea>
                    </div>
                  </div>

                <div class="usersubmitBtns mt-5">
                    <div class="mr-4">
                        <button type="submit" class="btn formBtn submitBtn">Submit</button>
                    </div>
                    <div>
                        <button type="button" class="btn formBtn cancelBtn">Cancel</button>
                    </div>
                </div>
              </form>
              </div>
        </section>