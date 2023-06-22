{{-- Aside --}}

<aside id="sidebar-wrapper">
    {{-- <div class="sidebar-brand">
        <a href="{{ url('/') }}"><img src="{{ asset('images/svpnpa.png') }}" alt="dashboard" /></i></a>
        <span>Sardar Vallabhbhai Patel <br> National Police Academy</span>
    </div> --}}
    <ul class="sidebar-nav" id="accordion">
        <li class="active">
            <a href="{{ url('/') }}"><img src="{{ asset('images/dashboard.png') }}" alt="dashboard" /></i><span>Dashboard</span></a>
        </li>
        <li class="usermanagement sidebar-nav-main panel">
            <a href="#" data-toggle="collapse" data-target="#mytarget_sub"><img src="{{ asset('images/target.png') }}" alt="dashboard" /></i><span>My Targets</span></a>
            <ul class="collapse" id="mytarget_sub" data-parent="#accordion">
                <li><a href="{{ url('/user-mytarget') }}">Set Goals</a></li>
                <li><a href="{{ url('/mytarget-view') }}">View Targets</a></li>
            </ul>
        </li>
        <li class="attendance sidebar-nav-main">
            <a href="{{ url('/user-attendance') }}"><img src="{{ asset('images/attendance.png') }}" alt="dashboard" /></i><span>Attendance</span></a>
        </li>
        <li class="timetable sidebar-nav-main panel">
            <a href="#" data-toggle="collapse" data-target="#timetable_sub"><img src="{{ asset('images/timetable.png') }}" alt="dashboard" /></i><span>Timetable</span></a>
            <ul class="collapse" id="timetable_sub" data-parent="#accordion">
                <li><a href="{{ url('/user-timetable') }}">View Timetable</a></li>
                <li><a href="{{ url('/view-extrasession') }}">View Extra Session</a></li>
            </ul>
        </li>
        <li class="hospitalization sidebar-nav-main">
            <a href="{{ url('/user-hospitalization') }}"><img src="{{ asset('images/hospital.png') }}" alt="dashboard" /></i><span>Hospitalization</span></a>
        </li>
        <li class="healthprofiles sidebar-nav-main">
            <a href="{{ url('/user-healthprofiles') }}"><img src="{{ asset('images/healthprofiles.png') }}" /><span>Health Profiles</span></a>
        </li>
        <li class="fitnessanalytics sidebar-nav-main panel">
            {{-- <a href="#" data-toggle="collapse" data-target="#timetable_sub"><img src="{{ asset('images/timetable.png') }}" alt="dashboard" /></i><span>Timetable</span></a> --}}
            <a href="#" data-toggle="collapse" data-target="#fitness_sub"><img src="{{ asset('images/running.png') }}" alt="dashboard" /></i><span>Fitness Evaluation</span></a>
                <ul class="collapse" id="fitness_sub" data-parent="#accordion">
                    <li><a href="{{ url('/user-fitnessanalytics') }}">Fitness Analytics</a></li>
                    {{-- <li><a href="{{ url('/user-general-assesment-data') }}">General Assesment</a></li> --}}
                </ul>
            {{-- <a href="{{ url('/user-fitnessanalytics') }}"><img src="{{ asset('images/running.png') }}" alt="dashboard" /></i><span>Fitness Evaluvation</span></a> --}}
        </li>
        <li class="statistics sidebar-nav-main panel">
            {{-- <a href="{{ url('/user-statistics') }}"><img src="{{ asset('images/statistics.png') }}" alt="dashboard" /></i><span>Statistics</span></a> --}}
            <a href="#" data-toggle="collapse" data-target="#statistics_sub"><img src="{{ asset('images/statistics.png') }}" alt="dashboard" /></i><span>Statistics</span></a>
            <ul class="collapse" id="statistics_sub" data-parent="#accordion">
                <li><a href="{{ url('/user-statistics') }}">Report</a></li>
                {{--  <li><a href="{{ url('/user-monthlyreport') }}">Monthly Report</a></li>  --}}
              {{-- <li><a href="{{ url('/squadperformance') }}">Squad Performance</a></li>
              <li><a href="{{ url('/individualperformance') }}">Individual Performance</a></li> --}}
            </ul>
        </li>
    </ul>
</aside>
