{{-- Extends layout --}}
@extends('layouts.faculty.template')

{{-- Content --}}
@section('content')

<section id="createactivity" class="content-wrapper_sub activities-wrapper tab-content">
    <div class="user_manage">
        <div class="row">
            <div class="col-md-6">
                <h4>Probationer List</h4>
            </div>
            <div class="col-md-6">

            </div>
        </div>
        <form name="get_probationers_form" id="get_probationers_form" action="" method="post" class="userform" accept-charset="utf-8">

            @php
            $batches = DB::table('batches')->get();
            @endphp
            <div>
                <div class="width-half rl-margin-auto">
                    <label>Select Batch:</label>
                    <select name="batch_id" id="batch_id" class="form-control reqField">
                        <option value="">Select...</option>
                        @if( !empty($batches) )
                        @foreach($batches as $batch)
                        <option value="{{ $batch->id }}" @if($batch->id == Session::get('current_batch')) selected @endif>{{ $batch->BatchName }}</option>
                        @endforeach
                        @endif
                    </select>
                </div>
            </div>

            <div class="usersubmitBtns mt-5">
                <div class="mr-4">
                    <button type="submit" class="btn formBtn submitBtn">Proceed</button>
                </div>
            </div>
        </form>

        <div id="probationers_list" class="mt-5"></div>
    </div>
</section>

@endsection
