{{-- Extends layout --}}
@extends('layouts.faculty.template')

{{-- Content --}}
@section('content')

<section id="createactivity" class="content-wrapper_sub activities-wrapper tab-content">
    <div class="user_manage">
        <div class="row">
            <div class="col-md-6">
                <h4>Squad List</h4>
            </div>
            <div class="col-md-6">

            </div>
        </div>
        <form name="get_squads_form" id="get_squads_form" action="" method="post" class="userform" accept-charset="utf-8">

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

        <div id="squad_list" class="mt-5"></div>

        {{-- Probationers Modal --}}
        <div class="modal fade" id="probationersModal" tabindex="-1" role="dialog" aria-labelledby="probationersModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content text-center">

                    <div class="modal-body">
                        <div id="probationersModalContent"></div>
                        <div id="probationersModalBtns">
                            <div class="modal-btn-secondary">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

{{-- @section('scripts')
<script src="{{ asset('js/activities.js') }}" type="text/javascript"></script>
@endsection --}}
