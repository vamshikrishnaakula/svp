{{-- Extends layout --}}
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

    @endif

    <section id="healthprofile" class="content-wrapper_sub tab-content">
      <div class="user_manage">
        <div class="row">
          <div class="col-md-9">
            <h4>Health Profiles</h4>
          </div>
          <div class="col-md-3">
            <div class="useractionBtns">
                <a href="#" data-toggle="tooltip" title="import"> <img src="./images/import.png" /></a>
                <a href="#" data-toggle="tooltip" title="excel"> <img src="./images/excel.png" /></a>
                <a href="#" data-toggle="tooltip" title="download"> <img src="./images/download1.png" /></a>
                <a href="#" data-toggle="tooltip" title="print"> <img src="./images/print1.png" /></a>



            </div>
          </div>
          {{-- <div class=" userprofileimg col-md-2">
            <img src="./images/probationerprofile.png" class="rounded-circle" />
          </div> --}}
        </div>

        <form class="form-inline mt-5" style="justify-content: center">
          <div class="row">
                <label class="col-md-3">Roll No :</label>
                <input class="col-md-6 form-control" type="number" id="roll_no" name="roll_no">
                <div class="usersubmitBtns col-md-3">
                    <button class="btn formBtn submitBtn" id = "get_prob_details">Get Data</button>
                </div>
          </div>
        </form>

          <div class="healthprofiledetails p-5">
            <div class="row">
                <div class="col-md-3 text-center">
                    <img src="./images/probationerprofile.png" class="rounded-circle" width="100" />
                </div>
                <div class="col-md-9">
                    <table class="table table-bordered mb-0 h-100">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Date Of Birth</th>
                                <th>Gender</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Madhukar</td>
                                <td>01-01-2020</td>
                                <td>Male</td>
                            </tr>
                        </tbody>
                    </table>
                   <div>
                   </div>

                </div>

            </div>
        </div>
    </div>

        <div class="mt-4">
        <ul class="nav nav-tabs">
          <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#familyinfo">Family Info</a></li>
          <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#generalinfo">General Info</a></li>
          <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#familyhistory">Family History</a></li>
          <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#physicalexam">Physical Exam</a></li>
          <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#investigation">Investigation</a></li>
        </ul>

        <div class="tab-content">
          <div class="text-right mb-4">
            <img class="editProfiles" src="./images/edithealthprofile.png" />
            <img class="cancelprofiles" style="display: none" src="./images/wrong.png" />
        </div>
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
                  <tr>
                    <td><span>Madhukar</span>
                        <input type="text" class="form-control" />
                    </td>
                    <td><span>25</span>
                        <input type="text" class="form-control" />
                    </td>
                    <td><span>Male</span>
                        <input type="text" class="form-control" />
                    </td>
                    <td><span>Son</span>
                        <input type="text" class="form-control" />
                    </td>
                    <td> <img class="editdependent" src="{{ asset('images/edit.png') }}" />
                        <img class="tick" style="display: none" src="{{ asset('images/tick.png') }}" />
                        <img src="{{ asset('images/trash.png') }}" />
                    </td>
                </tr>
                <tr>
                    <td><span>Madhukar</span>
                        <input type="text" class="form-control" />
                    </td>
                    <td><span>25</span>
                        <input type="text" class="form-control" />
                    </td>
                    <td><span>Male</span>
                        <input type="text" class="form-control" />
                    </td>
                    <td><span>Son</span>
                        <input type="text" class="form-control" />
                    </td>
                    <td> <img class="editdependent" src="{{ asset('images/edit.png') }}" />
                        <img class="tick" style="display: none" src="{{ asset('images/tick.png') }}" />
                        <img src="{{ asset('images/trash.png') }}" />
                    </td>
                </tr>
                  </tbody>

                </table>

              </div>
            </div>
          </div>
          <div id="generalinfo" class="tab-pane fade">
            <form action="{{ route('healthprofile.store') }}" method="POST" id ="generalInfoSubmit" name ="generalInfoSubmit">
            @csrf

              <div class="row">
                <div class="col-md-6">
                  <div class="generalinfo">
                  <div class="form-group">
                    <label>Height(cms) :</label>
                    <p>5.7 inch</p>
                    <input type="text" class="form-control" id ="Height" name = "Height">
                    <input type='hidden' class='form-control' id='pid1' name = "pid_generalinfo" />
                  </div>
                  <div class="form-group">
                    <label>Weight( kgs ) :</label>
                    <p>74kg</p>
                    <input type="text" class="form-control" id ="Weight" name = "Weight">
                  </div>
                  <div class="form-group">
                    <label>Past History :</label>
                    <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap in</p>
                    <textarea class="form-control" id ="PastHistory" name = "PastHistory"></textarea>
                  </div>
                </div>
                </div>
                <div class="col-md-6">
                  <div class="readings">
                    <h6><b>Chest Reading</b></h6>

                    <div class="form-group">
                      <label >Expi(cms) :</label>
                      <p>57 cms</p>
                      <input type="text" class="form-control" id ="Expi" name = "Expi">
                    </div>
                    <div class="form-group">
                      <label >Ins(cms) :</label>
                      <p>57 cms</p>
                      <input type="text" class="form-control" id ="Ins" name = "Ins">
                    </div>
                    <div class="form-group">
                      <label >Expansion(cms) :</label>
                      <p>57 cms</p>
                      <input type="text" class="form-control" id ="Expansion" name = "Expansion">
                    </div>

                  </div>
                </div>
              </div>
              <div class="usersubmitBtns mt-3">
                <div class="mr-4">
                  <button type="submit" class="btn formBtn submitBtn">Submit</button>
                </div>
              </div>
            </form>
          </div>
          <div id="familyhistory" class="tab-pane fade">
            <form action="{{ route('healthprofile.store') }}" method="POST">
              <div class="row familyhistory">
                <div class="col-md-7">
                <h6><b>Family History</b></h6>
                <div class="row">
                  <div class="form-group col-md-12 diabetes">
                    <label>Diabetes :</label>
                    <span>No</span>
                    <div class="checkboxx">
                    <input type="checkbox" class="customcheckbox" id="cbxyes1" style="display: none;" name ="Diabetes">
                    <label for="cbxyes1" class="check">
                      <svg width="18px" height="18px" viewBox="0 0 18 18">
                        <path d="M1,9 L1,3.5 C1,2 2,1 3.5,1 L14.5,1 C16,1 17,2 17,3.5 L17,14.5 C17,16 16,17 14.5,17 L3.5,17 C2,17 1,16 1,14.5 L1,9 Z"></path>
                        <polyline points="1 9 7 14 15 4"></polyline>
                      </svg>
                      Yes
                    </label>

                    <input type="checkbox" id="cbxno1" class="customcheckbox" style="display: none;" name ="Diabetes">
                    <label for="cbxno1" class="check">
                      <svg width="18px" height="18px" viewBox="0 0 18 18">
                        <path d="M1,9 L1,3.5 C1,2 2,1 3.5,1 L14.5,1 C16,1 17,2 17,3.5 L17,14.5 C17,16 16,17 14.5,17 L3.5,17 C2,17 1,16 1,14.5 L1,9 Z"></path>
                        <polyline points="1 9 7 14 15 4"></polyline>
                      </svg>
                      No
                    </label>
                </div>
                  </div>
                </div>
                <div class="row">
                  <div class="form-group col-md-12 heartdiseases">
                    <label>Heart Diseases :</label>
                    <span>No</span>
                    <div class="checkboxx">
                    <input type="checkbox" id="cbxyes2" class="customcheckbox" style="display: none;" name ="heartdiseases">
                    <label for="cbxyes2" class="check">
                      <svg width="18px" height="18px" viewBox="0 0 18 18">
                        <path d="M1,9 L1,3.5 C1,2 2,1 3.5,1 L14.5,1 C16,1 17,2 17,3.5 L17,14.5 C17,16 16,17 14.5,17 L3.5,17 C2,17 1,16 1,14.5 L1,9 Z"></path>
                        <polyline points="1 9 7 14 15 4"></polyline>
                      </svg>
                      Yes
                    </label>
                    <input type="checkbox" id="cbxno2" class="customcheckbox" style="display: none;" name ="heartdiseases">
                    <label for="cbxno2" class="check">
                      <svg width="18px" height="18px" viewBox="0 0 18 18">
                        <path d="M1,9 L1,3.5 C1,2 2,1 3.5,1 L14.5,1 C16,1 17,2 17,3.5 L17,14.5 C17,16 16,17 14.5,17 L3.5,17 C2,17 1,16 1,14.5 L1,9 Z"></path>
                        <polyline points="1 9 7 14 15 4"></polyline>
                      </svg>
                      No
                    </label>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="form-group col-md-12">
                    <label>Migraine :</label>
                    <span>No</span>
                    <div class="checkboxx">
                    <input type="checkbox" id="cbxyes3" class="customcheckbox" style="display: none;"  name ="migrane">
                    <label for="cbxyes3" class="check">
                      <svg width="18px" height="18px" viewBox="0 0 18 18">
                        <path d="M1,9 L1,3.5 C1,2 2,1 3.5,1 L14.5,1 C16,1 17,2 17,3.5 L17,14.5 C17,16 16,17 14.5,17 L3.5,17 C2,17 1,16 1,14.5 L1,9 Z"></path>
                        <polyline points="1 9 7 14 15 4"></polyline>
                      </svg>
                      Yes
                    </label>
                    <input type="checkbox" id="cbxno3" class="customcheckbox" style="display: none;" name ="migrane">
                    <label for="cbxno3" class="check">
                      <svg width="18px" height="18px" viewBox="0 0 18 18">
                        <path d="M1,9 L1,3.5 C1,2 2,1 3.5,1 L14.5,1 C16,1 17,2 17,3.5 L17,14.5 C17,16 16,17 14.5,17 L3.5,17 C2,17 1,16 1,14.5 L1,9 Z"></path>
                        <polyline points="1 9 7 14 15 4"></polyline>
                      </svg>
                      No
                    </label>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="form-group col-md-12">
                    <label>Epilepsy :</label>
                    <span>No</span>
                    <div class="checkboxx">
                    <input type="checkbox" id="cbxyes4" class="customcheckbox" style="display: none;" name ="epilepsy">
                    <label for="cbxyes4" class="check">
                      <svg width="18px" height="18px" viewBox="0 0 18 18">
                        <path d="M1,9 L1,3.5 C1,2 2,1 3.5,1 L14.5,1 C16,1 17,2 17,3.5 L17,14.5 C17,16 16,17 14.5,17 L3.5,17 C2,17 1,16 1,14.5 L1,9 Z"></path>
                        <polyline points="1 9 7 14 15 4"></polyline>
                      </svg>
                      Yes
                    </label>
                    <input type="checkbox" id="cbxno4" class="customcheckbox" style="display: none;" name ="epilepsy">
                    <label for="cbxno4" class="check">
                      <svg width="18px" height="18px" viewBox="0 0 18 18">
                        <path d="M1,9 L1,3.5 C1,2 2,1 3.5,1 L14.5,1 C16,1 17,2 17,3.5 L17,14.5 C17,16 16,17 14.5,17 L3.5,17 C2,17 1,16 1,14.5 L1,9 Z"></path>
                        <polyline points="1 9 7 14 15 4"></polyline>
                      </svg>
                      No
                    </label>
                </div>
                  </div>
                </div>
                <div class="row">
                  <div class="form-group col-md-12">
                    <label>Allergy :</label>
                    <span>No</span>
                    <div class="checkboxx">
                    <input type="checkbox" id="cbxyes5" class="customcheckbox" style="display: none;" name ="allergy">
                    <label for="cbxyes5" class="check">
                      <svg width="18px" height="18px" viewBox="0 0 18 18">
                        <path d="M1,9 L1,3.5 C1,2 2,1 3.5,1 L14.5,1 C16,1 17,2 17,3.5 L17,14.5 C17,16 16,17 14.5,17 L3.5,17 C2,17 1,16 1,14.5 L1,9 Z"></path>
                        <polyline points="1 9 7 14 15 4"></polyline>
                      </svg>
                      Yes
                    </label>
                    <input type="checkbox" id="cbxno5" class="customcheckbox" style="display: none;" name ="allergy">
                    <label for="cbxno5" class="check">
                      <svg width="18px" height="18px" viewBox="0 0 18 18">
                        <path d="M1,9 L1,3.5 C1,2 2,1 3.5,1 L14.5,1 C16,1 17,2 17,3.5 L17,14.5 C17,16 16,17 14.5,17 L3.5,17 C2,17 1,16 1,14.5 L1,9 Z"></path>
                        <polyline points="1 9 7 14 15 4"></polyline>
                      </svg>
                      No
                    </label>
                </div>
                  </div>
                </div>
              </div>

              <div class="col-md-5 personalhistory">
                <h6><b>Personal History</b></h6>
                    <div class="checkbox form-group">
                        <label>Smoking :</label>
                        <div class="checkboxx">
                      <input type="checkbox" id="cbxp1" class="customcheckbox" style="display: none;" name ="smoking">
                      <label for="cbxp1" class="check">
                        <svg width="18px" height="18px" viewBox="0 0 18 18">
                          <path d="M1,9 L1,3.5 C1,2 2,1 3.5,1 L14.5,1 C16,1 17,2 17,3.5 L17,14.5 C17,16 16,17 14.5,17 L3.5,17 C2,17 1,16 1,14.5 L1,9 Z"></path>
                          <polyline points="1 9 7 14 15 4"></polyline>
                        </svg>
                      </label>
                        </div>

                        <span>No</span>
                    </div>
                    <div class="checkbox form-group">
                        <label>Alchohol :</label>
                        <span>No</span>
                        <div class="checkboxx">
                      <input type="checkbox" id="cbxp2" class="customcheckbox" style="display: none;" name ="alcohol">
                      <label for="cbxp2" class="check">
                        <svg width="18px" height="18px" viewBox="0 0 18 18">
                          <path d="M1,9 L1,3.5 C1,2 2,1 3.5,1 L14.5,1 C16,1 17,2 17,3.5 L17,14.5 C17,16 16,17 14.5,17 L3.5,17 C2,17 1,16 1,14.5 L1,9 Z"></path>
                          <polyline points="1 9 7 14 15 4"></polyline>
                        </svg>

                      </label>
                        </div>
                    </div>
                    <div class="checkbox form-group">
                        <label>Veg :</label>
                        <span>No</span>
                        <div class="checkboxx">
                      <input type="checkbox" id="cbxp3" class="customcheckbox" style="display: none;" name ="veg">
                      <label for="cbxp3" class="check">

                        <svg width="18px" height="18px" viewBox="0 0 18 18">
                          <path d="M1,9 L1,3.5 C1,2 2,1 3.5,1 L14.5,1 C16,1 17,2 17,3.5 L17,14.5 C17,16 16,17 14.5,17 L3.5,17 C2,17 1,16 1,14.5 L1,9 Z"></path>
                          <polyline points="1 9 7 14 15 4"></polyline>
                        </svg>

                      </label>
                        </div>
                    </div>


                    <div class="checkbox form-group">
                        <label>  Non-Veg :</label>
                        <span>No</span>
                        <div class="checkboxx">
                      <input type="checkbox" id="cbxp4" class="customcheckbox" style="display: none;" name ="Nonveg">
                      <label for="cbxp4" class="check">
                        <svg width="18px" height="18px" viewBox="0 0 18 18">
                          <path d="M1,9 L1,3.5 C1,2 2,1 3.5,1 L14.5,1 C16,1 17,2 17,3.5 L17,14.5 C17,16 16,17 14.5,17 L3.5,17 C2,17 1,16 1,14.5 L1,9 Z"></path>
                          <polyline points="1 9 7 14 15 4"></polyline>
                        </svg>
                      </label>
                        </div>
                    </div>
              </div>
              </div>
              <div class="usersubmitBtns mt-3">
                <div class="mr-4">
                  <button type="submit" class="btn formBtn submitBtn">Submit</button>
                </div>
              </div>
            </form>
          </div>
          <div id="physicalexam" class="tab-pane fade">
            <form class="physicalexamform" action="{{ route('healthprofile.store') }}" method="POST">
            @csrf
          <div class="row">
            <div class="col-md-5">
              <div class="form-group">
                <label >Blood Pressure :</label>
                <input type="text" class="form-control" name="bloodpressure">
                <span>120/80</span>
                <input type='hidden' class='form-control' id='pid2' name = "pid_physicalexam" />
              </div>
              <div class="form-group">
                <label >Pulse :</label>
                <span>96</span>
                <input type="text" class="form-control" name="pulse">
              </div>
              <div class="form-group">
                <label >ENT :</label>
                <span>fdsfsd</span>
                <input type="text" class="form-control" name="ent">
              </div>
              <div class="form-group">
                <label >Dental Examination :</label>
                <span>9fwerw</span>
                <input type="text" class="form-control" name="dentalexamination">
              </div>
              <div class="form-group">
                <label >Heart :</label>
                <span>9fwerw</span>
                <input type="text" class="form-control" name="heart">
              </div>
              <div class="form-group">
                <label >Lungs :</label>
                <span>9fwerw</span>
                <input type="text" class="form-control" name="lungs">
              </div>
              <div class="form-group">
                <label >Abdomen :</label>
                <span>9fwerw</span>
                <input type="text" class="form-control" name="abodmen">
              </div>
            </div>
            <div class="col-md-2"></div>
            <div class="col-md-5">
              <div class="eyesight mb-4">
              <h6><b>Eye Sight</b></h6>
              <h6><b>With Glasses</b></h6>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label >left :</label>
                    <span>9fwerw</span>
                    <input type="text" class="form-control" name="lefteye">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label >right :</label>
                    <span>9fwerw</span>
                    <input type="text" class="form-control" name="righteye">
                  </div>
                </div>
              </div>
              <h6><b>Without Glasses</b></h6>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label >left :</label>
                    <span>9fwerw</span>
                    <input type="text" class="form-control" name="leftwithoutglasseseye">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label >right :</label>
                    <span>9fwerw</span>
                    <input type="text" class="form-control" name="rightwithoutglasseseye">
                  </div>
                </div>
              </div>
              </div>
              <div class="form-group">
                <label >Urological System :</label>
                <span>9fwerw</span>
                <input type="text" class="form-control" name="urological">
              </div>
              <div class="form-group">
                <label >Athlete/Non Athlete :</label>
                <span>9fwerw</span>
                <input type="text" class="form-control" name="athlete">
              </div>
              <div class="form-group">
                <label >Any Defect or Deformity :</label>
                <span>9fwerw</span>
                <input type="text" class="form-control" name="defectdeformity">
              </div>
              <div class="form-group">
                <label >Any scars of operation :</label>
                <span>9fwerw</span>
                <input type="text" class="form-control" name="anyscarsoperation">
              </div>
            </div>
          </div>
          <div class="usersubmitBtns mt-3">
            <div class="mr-4">
              <button type="submit" class="btn formBtn submitBtn">Submit</button>
            </div>
          </div>
          </form>
          </div>
          <div id="investigation" class="tab-pane fade">
            <form action="{{ route('healthprofile.store') }}" method="POST">
            @csrf
              <div class="row">
                <div class="col-md-5">
                  <div class="form-group">
                    <label >Urine Examination :</label>
                    <input type="text" class="form-control" name="urine">
                    <input type='hidden' class='form-control' id='pid3' name = "pid_investigation" />
                    <span>bchjf</span>
                  </div>
                  <div class="form-group">
                    <label >Blood Group :</label>
                    <input type="text" class="form-control" name="bloodgroup">
                    <span>bchjf</span>
                  </div>
                  <div class="form-group">
                    <label >RH Factor :</label>
                    <input type="text" class="form-control" name="rhfactor">
                    <span>bchjf</span>
                  </div>
                  <div class="form-group">
                    <label >Xray Testing PA view :</label>
                    <input type="text" class="form-control" name="xraytesting">
                    <span>bchjf</span>
                  </div>
                </div>
                <div class="col-md-2"></div>
                <div class="col-md-5">
                  <h6><b>Immunization</b></h6>
                  <div class="form-group">
                    <label >Tetanus Oxide</label>
                    <div class="form-group">
                    <input type="text" class="form-control" name="tetanus">
                    </div>
                    <div class="form-group">
                    <input type="text" class="form-control" name="tetanus">
                    </div>
                    <div class="form-group">
                    <input type="text" class="form-control" name="tetanus">
                    </div>
                  </div>

                </div>
              </div>
              <div class="usersubmitBtns mt-3">
                <div class="mr-4">
                  <button type="submit" class="btn formBtn submitBtn">Submit</button>
                </div>
              </div>
            </form>
          </div>
        </div>

      </div>

    </section>
    @endsection

@section('scripts')

<script>
    //   $(function() {
    //   $('#familyhistory  input[type=checkbox]').click(function () {
    //     $(this).toggleClass('active');
    //     $(this).siblings().removeClass('active');
    //   })
    // })

    $('.addDependents').click(function() {
      $(".dependents").append("<form id = 'contactForm1'><div class='row'><div class='col'><label>Name :</label><input type='text' class='form-control' id='dname' /></div><div class='col'><label>Age :</label><input type='number' class='form-control' id='dage' /></div><div class='col'><label>Gender :</label><input type='text' class='form-control' id='dgender' /></div><div class='col'><label>Relationship :</label><input type='text' class='form-control' id='drelationship' /></div><a class='deleteDependents depicons mr-2'><img src='/images/wrong.png' /></a><a class='submitDependent depicons' id = 'dependentsubmit'><img src='/images/tick.png' /></a></div></form>");
    })

    $("#familyinfo table tbody td img.editdependent").click(function() {
        debugger
        $(this).hide();
        $(this).siblings('img.tick').show();
        $(this).parent().siblings().children('span').hide();
        $(this).parent().siblings().children('input').show();
    })

    $('.editProfiles').click(function() {
      debugger
        $('#familyinfo table tbody span, #generalinfo p, #familyhistory span, #physicalexam span, #investigation span').hide();
        $('#familyinfo table tbody input, #generalinfo input,#generalinfo textarea,#physicalexam input,#investigation input, .checkboxx, .cancelprofiles').css('display', 'inline-block');
        $(this).hide();
    })

    $('.cancelprofiles').click(function() {
      debugger
        $('#familyinfo table tbody span, #familyhistory span, #physicalexam span, #investigation span, .editProfiles').css('display', 'inline-block');
        $('#familyinfo table tbody input, #generalinfo input,#generalinfo textarea,#physicalexam input,#investigation input, .checkboxx').hide();
        $(' #generalinfo p').css('display', 'block');
        $(this).hide();
    })

    $(function() {
    $('.dependents').on('click', ' a.deleteDependents', function() {
      $(this).parent().remove();
    })
    })

    $(function() {
    $('.dependents').on('click', 'a.submitDependent', function() {
     // var dependentvalues = $(this).siblings().children('input').val()
     var name = $('#dname').val();
     var age = $('#dage').val();
     var gender = $('#dgender').val();
     var pid = $('#pid').val();
     var relationship = $('#drelationship').val();
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
            success: function(data){
              $('#d_table tbody').append('<tr><td>' + data[i].DependentName + '</td><td>' + data[i].DependentAge + '</td><td>' + data[i].DependentGender + '</td><td>' + data[i].DependentRelationship + '</td><td><a href='+ url +'><img src="/images/trash.png" /><span></span></a></td></tr>');
            }
        })

      $(this).parent().remove();
    })
    })


    $('#get_prob_details').click(function(e) {debugger
      e.preventDefault();
        $('#d_table tbody tr').remove();
        var id = $('#roll_no').val();
        $.ajax({
            url: '/getprobdata',
            type: "POST",
            data:{
                "_token": "{{ csrf_token() }}",
                 "id":id},
            success: function(data){debugger
            console.log(data);
                $.each(data, function(i) {
                  var url = "{{ url ('/healthprofiles/:id' )}}";
                  url = url.replace(':id', data[i].id);
                    $("#prob_name").text(data[i].Name);
                    $("#prob_dob").text(data[i].Dob);
                    $("#gender").text(data[i].gender);
                    $("#pid").val(data[i].pids);
                    $("#pid1").val(data[i].pids);
                    $("#pid2").val(data[i].pids);
                    $("#pid3").val(data[i].pids);
                    $('#d_table tbody').append('<tr><td>' + data[i].DependentName + '</td><td>' + data[i].DependentAge + '</td><td>' + data[i].DependentGender + '</td><td>' + data[i].DependentRelationship + '</td><td><a href='+ url +'><img src="/images/trash.png" /><span></span></a></td></tr>');
                });
            }
        })
    });

    // $('#get_general_details').click(function(e) {debugger
    //   e.preventDefault();
    //     var data = $("#generalInfoSubmit").serialize();
    //     var id = $('#pid').val();
    //     $.ajax({
    //         url: '/getprobdata',
    //         type: "POST",
    //         data:{
    //             "_token": "{{ csrf_token() }}",
    //              "id":id},
    //         success: function(data){debugger
    //                   }
    //     })
    // });



    </script>

@endsection

