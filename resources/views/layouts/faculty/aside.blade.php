{{-- Aside --}}

<aside id="sidebar-wrapper">

      {{-- Dashboard--}}
    <ul class="sidebar-nav" id="accordion">
        <li class="active">
            <a href="{{ url('/') }}"><img src="{{ asset('images/dashboard.png') }}" alt="dashboard" /></i><span>Dashboard</span></a>
        </li>

            {{-- Timetable--}}
            <li class="timetable sidebar-nav-main">
                <a href="#" data-toggle="collapse" data-target="#timetable_sub"><img src="{{ asset('images/timetable.png') }}" alt="timetable" /></i><span>Timetable</span></a>
                <ul class="collapse" id="timetable_sub" data-parent="#accordion">
                    <li><a href="{{ url('/timetables-view') }}">View Timetable</a></li>
                    <li><a href="{{ url('/timetables-missed-classes') }}">View Missed Classes</a></li>
                  </ul>
            </li>

            {{-- Attendence Management--}}
        <li class="attendance sidebar-nav-main">
            <a href="#" data-toggle="collapse" data-target="#attendance_sub"><img src="{{ asset('images/attendance.png') }}" alt="dashboard" /></i><span>Attendance Management</span></a>
            <ul class="collapse" id="attendance_sub" data-parent="#accordion">
                <li><a href="{{ url('/attendance-monthly-report') }}">Attendance Report</a></li>
                <li><a href="{{ url('/attendance-monthly-sessions') }}">Monthly sessions</a></li>
                <li><a href="{{ url('/attendance-missed-sessions') }}">Missed Sessions</a></li>
            </ul>
        </li>

        {{-- Statistics--}}
        <li class="timetable sidebar-nav-main">
            <a href="#" data-toggle="collapse" data-target="#Statistics_sub"><img src="{{ asset('images/statistics.png') }}" alt="dashboard" /></i><span>Statistics</span></a>
            <ul class="collapse" id="Statistics_sub" data-parent="#accordion">
                <li><a href="{{ url('reports') }}">Overall Reports</a></li>
                <li><a href="{{ url('reports/classes-conduct-report') }}">Classes Conduct Report</a></li>
                <li><a href="{{ url('reports/missed-classes-report') }}">Missed Classes Reports</a></li>
                <li><a href="{{ url('reports/extra-classes-report') }}">Extra Classes Reports</a></li>
                <li><a href="{{ url('reports/pass-fail-report') }}">Pass/Fail Reports</a></li>
                <li><a href="{{ url('charts') }}">Charts</a></li>
                 <li><a href="{{ url('compare-probationers') }}">Compare Probationers</a></li>

                {{--  <li><a href="{{ url('compare-fitnessanalysis') }}">Fitness Analytics</a></li>  --}}
              </ul>
        </li>

        {{-- Fitness--}}
        <li class="fitnessevaluvation sidebar-nav-main">
            <a href="#" data-toggle="collapse" data-target="#FitnessEval_sub">
                <img src="{{ asset('images/running.png') }}" alt="dashboard" />
                </i><span style="padding-left:12px">Fitness Evaluation</span>
            </a>
            <ul class="collapse" id="FitnessEval_sub" data-parent="#accordion">
                <li><a href="{{ url('/fitnessanalytics') }}">Fitness Analytics</a></li>
                <li><a href="{{ url('compare-fitnessanalysis') }}">Fitness Statistics</a></li>
              </ul>
        </li>

         {{-- Activities--}}
         <li class="activities sidebar-nav-main">
            <a href="{{ url('/activity-list') }}"><img src="{{ asset('images/activity.png') }}" alt="dashboard" /></i><span>Activities</span></a>
        </li>

          {{-- User Managment--}}
        <li class="usermanagement sidebar-nav-main panel" id="accordion">
            <a href="#usermenu_sub" data-toggle="collapse" data-target="#usermenu_sub"><img src="{{ asset('images/user.png') }}" alt="dashboard" /></i><span>User Management</span></a>
            <ul id="usermenu_sub" class="collapse" data-parent="#accordion">
                  <li><a href="{{ url('squad-list') }}">Squad List</a></li>
                  <li><a href="{{ url('staff-list') }}">Staff List</a></li>
                  <li><a href="{{ url('probationer-list') }}">Probationers List</a></li>
            </ul>
        </li>


        {{-- <li class="events sidebar-nav-main">
            <a href="#" data-toggle="collapse" data-target="#events_sub"><img src="{{ asset('images/event.png') }}" alt="dashboard" /></i><span>Events</span></a>
            <ul class="collapse" id="events_sub" data-parent="#accordion">
                <li><a href="#createevent">Create Event</a></li>
                <li><a href="#editevent">Edit Event</a></li>
                <li><a href="#outdooreevents">Outdoor Events</a></li>
            </ul>
        </li> --}}


          {{-- Hospitalization--}}
        <li class="hospitalization sidebar-nav-main">
            <a href="#" data-toggle="collapse" data-target="#hospitalization_sub"><img src="{{ asset('images/hospital.png') }}" alt="dashboard" /></i><span>Hospitalization</span></a>
            <ul class="collapse" id="hospitalization_sub" data-parent="#accordion">
                <li><a href="{{ url('/patient-list') }}">Patient List</a></li>
                <li><a href="{{ url('/health-profile') }}">Health Profiles</a></li>
                {{-- <li><a href="{{ url('/discharge-summary') }}">Discharge Summary</a></li> --}}
                <li><a href="{{ url('/medical-records') }}">Medical Records</a></li>
                <li><a href="{{ url('/medical-examination') }}">Medical Examination</a></li>
              </ul>
        </li>
        {{-- <li>
            <a href="{{ url('/fitness-evaluation') }}"><img src="{{ asset('images/running.png') }}" alt="dashboard" /></i><span>Fitness Evaluation</span></a>
        </li> --}}

        {{-- Personal Notes--}}
        <li class="batchandsquads sidebar-nav-main panel">
            {{--  <a href="{{ url('/notes') }}"><img src="{{ asset('images/notes_icon.png') }}" alt="icon" /></i><span>Personal Notes</span></a>  --}}
            <a href="#" data-toggle="collapse" data-target="#notes_sub"><img src="{{ asset('images/notes_icon.png') }}" alt="dashboard" /></i><span>Personal Notes</span></a>
            <ul class="collapse" id="notes_sub" data-parent="#accordion">
                <li><a href="{{ url('/notes') }}">Personal Notes</a></li>
                <li><a href="{{ url('/generalassesment') }}">General Assesment</a></li>
              </ul>
        </li>
    </ul>
</aside>
