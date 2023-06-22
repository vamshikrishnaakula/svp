{{-- Extends layout --}}
@extends('layouts.default')

{{-- Content --}}
@section('content')
    <section id="event" class="content-wrapper_sub">
        <div class="row">
            <div class="col-md-10">
                <h4>Scheduler - View</h4>

            </div>
            <div class="col-md-2 text-right">

                <a href="/editSchedule/{{$event->id}}" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i>&nbsp; Edit</a>

            </div>
        </div>

        <div class="row my-3">
            <div class="col-md-12">
                <div class="auth_event_sec bg-white px-2 py-2">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Batch</th>
                                <th>Competition</th>
                                <th>Category</th>
                                <th>Event Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ batch_name($event->batch_id) }}</td>
                                <td>Annual {{ $event->competition }}</td>
                                <td>{{ $event->category }}</td>
                                <td>{{ $event->event_name }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="d-flex align-items-center time_Sec">
                        <div class="col-sm-3">
                            <label>Round Name</label>
                            <div><small>{{ $check_event_scheduler->roundno }}</small></div>
                        </div>
                        <div class="col-sm-1"></div>
                        <div class="col-sm-2 d-flex align-items-center justify-content-between">
                        <div class="">
                            <label>Date/Time </label>
                            <div class="form-group">
                                <div><small>{{ date('d/m/Y h:i a', $check_event_scheduler->date) }}</small></div>
                            </div>
                        </div>

                    </div>
                      <div class="col-sm-3"></div>
                        <div class="col-sm-3">
                            <label>Enter Venue Details</label>
                           <div><small>{{ $check_event_scheduler->venue }}</small></div>
                        </div>
                    </div>
                    <table id="dtBasicExample" class="table table-bordered">
                        <thead class="bg">
                            <tr>
                                <th>Roll Number</th>
                                <th>Squad</th>
                                <th>Probationer Name</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($scheduled_probationers as $scheduled_probationer )
                            <?php
                               $squad_id = App\Models\probationer::where('id', $scheduled_probationer->probationers_id)->value('squad_id');
                            ?>
                            <tr>
                            <td>{{probationer_rollnumber($scheduled_probationer->probationers_id)}}</td>
                            <td>{{squad_number((int)$squad_id)}}</td>
                            <td>{{probationer_name($scheduled_probationer->probationers_id)}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </section>

@endsection

