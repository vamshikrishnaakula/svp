
<div>
@include('layouts.header')
@extends('layouts.footer')

@if (session()->has('message'))
<div class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md my-3" role="alert">
<div class="flex">
<div>
<p class="text-sm">{{ session('message') }}</p>
</div>
</div>
</div>
@endif




@php
  $i = 1;
  $j = 1;
  foreach($data as $dt)
{
  $dt[0];
   echo "</br>";

}


exit;
@endphp

@if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
  @elseif ($message = Session::get('delete'))
        <div class="alert alert-danger">
            <p>{{ $message }}</p>
        </div>

    @endif

    <section id="squadlist" class="content-wrapper_sub tab-content">
        <div class="user_manage">
          <div class="row">
            <div class="col-md-6">
              <h4>Squad List</h4>
            </div>
            <div class="col-md-6 text-right">
              <div class="download">
                <a href="#" class="mr-5"><img src="/images/download.png" width="60px" /></a>
                <a href="#"><img src="/images/print.png" width="35px" /></a>
              </div>

            </div>
          </div>
          <div class="row mt-5">
            <div class="col-md-12 text-center">
            <form class="userform" action="{{ url('/getsquaddata') }}" method="POST">
             @csrf
              <div class="row">
                <div class="col-md-3"></div>
                <label for="squadno" class="col-md-2">Select Batch No</label>
                <select class="form-control col-md-4" id="batch_id" name="batch_id">
                  <option>Select  Batch Number</option>
                  @foreach($batch as $batches)
                    <option value="{{ $batches->id }}" @if($batches->id == Session::get('current_batch')) selected @endif>{{ $batches->BatchName }}</option>
                  @endforeach
                </select>
                @error('batch_id') <span class="text-red-500">{{ $message }}</span>@enderror
                <div class="usersubmitBtns">
                    <div class="mr-4">
                        <button type="submit" class="btn formBtn submitBtn">Submit</button>
                    </div>
                 </div>
              </div>
            </form>
            </div>
          </div>

          <div class="listdetails mt-4">
            <div class="squadlisthead">
              <div class="row">
                <div class="col-md-6">
                  <div class="group">
                    <img src="./images/Group.png" />
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="row group group_right">
                    <div class="col-md-9">
                      <input type="search" placeholder="search" class="form-control" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div id="accordion">

            @foreach($data as $squadnumbers)
                <div class="card">

                  <div class="card-header" id="heading_{{ $j }}" data-toggle="collapse" data-target="#collapse_{{ $j }}" aria-expanded="true" aria-controls="collapseOne">

                  <div class="row">
                    <div class="form-check col-md-4">
                        <label class="form-check-label">
                         <span>{{ $squadnumbers }}</span>
                        </label>
                      </div>
                      <div class="col-md-4 text-center">
                        <span>{{ $squadnumbers }}</span>
                      </div>
                  <div class="col-md-4 text-right">
                    <a href="./editstaff.html"  data-toggle="tooltip" title="Edit"><img src="./images/edit.png" /></a>
                    <a href="#"  data-toggle="tooltip" title="Delete"><img src="./images/trash.png" /></a>
                  </div>
                   </div>

                  </div>
                  <div id="collapse_{{ $j }}" class="collapse show" aria-labelledby="heading_{{ $j }}" data-parent="#accordion">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                              <thead>
                                <tr>
                                  <th>Cadre</th>
                                  <th>Batch Number</th>
                                  <th>Roll Number</th>
                                  <th>Name</th>
                                </tr>
                              </thead>
                              <tbody>
                                  @foreach($prob as $squad)
                                <tr>
                                  <td>{{ $squad->Cadre }}</td>
                                  <td>{{ $squad->batch_id }}</td>
                                  <td>{{ $squad->RollNumber }}</td>
                                  <td>{{ $squad->Name }}</td>
                                  <td>
                                    <a href="#"  data-toggle="tooltip" title="Delete"><img src="/images/trash.png" /></a>
                                  </td>
                                </tr>
                                @endforeach
                              </tbody>
                            </table>

                          </div>
                    </div>
                  </div>
                  @endforeach
                </div>

</section>
</div>


<script>
    $('.collapse').collapse()
</script>
