{{-- Extends layout --}}
@extends('layouts.faculty.template')

{{-- Content --}}
@section('content')

<section id="viewsessions" class="content-wrapper_sub">
    <div class="user_manage">
        <div class="row">
            <div class="col-md-9">
                <h4>Extra Sessions</h4>
            </div>
            <div class="col-md-3">
                <div class="userBtns">
                    {{-- <a href="#" data-toggle="tooltip" title="download"> <img src="{{ asset('images/download1.png') }}" /></a>
                    <a href="#" data-toggle="tooltip" title="print"> <img src="{{ asset('images/print1.png') }}" /></a> --}}
                </div>
            </div>
        </div>

        @php
        $batches = DB::table('batches')->get();

        // echo set_extra_session_attendance(6, 3, 'P');
        @endphp

        <form name="get_extrasession_form" id="get_extrasession_form" action="{{ url('timetables/ajax') }}" method="post" class="width-half rl-margin-auto" accept-charset="utf-8">

            <div class="row mt-5">
                <div class="col">
                    <div class="form-group">
                        <label>Select Batch</label>
                        <select name="batch_id" id="batch_id" class="form-control reqField">
                            <option value="">Select batch...</option>
                            @if( !empty($batches) )
                            @foreach($batches as $batch)
                            <option value="{{ $batch->id }}" @if($batch->id == Session::get('current_batch')) selected @endif>{{ $batch->BatchName }}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <label>Date</label>
                    <div class="form-group">
                        <input type="text" name="session_date" id="session_date" class="form-control datePicker reqField" autocomplete="off" />
                    </div>
                </div>
            </div>

            <div id="get_extrasession_status" class="mt-3"></div>

            <div class="usersubmitBtns mt-5">
                <div class="mr-4">
                    <button type="button" onclick="window.get_extraSessions();" class="btn formBtn submitBtn">Proceed</button>
                </div>
            </div>
        </form>

        <div class="sessionbg mt-5 mb-0 p-2">
            <h5 id="session_list_title">Upcoming Sessions</h5>
        </div>

        @php
            $ExtraSessions  = App\Models\ExtraSession::whereDate('date', '>=', date('Y-m-d'))
                ->where('activity_id', '!=', 0)
                ->whereNotNull('activity_id')
                ->where('session_start', '>', 0)
                ->orderBy('session_start', 'asc')->get();
        @endphp

        <table id="extra_sessions_table">
            <thead>
                <tr>
                    <th>Batch</th>
                    <th>Activity</th>
                    <th>Sub Activity</th>
                    <th>Time</th>
                    <th>Date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @if(count($ExtraSessions)>0)
                    @foreach ($ExtraSessions as $ExtraSession)
                        @php
                            $sessionId   = $ExtraSession->id;
                            $batch_id   = $ExtraSession->batch_id;
                            $batch      = batch_name($batch_id);

                            $activity_id     = $ExtraSession->activity_id;
                            $activity        = activity_name($activity_id);

                            $subActivity_id  = $ExtraSession->subactivity_id;
                            $subActivity     = "";
                            if(!empty($subActivity_id)) {
                                $subActivity     = activity_name($subActivity_id);
                            }

                            $date     = $ExtraSession->date;
                            $session_start  = $ExtraSession->session_start;
                            $session_start  = date('h:i A', $session_start);

                            $session_end    = $ExtraSession->session_end;
                            $session_end    = date('h:i A', $session_end);
                        @endphp
                        <tr>
                            <td>{{ $batch }}</td>
                            <td>{{ $activity }}</td>
                            <td>{{ $subActivity }}</td>
                            <td>{{ $session_start }} - {{ $session_end }}</td>
                            <td>{{ $date }}</td>
                            <td>
                                <a href="#" onclick="window.get_extraSessionsMeta({{ $sessionId }}); return false;">
                                    <img src="{{ asset('/images/view.png') }}">
                                </a>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</section>
@endsection

@section('scripts')

<script>
    //
</script>
@endsection
