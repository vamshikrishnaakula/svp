{{-- Aside --}}

<aside id="sidebar-wrapper">
    <ul class="sidebar-nav" id="accordion">
        <li class="active">
            <a href="{{ url('/') }}"><img src="{{ asset('images/dashboard.png') }}" alt="dashboard" /></i><span>Dashboard</span></a>
        </li>
        <li>
            <a href="{{ url('doctor') }}"><img src="{{ asset('images/appointment.png') }}" alt="dashboard" /></i><span>Appointments</span></a>
        </li>


        {{--  <li>
                <a href="{{ url('inpatientlist') }}"><img src="{{ asset('images/hospital.png') }}" alt="dashboard" /></i><span>In Patients List</span></a>
        </li>  --}}


         <li class="usermanagement sidebar-nav-main panel" id="accordion">
		        <a href="#patients_sub" data-toggle="collapse" data-target="#patients_sub"><img src="{{ asset('images/hospital.png') }}" alt="dashboard" /></i><span>In Patients</span></a>
			      <ul id="patients_sub" class="collapse" data-parent="#accordion">
                  <li><a href="{{ url('inpatientlist') }}"></i><span>InPatients List</span></a></li>
                  {{-- <li><a href="{{ url('dischargesummary') }}"></i><span>Discharge Summary</span></a></li> --}}
            </ul>
        </li>
        <li>
            <a href="{{ url('medicalexams') }}"><img src="{{ asset('images/medical-research.png') }}" alt="dashboard" /></i><span>Medical Examination</span></a>
        </li>

        <li class="usermanagement sidebar-nav-main panel" id="accordion">
		    <a href="#add_data_sub" data-toggle="collapse" data-target="#add_data_sub"><img src="{{ asset('images/adddata.png') }}" alt="dashboard" /></i><span>Add Data</span></a>
		        <ul id="add_data_sub" class="collapse" data-parent="#accordion">
                <li><a href="{{ url('adddata') }}">Add Medical Data</a></li>
                <li><a href="{{ url('viewdata') }}">View Medical Data</a></li>
                <li><a href="{{ url('viewlabreports') }}">View LabTest Data</a></li>
                <li><a href="{{ url('/sickreport') }}">Sick reports</a></li>
            </ul>
        </li>

        <li class="hospitalization sidebar-nav-main">
            <a href="#" data-toggle="collapse" data-target="#hospitalization_sub"><img src="{{ asset('images/hospital.png') }}" alt="dashboard" /></i><span>Hospitalization</span></a>
            <ul class="collapse" id="hospitalization_sub" data-parent="#accordion">
                <li><a href="{{ url('/healthprofiles') }}">Health Profiles</a></li>
              </ul>
        </li>

    </ul>
</aside>
