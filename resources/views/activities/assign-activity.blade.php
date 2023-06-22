{{-- Extends layout --}}
@extends('layouts.default')

{{-- Content --}}
@section('content')

<section id="assignactivity" class="content-wrapper_sub tab-content">
    <div class="user_manage">
      <div class="row">
        <div class="col-md-6">
          <h4>Assign Activity Trainer</h4>
        </div>
      </div>

      <form name="get_assignActivity_form" id="get_assignActivity_form" action="{{ url('activities/ajax') }}"
        method="post" class="" accept-charset="utf-8">
        @csrf

        @php
        $batches = DB::table('batches')->get();
        @endphp

        <div class="assignactivity">
            <div class="row mt-5">
                <div class="col-md-2"></div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Select Batch:</label>
                        <select name="batch_id" id="batch_id" onchange="window.get_activity_options(this, 'activity_id');" class="form-control">
                            <option value="">Select batch...</option>
                            @if( !empty($batches) )
                            @foreach($batches as $batch)
                            <option value="{{ $batch->id }}" @if($batch->id == Session::get('current_batch')) selected @endif>{{ $batch->BatchName }}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Select Activity:</label>
                        <select name="activity_id" id="activity_id" class="form-control">
                            <option value="">Select...</option>
                            {{-- @if($activities)
                                @foreach ($activities as $activity)
                                    <option value="{{ $activity->id }}">{{ $activity->name }}</option>
                                @endforeach
                            @endif --}}
                        </select>
                    </div>
                </div>
            </div>

        </div>

        <div class="usersubmitBtns mt-5">
          <div class="mr-4">
              <button type="button" onclick="window.get_assignActivity_submit();"
                    class="btn formBtn submitBtn">Proceed</button>
          </div>
        </div>
      </form>

      <div id="get_assignActivity_status" class="mt-5"></div>
      <div id="assignActivity-container" class="table-responsive mt-5"></div>

  </section>

@endsection
