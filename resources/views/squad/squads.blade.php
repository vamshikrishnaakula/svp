{{-- Extends layout --}}
@extends('layouts.default')

{{-- Content --}}
@section('content')

@php
  $i = 1;
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

<section id="createsquad" class="content-wrapper_sub">
      <div class="user_manage">
        <div class="row">
        <div class="col-md-10">
              <h4>Create Squad</h4>
            </div>
            <div class="col-md-2">
                <div class="userBtns">
                    <!-- <a href="#" data-toggle="tooltip" title="import"> <img src="./images/import.png" /></a>
                    <a href="#" data-toggle="tooltip" title="excel"> <img style="width:29px !important"  src="./images/excel.png" /></a> -->
                </div>
            </div>
      </div>
      <form action="{{ route('squads.store') }}" method="POST">
      @csrf

            <div class="row mt-5">
            <div class="col-sm-4">
                    <label>Select Batch</label>
                            <select class="form-control" id="Batch_Id" name='Batch_Id' required>
                            <option value="">Select  Batch Number</option>
                            @foreach($batch as $batches)
                                <option value="{{ $batches->id }}" @if($batches->id == Session::get('current_batch')) selected @endif>{{ $batches->BatchName }}</option>
                             @endforeach
                              </select>
                              <!-- <p id="selectTriggerFilter"><label>Select Batch</label></p> -->
                    </div>
                    <input type="hidden" class="form-control" id="token" value="">
                <div class="col-sm-4">

                    <label for="squadno">Squad Number</label>
                    <input type="text" class="form-control" id="SquadNumber" name='SquadNumber' required>
                </div>

                <div class="col-sm-4">

                            <label>Drill Instructor</label>
                            <select class="form-control" id="DrillInspector_Id" name='DrillInspector_Id' required>
                            <option value="">Select</option>
                            @foreach($staffs as $staff)
                                <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                             @endforeach
                              </select>
                </div>
            </div>


          <div class="row mt-5">
              <div class="col-md-5">
                <div class="probationerlist">
                    <div class="squadbg">
                    <div class="row">
                        <div class="col-md-5">
                            <h6>Probationers List</h6>
                        </div>
                        <div class="col-md-7">
                              <div class="form-group has-search">
                                <span class="fa fa-search form-control-feedback"></span>
                                <input type="text" id="search" name='search' class="form-control" placeholder="Search">
                            </div>
                        </div>
                    </div>
                </div>
                    <table id="prob_list" class="createsquadproblist" style="margin-top: -1px !important">
                        <thead>
                                <tr>
                                    <th width="10%"></th>
                                    <th>Batch No</th>
                                    <th>Roll No</th>
                                    <th width="40%">Name</th>
                                    <th></th>
                                </tr>
                        </thead>
                            <tbody>
                                @foreach ($probationers as $probationer)
                                <tr>
                                    <td><input type='checkbox' class='form-control' name = 'check-tab1' /></td>
                                    <td>{{$probationer->BatchName}}</td>
                                    <td>{{$probationer->RollNumber}}</td>
                                    <td>{{$probationer->Name}}</td>
                                    <td>{{$probationer->id}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                    </table>
                </div>
              </div>
              <div class="swapBtn col-md-2">
                <button type="button" class="btn btn-secondary" onclick="window.tab2_To_tab1();">
                    <i class="fas fa-angle-left"></i>
                </button>
                <button type="button" class="btn btn-secondary" onclick="window.tab1_To_tab2();">
                    <i class="fas fa-angle-right"></i>
                </button>
            </div>
              <div class="col-md-5">
                <div class="probationerlist">
                    <div class="squadbg">
                    <div class="row">
                        <div class="col-md-5">
                            <h6>Added Members</h6>
                        </div>
                        <!-- <div class="col-md-7">
                            <input type="search" class="form-control" placeholder="search" />
                        </div> -->
                    </div>
                </div>

                    <table id="Addedprob" class="createsquadaddedlist" name="Addedprob">
                    {{ csrf_field() }}
                    <thead>
                                <tr>
                                    <th width="10%"></th>
                                    <th>Batch No</th>
                                    <th>Roll No</th>
                                    <th colspan="2">Name</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                        </thead>
                        <tbody>
                            <tr></tr>
                        </tbody>
                    </table>
                </div>

              </div>
          </div>
</div>

          <div class="usersubmitBtns mt-5">
            <div class="mr-4">
                <button type="submit" class="btn formBtn submitBtn">Submit</button>
            </div>
        </div>
        </form>
</section>
@endsection

@section('scripts')
<script src="{{ asset('js/squads.js') }}" type="text/javascript"></script>
@endsection
