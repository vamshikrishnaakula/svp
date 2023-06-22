{{-- Extends Pb Dashboard Template --}}
@extends('layouts.doctor.template')

{{-- Content --}}
@section('content')

<section id="prescriptioninfo" class="content-wrapper_sub tab-content">
  <div class="row">
        <div class="col-md-12">
            <div class="prc_title">
                <h4 class="font-weight-bold">Prescription</h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="prt_section">
                <div class="date-part">
                    <label>Date</label>
                    <h5>{{date('d-m-Y', strtotime($prob_data['Appoinment_Time']))}}</h5>
                </div>
                <div class="sym-part">
                    <label>Symptoms</label>
                    <h5>{{$prob_data['Symptoms']}}</h5>
                </div>
                <div class="prt-part">
                    {{--  <a href=""><img src="../../images/download_arrow_down.svg" /></a>
                    <a href=""><img src="../../images/print_view_icon.svg" /></a>  --}}
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="info-box">
                <div class="box-title">
                    <h4>Vital Signs</h4>
                </div>
                @if(!$vitalsigns->isEmpty())
                         @foreach ($vitalsigns  as $vitalsign )
                             <div class="box-body">
                                <p>{{$vitalsign->vitalsign}}</p>
                            </div>
                        @endforeach
                        @else
                            <div class="col-sm-2">
                                 <p> No Vitalsigns</p>
                             </div>
                        @endif
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="info-box">
                <div class="box-title">
                    <h4>Medication</h4>
                </div>
                <div class="box-body">
                    <table class="prc_table">

                         @if(!$prescription_summary->isEmpty())
                           <thead>
                            <tr>
                                <th>Drug</th>
                                <th>Dosage</th>
                                <th>Frequency</th>
                                <th>Duration</th>
                                <th>Instruction</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($prescription_summary  as $prescitpion )
                            <tr>
                                <td>{{$prescitpion->drug}}</td>
                                <td>{{$prescitpion->dosage}}</td>
                                <td>{{$prescitpion->frequency}}</td>
                                <td>{{$prescitpion->duration}}</td>
                                <td>{{$prescitpion->instructions}}</td>
                            </tr>
                        @endforeach
                         </tbody>
                        @else
                            <tr rowspan="5">No Medication</tr>
                        @endif

                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="info-box">
                <div class="box-title">
                    <h4>Lab Tests</h4>
                </div>
                <div class="box-body">
                    <div class="row">
                         @if(!$labreports->isEmpty())
                         @foreach ($labreports  as $labreport )
                            <div class="col-sm-2">
                            <p>{{$labreport->labreports}}</p>
                        </div>
                        @endforeach
                        @else
                            <div class="col-sm-2">
                                 <p> No Lab Records</p>
                             </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="info-box">
                <div class="box-title">
                    <h4>Doctor Advice</h4>
                </div>
                <div class="box-body">
                    <div class="row">
                        @if(!$vitalsigns->isEmpty())
                        @foreach ($vitalsigns  as $vitalsign )
                            <div class="box-body">
                               <p>{{$vitalsign->doctor_advice}}</p>
                           </div>
                       @endforeach
                       @else
                           <div class="col-sm-2">
                                <p> No doctor advice</p>
                            </div>
                       @endif
                    </div>
                </div>
            </div>
        </div>

</section>


@endsection
