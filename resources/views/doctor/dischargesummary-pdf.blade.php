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
                $Appointments = DB::table('appoinments')->where('in_patients.id', $id)
                                     ->leftJoin('in_patients', 'in_patients.appointment_id', '=', 'appoinments.id')
                                     ->select('appoinments.Doctor_Id', 'appoinments.Appoinment_Time', 'appoinments.Symptoms', 'appoinments.id')
                                     ->first();
                $Prescriptions = DB::table('probationer_prescription')
                                ->where('appointment_id', $id)
                                ->get();
                $vitalsign = DB::table('probationer_vitalsign')
                                ->where('appointment_id', $id)
                                ->first();
                $labreports = DB::table('probationer_labreports')
                                ->where('appointment_id', $id)
                                ->get();


                $Doctor_id = $Appointments->Doctor_Id;
                $Doctor_users = DB::table('users')->where('id', $Doctor_id)->first();

                $count1 = DB::table('probationer_inpatient_prescription')
                        ->where('inpatient_id', $id)
                        ->where('prescription_number', '!=',  '0')
                        ->groupBy('prescription_number')->get();
                $dicharge_medication = DB::table('probationer_inpatient_prescription')
                        ->where('inpatient_id', $id)
                        ->where('prescription_number', '=',  '0')
                        ->get();
           if(count($count1) != '0')
           {
            for ($n = 1; $n <= count($count1); $n++) {
                    $procedure = DB::table('probationer_inpatient_procedure')
                    ->where('inpatient_id', $id)->where('prescription_number', $n)
                    ->get();

                    $in_labreports = DB::table('probationer_inpatient_labreports')
                    ->where('inpatient_id', $id)->where('prescription_number', $n)
                    ->get();
                    $in_prescription = DB::table('probationer_inpatient_prescription')
                    ->select(DB::raw('*'))
                    ->where('inpatient_id', $id)->where('prescription_number', $n)
                    ->get();
                    if($procedure != '')
                    {
                            $probationer_inpatient_procedure[] = array(
                            "date" => $procedure[0]->created_at,
                                "procedure" => $procedure
                            );
                    }
                    if($in_labreports != '')
                    {
                    $probationer_inpatient_labreports[] = array(
                        "date" => $in_labreports[0]->created_at,
                        "labreports" => $in_labreports
                    );
                    }
                    if($in_prescription != '')
                    {
                    $probationer_inpatient_prescription[] = array(
                        "date" => $in_prescription[0]->created_at,
                        "prescription" => $in_prescription
                    );
                    }
              }
           }

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
                            <td><b>Patient Id :</b> {{ $Probationer['RollNumber'] }}</td>
                            <td><b>Gender :</b>{{ $Probationer['gender'] }}</td>
                            <td><b>Doctor Name :</b> {{ $Doctor_users->name }}</td>
                        </tr>
                        <tr>
                            <td><b>Patient Name :</b> {{ $Probationer['Name'] }}</td>
                            <td><b>Symptoms :</b> {{ $Appointments->Symptoms }}</td>
                            <td><b>Reg Id :</b> {{ $Appointments->id }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="vital-signs">
                <div class="text-left">
                    <h4>Procedure</h4>
                </div>
                <hr>

            @if (!empty($probationer_inpatient_procedure))
                  @foreach ($probationer_inpatient_procedure as $records)
                    <h4>{{ date('d-m-Y h:i a', strtotime($records['date'])) }}</h4>
                        @foreach ($records['procedure'] as $record)
                             <div>
                            <p>{{ isset($record->procedure) ? $record->procedure : ''}}</p>
                        </div>
                        @endforeach
                @endforeach
             @else
                <p>No Procedure</p>
            @endif
                </div>
            <div class="lab-tests">
                <div class="text-left">
                    <h4>Lab Tests</h4>
                </div>
                <hr>

        @if (!empty($probationer_inpatient_labreports))
              @foreach ($probationer_inpatient_labreports as $probationer_inpatient_labreport)
                             <h4>{{ date('d-m-Y h:i a', strtotime($probationer_inpatient_labreport['date'])) }}</h4>
                                <table class="table">
                                <tbody>
                                    <tr>
                            @foreach ($probationer_inpatient_labreport['labreports'] as $labreport)
                                 <td>{{ isset($labreport->labreports) ? $labreport->labreports : ''}}</td>
                            @endforeach
                            </tr>
                    </tbody>
                </table>
             @endforeach
             @else
                <p>No Lab Test prescribed</p>
           @endif




            </div>

            <div class="medication">
                <div class="text-left">
                    <h4>Medication</h4>
                </div>
                <hr>
                <div class="medicine-table">

                    @if (!empty($probationer_inpatient_prescription))
                         @foreach ($probationer_inpatient_prescription as $pre)
                        <h4>{{ date('d-m-Y h:i a', strtotime($pre['date'])) }}</h4>
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
                        @foreach ($pre['prescription'] as $Prescriptions)
                             <tr>
                                    <td>{{ $Prescriptions->drug }}</td>
                                    <td>{{ $Prescriptions->dosage }}</td>
                                    <td>{{ $Prescriptions->frequency }}</td>
                                    <td>{{ $Prescriptions->duration }}</td>
                                    <td>{{ $Prescriptions->instructions }}</td>
                                </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @endforeach

                    @else
                    <p>No Medication prescribed</p>

                    @endif


                </div>
            </div>
            <div class="medication">
                <div class="text-left">
                    <h4>Discharge Medication</h4>
                </div>
                <hr>
                <div class="medicine-table">

                        @if (!empty($dicharge_medication))
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
                             @foreach ($dicharge_medication as $dicharge_medications)
                             <tr>
                                    <td>{{ $dicharge_medications->drug }}</td>
                                    <td>{{ $dicharge_medications->dosage }}</td>
                                    <td>{{ $dicharge_medications->frequency }}</td>
                                    <td>{{ $dicharge_medications->duration }}</td>
                                    <td>{{ $dicharge_medications->instructions }}</td>
                                </tr>
                        @endforeach
                         </tbody>
                        </table>
                        @else
                            <p>No Discharge medication </p>
                        @endif



                </div>
            </div>

            <div class="medication">
                <div class="text-left">
                    <h4>Advice on Discharge</h4>
                </div>
                <hr>
                <div class="medicine-table">

                        @if (!empty($doctor_advice))
                            <p>{{ $doctor_advice }}</p>
                        @else
                            <p>No Discharge medication </p>
                        @endif

                </div>
            </div>
        <hr class="last-hr">
    </div>
</section>

</body>
</html>





