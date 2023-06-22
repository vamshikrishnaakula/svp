@include('layouts.header')
@extends('layouts.footer')
<section id="stafflist" class="content-wrapper_sub tab-content">
            <div class="user_manage">
                <div class="row">
                    <div class="col-md-6">
                      <h4>StaffList</h4>
                    </div>
                    <div class="col-md-6 text-right">
                        <div class="download">
                            <a href="#" class="mr-5"><img src="./images/download.png" width="60px" /></a>
                            <a href="#"><img src="./images/print.png" width="35px"/></a>
                        </div>

                    </div>
                </div>
              <div class="listdetails mt-4">
                  <div class="squadlisthead">
                      <div class="row">
                          <div class="col-md-6">
                              <div class="group">
                                <img src="./images/staff.png" />   
                            </div>                                                                
                          </div>
                          <div class="col-md-6">
                              <div class="row group group_right">
                                  <div class="col-md-9">
                                    <input type="search" placeholder="search" class="form-control" />
                                  </div>
                                  <div class="col-md-3 trash">
                                    <a href="#">
                                    <img src="./images/trash.png"/>
                                    <p>Delete</p>
                                  </a>
                                  </div>
                              </div>                     
                      </div>
                    </div>
                  </div>
                  <div class="table-responsive">
                      <table class="table">
                          <thead>
                              <tr>
                                  <th width=10%;></th>
                                  <th>Staff ID</th>
                                  <th>Batch Number</th>
                                  <th>Roll Number</th>
                                  <th>Name</th>
                                  <th>Category</th>
                                  <th>Mobile number</th>
                              </tr>
                          </thead>
                          <tbody>
                              <tr>
                                  <td><input type="checkbox" class="form-control" /></td>
                                  <td>gretrhr</td>
                                  <td>435345</td>
                                  <td>6546456</td>
                                  <td>gregerg</td>
                                  <td>gerger</td>
                                  <td>34636363</td>
                                  <td><a href="./editstaff.html"><img src="./images/edit.png" /><span>Edit</span></a></td>
                              </tr>
                              <tr>
                                <td><input type="checkbox" class="form-control" /></td>
                                <td>gretrhr</td>
                                <td>435345</td>
                                <td>6546456</td>
                                <td>gregerg</td>
                                <td>gerger</td>
                                <td>34636363</td>
                                <td><a href="#"><img src="./images/edit.png" /><span>Edit</span></a></td>
                            </tr>
                          </tbody>
                      </table>
                     
                  </div>

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
        </section>
