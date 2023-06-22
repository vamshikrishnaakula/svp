{{-- Extends layout --}}
@extends('layouts-Receptionist.default')

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

    <section id="healthprofile" class="content-wrapper_sub tab-content">
      <div class="user_manage">
      <div class="row">
          <div class="col-md-9">
            <div id="error"></div>
            <h4>Health Profiles</h4>
          </div>
          <div class="col-md-3">
            <div class="useractionBtns">
                <!-- <a href="#" data-toggle="tooltip" title="import"> <img src="{{ asset('images/import.png') }}" /></a>
                <a href="#" data-toggle="tooltip" title="excel"> <img src="{{ asset('images/excel.png') }}" /></a>
                <a href="#" data-toggle="tooltip" title="download"> <img src="{{ asset('images/download1.png') }}" /></a>
                <a href="#" data-toggle="tooltip" title="print"> <img src="{{ asset('images/print1.png') }}" /></a> -->



            </div>
          </div>
          {{-- <div class=" userprofileimg col-md-2">
            <img src="./images/probationerprofile.png" class="rounded-circle" />
          </div> --}}
        </div>

        <form class="mt-5" id="get_prob_details" name="get_prob_details" style="padding: 20px 0px">
        <div class="row">
              <div class="col-md-5">
                <label class="text-center">Name / Roll No :</label>
                <div class="row no-gutters" style="display:flex; align-items: center;">
                  <div class="col-md-6">
                <input class="form-control" type="text" id="roll_no" name="roll_no">
                <input class="form-control" type="hidden" id="prob_id" name="prob_id">
              </div>
                <div class="rollnosubmit">
                    <button style="background: transparent;" type="submit" class ="btn btn-img" id ="get_prob_details"><img src="{{ asset('images/submit.png') }}" /></button>
                </div>
                </div>
              </div>
         </form>

              <div class="col-md-5">
                  <div class="prob_info">
              <div class="form-group">
                       <label>Name :</label>
                       <span id="prob_name"></span>
                        </div>
                        <div class="form-group">
                       <label>Squad Number :</label>
                       <span id="prob_dob"></span>
                        </div>
                        <div class="form-group">
                       <label>Mobile Number :</label>
                       <span id="prob_gender"></span>
                        </div>
                       <div>
                    </div>

                    <input type='hidden' class='form-control' id='pid' />
                  </div>
              </div>
              <div class="col-md-2 text-center">
                <img id="profile_images" src="" class="rounded-circle img-align" />
            </div>
          </div>
          <!-- <div class="row">
              <div class="col-md-5">
                <label class="text-center">Roll Number :</label>
                <div class="row no-gutters">
                <input class="form-control col-md-6" type="number" id="roll_no" name="roll_no">
                <div class=" rollnosubmit">
                <a href="#" class="col-md-6 pl-3" id="get_prob_details"><img src="{{ asset('images/submit.png') }}" /></a>
                </div>
                </div>
              </div>
              <div class="col-md-7">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <img src="{{ asset('images/probationerprofile.png') }}" class="rounded-circle" width="120" />
                    </div>
                    <div class="col-md-8 prob_info">
                        <div class="form-group">
                       <label>Name :</label>
                       <span id="prob_name"></span>
                        </div>
                        <div class="form-group">
                       <label>Date Of Birth :</label>
                       <span id="prob_dob"></span>
                        </div>
                        <div class="form-group">
                       <label>Gender :</label>
                       <span id="prob_gender"></span>
                        </div>
                       <div>
                       </div>

                    </div>
                    <input type='hidden' class='form-control' id='pid' />
            </div>
              </div>
          </div> -->

        <div class="mt-5">
        <ul class="nav nav-tabs">
          <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#familyinfo">Family Info</a></li>
          <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#generalinfo">General Info</a></li>
          <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#familyhistory">Family History</a></li>
          <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#physicalexam">Physical Exam</a></li>
          <!-- <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#medicalexam">Medical Exam</a></li> -->
          <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#investigation">Investigation</a></li>
          <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#prescription">Prescription</a></li>
          <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#dischargesummary">Discharge Summary</a></li>
          <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#reports">Lab Reports</a></li>
          <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#viewhistory">View History</a></li>
        </ul>

        <div class="tab-content">

          <div id="familyinfo" class="tab-pane fade in active show">
            <div class="listdetails mt-4">
              <div class="squadlisthead">
              <div class="row">
                  <div class="col-md-6">
                    <div class="group">
                      <h4 class="ml-4">Dependents</h4>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="row">
                      <div class="col-md-12 patient_userBtns">
                        <a  class="addDependents">
                            <img src="{{ asset('images/adddependent.png') }}" />
                            <p>Add</p>
                        </a>
                      </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="table-responsive dependents">
                <table class="table" id = "d_table">
                    <thead>
                        <th>Name</th>
                        <th>Age</th>
                        <th>Gender</th>
                        <th>Relationship</th>
                        <th></th>
                    </thead>
                  <tbody>

                  </tbody>

                </table>

              </div>
              <!-- <div class="table-responsive dependents">
                <table class="table" id = "d_table">
                  <tbody>
                  <tr>
                    <td colspan = '4'> Please Enter Probationer Roll No and submit</td>
                </tr>
                  </tbody>

                </table>

              </div> -->
            </div>
          </div>
          <div id="generalinfo" class="tab-pane fade">
            <div class="text-right mb-4">
                <img class="editProfiles" src="{{ asset('images/edithealthprofile.png') }}" />
                <img class="cancelprofiles" style="display: none" src="{{ asset('images/wrong.png') }}" />
            </div>
            <form action="{{ route('healthprofile.store') }}" method="POST" id ="generalInfoSubmit" name ="generalInfoSubmit">
            @csrf

              <div class="row">
                <div class="col-md-6">
                  <div class="generalinfo">
                  <div class="form-group">
                    <label>Height(cms) :</label>
                    <p id="pHeight"></p>
                    <input type="text" class="form-control" id ="Height" name = "Height">
                    <input type='hidden' class='form-control' id='pid1' name = "pid_generalinfo" />
                  </div>
                  <div class="form-group">
                    <label>Weight( kgs ) :</label>
                    <p id="pWeight"></p>
                    <input type="text" class="form-control" id ="Weight" name = "Weight">
                  </div>
                  <div class="form-group">
                    <label>Past History :</label>
                    <p id ="pPastHistory"></p>
                    <textarea class="form-control" id ="PastHistory" name = "PastHistory"></textarea>
                  </div>
                </div>
                </div>
                <div class="col-md-6">
                  <div class="readings">
                    <h6><b>Chest Reading</b></h6>
                    <div class="form-group">
                      <label >Expi(cms) :</label>
                      <p id="pExpi"></p>
                      <input type="text" class="form-control" id ="Expi" name = "Expi">
                    </div>
                    <div class="form-group">
                      <label >Ins(cms) :</label>
                      <p id="pIns"></p>
                      <input type="text" class="form-control" id ="Ins" name = "Ins">
                    </div>
                    <div class="form-group">
                      <label >Expansion(cms) :</label>
                      <p id="pExpansion"></p>
                      <input type="text" class="form-control" id ="Expansion" name = "Expansion">
                    </div>

                  </div>
                </div>
              </div>
              <div class="usersubmitBtns mt-4">
                <div class="mr-4">
                <button type="button" class="btn formBtn submitBtn"  onclick = "getdata()" id = "get_general_details">Submit</button>
                </div>
              </div>
            </form>
          </div>
          <!-- <div id="generalinfo" class="tab-pane fade">
            <form method="POST" id ="generalInfoSubmit" name ="generalInfoSubmit">
            @csrf
              <div class="row">
                <div class="col-md-6">
                  <div class="generalinfo">
                  <div class="form-group">
                    <label>Height(cms)</label>
                    <input type="text" class="form-control" id ="Height" name = "Height">
                    <input type='hidden' class='form-control' id='pid1' name = "pid_generalinfo" />
                  </div>
                  <div class="form-group">
                    <label>Weight( kgs )</label>
                    <input type="text" class="form-control" id ="Weight" name = "Weight">
                  </div>
                  <div class="form-group">
                    <label>Past History</label>
                    <textarea class="form-control" id ="PastHistory" name = "PastHistory"></textarea>
                  </div>
                </div>
                </div>
                <div class="col-md-6">
                  <div class="readings">
                    <p><b>Chest Reading</b></p>

                    <div class="form-group">
                      <label >Expi(cms)</label>
                      <input type="text" class="form-control" id ="Expi" name = "Expi">
                    </div>
                    <div class="form-group">
                      <label >Ins(cms)</label>
                      <input type="text" class="form-control" id ="Ins" name = "Ins">
                    </div>
                    <div class="form-group">
                      <label >Expansion(cms)</label>
                      <input type="text" class="form-control" id ="Expansion" name = "Expansion">
                    </div>

                  </div>
                </div>
              </div>

              <div class="usersubmitBtns mt-5">
                <div class="mr-4">
                  <button type="button" class="btn formBtn submitBtn"  onclick = "getdata()" id = "get_general_details">Submit</button>
                </div>
              </div>
            </form>
          </div> -->
          <div id="familyhistory" class="tab-pane fade">
            <div class="text-right mb-4">
                <img class="editProfiles" src="{{ asset('images/edithealthprofile.png') }}" />
                <img class="cancelprofiles" style="display: none" src="{{ asset('images/wrong.png') }}" />
            </div>
            <form method="POST" id ="familyInfoSubmit" name ="familyInfoSubmit">
              <div class="row familyhistory">
                <div class="col-md-7">
                <h6><b>Family History</b></h6>
                <div class="row">
                <input type='hidden' class='form-control' id='pid4' name = "pid_familyhistory" />
                  <div class="form-group col-md-12 diabetes d-inline-flex">
                    <label>Diabetes</label>
                    <p id="p_cbxyes1"></p>
                    <div class="checkboxx">

                    <input type="checkbox" class="customcheckbox" id="cbxyes1" style="display: none;" name ="Diabetes" value="1">
                    <label for="cbxyes1" class="check">
                      <svg width="18px" height="18px" viewBox="0 0 18 18">
                        <path d="M1,9 L1,3.5 C1,2 2,1 3.5,1 L14.5,1 C16,1 17,2 17,3.5 L17,14.5 C17,16 16,17 14.5,17 L3.5,17 C2,17 1,16 1,14.5 L1,9 Z"></path>
                        <polyline points="1 9 7 14 15 4"></polyline>
                      </svg>
                      Yes
                    </label>

                   <!--  <input type="checkbox" id="cbxno1" class="customcheckbox" style="display: none;" name ="Diabetes"  value="1">
                    <label for="cbxno1" class="check">
                      <svg width="18px" height="18px" viewBox="0 0 18 18">
                        <path d="M1,9 L1,3.5 C1,2 2,1 3.5,1 L14.5,1 C16,1 17,2 17,3.5 L17,14.5 C17,16 16,17 14.5,17 L3.5,17 C2,17 1,16 1,14.5 L1,9 Z"></path>
                        <polyline points="1 9 7 14 15 4"></polyline>
                      </svg>
                      No
                    </label> -->
                </div>
                  </div>
                </div>
                <div class="row">
                  <div class="form-group col-md-12 heartdiseases d-inline-flex">
                    <label>Heart Diseases</label>
                    <p id="p_cbxyes2"></p>
                    <div class="checkboxx">
                    <input type="checkbox" id="cbxyes2" class="customcheckbox" style="display: none;" name ="heartdiseases" value="1">
                    <label for="cbxyes2" class="check">
                      <svg width="18px" height="18px" viewBox="0 0 18 18">
                        <path d="M1,9 L1,3.5 C1,2 2,1 3.5,1 L14.5,1 C16,1 17,2 17,3.5 L17,14.5 C17,16 16,17 14.5,17 L3.5,17 C2,17 1,16 1,14.5 L1,9 Z"></path>
                        <polyline points="1 9 7 14 15 4"></polyline>
                      </svg>
                      Yes
                    </label>
                    <!-- <input type="checkbox" id="cbxno2" class="customcheckbox" style="display: none;" name ="heartdiseases" value="1"> -->
                    <!-- <label for="cbxno2" class="check">
                      <svg width="18px" height="18px" viewBox="0 0 18 18">
                        <path d="M1,9 L1,3.5 C1,2 2,1 3.5,1 L14.5,1 C16,1 17,2 17,3.5 L17,14.5 C17,16 16,17 14.5,17 L3.5,17 C2,17 1,16 1,14.5 L1,9 Z"></path>
                        <polyline points="1 9 7 14 15 4"></polyline>
                      </svg>
                      No
                    </label> -->
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="form-group col-md-12 d-inline-flex">
                    <label>Migraine</label>
                    <p id="p_cbxyes3"></p>
                    <div class="checkboxx">
                    <input type="checkbox" id="cbxyes3" class="customcheckbox" style="display: none;"  name ="migrane" value="1">
                    <label for="cbxyes3" class="check">
                      <svg width="18px" height="18px" viewBox="0 0 18 18">
                        <path d="M1,9 L1,3.5 C1,2 2,1 3.5,1 L14.5,1 C16,1 17,2 17,3.5 L17,14.5 C17,16 16,17 14.5,17 L3.5,17 C2,17 1,16 1,14.5 L1,9 Z"></path>
                        <polyline points="1 9 7 14 15 4"></polyline>
                      </svg>
                      Yes
                    </label>
                   <!--  <input type="checkbox" id="cbxno3" class="customcheckbox" style="display: none;" name ="migrane" value="1">
                    <label for="cbxno3" class="check">
                      <svg width="18px" height="18px" viewBox="0 0 18 18">
                        <path d="M1,9 L1,3.5 C1,2 2,1 3.5,1 L14.5,1 C16,1 17,2 17,3.5 L17,14.5 C17,16 16,17 14.5,17 L3.5,17 C2,17 1,16 1,14.5 L1,9 Z"></path>
                        <polyline points="1 9 7 14 15 4"></polyline>
                      </svg>
                      No
                    </label> -->
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="form-group col-md-12 d-inline-flex">
                    <label>Epilepsy</label>
                    <p id="p_cbxyes4"></p>
                    <div class="checkboxx">
                    <input type="checkbox" id="cbxyes4" class="customcheckbox" style="display: none;" name ="epilepsy" value="1">
                    <label for="cbxyes4" class="check">
                      <svg width="18px" height="18px" viewBox="0 0 18 18">
                        <path d="M1,9 L1,3.5 C1,2 2,1 3.5,1 L14.5,1 C16,1 17,2 17,3.5 L17,14.5 C17,16 16,17 14.5,17 L3.5,17 C2,17 1,16 1,14.5 L1,9 Z"></path>
                        <polyline points="1 9 7 14 15 4"></polyline>
                      </svg>
                      Yes
                    </label>
                 <!--    <input type="checkbox" id="cbxno4" class="customcheckbox" style="display: none;" name ="epilepsy" value="1">
                    <label for="cbxno4" class="check">
                      <svg width="18px" height="18px" viewBox="0 0 18 18">
                        <path d="M1,9 L1,3.5 C1,2 2,1 3.5,1 L14.5,1 C16,1 17,2 17,3.5 L17,14.5 C17,16 16,17 14.5,17 L3.5,17 C2,17 1,16 1,14.5 L1,9 Z"></path>
                        <polyline points="1 9 7 14 15 4"></polyline>
                      </svg>
                      No
                    </label> -->
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="form-group col-md-12 d-inline-flex">
                    <label>Allergy</label>
                    <p id="p_cbxyes5"></p>
                    <div class="checkboxx">
                    <input type="checkbox" id="cbxyes5" class="customcheckbox" style="display: none;" name ="allergy" value="1">
                    <label for="cbxyes5" class="check">
                      <svg width="18px" height="18px" viewBox="0 0 18 18">
                        <path d="M1,9 L1,3.5 C1,2 2,1 3.5,1 L14.5,1 C16,1 17,2 17,3.5 L17,14.5 C17,16 16,17 14.5,17 L3.5,17 C2,17 1,16 1,14.5 L1,9 Z"></path>
                        <polyline points="1 9 7 14 15 4"></polyline>
                      </svg>
                      Yes
                    </label>
                    <!-- <input type="checkbox" id="cbxno5" class="customcheckbox" style="display: none;" name ="allergy" value="1">
                    <label for="cbxno5" class="check">
                      <svg width="18px" height="18px" viewBox="0 0 18 18">
                        <path d="M1,9 L1,3.5 C1,2 2,1 3.5,1 L14.5,1 C16,1 17,2 17,3.5 L17,14.5 C17,16 16,17 14.5,17 L3.5,17 C2,17 1,16 1,14.5 L1,9 Z"></path>
                        <polyline points="1 9 7 14 15 4"></polyline>
                      </svg>
                      No
                    </label> -->
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-5 personalhistory">
                <h6><b>Personal History</b></h6>
                    <div class="checkbox form-group d-inline-flex">
                        <label>Smoking</label>

                        <div class="checkboxx">
                      <input type="checkbox" id="cbxp1" class="customcheckbox" style="display: none;" name ="smoking" value="1">
                      <label for="cbxp1" class="check">
                        <svg width="18px" height="18px" viewBox="0 0 18 18">
                          <path d="M1,9 L1,3.5 C1,2 2,1 3.5,1 L14.5,1 C16,1 17,2 17,3.5 L17,14.5 C17,16 16,17 14.5,17 L3.5,17 C2,17 1,16 1,14.5 L1,9 Z"></path>
                          <polyline points="1 9 7 14 15 4"></polyline>
                        </svg>

                      </label>
                    </div>
                    <p id="p_cbxp1"></p>
                    </div>
                    <div class="checkbox form-group d-inline-flex">
                        <label>Alcohol</label>

                        <div class="checkboxx">
                      <input type="checkbox" id="cbxp2" class="customcheckbox" style="display: none;" name ="alcohol" value="1">
                      <label for="cbxp2" class="check">
                        <svg width="18px" height="18px" viewBox="0 0 18 18">
                          <path d="M1,9 L1,3.5 C1,2 2,1 3.5,1 L14.5,1 C16,1 17,2 17,3.5 L17,14.5 C17,16 16,17 14.5,17 L3.5,17 C2,17 1,16 1,14.5 L1,9 Z"></path>
                          <polyline points="1 9 7 14 15 4"></polyline>
                        </svg>
                      </label>
                        </div>
                        <p id="p_cbxp2"></p>
                    </div>
                    <div class="checkbox form-group d-inline-flex">
                        <label>Veg</label>
                        <p id="p_cbxp3"></p>
                        <div class="checkboxx">

                      <input type="checkbox" id="cbxp3" class="customcheckbox" style="display: none;" name ="veg" value="1">
                      <label for="cbxp3" class="check">
                        <svg width="18px" height="18px" viewBox="0 0 18 18">
                          <path d="M1,9 L1,3.5 C1,2 2,1 3.5,1 L14.5,1 C16,1 17,2 17,3.5 L17,14.5 C17,16 16,17 14.5,17 L3.5,17 C2,17 1,16 1,14.5 L1,9 Z"></path>
                          <polyline points="1 9 7 14 15 4"></polyline>
                        </svg>

                      </label>
                    </div>
                    <p id="p_cbxp3"></p>
                    </div>

                    <div class="checkbox form-group d-inline-flex">
                        <label>Non-Veg</label>

                        <div class="checkboxx">
                      <input type="checkbox" id="cbxp4" class="customcheckbox" style="display: none;" name ="Nonveg" value="1">
                      <label for="cbxp4" class="check">
                        <svg width="18px" height="18px" viewBox="0 0 18 18">
                          <path d="M1,9 L1,3.5 C1,2 2,1 3.5,1 L14.5,1 C16,1 17,2 17,3.5 L17,14.5 C17,16 16,17 14.5,17 L3.5,17 C2,17 1,16 1,14.5 L1,9 Z"></path>
                          <polyline points="1 9 7 14 15 4"></polyline>
                        </svg>

                      </label>
                        </div>
                        <p id="p_cbxp4"></p>
                    </div>
              </div>
              </div>
              <div class="usersubmitBtns mt-5">
                <div class="mr-4">
                  <button type="button" onclick = "getdata2()" id="get_family_history"  class="btn formBtn submitBtn">Submit</button>
                </div>
              </div>
            </form>
          </div>
          <div id="physicalexam" class="tab-pane fade">
            <div class="text-right mb-4">
                <img class="editProfiles" src="{{ asset('images/edithealthprofile.png') }}" />
                <img class="cancelprofiles" style="display: none" src="{{ asset('images/wrong.png') }}" />
            </div>
            <form class="physicalexamform" method="POST" id ="physicalInfoSubmit" name ="physicalInfoSubmit">
            @csrf
          <div class="row">
            <div class="col-md-5">
              <div class="form-group">
                <label >Blood Pressure</label>
                <input type="text" class="form-control" name="bloodpressure">
                <span id="pbloodpressure"></span>
                <input type='hidden' class='form-control' id='pid2' name = "pid_physicalexam" />
              </div>
              <div class="form-group">
                <label >Pulse</label>
                <span id="ppulse"></span>
                <input type="text" class="form-control" name="pulse">
              </div>
              <div class="form-group">
                <label >ENT</label>
                <span id="pent"></span>
                <input type="text" class="form-control" name="ent">
              </div>
              <div class="form-group">
                <label >Dental Examination</label>
                <span id="pdentalexamination"></span>
                <input type="text" class="form-control" name="dentalexamination">
              </div>
              <div class="form-group">
                <label >Heart</label>
                <span id="pheart"></span>
                <input type="text" class="form-control" name="heart">
              </div>
              <div class="form-group">
                <label >Lungs</label>
                <span id="plungs"></span>
                <input type="text" class="form-control" name="lungs">
              </div>
              <div class="form-group">
                <label >Abdomen</label>
                <span id="pabodmen"></span>
                <input type="text" class="form-control" name="abodmen">
              </div>
            </div>
            <div class="col-md-2"></div>
            <div class="col-md-5">
              <div class="eyesight mb-4">
              <h6><b>Eye Sight</b></h6>
              <p>With Glasses</p>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label >left</label>
                    <span id="plefteye"></span>
                    <input type="text" class="form-control" name="lefteye">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label >right</label>
                    <span id="rrighteye"></span>
                    <input type="text" class="form-control" name="righteye">
                  </div>
                </div>
              </div>
              <p>Without Glasses</p>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label >left</label>
                    <span id="pleftwithoutglasseseye"></span>
                    <input type="text" class="form-control" name="leftwithoutglasseseye">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label >right</label>
                    <span id="prightwithoutglasseseye"></span>
                    <input type="text" class="form-control" name="rightwithoutglasseseye">
                  </div>
                </div>
              </div>
              </div>
              <div class="form-group">
                <label >Urological System</label>
                <span id="purological"></span>
                <input type="text" class="form-control" name="urological">
              </div>
              <div class="form-group">
                <label >Athlete/Non Athlete</label>
                <span id="pathlete"></span>
                <input type="text" class="form-control" name="athlete">
              </div>
              <div class="form-group">
                <label >Any Defect or Deformity</label>
                <span id="pdefectdeformity"></span>
                <input type="text" class="form-control" name="defectdeformity">
              </div>
              <div class="form-group">
                <label >Any scars of operation</label>
                <span id="panyscarsoperation"></span>
                <input type="text" class="form-control" name="anyscarsoperation">
              </div>
            </div>
          </div>
          <div class="usersubmitBtns mt-5">
            <div class="mr-4">
              <button type="button" onclick = "getdata3()" id="get_physical_exam"  class="btn formBtn submitBtn">Submit</button>
            </div>
          </div>
          </form>
          </div>
          <!-- <div id="medicalexam" class="tab-pane fade">
            <form method="POST" id ="generalInfoSubmit" name ="generalInfoSubmit">
            @csrf
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label >Temperature</label>
                            <input type="text" class="form-control" name="temperature">
                            <input type='hidden' class='form-control' id='pid5' name = "pid_medical" />
                          </div>
                          <div class="form-group">
                            <label >Antigen test</label>
                            <input type="text" class="form-control" name="antigen">
                          </div>
                          <div class="form-group">
                            <label >RTPCR</label>
                            <input type="text" class="form-control" name="rtpcr">
                          </div>
                          <div class="form-group">
                            <label >Haemoglobin</label>
                            <input type="text" class="form-control" name="haemoglobin">
                          </div>
                          <div class="form-group">
                            <label >Calcium</label>
                            <input type="text" class="form-control" name="calcium">
                          </div>

                    </div>
                    <div class="col-md-2"></div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label >Vitamin D</label>
                            <input type="text" class="form-control" name="vitamind">
                          </div>
                          <div class="form-group">
                            <label >Vitamin B12</label>
                            <input type="text" class="form-control" name="vitaminb12">
                          </div>
                          <div class="form-group">
                            <label >Pre-existing injury</label>
                            <textarea type="text" class="form-control" name="existinginjury"></textarea>
                          </div>
                          <div class="form-group">
                            <label>Family members ever tested Covid +ve</label>
                            <select class="form-control" name="covid">
                                <option>Select</option>
                              <option>Yes</option>
                              <option>No</option>
                            </select>
                          </div>
                    </div>
                </div>
                <div class="usersubmitBtns mt-5">
                    <div class="mr-4">
                      <button type="submit" class="btn formBtn submitBtn">Submit</button>
                    </div>
                  </div>
            </form>
          </div> -->
          <div id="investigation" class="tab-pane fade">
            <div class="text-right mb-4">
                <img class="editProfiles" src="{{ asset('images/edithealthprofile.png') }}" />
                <img class="cancelprofiles" style="display: none" src="{{ asset('images/wrong.png') }}" />
            </div>
            <form method="POST" id ="investigationInfoSubmit" name ="investigationInfoSubmit">
            @csrf
              <div class="row">
                <div class="col-md-5">
                  <div class="form-group">
                    <label >Urine Examination</label>
                    <input type="text" class="form-control" name="urine">
                    <input type='hidden' class='form-control' id='pid3' name = "pid_investigation" />
                    <span id="purine"></span>
                  </div>
                  <div class="form-group">
                    <label >Blood Group</label>
                    <input type="text" class="form-control" name="bloodgroup">
                    <span id="pbloodgroup"></span>
                  </div>
                  <div class="form-group">
                    <label >RH Factor</label>
                    <input type="text" class="form-control" name="rhfactor">
                    <span id="prhfactor"></span>
                  </div>
                  <div class="form-group">
                    <label >X-Ray Chest PA View</label>
                    <input type="text" class="form-control" name="xraytesting">
                    <span id="pxraytesting"></span>
                  </div>
                </div>
                <div class="col-md-2"></div>
                <div class="col-md-5">
                  <h6><b>Immunization</b></h6>
                  <div class="form-group">
                    <label >Tetanus Oxide</label>
                    <div class="form-group">
                    <input type="text" class="form-control" name="tetanus1">
                    <span id="ptetanus1"></span>
                    </div>
                    <div class="form-group">
                    <input type="text" class="form-control" name="tetanus2">
                    <span id="ptetanus2"></span>
                    </div>
                    <div class="form-group">
                    <input type="text" class="form-control" name="tetanus3">
                    <span id="ptetanus3"></span>
                    </div>
                  </div>

                </div>
              </div>
              <div class="usersubmitBtns mt-5">
                <div class="mr-4">
                  <button type="button" onclick = "getdata4()" id="get_investigation" class="btn formBtn submitBtn">Submit</button>
                </div>
              </div>
            </form>
          </div>

          <div id="prescription" class="tab-pane fade">
            <div class="text-right mb-4">
            <table id='prescription'>
                <thead class="mb-3">
                    <tr style="text-align: center;">
                        <th width="5%">S.NO</th>
                        <th >DATE</th>
                        <th>DOCTOR NAME</th>
                        <th>HOSPITAL NAME</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
            </div>
          </div>

          <div id="dischargesummary" class="tab-pane fade">
            <div class="text-right mb-4">
            <!-- <table class="table txt-center tableinfo" id='dischargesummary'> -->
            <table id='dischargesummary'>
                <thead class="mb-3">
                    <tr style="text-align: center;">
                        <th width="5%">S.NO</th>
                        <!-- <th>Patient Id</th> -->
                        <th>DOCTOR NAME</th>
                        <th>Admitted Date</th>
                        <th>Discharge Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
            </div>
          </div>
          <div id="reports" class="tab-pane fade">
            <div class="text-right mb-4">
            <table id='reports'>
                <thead class="mb-3">
                    <tr style="text-align: center;">
                      <th></th>
                        <th>Date</th>
                        <th>Test Name</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
            </div>
          </div>


             <div id="viewhistory" class="tab-pane fade">
            <div class="text-right mb-4">
            <table id='viewhistory'>
                <thead class="mb-3">
                    <tr style="text-align: center;">
                      <th width="5%">S.NO</th>
                      <th>DOCTOR NAME</th>
                      <th>Date</th>
                      <th>Discharge Date</th>
                      <th>No of Days</th>
                      <!-- <th>Patient Type</th> -->
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
            </div>
          </div>


        </div>

      </div>

    </section>
    @endsection

@section('scripts')

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
    $("#get_general_details").hide();
    $("#get_family_history").hide();
    $("#get_physical_exam").hide();
    $("#get_investigation").hide();

    $('.addDependents').click(function() {
      $(".dependents").append("<form id = 'contactForm1'><div class='row'><div class='col'><input type='text' class='form-control reqField' id='dname' /></div><div class='col'><input type='number' class='form-control' id='dage' /></div><div class='col'><select class='form-control' id = 'dgender' name='Gender' required><option value=''>Select</option><option>Male</option><option>Female</option><option>Others</option></select></div><div class='col'><input type='text' class='form-control' id='drelationship' /></div><a class='deleteDependents depicons mr-2'><img src='/images/wrong.png' /></a><a class='submitDependent depicons' id = 'dependentsubmit'><img src='/images/tick.png' /></a></div></form>");
    })

    $(function() {
    $('.dependents').on('click', ' a.deleteDependents', function() {
      $(this).parent().remove();
    })
    })

    $('#generalinfo .editProfiles').click(function() {

        $(' #generalinfo p').hide();
        $(' #generalinfo input,#generalinfo textarea, #generalinfo .cancelprofiles').css('display', 'inline-block');
        $("#get_general_details").show();
        $(this).hide();
    })

    $('#generalinfo .cancelprofiles').click(function() {

        $('#generalinfo, .editProfiles').show();
        $(' #generalinfo input,#generalinfo textarea').hide();
        $(' #generalinfo p').css('display', 'block');
        $("#get_general_details").hide();
        $(this).hide();
    })


    $('#familyhistory .editProfiles').click(function() {

        $(' #familyhistory span').hide();
        $(' #familyhistory p').hide();
        $(' .checkboxx, #familyhistory .cancelprofiles').css('display', 'inline-block');
        $("#get_family_history").show();
        $(this).hide();
    })

    $('#familyhistory .cancelprofiles').click(function() {

        $('#familyhistory span, #familyhistory .editProfiles').show();
        $(' #familyhistory p').show();
        $('.checkboxx').hide();
        $("#get_family_history").hide();
        $(this).hide();
    })

    $('#physicalexam .editProfiles').click(function() {

        $(' #physicalexam span').hide();
        $(' #physicalexam input, #physicalexam .cancelprofiles').css('display', 'inline-block');
        $("#get_physical_exam").show();
        $(this).hide();
    })

    $('#physicalexam .cancelprofiles').click(function() {

        $('#physicalexam span, #physicalexam .editProfiles').show();
        $('#physicalexam input').hide();
        $("#get_physical_exam").hide();
        $(this).hide();
    })

    $('#investigation .editProfiles').click(function() {

        $(' #investigation span').hide();
        $(' #investigation input, #investigation .cancelprofiles').css('display', 'inline-block');
        $("#get_investigation").show();
        $(this).hide();
    })

    $('#investigation .cancelprofiles').click(function() {

        $('#investigation span, #investigation .editProfiles').show();
        $('#investigation input').hide();
        $("#get_investigation").hide();
        $(this).hide();
    })


    $(function() {
    $('.dependents').on('click', 'a.submitDependent', function(e) {
      e.preventDefault();
      var isValid = true;
     var name = $('#dname').val();
     var age = $('#dage').val();
     var gender = $('#dgender').val();
     var pid = $('#pid').val();
     var relationship = $('#drelationship').val();

     if ($("#dname").hasClass("reqField")) {
        if ($("#dname").val().trim() == "") {
            $("#dname").addClass("input-error");
            var isValid = false;
        }
      }
 if (isValid == true) {
     $.ajax({
            url: '/insertfamily',
            type: "POST",
            data:{
                "_token": "{{ csrf_token() }}",

                 "Name":name,
                 "Age":age,
                 "gender":gender,
                 "relationship":relationship,
                 "pid":pid,
                },
                beforeSend: function (xhs) {
                        window.loadingScreen("show");
                  },
            success: function(data){
              window.loadingScreen("hide");
              $("#get_prob_details").submit();
            }
        })

        $("#get_prob_details").trigger("click");
        $('#dname').val('');
        $('#dage').val('');
        $('#dgender').val('');
        $('#drelationship').val('');
    }
    })
    })


    $('#get_prob_details').submit(function(e) {
      e.preventDefault();
        $('#d_table tbody tr').remove();
        $('#prescription tbody tr').remove();
        $('.tab-content').find('input:text').val('');
        $('.tab-content').find('p').text('');
        $('.tab-content').find('span').text('');
        $('input[type=checkbox]').removeClass("active");
        $('#PastHistory').val('');

        var id = $('#prob_id').val();

        $.ajax({
            url: '/getprobdata',
            type: "POST",
            data:{
                "_token": "{{ csrf_token() }}",
                 "id":id},
                beforeSend: function (xhs) {
                 //       window.loadingScreen("show");
                  },
            success: function(data){
              window.loadingScreen("hide");
              if(data == '')
              {
                $('#error').empty();
                var e = $('<div class="alert alert-danger"><p>Please Enter valid Probationer RollNumber</p></div>');
                $('#error').append(e);
                $("div.alert").fadeTo(2000, 500).slideUp(500, function(){
                $("div.alert").slideUp(500);
                });
              }
              else{

           var i = 0;
                $.each(data['prob_details'], function() {
                  var url = "{{ url ('/healthprofiles/:id' )}}";
                  url = url.replace(':id', data['prob_details'][i].id);
                  var depenedent = (data['prob_details'][i].id);

                    $("#pid").val(data['prob_details'][i].pids);
                    $("#pid1").val(data['prob_details'][i].pids);
                    $("#pid2").val(data['prob_details'][i].pids);
                    $("#pid3").val(data['prob_details'][i].pids);
                    $("#pid4").val(data['prob_details'][i].pids);
                    $("#pid5").val(data['prob_details'][i].pids);
                    $("#prob_name").text(data['prob_details'][i].Name);
                   $("#prob_dob").text(data['prob_details'][i].SquadNumber);
                    $("#prob_gender").text(data['prob_details'][i].MobileNumber);
                    if(data['prob_details'][i].profile_url != null)
                    {
                        $("#profile_images").attr('src' , "data:image/png;base64,"+data['prob_details'][i].profile_url);
                    }
                    else
                    {
                        $("#profile_images").attr('src' ,"/images/profileimage.png");
                    }

                    // $('#prob_basic_details tbody').append('<tr><td>'+ data['prob_details'][i].Name +'</td><td>'+ data['prob_details'][i].Dob +'</td><td>'+ data['prob_details'][i].gender +'</td></tr>')
                    if(data['prob_details'][i].DependentName != null){
                      $('#d_table tbody').append('<tr><td>' + data['prob_details'][i].DependentName + '</td><td>' + data['prob_details'][i].DependentAge + '</td><td>' + data['prob_details'][i].DependentGender + '</td><td>' + data['prob_details'][i].DependentRelationship + '</td><td><a onclick="deletedependent('+ data['prob_details'][i].id +');"><img src="/images/trash.png" /><span></span></a></td></tr>');
                    }
                    i++;
                });
                $('input[name="Height"]').val(data['prob_all_details'][0].Height);
                $('#pHeight').text(data['prob_all_details'][0].Height);
                $('input[name="Weight"]').val(data['prob_all_details'][0].Weight);
                $('#pWeight').text(data['prob_all_details'][0].Weight);
                $('input[name="Expi"]').val(data['prob_all_details'][0].Expi);
                $('#pExpi').text(data['prob_all_details'][0].Expi);
                $('input[name="Ins"]').val(data['prob_all_details'][0].Ins);
                $('#pIns').text(data['prob_all_details'][0].Ins);
                $('input[name="Expansion"]').val(data['prob_all_details'][0].Expansion);
                $('#pExpansion').text(data['prob_all_details'][0].Expansion);
                $('#PastHistory').val(data['prob_all_details'][0].PastHistory);
                $('#pPastHistory').text(data['prob_all_details'][0].PastHistory);
                $('input[name="bloodpressure"]').val(data['prob_all_details'][0].Bloodpressure);
                $('#pbloodpressure').text(data['prob_all_details'][0].Bloodpressure);
                $('input[name="pulse"]').val(data['prob_all_details'][0].Pulse);
                $('#ppulse').text(data['prob_all_details'][0].Pulse);
                $('input[name="ent"]').val(data['prob_all_details'][0].Ent);
                $('#pent').text(data['prob_all_details'][0].Ent);
                $('input[name="dentalexamination"]').val(data['prob_all_details'][0].Dental);
                $('#pdentalexamination').text(data['prob_all_details'][0].Dental);
                $('input[name="heart"]').val(data['prob_all_details'][0].Heart);
                $('#pheart').text(data['prob_all_details'][0].Heart);
                $('input[name="lungs"]').val(data['prob_all_details'][0].Lungs);
                $('#plungs').text(data['prob_all_details'][0].Lungs);
                $('input[name="abodmen"]').val(data['prob_all_details'][0].Abdomen);
                $('#pabodmen').text(data['prob_all_details'][0].Abdomen);
                $('input[name="lefteye"]').val(data['prob_all_details'][0].Eyewithleft);
                $('#plefteye').text(data['prob_all_details'][0].Eyewithleft);
                $('input[name="righteye"]').val(data['prob_all_details'][0].Eyewithright);
                $('#rrighteye').text(data['prob_all_details'][0].Eyewithright);
                $('input[name="leftwithoutglasseseye"]').val(data['prob_all_details'][0].Eyewithoutleft);
                $('#pleftwithoutglasseseye').text(data['prob_all_details'][0].Eyewithoutleft);
                $('input[name="rightwithoutglasseseye"]').val(data['prob_all_details'][0].Eyewithoutright);
                $('#prightwithoutglasseseye').text(data['prob_all_details'][0].Eyewithoutright);
                $('input[name="urological"]').val(data['prob_all_details'][0].Urological);
                $('#purological').text(data['prob_all_details'][0].Urological);
                $('input[name="athlete"]').val(data['prob_all_details'][0].Athlete);
                $('#pathlete').text(data['prob_all_details'][0].Athlete);
                $('input[name="defectdeformity"]').val(data['prob_all_details'][0].Defectordeformity);
                $('#pdefectdeformity').text(data['prob_all_details'][0].Defectordeformity);
                $('input[name="anyscarsoperation"]').val(data['prob_all_details'][0].Scarsoperation);
                $('#panyscarsoperation').text(data['prob_all_details'][0].Scarsoperation);
                $('input[name="urine"]').val(data['prob_all_details'][0].Urine);
                $('#purine').text(data['prob_all_details'][0].Urine);
                $('input[name="bloodgroup"]').val(data['prob_all_details'][0].Bloodgroup);
                $('#pbloodgroup').text(data['prob_all_details'][0].Bloodgroup);
                $('input[name="rhfactor"]').val(data['prob_all_details'][0].Rhfactor);
                $('#prhfactor').text(data['prob_all_details'][0].Rhfactor);
                $('input[name="xraytesting"]').val(data['prob_all_details'][0].Xray);
                $('#pxraytesting').text(data['prob_all_details'][0].Xray);
                $('input[name="tetanus1"]').val(data['prob_all_details'][0].Tetanus1);
                $('#ptetanus1').text(data['prob_all_details'][0].Tetanus1);
                $('input[name="tetanus2"]').val(data['prob_all_details'][0].Tetanus2);
                $('#ptetanus2').text(data['prob_all_details'][0].Tetanus2);
                $('input[name="tetanus3"]').val(data['prob_all_details'][0].Tetanus3);
                $('#ptetanus3').text(data['prob_all_details'][0].Tetanus3);

                if( data['prob_all_details'][0].Diabetes === 1 ) {$('#p_cbxyes1').text('Yes');}
                else if( data['prob_all_details'][0].Diabetes === 0 ){$('#p_cbxyes1').text('No');}

                if( data['prob_all_details'][0].Diabetes === 1 ) {$('#p_cbxyes1').text('Yes');$('#cbxyes1').prop('checked', true);$('#cbxyes1').attr('class', 'customcheckbox active');}else if( data['prob_all_details'][0].Diabetes === 0 ){$('#p_cbxyes1').text('No');}
              if( data['prob_all_details'][0].HeartDiseases === 1 ) {$('#p_cbxyes2').text('Yes');$('#cbxyes2').prop('checked', true);$('#cbxyes2').attr('class', 'customcheckbox active');}else if( data['prob_all_details'][0].HeartDiseases === 0 ){$('#p_cbxyes2').text('No');}
              if( data['prob_all_details'][0].Migrane === 1 ) {$('#p_cbxyes3').text('Yes');$('#cbxyes3').prop('checked', true);$('#cbxyes3').attr('class', 'customcheckbox active');}else if( data['prob_all_details'][0].Migrane === 0 ){$('#p_cbxyes3').text('No');}
              if( data['prob_all_details'][0].Epilepsy === 1 ) {$('#p_cbxyes4').text('Yes');$('#cbxyes4').prop('checked', true);$('#cbxyes4').attr('class', 'customcheckbox active');}else if( data['prob_all_details'][0].Epilepsy === 0 ){$('#p_cbxyes4').text('No');}
              if( data['prob_all_details'][0].Allergy === 1 ) {$('#p_cbxyes5').text('Yes');$('#cbxyes5').prop('checked', true);$('#cbxyes5').attr('class', 'customcheckbox active');}else if( data['prob_all_details'][0].Allergy === 0 ){$('#p_cbxyes5').text('No');}
              if( data['prob_all_details'][0].Smoking === 1 ) {$('#p_cbxp1').text('Yes');$('#cbxp1').prop('checked', true); $('#cbxp1').attr('class', 'customcheckbox active');}else if( data['prob_all_details'][0].Smoking === 0 ){$('#p_cbxp1').text('No');}
              if( data['prob_all_details'][0].Alchohol === 1 ) {$('#p_cbxp2').text('Yes');$('#cbxp2').prop('checked', true);$('#cbxp2').attr('class', 'customcheckbox active');}else if( data['prob_all_details'][0].Alchohol === 0 ){$('#p_cbxp2').text('No');}
              if( data['prob_all_details'][0].Veg === 1 ) {$('#p_cbxp3').text('Yes');$('#cbxp3').prop('checked', true);$('#cbxp3').attr('class', 'customcheckbox active');}else if( data['prob_all_details'][0].Veg === 0 ){$('#p_cbxp3').text('No');}
              if( data['prob_all_details'][0].NonVeg === 1 ) {$('#p_cbxp4').text('Yes');$('#cbxp4').prop('checked', true);$('#cbxp4').attr('class', 'customcheckbox active');}else if( data['prob_all_details'][0].NonVeg === 0 ){$('#p_cbxp4').text('No');}


              var j = 0;
              $.each(data['prescriptions'], function() {
                var route = "{{ route('userprescription', ':id') }}";
                route = route.replace(':id', data['prescriptions'][j].id);
                var k = j + 1;
                $('#prescription tbody').append('<tr style="text-align: center;"><td>' + k + '</td><td>' + data['prescriptions'][j].date + '</td><td>' + data['prescriptions'][j].doctor_name + '</td><td>'+ "SVPNPA Hospital" +'</td><td><div><a href='+ route +' target="_blank" data-toggle="tooltip" title="download"><img class="prescription-view-img" src="{{ asset('images/download1.png') }}" /></a></div></td></tr>');
                j++;
              });


              var m = 0;
              var p = j;
              $.each(data['outdoor_prescriptions'], function() {
                var a =  moment(data['outdoor_prescriptions'][m].created_at).format("YYYY-MM-DD")
                var url = "{{ url ('/downloads/:id')}}";
                url = url.replace(':id', data['outdoor_prescriptions'][m].FileDirectory);
                 var n = p + 1;
                $('#prescription tbody').append('<tr style="text-align: center;"><td>' + n + '</td><td>' + a + '</td><td>' + data['outdoor_prescriptions'][m].doctor_name + '</td><td>'+ data['outdoor_prescriptions'][m].h_name +'</td><td><div><a href='+ url +' target="_blank" data-toggle="tooltip" title="download"><img class="prescription-view-img" src="{{ asset('images/download1.png') }}" /></a></div></td></tr>');
                m++;
              });


            }
          }
        })
    });

    function getdata()
    {
      var inputs = $('#generalInfoSubmit').serializeArray();
      $.ajax({
            url: '/inserthealthprofiles',
            type: "POST",
            data:{
                "_token": "{{ csrf_token() }}",
                 "data":inputs,
                },
            success: function(data){
              $('input[name="Height"]').val(data[0].Height);
                $('#pHeight').text(data[0].Height);
                $('input[name="Weight"]').val(data[0].Weight);
                $('#pWeight').text(data[0].Weight);
                $('input[name="Expi"]').val(data[0].Expi);
                $('#pExpi').text(data[0].Expi);
                $('input[name="Ins"]').val(data[0].Ins);
                $('#pIns').text(data[0].Ins);
                $('input[name="Expansion"]').val(data[0].Expansion);
                $('#pExpansion').text(data[0].Expansion);
                $('#PastHistory').val(data[0].PastHistory);
                $('#pPastHistory').text(data[0].PastHistory);
                $('#generalinfo .cancelprofiles').trigger("click")
            }

        })
    }
    function deletedependent(id)
    {
      var depenedent_id = id;
        $.ajax({
            url: '/delete_dependent',
            type: "POST",
            data:{
                "_token": "{{ csrf_token() }}",
                 "id":depenedent_id,
                },
            success: function(data){
                var rData = data.replace( /[\r\n]+/gm, "" );
            if(rData == '1')
            {
                $("#get_prob_details").submit();
                $('#error').empty();
                var e = $('<div class="alert alert-danger"><p>Dependent deleted successfully</p></div>');
                $('#error').append(e);
                $("div.alert").fadeTo(2000, 500).slideUp(500, function(){
                $("div.alert").slideUp(500);
            });
            }
            else
            {

            }

            }
        })

    }

    function getdata2()
    {
    var diabetes = $('#cbxyes1').is(":checked");
    var heartdiseases = $('#cbxyes2').is(":checked");
    var migrane = $('#cbxyes3').is(":checked");
    var epilepsy = $('#cbxyes4').is(":checked");
    var allergy = $('#cbxyes5').is(":checked");
    var smoking = $('#cbxp1').is(":checked");
    var alchocol = $('#cbxp2').is(":checked");
    var veg = $('#cbxp3').is(":checked");
    var nonveg = $('#cbxp4').is(":checked");
    var pid = $('#pid4').val();
    var inputs = $('#familyInfoSubmit').serializeArray();
      $.ajax({
            url: '/insertfamilyhistory',
            type: "POST",
            data:{
                "_token": "{{ csrf_token() }}",
                 "diabetes":diabetes,
                 "heartdiseases":heartdiseases,
                 "migrane":migrane,
                 "epilepsy":epilepsy,
                 "allergy":allergy,
                 "smoking":smoking,
                 "alchocol":alchocol,
                 "veg":veg,
                 "nonveg":nonveg,
                 "pid":pid,
                 "data":inputs,
                },
            success: function(data){
              if( data[0].Diabetes === 1 ) {$('#p_cbxyes1').text('Yes');}else if( data[0].Diabetes === 0 ){$('#p_cbxyes1').text('No');}
              if( data[0].HeartDiseases === 1 ) {$('#p_cbxyes2').text('Yes');}else if( data[0].HeartDiseases === 0 ){$('#p_cbxyes2').text('No');}
              if( data[0].Migrane === 1 ) {$('#p_cbxyes3').text('Yes');}else if( data[0].Migrane === 0 ){$('#p_cbxyes3').text('No');}
              if( data[0].Epilepsy === 1 ) {$('#p_cbxyes4').text('Yes');}else if( data[0].Epilepsy === 0 ){$('#p_cbxyes4').text('No');}
              if( data[0].Allergy === 1 ) {$('#p_cbxyes5').text('Yes');}else if( data[0].Allergy === 0 ){$('#p_cbxyes5').text('No');}
              if( data[0].Smoking === 1 ) {$('#p_cbxp1').text('Yes');}else if( data[0].Smoking === 0 ){$('#p_cbxp1').text('No');}
              if( data[0].Alchohol === 1 ) {$('#p_cbxp2').text('Yes');}else if( data[0].Alchohol === 0 ){$('#p_cbxp2').text('No');}
              if( data[0].Veg === 1 ) {$('#p_cbxp3').text('Yes');}else if( data[0].Veg === 0 ){$('#p_cbxp3').text('No');}
              if( data[0].NonVeg === 1 ) {$('#p_cbxp4').text('Yes');}else if( data[0].NonVeg === 0 ){$('#p_cbxp4').text('No');}
              $('#familyhistory .cancelprofiles').trigger("click")
            }
        })
    }

    function getdata3()
    {
      var inputs = $('#physicalInfoSubmit').serializeArray();
      $.ajax({
            url: '/inserthealthprofiles',
            type: "POST",
            data:{
                "_token": "{{ csrf_token() }}",
                 "data":inputs,
                },
            success: function(data){
              $('input[name="bloodpressure"]').val(data[0].Bloodpressure);
                $('#pbloodpressure').text(data[0].Bloodpressure);
                $('input[name="pulse"]').val(data[0].Pulse);
                $('#ppulse').text(data[0].Pulse);
                $('input[name="ent"]').val(data[0].Ent);
                $('#pent').text(data[0].Ent);
                $('input[name="dentalexamination"]').val(data[0].Dental);
                $('#pdentalexamination').text(data[0].Dental);
                $('input[name="heart"]').val(data[0].Heart);
                $('#pheart').text(data[0].Heart);
                $('input[name="lungs"]').val(data[0].Lungs);
                $('#plungs').text(data[0].Lungs);
                $('input[name="abodmen"]').val(data[0].Abdomen);
                $('#pabodmen').text(data[0].Abdomen);
                $('input[name="lefteye"]').text(data[0].Eyewithleft);
                $('#plefteye').text(data[0].Eyewithleft);
                $('input[name="righteye"]').val(data[0].Eyewithright);
                $('#rrighteye').text(data[0].Eyewithright);
                $('input[name="leftwithoutglasseseye"]').val(data[0].Eyewithoutleft);
                $('#pleftwithoutglasseseye').text(data[0].Eyewithoutleft);
                $('input[name="rightwithoutglasseseye"]').val(data[0].Eyewithoutright);
                $('#prightwithoutglasseseye').text(data[0].Eyewithoutright);
                $('input[name="urological"]').val(data[0].Urological);
                $('#purological').text(data[0].Urological);
                $('input[name="athlete"]').val(data[0].Athlete);
                $('#pathlete').text(data[0].Athlete);
                $('input[name="defectdeformity"]').val(data[0].Defectordeformity);
                $('#pdefectdeformity').text(data[0].Defectordeformity);
                $('input[name="anyscarsoperation"]').val(data[0].Scarsoperation);
                $('#panyscarsoperation').text(data[0].Scarsoperation);
                $('#physicalexam .cancelprofiles').trigger("click")
            }
        })
    }
    function getdata4()
    {
      var inputs = $('#investigationInfoSubmit').serializeArray();
      $.ajax({
            url: '/inserthealthprofiles',
            type: "POST",
            data:{
                "_token": "{{ csrf_token() }}",
                 "data":inputs,
                },
            success: function(data){
              $('input[name="urine"]').val(data[0].Urine);
                $('#purine').text(data[0].Urine);
                $('input[name="bloodgroup"]').val(data[0].Bloodgroup);
                $('#pbloodgroup').text(data[0].Bloodgroup);
                $('input[name="rhfactor"]').val(data[0].Rhfactor);
                $('#prhfactor').text(data[0].Rhfactor);
                $('input[name="xraytesting"]').val(data[0].Xray);
                $('#pxraytesting').text(data[0].Xray);
                $('input[name="tetanus1"]').val(data[0].Tetanus1);
                $('#ptetanus1').text(data[0].Tetanus1);
                $('input[name="tetanus2"]').val(data[0].Tetanus2);
                $('#ptetanus2').text(data[0].Tetanus2);
                $('input[name="tetanus3"]').val(data[0].Tetanus3);
                $('#ptetanus3').text(data[0].Tetanus3);
                $('#investigation .cancelprofiles').trigger("click")
            }
        })
    }

    $('#get_prob_details').submit(function(e) {

    var validate =  $("#get_prob_details").validate({
            rules: {
                rollnumber: {
                required: true,
            }
            },
            messages: {
                rollnumber: {
                required: "Please enter RollNumber",
                }
            }
        }).form();
       if(validate == true)
           {
            var id = $('#prob_id').val();
          $.ajax({
            url: '/get_inpatients_data',
            type: "POST",
            data:{
                "_token": "{{ csrf_token() }}",
                 "id":id,
                },
                success: function(data){
                //console.log(data);
                if(data == '')
                {
                    $("#dischargesummary thead").empty();
                    $("#dischargesummary tbody").empty();
                    $('#dischargesummary tbody').append('<tr style="text-align: center;"><td>No Discharge Summary Found for this Probationer</td></tr>');
                }
                else {
                   var x=1;debugger;
                    $("#dischargesummary thead").empty();
                    $("#dischargesummary tbody").empty();
                    $('#dischargesummary thead').append('<tr style="text-align: center;"><th>S.NO</th><th>Doctor Name</th><th>Date of joining</th><th>Discharge Date</th><th></th></tr>');
                    $.each(data, function(i) {
                        var url = "{{ url ('/discharge_summary/:id')}}";
                        url = url.replace(':id', data[i].in_pat_id);
                        $('#dischargesummary tbody').append('<tr style="text-align: center;"><td>'+ x++ +'</td><td>' + data[i].name + '</td><td>' + data[i].admitted_date + '</td><td>' + data[i].discharge_date + '</td><td><a href="' + url + '" ><img class="discharge-summary-view-img" src="{{ asset('images/download1.png') }}" /><span></span></a></td></tr>');
                    });
                }
            }
        })
        }
});



$('#get_prob_details').submit(function(e) {

var validate =  $("#get_prob_details").validate({
        rules: {
            rollnumber: {
            required: true,
        }
        },
        messages: {
            rollnumber: {
            required: "Please enter RollNumber",
            }
        }
    }).form();
   if(validate == true)
       {
        var id = $('#prob_id').val();
      $.ajax({
        url: '/get_patient_history',
        type: "POST",
        data:{
            "_token": "{{ csrf_token() }}",
             "id":id,
            },
            success: function(data){
            //console.log(data);
            if(data == '')
            {
                $("#viewhistory thead").empty();
                $("#viewhistory tbody").empty();
                $('#viewhistory tbody').append('<tr style="text-align: center;"><td>No Records Found for this Probationer</td></tr>');
            }
            else {
               var x=1;
                $("#viewhistory thead").empty();
                $("#viewhistory tbody").empty();
                $('#viewhistory thead').append('<tr style="text-align: center;"><th>S.NO</th><th>Doctor Name</th><th>Date of joining</th><th>Discharge Date</th><th>No of Days</th></tr>');
                $.each(data, function(i) {
                    var url = "{{ url ('/discharge_summary/:id')}}";
                    url = url.replace(':id', data[i].in_pat_id);

                    // var date_start =  moment(data[i].admitted_date).format("DD/MM/YYYY")
                    // var date_end =  moment(data[i].discharge_date).format("DD/MM/YYYY")

                    //var count = daysdifference(date_start, date_end);
                  // var count =  date_start.getTime() - date_end.getTime();

                    $('#viewhistory tbody').append('<tr style="text-align: center;"><td>'+ x++ +'</td><td>' + data[i].name + '</td><td>' + data[i].admitted_date + '</td><td>' + data[i].discharge_date + '</td><td>' + data[i].count + '</td></tr>');
                });
            }
        }
    })
    }
});



$('#get_prob_details').submit(function(e) {debugger;
    var validate =  $("#get_prob_details").validate({
            rules: {
                rollnumber: {
                required: true,
            }
            },
            messages: {
                rollnumber: {
                required: "Please enter RollNumber",
                }
            }
        }).form();
       if(validate == true)
           {debugger;
            var id = $('#prob_id').val();
          $.ajax({
            url: '/get_patient_report',
            type: "POST",
            data:{
                "_token": "{{ csrf_token() }}",
                 "id":id,
                },
                success: function(data){debugger;
                //console.log(data);
                if(data == '')
                {
                    $("#reports thead").empty();
                    $("#reports tbody").empty();
                    $('#reports tbody').append('<tr style="text-align: center;"><td>No Reports Found for this Probationer</td></tr>');
                }
                else {debugger;
                   var x=1;
                    $("#reports thead").empty();
                    $("#reports tbody").empty();
                    $('#reports thead').append('<tr style="text-align: center;"><th>S.NO</th><th>Date</th><th>Test Name</th>< Date</th><th></th></tr>');
                    $.each(data, function(i) {debugger;

                    var a =  moment(data[i].created_at).format("DD/MM/YYYY")
                        var url = "{{ url ('/downloads/:id')}}";
                        url = url.replace(':id', data[i].FileDirectory);
                        $('#reports tbody').append('<tr style="text-align: center;"><td>'+ x++ +'</td><td>' + a + '</td><td>' + data[i].ReportName + '</td><td>' + '</td><td><a href="' + url + '" ><img class="prescriptions-view-img" src="{{ asset('images/download1.png') }}" /><span></span></a></td></tr>');
                    });
                }
            }
        })
        }
    });


    </script>

@endsection

