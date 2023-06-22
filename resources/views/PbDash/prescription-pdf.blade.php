<!DOCTYPE html>
<html>
<head>
    <title>{{ $pdf_title }}</title>
    <style>

        body {
            font-family: "Poppins", sans-serif;
        }

        h4 {
            font-family: "Poppins", sans-serif;
        }
        .content-wrapper {
            padding: 30px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        table thead tr th,
        table tbody th {
            font-family: "Red Hat Text", sans-serif !important;
        }

        .sams-logo img {
            width: 60px;
        }

        .half-width {
            width: 50%!important;
        }

        .full-width {
            width: 100%!important;
        }

        .col-left {
            float: left;
        }

        .col-right {
            text-align: right;
        }


        td, th {
        /* border: none; */
        /* border: 1px solid #83878D; */
        text-align: left;
        padding: 8px;
        }

        .medicine-table td, th {
        border: 1px solid #dadee4;
        }

        hr {
            border: 1px solid #83878D;
            border-radius: 3px;
        }

        .vital-signs,
        .lab-tests,
        .medication {
            margin-top: 25px;
            padding-left: 10px;
        }

        .last-hr {
            margin-top: 30px;
        }


    </style>
</head>
<body>

    <section class="content-wrapper">
        <div class="sams-logo">
            {{-- <a class="navbar-brand"><img src="{{ asset('images/logo1.jpeg') }}" alt="dashboard" /></i></a> --}}
            {{-- <a class="navbar-brand" href="{{ url('/') }}"><img src="{{ asset('images/logo1.jpeg') }}" alt="dashboard" /></i></a> --}}
            
        </div>
        <div class="prescription-sections">
            <?php
            
                $Probationer = App\Models\probationer::where('id', $Probationer)->first();
                $Appointments = DB::table('appoinments')->where('id', $id)->first();
                $Prescriptions = DB::table('probationer_prescription')
                                ->where('appointment_id', $id)
                                ->get();
                $vitalsign = DB::table('probationer_vitalsign')
                                ->where('appointment_id', $id)
                                ->first();
                $labreports = DB::table('probationer_labreports')
                                ->where('appointment_id', $id)
                                ->get();
                $doctor_advices = DB::table('probationer_vitalsign')
                                ->where('appointment_id', $id)
                                ->get();
                $Doctor_id = $Appointments->Doctor_Id;
                $Doctor_users = DB::table('users')->where('id', $Doctor_id)->first();
            ?>
            <div class="patient-details">
                <div class="patient-details-title full-width">
                    <table class="table">
                        <tbody>
                            <tr class="full-width">
                                <td class="col-left half-width"><h4>Patient Details</h4></td>
                                <td class="col-right half-width"><p><b> Date :</b> {{ date('d-m-Y', strtotime($Appointments->Appoinment_Time)) }}, {{ date('h:i a', strtotime($Appointments->Appoinment_Time)) }}</p></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <hr>
                <table class="table">
                    <tbody>
                        <tr>
                            <td><b>Patient Id :</b> {{ isset($Probationer->RollNumber) ? $Probationer->RollNumber : '' }}</td>
                            <td><b>Gender :</b>{{ isset($Probationer->gender) ? $Probationer->gender : '' }}</td>
                            <td><b>Doctor Name :</b> {{ isset($Doctor_users->name) ? $Doctor_users->name : '' }}</td>
                        </tr>
                        <tr>
                            <td><b>Patient Name :</b> {{ isset($Probationer->Name) ? $Probationer->Name : '' }}</td>
                            <td><b>Symptoms :</b> {{ isset($Appointments->Symptoms) ? $Appointments->Symptoms : '' }}</td>
                            <td><b>Reg Id :</b> {{ isset($Appointments->id) ? $Appointments->id : '' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="vital-signs">
                <div class="text-left">
                    <h4>Vital Signs</h4>
                </div>
                <hr>
                <div>
                    <p>{{ isset($vitalsign->vitalsign) ? $vitalsign->vitalsign : ''}}</p>
                </div>
            </div>
            <div class="lab-tests">
                <div class="text-left">
                    <h4>Lab Tests</h4>
                </div>
                <hr>
                <table class="table">
                    <tbody>
                        <tr>
                        
                        @foreach ($labreports as $labreport)
                            <td>{{ isset($labreport->labreports) ? $labreport->labreports : ''}}</td>
                        @endforeach
                        
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="medication">
                <div class="text-left">
                    <h4>Medication</h4>
                </div>
                <hr>
                <div class="medicine-table">
                    <table class="table text-center">
                        <thead>
                            <tr>
                                <th style="width:18%;">Drug</th>
                                <th style="width:18%;">Dosage</th>
                                <th style="width:18%;">Frequency</th>
                                <th style="width:18%;">Duration</th>
                                <th style="width:28%;">Instructions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($Prescriptions as $Prescription)
                                <tr>
                                    <td>{{ $Prescription->drug }}</td>
                                    <td>{{ $Prescription->dosage }}</td>
                                    <td>{{ $Prescription->frequency }}</td>
                                    <td>{{ $Prescription->duration }}</td>
                                    <td>{{ $Prescription->instructions }}</td>
                                </tr>
                            @endforeach

                            <tr>


                        </tbody>
                    </table>
                </div>
            </div>

            <div class="lab-tests">
                <div class="text-left">
                    <h4>Doctor Advice</h4>
                </div>
                <hr>
                <table class="table">
                    <tbody>
                        <tr>
                        
                        @foreach ($doctor_advices as $doctor_advice)
                            <td>{{ isset($doctor_advice->doctor_advice) ? $doctor_advice->doctor_advice : ''}}</td>
                        @endforeach
                        
                        </tr>
                    </tbody>
                </table>
            </div>


            <hr class="last-hr">
        </div>
    </section>

</body>
</html>





