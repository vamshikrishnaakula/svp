<form id="create_extraclass_form" action="{{ url('timetables/ajax') }}" method="post" class="width-three-fourth rl-margin-auto" accept-charset="utf-8">
    {{-- <span id="close_create_extrasession_form"><i class="far fa-times-circle"></i></span> --}}

    @php
        $batch_id       = $request->batch_id;
        $activity_id    = $request->activity_id;
        // $subactivity_id = $request->subactivity_id;
        $di_id          = intval($request->di_id);
        $session_date   = $request->session_date;

        $batchName  = App\Models\Batch::where('id', $batch_id)->value('BatchName');

        $activityName  = activity_name($activity_id);
        // $subActivityName  = empty($subactivity_id) ? "" : activity_name($subactivity_id);

        $subactivities = App\Models\Activity::where('parent_id', $activity_id)->where('type', 'subactivity')->get();
        $subactivity_count = $subactivities->count();

        // $components = "";
        // $components_count = 0;
        // if(!empty($subactivity_id)) {
        //     $components = App\Models\Activity::where('parent_id', $subactivity_id)->where('type', 'component')->get();
        //     $components_count = $components->count();
        // }

        $get_DI     = get_staffs();

        $plusIcon   = asset('images/plus-icon-rounded.png');
        $delIcon   = asset('images/wrong.png');
    @endphp

    <div class="row mt-5" style="max-width: 850px;">
        <div class="col">
            <div class="form-group">
                <label>Select Batch</label>
                <input type="text" value="{{ $batchName }}" class="form-control" disabled>
            </div>
        </div>
        <div class="col">
            <div class="form-group">
                <label>Activity</label>
                <input type="text" value="{{ $activityName }}" class="form-control" disabled>
            </div>
        </div>

        <div class="col">
            <div class="form-group">
                <label>Sub Activity</label>
                @if($subactivity_count === 0)
                    <select name="subactivity_id" id="subactivity_id" data-has-subactivity="no" class="form-control subactivity_id">
                        <option value="">-- No Sub Activity --</option>
                    </select>
                @else
                    <select name="subactivity_id" id="subactivity_id" data-has-subactivity="yes" class="form-control subactivity_id reqField">
                        <option value="">Select Sub Activity...</option>
                        @foreach($subactivities as $subactivity)
                            <option value="{{ $subactivity->id }}">{{ $subactivity->name }}</option>
                        @endforeach
                    </select>
                @endif
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-md-6">
            <div class="probationerlist no-scroll">
                <div class="squadbg">
                    <div class="row">
                        <div class="col-md-5">
                            <h6>Probationers List</h6>
                        </div>
                        <div class="col-md-7">
                            <div class="form-group has-search mb-0">
                                <span class="fa fa-search form-control-feedback"></span>
                                <input type="text" id="search" name='search' class="form-control searchInTable" data-table="extrasession_prob_list" placeholder="Search">
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <table id="extrasession_prob_list" class="table-scroll-tbody cb-prob-list-table" style="margin-top: -1px !important">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Batch No</th>
                                <th>Roll No</th>
                                <th>Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($Probationers as $Probationer)

                            <tr id="extra-session-pb-tr-{{ $Probationer->id }}" class="extra-session-pb-tr" data-pb-id="{{ $Probationer->id }}">
                                <td><input type="checkbox" name="extra_session_pb_cb" value="{{ $Probationer->id }}" class="form-control extra-session-pb-cb" /></td>
                                <td>{{ $Batch->BatchName }}</td>
                                <td>{{ $Probationer->RollNumber }}</td>
                                <td>{{ $Probationer->Name }}</td>
                            </tr>

                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="probationerlist no-scroll">
                <div class="squadbg">
                    <div class="row">
                        <div class="col-md-5">
                            <h6>Added Members</h6>
                        </div>

                    </div>
                </div>
                <table id="extrasession_addedprob_list" class="table-scroll-tbody cb-prob-list-table">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Batch No</th>
                            <th>Roll No</th>
                            <th>Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="no-data-tr">
                            <td colspan="4">No data available in table</th>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <div class="mt-5">
        <table class="table extrasession-sessions">
            <tr>
                <th>Session</th>
                <th>Staff</th>
                <th>Date</th>
                <th>Time</th>
                <th></th>
            </tr>
            <tr>
                <td>Session 1</td>
                <td>
                    <select name="di_id[]" class="form-control reqField">
                        <option value="">Select Drill Inspector...</option>
                        @if( !empty($get_DI) )
                            @foreach($get_DI as $DI)
                                <option value="{{ $DI->id }}" {{ ($DI->id === $di_id) ? "selected" : "" }}>{{ $DI->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </td>
                <td>
                    <input type="text" name="session_date[]" id="session_date_1" placeholder="YYYY-MM-DD" class="form-control datePicker reqField" autocomplete="off" />
                </td>
                <td>
                    <input type="text" name="session_time[]" value="" placeholder="HH:MM - HH:MM"
                        data-valid-example="08:30 - 09:30" class="form-control jquery-timerange-mask reqField" />
                </td>
                <td style="width:120px;">
                    <img src="{{ $plusIcon }}" alt="Add" class="plus-icon mr-1">
                    <img src="{{ $delIcon }}" alt="Remove" class="cross-icon">
                </td>
            </tr>
        </table>
    </div>

    <div class="hidden">
        <input type="hidden" name="batch_id" value="{{ $request->batch_id }}" class="hidden" />
        <input type="hidden" name="activity_id" value="{{ $request->activity_id }}" class="hidden" />
    </div>

    <div id="create_extraclass_status" class="mt-3"></div>

    <div class="usersubmitBtns mt-2">
        <div class="">
            <button type="button" id="close_create_extraclass_form" class="btn formBtn cancelBtn">Cancel</button>
            <button type="button" onclick="window.create_extra_class();" class="btn formBtn submitBtn">Submit</button>
        </div>
    </div>
</form>
