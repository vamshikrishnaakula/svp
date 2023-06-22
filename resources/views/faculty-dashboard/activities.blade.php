{{-- Extends layout --}}
@extends('layouts.faculty.template')

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

                            <select name="batch_id" id="batch_id" onchange="window.get_activities_by_batch(this);" class="form-control">
                                <option value="0">Select batch...</option>
                                @if( !empty($batches) )
                                @foreach($batches as $batch)
                                <option value="{{ $batch->id }}">{{ $batch->BatchName }}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
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
                        <th></th>
                    </tr>
                </thead>
                <tbody id="activity-list-tbody">
                    <?php
                    $activities = App\Models\Activity::where('type', 'activity')->get();
                    $sl = 1;
                    ?>

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
                        <td><a href="{{ url('activity-view/'.$activity->id) }}" data-toggle="tooltip" title="View"><img src="{{ asset('images/view.png') }}" /></a></td>
                    </tr>

                    @php
                    $sl++;
                    @endphp

                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>

@endsection
