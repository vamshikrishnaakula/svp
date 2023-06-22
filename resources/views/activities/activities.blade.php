{{-- Extends layout --}}
@extends('layouts.default')

{{-- Content --}}
@section('content')

<section id="activitieslist" class="content-wrapper_sub tab-content">
    <div class="user_manage">
        <div class="squadlisthead">
            <div class="row">
                <div class="col-md-6">
                    <div class="activityhead">
                        <img src="{{ asset('images/indooractivities.png') }}" />
                        <h4 class="mb-0 ml-4">List of Activities</h4>
                        <div style="padding-left: 10px;">
                            @php
                            $batches = DB::table('batches')->get();
                            @endphp

                            <select name="batch_id" id="batch_id" onchange="window.get_list_of_activities_table(this, 'activity_id');" class="form-control">
                                <option value="">Select batch...</option>
                                @if( !empty($batches) )
                                @foreach($batches as $batch)
                                 <option value="{{ $batch->id }}" {{$batch->id == Session::get('current_batch')  ? 'selected' : ''}}>{{ $batch->BatchName }}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-6  d-flex align-items-center">

                    <div id="activity-search-container" class="form-group has-search mb-0 mt-0">
                        <span class="fa fa-search form-control-feedback" style="left:15px;"></span>
                        <input type="text" name='search' class="form-control search" placeholder="Search">
                    </div>
                     <div class="userBtns ml-3">
                     <a href="#" onclick="window.get_activityImport_modal()" data-toggle="tooltip" title="Import and Export Activities"> <img src="{{ asset('images/import.png') }}" /></a>
                    </a>
</div>
                </div>
            </div>
        </div>
        <div id="activity-list" class="listdetails">
            <table class="table">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th class="text-left">Batch Number</th>
                        <th class="text-left">Activity Name</th>
                        <th>Units</th>
                        <th>
                        </th>
                    </tr>
                </thead>
                <tbody id="activity-list-tbody">
                    @php
                    $sl = 1;
                    @endphp

                    @foreach($activities as $activity)

                    <tr>
                        {{-- <td><input type="checkbox" class="form-control" /></td> --}}
                        <td>{{ $sl }}</td>
                        <td class="text-left">
                            @php
                                $batch  = App\Models\Batch::find($activity->batch_id);
                                if($batch) {
                                    echo $batch->BatchName;
                                }
                            @endphp
                        </td>
                        <td class="text-left">{{ $activity->name }}</td>
                        <td>{{ $activity->unit }}</td>
                        <td><a href="{{ route('activities.edit',$activity->id) }}" data-toggle="tooltip" title="Edit"><img src="{{ asset('images/edit.png') }}" /></a></td>
                    </tr>

                    @php
                    $sl++;
                    @endphp

                    @endforeach
                </tbody>
            </table>
        </div>

        <?php
            $deletedActivities  = App\Models\Activity::onlyTrashed()->where('type', 'activity')->get();
            if( count($deletedActivities) ) {
            ?>
            <div class="squadlisthead" style="margin-top: 50px;">
                <div class="activityhead">
                    <img src="{{ asset('images/indooractivities.png') }}" />
                    <h4 class="mb-0 ml-4">Activities in Trashed</h4>
                </div>
            </div>

            <div id="trashed-activity-list" class="listdetails">
                <table class="table">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th class="text-left">Batch Number</th>
                            <th class="text-left">Activity Name</th>
                            <th>Units</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="activity-list-tbody">
                        <?php
                        $sl = 1;

                        ?>
                        @foreach($deletedActivities as $activity)

                        <tr>
                            {{-- <td><input type="checkbox" class="form-control" /></td> --}}
                            <td>{{ $sl }}</td>
                            <td class="text-left">
                                @php
                                    $batch  = App\Models\Batch::find($activity->batch_id);
                                    if($batch) {
                                        echo $batch->BatchName;
                                    }
                                @endphp
                            </td>
                            <td class="text-left">{{ $activity->name }}</td>
                            <td>{{ $activity->unit }}</td>
                            <td><a href="#" onclick="window.get_undeleteActivity({{ $activity->id }}, 'Activity'); return false;" data-toggle="tooltip" title="Restore"><img src="{{ asset('images/recover.png') }}" /></a></td>
                        </tr>

                        @php
                        $sl++;
                        @endphp

                        @endforeach
                    </tbody>
                </table>
            </div>
            <?php
            }
            ?>
    </div>
    {{-- <a href="#" class="float">
        <i class="fa fa-plus my-float"></i>
    </a> --}}
</section>

@endsection
