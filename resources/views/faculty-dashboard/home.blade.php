{{-- Extends layout --}}
{{-- @extends('layouts.faculty.template') --}}

<?php
$role = Auth::user()->role;
//echo $role;exit;
if($role === 'faculty') {
    $template   = 'layouts.faculty.template';
} else {
    $template   = 'layouts.default';
}
$app_view = session('app_view');
?>

@extends(($app_view) ? 'layouts.pbdash.mobile-template' : $template)

{{-- Content --}}
@section('content')

<section id="dashboard" class="content-wrapper tab-content">

    <div class="row">
        <div class="col-md-6">
            <div class="home-widget notifications">
                <h5>Notifications</h5>
                <ul class="circular">
                    <?php
                        $Notifications  = App\Models\Notification::orderBy('id', 'desc')->limit(10)->get();
                    ?>

                    @if(count($Notifications)>0)

                        @foreach ($Notifications as $notification)
                            <li><a href="{{ url('/notifications')}}">{{ $notification->title }}</a> </li>
                        @endforeach
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
        <div class="col-md-6">
            <div class="home-widget">
                <h5>Probationers Absent List</h5>
                <ul class="circular">
                    <?php
                    $today  = date('Y-m-d');
                    $Probationers   = App\Models\probationer::whereNotIn('probationers_dailyactivity_data.attendance', ['P', 'MDO', 'NCM'])
                        ->whereDate('timetables.date', $today)
                        ->join('probationers_dailyactivity_data', 'probationers.id', '=', 'probationers_dailyactivity_data.probationer_id')
                        ->join('timetables', 'probationers_dailyactivity_data.timetable_id', '=', 'timetables.id')
                        ->select('probationers.id', 'probationers.batch_id', 'probationers.Name')
                        ->groupBy('probationers.id')
                        ->get();

                    if( count($Probationers) > 0 ) {
                        foreach ($Probationers as $Probationer) {
                            $Batch  = batch_name($Probationer->batch_id);
                            echo "<li><span>{$Probationer->Name}</span><span>(Batch: {$Batch})</span></li>";
                        }
                    } else {
                        echo "<li class=\"no-result\"><div class=\"msg msg-info msg-full text-left\">No result found</div></li>";
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-6">
            <div class="home-widget">
                <h5>Probationers Visited Hospital</h5>
                <ul class="circular">
                    
                    <?php
                   $today_appoinments = App\Models\Appoinments::whereDay('appoinments.Appoinment_Time', now()->day)
                    ->leftJoin('probationers', 'appoinments.Probationer_Id', '=', 'probationers.id')
                    ->leftJoin('users', 'appoinments.Doctor_Id', '=', 'users.id')
                    ->select('probationers.Name','probationers.RollNumber','probationers.id', 'appoinments.Appoinment_Time', 'users.name as user_name', 'appoinments.id as appoinmentid', 'Status')
                    ->get();

                    //echo $today_appoinments;exit;

                    if( count($today_appoinments) > 0 ) {
                        foreach ($today_appoinments as $today_appoinments) {
                            //$Batch  = batch_name($Probationer->batch_id);
                            echo "<li><span>{$today_appoinments->Name}</span></li>";
                        }
                    } else {
                        echo "<li class=\"no-result\"><div class=\"msg msg-info msg-full text-left\">No Probationers Visited Hosiptal</div></li>";
                    }

                    ?>

                </ul>
            </div>


        </div>
        <div class="col-md-3">
            <div class="home-widget birthday">
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
                            @if( count($users) > 0)
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->role }}</td>
                                </tr>
                            @endforeach
                            @else
                                <tr class="no-result">
                                    <td colspan="2"><div class="msg msg-info msg-full text-left">No result found</div></td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
