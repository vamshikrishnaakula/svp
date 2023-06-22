<form name="assignActivity_form" id="assignActivity_form" action="{{ url('activities/ajax') }}" method="post" class=""
    accept-charset="utf-8">
    @csrf

    <?php
        $batch_id       = $request->batch_id;
        $activity_id    = $request->activity_id;

        if( empty($batch_id) || empty($activity_id) ) {
            echo "Batch Id and/or Activity Id is missing";
            return;
        }

        // $activities = App\Models\Activity::where('type', 'activity')->get();

        $squads = DB::table('squads')
            ->where('Batch_Id', $batch_id)
            ->get();
            $aRoles = ['drillinspector', 'si', 'adi'];
        $staffs = DB::table('users')
            ->whereIn('role', $aRoles)->get();
    ?>

    <input type="hidden" name="current_batch_id" value="{{ $batch_id }}" class="hidden" />
    <input type="hidden" name="current_activity_id" value="{{ $activity_id }}" class="hidden" />

    <div class="trainerslist mt-5">
        <div class="row" style="margin: 0;">
            <div class="col-sm-6" style="padding: 0;">
                <h4>List of Squads</h4>
            </div>
            <div id="assignActivity_status" class="col-sm-6 text-right" style="padding-left:0;padding-right:40;"></div>
        </div>
        <ul>
            @if( !empty($squads) )
                @foreach($squads as $squad)
                    @php
                        $squad_id   = $squad->id;
                        $squad_DI   = $squad->DrillInspector_Id;

                        $squadTrainer   = DB::table('squad_activity_trainer')
                            ->select('staff_id')
                            ->where('squad_id', $squad_id)
                            ->where('activity_id', $activity_id)->get()->first();

                        $squadTrainer_id    = 0;
                        if($squadTrainer) {
                            $squadTrainer_id    = $squadTrainer->staff_id;
                        }
                    @endphp
                    <li class="squad-list-item" data-squad-id="{{ $squad_id }}">
                        <div class="assigntrainer">
                            <span>Squad {{ $squad->SquadNumber }}</span>
                            <div class="form-group">
                                <select name="staff_id[{{ $squad_id }}]" id="staff_id[{{ $squad_id }}]" class="activityStaffList form-control">
                                    <option value="">Select trainer...</option>
                                    @php
                                    if( !empty($staffs) ) {
                                        foreach($staffs as $staff) {
                                            $staff_id  = $staff->id;
                                            $staff_name = $staff->name;

                                            $selected   = ($squadTrainer_id === $staff_id)? "selected" : "";
                                            $disabled    = ($squad_DI === $staff_id)? "disabled" : "";
                                            echo "<option value=\"{$staff_id}\" {$selected} {$disabled}>{$staff_name}</option>";
                                        }
                                    }
                                    @endphp
                                </select>
                            </div>
                        </div>
                    </li>
                @endforeach
            @endif
        </ul>
    </div>
</form>
