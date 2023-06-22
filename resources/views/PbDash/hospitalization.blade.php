{{-- Extends Pb Dashboard Template --}}
@extends('layouts.pbdash.template')

{{-- Content --}}
@section('content')


<section id="userhospitalization" class="content-wrapper_sub ">
    <div class="user_manage">
        <div class="row">
            <div class="col-md-6">
                <h4>Hospitalization</h4>
            </div>
        </div>

        <ul class="nav nav-tabs nav-fill mt-3 hospitalization-tab">
            <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#prescriptions">Prescriptions</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#dischargesummary">Discharge Summary</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#userreports">Reports</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#sickreport">Sick / Injury Report</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#medicalexamination">Medical Examination</a></li>
        </ul>

        <div class="tab-content">

            {{-- ***************************************************** prescriptions start **************************************************** --}}

            <div id="prescriptions" class="table-responsive mt-5 tab-pane fade in active show">

                @php
                    $user_id = Auth::id();
                    $probationer_id = App\Models\probationer::where('user_id', $user_id)->value('id');
                    $prescriptions = DB::table('probationer_prescription')->where('probationer_prescription.probationer_id', $probationer_id)
                                    ->leftJoin('appoinments', 'probationer_prescription.appointment_id', '=', 'appoinments.id')->groupBy('appointment_id')
                                    ->get();

                @endphp

                @if (count($prescriptions)>0)
                <table class="table txt-center tableinfo">
                    <thead class="mb-3">
                        <tr>
                            <th width="25%">DATE</th>
                            <th width="55%">DOCTOR NAME</th>
                            <th width="20%"></th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($prescriptions as $prescription)
                        <?php
                            $doctor_name =  DB::table('users')->where('id', $prescription->Doctor_Id)->value('name');
                        ?>
                            <tr>
                                <td>{{ date('d-m-Y', strtotime($prescription->date)) }}</td>
                                <td>{{ $doctor_name }}</td>
                                <td>
                                    <div class="prescription-view-img">
                                        <a class="" href="{{ route('userprescription', $prescription->appointment_id) }}" target="_blank" data-toggle="tooltip" title="download"><img src="{{ asset('images/download1.png') }}" /></a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
                @else
                <div class="text-center">You dont have any Prescription record.</div>

                @endif
            </div>

             {{-- ***************************************************** dischargesummary **************************************************** --}}

             <div id="dischargesummary" class="table-responsive tab-pane fade">

                @php
                    $user_id = Auth::id();
                    $probationer_id = App\Models\probationer::where('user_id', $user_id)->value('id');
                    $dischargesummary = DB::table('in_patients')
                                    ->where('probationer_id', $probationer_id)
                                    ->leftJoin('probationers', 'in_patients.Probationer_Id', '=', 'probationers.id')
                                    ->select('in_patients.id as in_pat_id', 'in_patients.appointment_id', 'in_patients.admitted_date', 'in_patients.discharge_date', 'probationers.id', 'probationers.Name', 'probationers.RollNumber')
                                   ->get();


                @endphp

                @if (count($dischargesummary)>0)
                <table class="table txt-center tableinfo">
                    <thead class="mb-3">
                        <tr>
                            <th width="15%">S.No</th>
                            <th width="25%">DOCTOR NAME</th>
                            <th width="25%">Admitted Date</th>
                            <th width="25%">Discharge date</th>
                            <th width="5%"></th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($dischargesummary as $key => $dischargesummarys)
                        <?php
                        $key++;
                            $doctor_name =  DB::table('users')->where('id', '512')->value('name');
                        ?>


                            <tr>
                                <td>{{ $key }}</td>
                                <td>{{ $doctor_name }}</td>
                                <td>{{ date('d-m-Y', strtotime($dischargesummarys->admitted_date)) }}</td>
                                <td>{{ date('d-m-Y', strtotime($dischargesummarys->discharge_date)) }}</td>
                                <td>
                                    <div class="prescription-view-img">
                                        <a class="" href="{{ url ('discharge_summary/' . $dischargesummarys->in_pat_id) }}" target="_blank" data-toggle="tooltip" title="download"><img src="{{ asset('images/download1.png') }}" /></a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
                @else
                <div class="text-center">You dont have any Prescription record.</div>

                @endif
            </div>



            {{-- ***************************************************** Reports start **************************************************** --}}

            <div id="userreports" class="mt-5 tab-pane fade ">


            @php
                $user_id = Auth::id();
                $probationer_id = App\Models\probationer::where('user_id', $user_id)->value('id');
                $lab_reports = DB::table('labreports')->where('Probationer_Id', $probationer_id)->get();
            @endphp

            @if (count($lab_reports)>0)
            <table class="table txt-center tableinfo">
                <thead class="mb-3">
                    <tr>
                        <th width="25%">DATE</th>
                        <th width="50%">TEST NAME</th>
                        <th width="25%"></th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($lab_reports as $report)
                    <?php
                     //   $doctor_name =  DB::table('users')->where('id', $prescription->Doctor_Id)->value('name');
                        $report_view = asset("/uploads/{$report->FileDirectory}");
                    ?>
                        <tr>
                            <td>{{ date('d-m-Y', strtotime($report->created_at)) }}</td>
                            <td>{{ $report->ReportName }}</td>
                            <td>
                                <div class="prescription-view-img">
                                    <a class="" href="/reportdownload/{{ $report->FileDirectory }}" data-toggle="tooltip" title="download"><img src="{{ asset('images/download1.png') }}" /></a>
                                    <a class="" href="{{ $report_view }}" target="_blank" data-toggle="tooltip" title="print"><img src="{{ asset('images/print1.png') }}" /></a>
                                </div>

                            </td>
                        </tr>
                    @endforeach

                </tbody>
            </table>
            @else
            <div class="text-center">You don't have any Test Report record.</div>

            @endif

            </div>

            {{-- ***************************************************** sick / injury start **************************************************** --}}

            <div id="sickreport" class="mt-5 tab-pane fade ">

                @php
                    $user_id = Auth::id();
                    $probationer_id = App\Models\probationer::where('user_id', $user_id)->value('id');
                    $sickreports = DB::table('probationer_sickreports')->where('Probationer_Id', $probationer_id)->get();
                @endphp


                    <div>
                        @if (count($sickreports)>0)

                        <div class="sickreport-header">
                            <div class="sick-heading-date">Date</div>
                            <div class="sick-heading-empty"></div>
                        </div>


                        @php
                            $i = 1;
                        @endphp
                        @foreach ($sickreports as $sickreport )
                        <?php
                            $collapse = ($i==1)?"":"collapsed";
                            $expanded = ($i==1)?"false":"true";
                            $show = ($i==1)?"show":"";

                        ?>
                        <div class="accordion" id="sickreportAccordion">
                            <div class="card mb-3">
                                <div class="card-header">
                                    <a class="{{ $collapse }}" data-toggle="collapse" data-target="#targetCollapse{{ $sickreport->id }}" aria-expanded="{{ $expanded }}" aria-controls="targetCollapse{{ $sickreport->id }}">
                                        <div class="text-center sick-accordion-heading">
                                            <span class="mb-0 accordion-heading-date">{{ $sickreport->date }}</span>
                                            <span class="accordion-heading-icon"><i class="fas fa-angle-down rotate-icon"></i></span>
                                        </div>
                                    </a>

                                </div>

                                <div id="targetCollapse{{ $sickreport->id }}" class="collapse {{ $show }} mt-3" aria-labelledby="targetCollapse{{ $sickreport->id }}" data-parent="#accordionExample{{ $sickreport->id }}">
                                    <div class="card-body">
                                        <p>{{ $sickreport->sickreport }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                        $i++;
                        ?>
                        @endforeach
                        @else <p class="text-center">Bravo!! You don't have any injury record.</p>
                        @endif
                    </div>
            </div>

            {{-- ***************************************************** Medical examination start **************************************************** --}}

            <div id="medicalexamination" class="mt-5 tab-pane fade ">

                {{-- <div class="form-group row justify-content-sm-center">
                    <label>Select Month :</label>
                    <div class="input-group col-3 ml-3" id="insert_datetimepicker" data-target-input="nearest" name="Dob">
                        <input type="text" class="form-control datetimepicker-input" data-target="#insert_datetimepicker"
                        data-toggle="datetimepicker" name="Dob" id="month_datepicker" autocomplete="off" required />
                    </div>
                    <div class="rollnosubmit col-2">
                        <a href="#" onclick="get_medexam_data()"><img src="{{ asset('images/submit.png') }}" /></a>
                    </div>
                </div> --}}

                <div class="row justify-content-md-center medical-exam-mobile">
                    <div class="form-group col-md-4">
                        <label>Select Month :</label>
                        <div class="input-group" id="insert_datetimepicker" data-target-input="nearest" name="Dob">
                            <input type="text" class="form-control datetimepicker-input" data-target="#insert_datetimepicker"
                            data-toggle="datetimepicker" name="Dob" id="month_datepicker" autocomplete="off" required />
                            <a href="#" class="desktop-button ml-4" onclick="get_medexam_data()"><img src="{{ asset('images/submit.png') }}" /></a>
                            <a href="#" class="btn btn-success btn-sm mobile-button ml-4" onclick="window.get_medexam_data()">Submit</a>
                        </div>
                    </div>

                </div>
                <div id="medicalexam_data"></div>
            </div>

        </div>
    </div>
</section>

<script>

function get_medexam_data() {
    var monthYear = $("#month_datepicker").val();
    if((monthYear.length == 0) || (monthYear.split('-').length != 2)) {
        $("#medicalexam_data").html("Select a Month");
        return;
    }

    var data = {
        "month_year": monthYear,
        "requestName": "get_medexam_data",
    };

    $.ajax({
        url: appUrl + "/user-ajax",
        data: data,
        type: "POST",
        beforeSend: function (xhr) {
            window.loadingScreen("show");
        },
        success: function (rData) {
            window.loadingScreen("hide");
            $("#medicalexam_data").html(rData);
        }
    });
}

$(document).ready (function (){
    $("#insert_datetimepicker").datetimepicker ({
    viewMode: 'months',
    format: 'MM-YYYY'
    });
});





</script>

@endsection
