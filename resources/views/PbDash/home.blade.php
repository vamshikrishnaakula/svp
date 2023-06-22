{{-- Extends Pb Dashboard Template --}}
@extends('layouts.pbdash.template')

{{-- Content --}}
@section('content')

<section id="dashboard" class="content-wrapper tab-content">
    <?php
        $userData = auth()->user();
        $pb_data   = App\Models\probationer::where('user_id', $userData->id)->first();
    ?>


    <div class="row h-50">
        <div class="col-md-4">
            <div class="notifications">
                <h5>Notifications</h5>
                <ul class="circular">
                    <?php
                        $Notifications  = [];
                        if(!empty($pb_data)) {

                            $Notifications = App\Models\Notification::where('recipient_type','probationer')->get();
                        }
                    ?>

                    @if(count($Notifications)>0)

                    <div>
                        @foreach($Notifications as $Notificationss)
                        <div class="notification-item read-notification">
                            <div class="notification-title-bar">
                                <h5 class="notification-title">{{ $Notificationss->title }}</h5>
                                {{-- <p class="notification-timestamp">{{ $createdAt }}</p> --}}
                            </div>
                            <div class="notification-message">{{ $Notificationss->message }}</div>
                        </div>
                     @endforeach

                     </div>
                    @else
                    <div class="notification-item">
                        <div class="msg msg-info msg-full text-left">
                            No notification found
                        </div>
                    </div>
                    @endif

                </ul>
            </div>
        </div>
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-6">
                    {{-- ------------------------------- Total Attended Session   ----------------------------}}

                    <div class="total-attended-session">
                        <div class="total-attendance-text">
                            <?php
                            $c_year = date('Y');
                            $c_month = date('m');

                            $pb_id = probationer_id();
                            $attendance = DB::table('probationers_dailyactivity_data')
                                        ->where('probationer_id', $pb_id)
                                        ->whereYear('date', $c_year)
                                        ->whereMonth('date', $c_month)
                                        ->whereIn('attendance', ['P', 'MDO', 'NCM'])
                                        ->groupBy('timetable_id')
                                        ->get();
                            ?>
                            {{-- <span class="">Total</span> --}}
                            <h5 class="">Session Attended</h5>
                        </div>
                        <div class="circle">
                            <div class="circle-content">{{ count($attendance) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    {{-- ------------------------------- Total Missed Session   ----------------------------}}

                    <div class="total-missed-session">
                        <div class="total-missed-text">
                            <?php
                            $c_year = date('Y');
                            $c_month = date('m');

                            $pb_id = probationer_id();
                            $squad_id = squad_id($pb_id);

                            $total = App\Models\Timetable::where('squad_id', $squad_id)
                                        ->whereYear('date', $c_year)
                                        ->whereMonth('date', $c_month)
                                        ->whereNotNull('activity_id')
                                        ->where("session_start", '>', 0)
                                        ->count();
                            $absent = $total - count($attendance);
                            ?>
                            {{-- <span class="">Total</span> --}}
                            <h5 class="pt-2">Session Missed</h5>
                        </div>
                        <div class="circle">
                            <div class="circle-content">{{ $absent }}</div>
                        </div>

                    </div>
                </div>
                <div class="row p-4">
                    <div class="col-md-6">
                        <div class="pbd-schedule">
                            <?php
                            $squad_id = isset($pb_data->squad_id);

                            $date = date('Y-m-d');
                            $timetables = App\Models\Timetable::where('squad_id', $squad_id)
                                    ->whereDate('date', $date)
                                    ->where('session_type', 'regular')
                                    ->orderBy('session_number', 'asc')->get();
                            ?>
                            <div class="schedule-title mb-3">
                                <a href="{{ url('/user-timetable') }}" ><img src="{{ asset('images/calendar-homepage.png') }}" alt="dashboard" /></i><span>Today s Schedule</span></a>
                            </div>
                            <div class="schedule-table">
                            <table class="table">
                                <tbody>
                                    <?php
                                        $i = 1;
                                    ?>
                                    @if(count($timetables)>0)
                                        @foreach ($timetables as $timetable)
                                            <tr>
                                                <td>Session {{ $i }}</td>
                                                <td>
                                                    <?php
                                                        if (!empty($timetable->activity_id)) {
                                                            $activity_id = $timetable->activity_id;
                                                            $subactivity_id = $timetable->subactivity_id;
                                                            if(!empty($subactivity_id)) {
                                                                echo activity_name($subactivity_id);
                                                            } else {
                                                                echo activity_name($activity_id);
                                                            }
                                                        }
                                                        else {
                                                            echo "--";
                                                        }
                                                    ?>
                                                </td>
                                            </tr>
                                            <?php $i++; ?>
                                        @endforeach
                                    @else
                                    <div class="msg msg-info msg-full text-left">
                                        No schedule for today
                                    </div>
                                    @endif
                                </tbody>
                            </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="birthday">
                            <div class="birthday-title">
                                <a href="#" ><img src="{{ asset('images/birthday.png') }}" alt="dashboard" /></i><span>Happy Birthdays</span></a>
                            </div>
                            <div class="birthday-table">
                                <table class="table">
                                    <?php
                                        $day = date('d');
                                        $month = date('m');
                                        $users = DB::table('users')
                                                ->whereMonth('Dob', $month)
                                                ->whereDay('Dob', $day)
                                                ->get();
                                    ?>
                                    <tbody>
                                        @foreach ($users as $user)
                                            <tr>
                                                <td>{{ $user->name }}</td>
                                                <td>{{ $user->role }}</td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</section>

@endsection
