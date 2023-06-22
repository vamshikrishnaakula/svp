{{-- Extends layout --}}
@extends('layouts.default')

{{-- Content --}}
 @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif
@section('content')
    <section id="createbatch" class="content-wrapper_sub">
        <div class="user_manage">
            <div class="row">
                <div class="col-md-12">
                  <h4>Edit Batch</h4>
                </div>
              </div>
              <form action="{{ route('batch.update', $row->id) }}" method="POST">
              @csrf
              @method('PUT')
                <div class="row mt-5">
                    <div class="col-md-12">
                      <div class="singleinput">                    
                        <label for="squadno">Enter Batch Name :</label>
                        <input type="text" class="form-control" id="BatchName" name="BatchName" value ="{{ $row->BatchName }}" >
                        @error('BatchName') <span class="text-red-500">{{ $message }}</span>@enderror
                      </div>
                    </div>
                  </div>
                  <div class="usersubmitBtns mt-5">
                    <div class="mr-4">
                      <button type="submit" class="btn formBtn submitBtn">Update</button>
                    </div>
                  </div>
            </form>
                  <div class="listdetails">
                  <div class="batchlisthead mt-5">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="group">
                          <h5 class="mb-0">List of Batches</h5>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="row group group_right">
                          <!-- <div class="col-md-9">
                            <input type="search" placeholder="search" class="form-control" />
                          </div> -->
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table">
                      <thead>
                        <tr>
                          <th>Batch Name</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>
                      @foreach($batches as $batch)
                        <tr>  
                          <td>{{ $batch->BatchName }}</td>
                          <td>
                            <a href="{{ route('batch.edit',$batch->id) }}"  data-toggle="tooltip" title="Edit"><img src="/images/edit.png" /></a>
                            <a href="" onclick="if(confirm('Do you want to delete this Batch?'))event.preventDefault(); document.getElementById('delete-{{$batch->id}}').submit();" class="btn btn-sm btn-outline-danger py-0"><img src="/images/trash.png" /></a>
                            <form id="delete-{{$batch->id}}" method="post" action="{{route('batch.destroy',$batch->id)}}" style="display: none;">
                            @csrf
                            @method('DELETE')
                            </form>
                          </td>
                        </tr>
                        @endforeach
                      </tbody>
                    </table>
        
                  </div>
                </div>
        </div>
    </section>
    @endsection

