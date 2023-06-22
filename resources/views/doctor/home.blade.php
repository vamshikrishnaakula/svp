{{-- Extends layout --}}
@extends('layouts.doctor.template')

{{-- Content --}}
@section('content')

<section id="dashboard" class="content-wrapper tab-content">
    @php
        $user_id = auth()->id();
    @endphp
    <div class="row">
        <div class="col-md-4">
            {{-- ------------ Today's Appointments ------------ --}}
            <div class="home-widget todays-appointments mb-4">
                <h5>Todays Appointments</h5>
                <div class="circular">
                    <?php
                        $appointments    = App\Models\Appoinments::where('Doctor_Id', $user_id)
                             ->whereDate('Appoinment_Time', date('Y-m-d'))
                            ->orderBy('Appoinment_Time', 'asc')->get();
                    ?>

                    @if(count($appointments)>0)
                        <table class="table table-stripped">
                            <tbody>
                            @foreach ($appointments as $appointment)
                                @php
                                    $appointment_id = $appointment->id;
                                    $patient        = probationer_name($appointment->Probationer_Id);
                                    $time           = date('H:i', strtotime($appointment->Appoinment_Time));
                                    $appointment_url    = url("/appointment_summary/{$appointment_id}");
                                    $editprescription = url("/edit_prescriptions/{$appointment_id}");
                                @endphp
                                <tr>
                                    <td>{{ $patient }}</td>
                                    <td>{{ $time }}</td>
                                    @if ($appointment->Status === 'Close')
                                    <td class="text-right">
                                        <a href="{{ $appointment_url }}" data-toggle="tooltip" title="View"><img src="{{ asset('/images/view.png') }}"></a>
                                        <a href="{{ $editprescription }}" data-toggle="tooltip"
                                            title="Edit"> <img src="{{ asset('/images/edit.png') }}" /></a>

                                            {{-- <a href="{{ route('probationers.edit',$probationer->id) }}" data-toggle="tooltip"
                                                title="Edit"> <img src="/images/edit.png" /></a> --}}
                                    </td>
                                    @endif

                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                    <div class="notification-item">
                        <div class="msg msg-info msg-full text-left">
                            No appointments today.
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- ------------ Notifications ------------ --}}

        </div>

        <div class="col-md-4">
            {{-- ------------ In Patients List ------------ --}}
            <div class="home-widget todays-appointments mb-4" style="background: #bbd3e8;">
                <h5>In Patients List</h5>
                <div class="circular" style="height: 250px;">
                    <?php
                        $patients = App\Models\InPatient::where('status', 'open')
                            ->orderBy('admitted_date', 'asc')->limit(10)->get();
                    ?>

                    @if(count($patients)>0)
                        <table class="table table-stripped">
                            <tbody>
                            @foreach ($patients as $patient)
                                @php
                                    $appointment_id = $patient->appointment_id;
                                    $patient_name   = probationer_name($patient->probationer_id);
                                    $date           = date('d-m-Y', strtotime($patient->admitted_date));
                                    $summery_url    = url("/inpatientprescription/{$appointment_id}");
                                @endphp
                                <tr>
                                    <td>{{ $patient_name }}</td>
                                    <td>{{ $date }}</td>
                                    <td class="text-right">
                                        <a href="{{ $summery_url }}" data-toggle="tooltip" title="View"><img src="{{ asset('/images/view.png') }}"></a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                    <div class="notification-item">
                        <div class="msg msg-info msg-full text-left">
                            No patients found.
                        </div>
                    </div>
                    @endif
                </div>
            </div>


            <div class="home-widget birthday">
                <div class="birthday-title">
                    <a href="#" ><img src="{{ asset('images/birthday.png') }}" alt="dashboard" /></i><span>Happy Birthdays</span></a>
                </div>

                <div class="birthday-table" style="height: 200px;">
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
        <div class="col-md-4">

            <div class="home-widget notifications">
                <h5>Notifications</h5>
                <ul class="circular">
                    <?php
                        $Notifications  = App\Models\Notification::query()
                            ->whereRaw("recipient_type IS NULL OR recipient_type IN ('', '0', 'doctor')")
                            ->orderBy('id', 'desc')->limit(10)->get();
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
    </div>
</section>

@endsection
