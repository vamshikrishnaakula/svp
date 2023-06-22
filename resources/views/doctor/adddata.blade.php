{{-- Extends layout --}}
@extends('layouts.doctor.template')

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

<section id="adddata" class="content-wrapper_sub tab-content">

<div class="row mt-4">
  <div class="col-md-6">
    <form action="{{ route('adddata.doctorstore') }}" method="POST"> 
    @csrf
      <h5>Lab Tests</h5>
      <div class="form-group">
        <input type="text" class="form-control" name = 'LabTestName' id = 'LabTestName'>
      </div>
      <div class="usersubmitBtns mt-4">
        <div class="mr-4">
          <button type="submit" class="btn formBtn submitBtn">Submit</button>
        </div>
        <!-- <div>
          <button type="button" class="btn formBtn cancelBtn">Cancel</button>
        </div> -->
      </div>
    </form>
  </div>
  <div class="col-md-6">
    <form action="{{ route('adddata.doctorstore') }}" method="POST">
    @csrf
      <h5>Medicines</h5>
      <div class="form-group">
        <label >Name</label>
        <input type="text" class="form-control" name='MedicineName'>
      </div>
      <div class="form-group">
        <label >Content</label>
        <input type="text" class="form-control" name='MedicineContent'>
      </div>
      <div class="form-group">
        <label >Type</label>
        <select class="form-control" id="Role" name='MedicineType'>
                            <option value="Tablet">Tablet</option>
                            <option value="Injection">Injection</option>
                            <option value="Surgicals">Surgicals</option>
                            <option value="Others">Others</option>
                          </select>
      </div>
      <div class="form-group">
        <label >manufacturer</label>
        <input type="text" class="form-control" name='MedicineManufacturer'>
      </div>
      <div class="form-group">
        <label >Dosage(mg)</label>
        <input type="text" class="form-control" name='MedicineDosage'>
      </div>

      <div class="usersubmitBtns mt-4">
        <div class="mr-4">
          <button type="submit" class="btn formBtn submitBtn">Submit</button>
        </div>
        <!-- <div>
          <button type="button" class="btn formBtn cancelBtn">Cancel</button>
        </div> -->
      </div>
    </form>
  </div>
</div>

</section>
@endsection

