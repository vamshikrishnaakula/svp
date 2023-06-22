{{-- Extends layout --}}
@extends('layouts.default')

{{-- Content --}}
@section('content')

<section id="patientlist" class="content-wrapper_sub tab-content">

    <div class="listdetails mt-4">
      <div class="squadlisthead">
        <div class="row">
          <div class="col-md-6">
            <div class="group">
              <img src="./images/patientlist.png" />
              <h4 class="ml-4">Patient List</h4>
            </div>
          </div>
          <div class="col-md-6">
            <div class="row">
              <div class="col-md-12 patient_userBtns">
                <a href="#"><img src="./images/download.svg" />
                  <p>Download</p>
                </a>


                <a href="#"><img src="./images/print.svg" />
                  <p>Print</p>
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
              <td>
                <a href="./editstaff.html" data-toggle="tooltip" title="Edit"><img src="./images/edit.png" /></a>
                <a href="#"  data-toggle="tooltip" title="Delete"><img src="./images/trash.png" /></a>
              </td>
            </tr>
            <tr>
              <td><input type="checkbox" class="form-control" /></td>
              <td>gretrhr</td>
              <td>435345</td>
              <td>6546456</td>
              <td>gregerg</td>
              <td>gerger</td>
              <td>34636363</td>
              <td>
                <a href="#" data-toggle="tooltip" title="Edit"><img src="./images/edit.png" /></a>
                <a href="#"  data-toggle="tooltip" title="Delete"><img src="./images/trash.png" /></a>
              </td>
            </tr>
          </tbody>
        </table>

      </div>
    </div>
  </section>

@endsection
