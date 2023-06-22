{{-- Extends layout --}}
@extends('layouts.default')

{{-- Content --}}
@section('content')

    <section id="event" class="content-wrapper_sub">
        <div class="row">
            <div class="col-md-10">
                <h4>Events Dashboard</h4>
            </div>
            <div class="col-md-2">
                 <form >
                    <div class="form-group">
                        {{--  <select class="form-control form-control-sm">
                            <option value="" disabled selected hidden>Select Branch</option>
                            <option>1</option>
                            <option>2</option>
                            <option>3</option>
                            <option>4</option>
                        </select>  --}}
                    </div>
                </form>
            </div>
        </div>
        <?php
        $annual_athletics = App\Models\Event::where('competition', 'Athletic')->get();
         $eventid = App\Models\Event::select('id')->get();
        // echo $eventid;exit;
         $eventname = App\Models\Event::select('event_name')->get();
         //

        $annual_aquatics = App\Models\Event::where('competition', 'Aquatic')->get();
        $inter_squads = App\Models\Event::where('competition', 'Squad')->get();
        ?>
        <div class="row my-2">
            <div class="col-md-6">
                <div class="auth_event_sec bg-white px-2 py-2">
                    <h6>Annual Athletic Events</h6>
                    <table class="table table-bordered" id="annual_athletics">
                        <thead>
                            <tr>
                                <td colspan="3">
                                    <div class="form-group has-search mb-0">
                                        <span class="fa fa-search form-control-feedback"></span>
                                        {{--  <input type="text" class="form-control form-control-sm" placeholder="Search">  --}}
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>Date</th>
                                <th>Event Name</th>
                                <th>Gender</th>
                            </tr>
                        </thead>

                        <tbody>
                            @if (!empty($annual_athletics))
                            @foreach ($annual_athletics as $annual_athletic)
                            <?php
                            $annual_athletics_date = App\Models\EventScheduler::where('event_id',$annual_athletic->id)->value('date');
                            ?>
                            <tr>
                                <td>{{ date('d-m-Y', substr($annual_athletics_date, 0, 10)); }}</td>
                                <td>{{$annual_athletic->event_name}}</td>
                                <td class="font-weight-bold">{{$annual_athletic->gender}}</td>
                            </tr>
                            @endforeach

                            @else

                            @endif

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-6">
            <div class="auth_event_sec bg-white px-2 py-2">
                    <h6>Annual Aquatic Events</h6>
                    <table class="table table-bordered" id="annual_Aquatic">
                        <thead>
                            <tr>
                                <td colspan="3">
                                    <div class="form-group has-search mb-0">
                                        <span class="fa fa-search form-control-feedback"></span>
                                        {{--  <input type="text" class="form-control form-control-sm" placeholder="Search">  --}}
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>Date</th>
                                <th>Event Name</th>
                                <th>Gender</th>
                            </tr>
                        </thead>

                        <tbody>
                            @if (!empty($annual_aquatics))
                            @foreach ($annual_aquatics as $annual_aquatic)
                            <?php
                            $annual_athletics_date = App\Models\EventScheduler::where('event_id',$annual_aquatic->id)->value('date');

                            ?>
                            <tr>
                                @if(!empty($annual_athletics_date))
                                <td>{{ date('d-m-Y', substr($annual_athletics_date, 0, 10)); }}</td>
                                <td>{{$annual_aquatic->event_name}}</td>
                                <td class="font-weight-bold">{{$annual_aquatic->gender}}</td>
                                @endif

                            </tr>
                            @endforeach

                            @else

                            @endif

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        {{--  <div class="row my-3">
            <div class="col-md-12">
                <div class="auth_event_sec bg-white px-2 py-2">
                    <h6>Inter - Squad Competitons</h6>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <td colspan="3">
                                    <div class="form-group has-search mb-0">
                                        <span class="fa fa-search form-control-feedback"></span>
                                        <input type="text" class="form-control form-control-sm" placeholder="Search">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>Date</th>
                                <th>Event Name</th>
                                <th>Gender</th>
                            </tr>
                        </thead>
                        <tbody>

                            @if (!empty($inter_squads))
                            @foreach ($inter_squads as $inter_squad)
                            <tr>
                                <td></td>
                                <td>{{$inter_squad->event_name}}</td>
                                <td class="font-weight-bold">{{$inter_squad->gender}}</td>
                            </tr>
                            @endforeach

                            @else

                            @endif

                        </tbody>
                    </table>
                </div>
            </div>
        </div>  --}}
    </section>

    <script>
        $('#annual_athletics').DataTable({
          "bLengthChange": false,
          language: { search: "", searchPlaceholder: "Search..." },
        })

        $('#annual_Aquatic').DataTable({
            "bLengthChange": false,
            language: { search: "", searchPlaceholder: "Search..." },
          })
            </script>

@endsection
