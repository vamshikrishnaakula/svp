{{-- Extends layout --}}
@extends('layouts.default')

{{-- Content --}}
@section('content')

<section id="medicalrecords" class="content-wrapper_sub">
    <div class="user_manage">
        <div class="row">
          <div class="col-md-5">
            <h4>Medical Records</h4>
          </div>
        </div>

        <div class="mt-5">
        <ul class="nav nav-tabs">
            <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#Prescriptions">Prescriptions</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#Reports">Reports</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#Sickreport">Sick / Injury Report</a></li>
        </ul>
        <div class="tab-content mt-5">
            <div id="Prescriptions" class="tab-pane fade in active show">
                <div class="row">
                    <div class="col-md-12">
                        <div class="singleinput">
                        <label>Enter Patient ID :</label>
                        <input type="number" class="form-control" id="" name="">
                    </div>
                    </div>
                  </div>
                  <div class="usersubmitBtns mt-4">
                    <div class="mr-4">
                      <button class="btn formBtn submitBtn">Submit</button>
                    </div>
                  </div>

                  <div class="listdetails">
                  <div class="table-responsive mt-5">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>DATE</th>
                                <th>DOCTOR NAME</th>
                                <th>STATUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>01-01-2021</td>
                                <td>Madhukar</td>
                                <td>Closed</td>
                                <td>
                                    <img src="./images/download.svg" alt="download" />
                                    <img src="./images/print.svg" alt="print" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                  </div>
                  </div>
            </div>
            <div id="Reports" class="tab-pane fade">
                <div class="row">
                    <div class="col-md-12">
                        <div class="singleinput">
                        <label>Enter Patient ID :</label>
                        <input type="number" class="form-control" id="" name="">
                    </div>
                    </div>
                  </div>
                  <div class="usersubmitBtns mt-4">
                    <div class="mr-4">
                      <button class="btn formBtn submitBtn">Submit</button>
                    </div>
                  </div>

                  <div class="listdetails">
                  <div class="table-responsive mt-5">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>DATE</th>
                                <th>DOCTOR NAME</th>
                                <th>TEST NAME</th>
                                <th>STATUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>01-01-2021</td>
                                <td>Madhukar</td>
                                <td>njasknka</td>
                                <td>Closed</td>
                                <td>
                                    <img src="./images/download.svg" alt="download" />
                                    <img src="./images/print.svg" alt="print" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                  </div>
                  </div>
            </div>
            <div id="Sickreport" class="tab-pane fade">
                <div class="row">
                    <div class="col-md-12">
                        <div class="singleinput">
                        <label>Enter Patient ID :</label>
                        <input type="number" class="form-control" id="" name="">
                    </div>
                    </div>
                  </div>
                  <div class="usersubmitBtns mt-4">
                    <div class="mr-4">
                      <button class="btn formBtn submitBtn">Submit</button>
                    </div>
                  </div>

                  <div id="accordion" class="mt-5">

                    <div class="card">
                      <div class="card-header row no-gutters" data-toggle="collapse" data-target="#generalinfo">
                         <div class="col-md-4">
                            19-11-2020
                         </div>
                         <div class="col-md-7">
                             Lcjpdjciwjicwjjweionwefwuiebwi
                         </div>
                      </div>
                      <div id="generalinfo" class="collapse show" data-parent="#accordion">
                        <div class="card-body p-4">
                            Lorem ipsum dolor sit amet, consectetur adipisicing elit. Eaque vitae quidem cumque facilis odio doloremque dolor repudiandae, explicabo adipisci maiores sunt dicta esse amet, dolores deleniti alias commodi! Veritatis, aut!
                        </div>
                      </div>
                    </div>
                  </div>
            </div>
        </div>
        </div>
    </div>

</section>

@endsection
