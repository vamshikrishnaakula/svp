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
                            <th>Missed</th>
                            <th>Created Classes</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($Probationers as $Probationer)
                        <?php

                        $extraSessionPbId  = \App\Models\ExtraSessionmeta::where('probationers.batch_id', $request->batch_id)
                        ->whereIn('extra_sessionmetas.attendance', ['P', 'MDO', 'NCM'])
                        ->whereNotNull('extra_sessionmetas.timetable_id')
                        ->where('extra_sessionmetas.probationer_id', $Probationer->id)
                        ->join('probationers', 'probationers.id', '=', 'extra_sessionmetas.probationer_id')
                        ->pluck('extra_sessionmetas.timetable_id')->toArray();

                        $probationerM  = \App\Models\probationer::query()
                                ->where('probationers.batch_id', $request->batch_id)
                                ->where('timetables.activity_id', $request->activity_id)
                                ->where('timetables.subactivity_id', $request->subactivity_id)
                                ->where('probationers.id', $Probationer->id)
                                ->whereNotIn('timetables.id', $extraSessionPbId)
                                ->whereNotIn('probationers_dailyactivity_data.attendance', ['P', 'MDO', 'NCM'])
                                ->join('probationers_dailyactivity_data', 'probationers.id', '=', 'probationers_dailyactivity_data.probationer_id')
                                ->join('timetables', 'probationers_dailyactivity_data.timetable_id', '=', 'timetables.id');

                                $Probationers_count  = $probationerM->groupBy('probationers_dailyactivity_data.timetable_id')->get();

                                $Probationer_count = count($Probationers_count);
                            $missed_sessions = $probationerM->where('probationers.id', $Probationer->id)
                            ->selectRaw("probationers_dailyactivity_data.date, timetables.session_number, probationers_dailyactivity_data.activity_id, probationers_dailyactivity_data.subactivity_id, probationers_dailyactivity_data.component_id")->groupBy('probationers_dailyactivity_data.timetable_id')->get();
                            $count = \App\Models\ExtraSession::join('extra_sessionmetas', 'extra_sessionmetas.extra_session_id', '=', 'extra_sessions.id')->where('activity_id', $request->activity_id)
                            ->where('subactivity_id', $request->subactivity_id)->where('extra_sessionmetas.probationer_id', $Probationer->id)->whereNull('extra_sessionmetas.attendance')->count();

                        ?>

                        @if (!empty($Probationer_count))
                        <tr id="extra-session-pb-tr-{{ $Probationer->id }}" class="extra-session-pb-tr" data-pb-id="{{ $Probationer->id }}">
                            @if ($count == $Probationer_count)
                                <td></td>
                            @else
                            <td><input type="checkbox" name="extra_session_pb_cb" value="{{ $Probationer->id }}" class="form-control extra-session-pb-cb" /></td>
                            @endif

                            <td>{{ $Batch->BatchName }}</td>
                            <td>{{ $Probationer->RollNumber }}</td>
                            <td>{{ $Probationer->Name }}</td>
                            <td>{{ $Probationer_count }}</td>
                            <td>{{ $count  }}</td>
                            {{--  <td><a href="#" data-toggle="modal" id="missed_classes_link"
                                data-target="#missed_classes_link{{$Probationer->id}}">view</a></td>  --}}


                                <td><a href="#"   data-placement="right" , data-toggle="popover" data-popover-target="#missed-{{ $Probationer->id }}"
                                 data-html="true"
                                data-content='
                                <table class="table popover__table" id="missed-{{ $Probationer->id }}">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Activity</th>
                                            <th>Sub Activity</th>
                                            <th>Component</th>
                                            <th>Session number</th>
                                        </tr>
                                    </thead>
                                    @foreach ($missed_sessions as $missed_session)
                                        <tr>
                                            <td>{{  date("d-m-Y", strtotime($missed_session->date)) }}</td>
                                            <td>{{ activity_name($missed_session->activity_id) }}</td>
                                            <td>{{ activity_name($missed_session->subactivity_id) }}</td>
                                            <td>{{ (isset($missed_session->component_id)) ? "-" : activity_name($missed_session->component_id)}}</td>
                                            <td>{{ $missed_session->session_number }}</td>
                                        </tr>
                                    @endforeach
                                </table>'
                                >View</a></td>

                        </tr>
                        @endif
                        <div class="modal fade" id="missed_classes_link{{$Probationer->id}}"
                            ... >
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
                        <th>Missed</th>
                        <th>Created Classes</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="no-data-tr">
                        <td colspan="5">No data available in table</th>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
</div>

<style>
 .popover {
        max-width: 100% !important;
    }

  .popover-body{
      padding:2px;
      margin-bottom:-15px;
  }
.popover__table tr th,
.popover__table tr td {
    padding: 5px;
    font-size: 0.8rem;
    border:1px solid #f2f2f2;

}
.popover {
    padding:0px;
    height:max-content;
}
</style>

<script>

    $(function() {
        $('[data-toggle="popover"]').each(function(i, obj) {
          var popover_target = $(this).data('popover-target');
          $(this).popover({
              html: true,
              trigger: 'focus',
              content: function(obj) {
                  return $(popover_target).html();
              }
          });
        });
      });
</script>
