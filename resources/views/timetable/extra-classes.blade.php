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
                    <h4 class="d-inline">Extra Classes</h4>
                    <button type="button" class="btn btn-primary btn-sm d-inline createNewBtn" data-target="{{ url('/timetables/create-extra-class') }}"><i class="fas fa-plus-circle"></i> Create</button>
                </div>
            </div>
            <div class="col-md-4">
                <div class="useractionBtns d-flex justify-content-end">
                    <a href="#" onclick="window.get_extraClassImport_modal()" data-toggle="tooltip" title="Import Attendance Data"> <img src="{{ asset('images/import.png') }}" /></a>
                </div>
            </div>
        </div>

        @php
        $batches = DB::table('batches')->get();

        // echo set_extra_session_attendance(6, 3, 'P');
        @endphp

        <form name="get_extraclass_form" id="get_extraclass_form" action="{{ url('timetables/ajax') }}" method="post" class="width-three-fourth rl-margin-auto" accept-charset="utf-8">

            <div class="row mt-5">
                <div class="col-md-4">
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

            <div id="get_extraclass_status" class="mt-3"></div>

            <div class="usersubmitBtns mt-5">
                <div class="mr-4">
                    <button type="button" onclick="window.get_extraClasses();" class="btn formBtn submitBtn">Proceed</button>
                </div>
            </div>
        </form>

        <div class="d-flex justify-content-between sessionbg mt-5 mb-0 p-2">
            <h5>Sessions list</h5>
            <button id="print_extra_classes" class="btn btn-warning me-2">Print</button>
        </div>

        @php
            $date = date("Y-m-d", strtotime("-2 months"));
            $ExtraClasses  = App\Models\ExtraClass::where('activity_id', '!=', 0)
                ->whereNotNull('activity_id')
                ->where('session_start', '>', 0)
                ->whereDate('date', '>', $date)
                ->orderBy('session_start', 'desc')
                ->paginate(50);
        @endphp

        <table id="extra_classes_table">
            <thead>
                <tr>
                    <th>Batch</th>
                    <th>Activity</th>
                    <th>Sub Activity</th>
                    {{-- <th>Component</th> --}}
                    <th>Time</th>
                    <th>Date</th>
                    <th>Staff / DI</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @if(count($ExtraClasses)>0)
                    @foreach ($ExtraClasses as $ExtraClass)
                        @php
                            $sessionId   = $ExtraClass->id;
                            $batch_id   = $ExtraClass->batch_id;
                            $batch      = batch_name($batch_id);

                            $activity_id     = $ExtraClass->activity_id;
                            $activity        = activity_name($activity_id);

                            $subActivity_id  = $ExtraClass->subactivity_id;
                            $subActivity     = "";
                            if(!empty($subActivity_id)) {
                                $subActivity     = activity_name($subActivity_id);
                            }

                            $component_id  = $ExtraClass->component_id;
                            $component     = "";
                            if(!empty($component_id)) {
                                $component     = activity_name($component_id);
                            }

                            $di_id      = $ExtraClass->drillinspector_id;
                            $di_name    = user_name($di_id);

                            $date     = $ExtraClass->date;
                            $session_start  = $ExtraClass->session_start;
                            $session_start  = date('h:i A', $session_start);

                            $session_end    = $ExtraClass->session_end;
                            $session_end    = date('h:i A', $session_end);

                            $downloadIcon   = asset('/images/download.svg');
                            $check_session_status = App\Models\ExtraClassmeta::where('extra_class_id', $sessionId)->whereNotNull('attendance')->count();
                            $status =  ($check_session_status === 0) ? "Pending" : "Completed";
                        @endphp
                        <tr>
                            <td>{{ $batch }}</td>
                            <td>{{ $activity }}</td>
                            <td>{{ $subActivity }}</td>
                            {{-- <td>{{ $component }}</td> --}}
                            <td>{{ $session_start }} - {{ $session_end }}</td>
                            <td>{{ $date }}</td>
                            <td>{{ $di_name }}</td>
                            <td> {{ $status}}</td>
                            <td>
                                @if ($check_session_status === 0)
                                <a href="#" onclick="window.get_editExtraClass({{ $sessionId }}); return false;" data-toggle="tooltip"  title="Edit sessions details">
                                    <img src="{{ asset('/images/edit.png') }}">
                                </a>
                                @else
                                <a href="#" onclick="" data-toggle="tooltip" title="Extra class completed">
                                    <img src="{{ asset('/images/edit.png') }}" >
                                </a>
                                @endif

                                <a href="#" onclick="window.download_extraClassData({{ $sessionId }}); return false;" data-toggle="tooltip" title="Download attendance data">
                                    <img src="{{ $downloadIcon }}" style="width:33px;">
                                </a>
                                <a href="#" onclick="window.get_extraClassMetas({{ $sessionId }}); return false;">
                                    <img src="{{ asset('/images/view.png') }}">
                                </a>
                                <a href=""
                                onclick="if(confirm('Do you want to delete this extra class?'))event.preventDefault(); document.getElementById('delete-{{$sessionId}}').submit();"><img
                                    src="{{ asset('images/trash.png') }}" style="width: 12%" /></a>
                                    <form id="delete-{{$sessionId}}" method="post"
                                        action="{{ url('extraclasses/delete/'.$sessionId) }}"
                                        style="display: none;">
                                        @csrf
                                        @method('POST')
                                    </form>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>

            <div id="extra_classes_pagination" class="mx-auto mt-3" style="width: fit-content;">
                {{ $ExtraClasses->links() }}
            </div>

        </table>
    </div>
</section>
@endsection

@section('header-scripts')
    <script src="{{ asset('js/jquery.inputmask.bundle.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/printThis.js') }}"></script>
@endsection

@section('scripts')

<script>
    $(document).on('click', 'button.createNewBtn', function(){
        window.location.href = $(this).attr('data-target');
        return;
    });

    // Print session list
    $(document).on("click", "#print_extra_classes", function() {
        $("#extra_classes_table").printThis();
    });

    // Print session probationer list
    $(document).on("click", "#print_extra_class_btn", function() {
        $("#successModalContent").printThis();
    });
</script>
@endsection
