<div class="">

    <ul class="nav nav-tabs mt-3 hospitalization-tab">
        <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#prescriptions">Prescriptions</a></li>
        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#userreports">Reports</a></li>
        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#sickreport">Sick / Injury Report</a></li>
    </ul>

    <div class="tab-content">

        {{-- ***************************************************** prescriptions start **************************************************** --}}

        <div id="prescriptions" class="table-responsive mt-5 tab-pane fade in active show">

            @php
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

        {{-- ***************************************************** Reports start **************************************************** --}}

        <div id="userreports" class="mt-5 tab-pane fade ">


            @php
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
                        $doctor_name =  DB::table('users')->where('id', $prescription->Doctor_Id)->value('name');
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
            <div class="text-center">You dont have any Test Report record.</div>

            @endif

        </div>

        {{-- ***************************************************** sick / injury start **************************************************** --}}

        <div id="sickreport" class="mt-5 tab-pane fade ">

            @php
            $sickreports = DB::table('probationer_sickreports')->where('Probationer_Id', $probationer_id)->orderBy('id', 'asc')->get();
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

    </div>
</div>
