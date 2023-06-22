{{-- Extends layout --}}
@extends('layouts.default')

{{-- Content --}}
@section('content')

<div id="error"></div>
@if ($message = Session::get('success'))
<div class="alert alert-success">
    <p>{{ $message }}</p>
</div>
@elseif ($message = Session::get('delete'))
<div class="alert alert-danger">
    <p>{{ $message }}</p>
</div>

@endif

<section id="viewsessions" class="content-wrapper_sub">
    <div class="user_manage">
        <div class="row">
            <div class="col-md-8">
                <div class="">
                    <h4 class="d-inline">Missed Classes</h4>
                    <button type="button" class="btn btn-primary btn-sm d-inline createNewBtn" data-target="{{ url('/timetables/create-missed-class') }}"><i class="fas fa-plus-circle"></i> Create</button>
                </div>
            </div>
            <div class="col-md-4">
                <div class="useractionBtns d-flex justify-content-end">
                    <a href="#" onclick="window.get_extraSessionImport_modal()" data-toggle="tooltip" title="Import Attendance Data"> <img src="{{ asset('images/import.png') }}" /></a>
                </div>
            </div>
        </div>

        @php
        $batches = DB::table('batches')->get();

        // echo set_extra_session_attendance(6, 3, 'P');
        @endphp

        <form name="get_extrasession_form" id="get_extrasession_form" action="{{ url('timetables/ajax') }}" method="post" class="width-three-fourth rl-margin-auto" accept-charset="utf-8">

            <div class="row mt-5">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Select Batch</label>
                        <select name="batch_id" id="batch_id" class="form-control reqField">
                            <option value="">Select batch...</option>
                            @if( !empty($batches) )
                            @foreach($batches as $batch)
                            <option value="{{ $batch->id }}" @if($batch->id === current_batch()) selected @endif>{{ $batch->BatchName }}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <label>Date (From):</label>
                    <div class="form-group">
                        <input type="text" name="date_from" id="date_from" class="form-control datePicker reqField" autocomplete="off" />
                    </div>
                </div>
                <div class="col-md-4">
                    <label>Date (To):</label>
                    <div class="form-group">
                        <input type="text" name="date_to" id="date_to" class="form-control datePicker reqField" placeholder="(optional)" autocomplete="off" />
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

        <div class="d-flex justify-content-between sessionbg mt-5 mb-0 p-2">
            <h5>Sessions list</h5>
            <button id="print_extra_sessions" class="btn btn-warning me-2">Print</button>
        </div>

        @php
            $date = date("Y-m-d", strtotime("-2 months"));
            $ExtraSessions  = App\Models\ExtraSession::where('activity_id', '!=', 0)
                ->whereNotNull('activity_id')
                ->where('session_start', '>', 0)
                ->whereDate('date', '>', $date)
                ->orderBy('session_start', 'desc')
                ->paginate(50);

        @endphp

        <table id="extra_sessions_table">
            <thead>
                <tr>
                    <th>Batch</th>
                    <th>Activity</th>
                    <th>Sub Activity</th>
                    <th>Time</th>
                    <th>Date</th>
                    <th>Staff / DI</th>
                    <th>Status</th>
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

                            $component_id  = $ExtraSession->component_id;
                            $component     = "";
                            if(!empty($component_id)) {
                                $component     = activity_name($component_id);
                            }

                            $di_id      = $ExtraSession->drillinspector_id;
                            $di_name    = user_name($di_id);

                            $date     = $ExtraSession->date;
                            $session_start  = $ExtraSession->session_start;
                            $session_start  = date('h:i A', $session_start);

                            $session_end    = $ExtraSession->session_end;
                            $session_end    = date('h:i A', $session_end);

                            $downloadIcon   = asset('/images/download.svg');
                            $check_session_status = App\Models\ExtraSessionmeta::where('extra_session_id', $sessionId)->whereNotNull('attendance')->count();

                            //echo $check_session_status;exit;

                            $status =  ($check_session_status === 0) ? "Pending" : "Completed";

                        @endphp
                        <tr class="alt">
                            <td>{{ $batch }}</td>
                            <td>{{ $activity }}</td>
                            <td>{{ $subActivity }}</td>
                            <td>{{ $session_start }} - {{ $session_end }}</td>
                            <td>{{ $date }}</td>
                            <td>{{ $di_name }}</td>
                            <td>{{ $status}}</td>

                            <td>

                                @if ($check_session_status === 0)
                                    <a href="#" onclick="window.get_editExtraSession({{ $sessionId }}); return false;" data-toggle="tooltip" id="edit_sessions_details" title="Edit sessions details" class="session_com" value="Edit sessions details">
                                        <img src="{{ asset('/images/edit.png') }}">
                                    </a>
                                @else
                                    <a href="#" onclick="" id="session_completed" class="session_com" data-toggle="tooltip" title="Session Completed" value="Session Completed">
                                        <img src="{{ asset('/images/edit.png') }}">
                                    </a>
                                @endif

                                <a href="#" onclick="window.download_extraSessionData({{ $sessionId }}); return false;" data-toggle="tooltip" title="Download attendance data">
                                    <img src="{{ $downloadIcon }}" style="width:33px;">
                                </a>
                                <a href="#" onclick="window.get_extraSessionsMeta({{ $sessionId }}); return false;" data-toggle="tooltip" title="View probationers">
                                    <img src="{{ asset('/images/view.png') }}">
                                </a>
                                <a href=""
                                onclick="if(confirm('Do you want to delete this missed class?'))event.preventDefault(); document.getElementById('delete-{{$sessionId}}').submit();"><img
                                    src="{{ asset('images/trash.png') }}" style="width: 12%"; /></a>
                                    <form id="delete-{{$sessionId}}" method="post"
                                        action="{{ url('missedclasses/delete/'.$sessionId) }}"
                                        style="display: none;">
                                        @csrf
                                        @method('POST')
                                    </form>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>

        <div id="extra_sessions_pagination" class="mx-auto mt-3" style="width: fit-content;">
            {{ $ExtraSessions->links() }}
        </div>
    </div>
</section>
@endsection

@section('header-scripts')
    <script src="{{ asset('js/jquery.inputmask.bundle.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/printThis.js') }}"></script>
@endsection

@section('scripts')

<script type="text/javascript">

        // $(document).ready(function() {
        // $('.status.val():contains("Session Completed")').closest('tr.alt').css('background-color', 'green');
        // //$('td.status[value=Session Completed]').closest('tr').css('background-color', 'red');
        // });

        $(document).ready(function() {
            $("tr").find("Session Completed").css({
                "background-color": "green",
                //"border": "2px solid green"
            });
        });


    $(document).on('click', 'button.createNewBtn', function(){
        window.location.href = $(this).attr('data-target');
        return;
    });

    // Print session list
    $(document).on("click", "#print_extra_sessions", function() {
        $("#extra_sessions_table").printThis();
    });

    // Print session probationer list
    $(document).on("click", "#print_extra_session_btn", function() {
        $("#successModalContent").printThis();
    });

</script>
@endsection
