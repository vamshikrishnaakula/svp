{{-- Extends layout --}}
<?php
$role = auth()->user()->role;
?>
@extends(($role === 'faculty') ? 'layouts.faculty.template' : 'layouts.doctor.template')

{{-- Content --}}
@section('content')

@if ($message = Session::get('success'))
<div class="alert alert-success alert-dismissible" id="alert_message">
    <p>{{ $message }}</p>
</div>
@elseif ($message = Session::get('delete'))
<div class="alert alert-danger alert-dismissible">
    <p>{{ $message }}</p>
</div>

@endif

<section id="patientdetails" class="content-wrapper_sub tab-content">
    <div class="row">
        <div class="col-md-12">
            <div class="patientbasicinfo">



                <div class="row">
                    <div class="form-group col-md-3">
                        <label>Patient ID :</label>
                        <span>{{$prob_data->RollNumber}}</span>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Gender :</label>
                        <span>{{$prob_data->gender}}</span>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-3">
                        <label>Patient Name :</label>
                        <span>{{$prob_data->Name}}</span>
                    </div>
                    <div class="form-group col-md-5">
                        <label>Symptoms :</label>
                        <span>{{$prob_data->Symptoms}}</span>
                    </div>
                </div>


                <ul class="nav nav-tabs mt-4">
                    <li class="nav-item"><a class="nav-link active" data-toggle="tab"
                            href="#prescriptions">Prescriptions</a></li>

                    <li class="nav-item"><a class="nav-link" data-toggle="tab"href="#prescriptionshistory">Prescriptions History</a></li>
                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#patientreport">Reports</a></li>
                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#patientinfo">Patient Info</a></li>
                </ul>
            </div>
        </div>
    </div>

    <form id="prescription" name="prescription" method="post" autocomplete="off">

        <div class=" tab-content mt-2">
            <div id="prescriptions" class="tab-pane fade in active show">
                <div class="admitpatients userSubmitBtns mb-2">
                    <button type="button" class="btn formBtn ml-3" style="background: #44588B;"
                        id="admit_hospital">Admit InPatient</button>
                    <button type="button" class="btn formBtn" style="background: #5FB35E;"
                        id="prescription1">Save</button>
                </div>

                <div class="vitalsigns p-4">
                    <h5 class="mb-4">Vital Signs</h5>
                    <input type="hidden" class="form-control" name="pid" id="pid" value="{{$prob_data->id}}" />
                    <input type="hidden" class="form-control" name="appoinment_id" id="appoinment_id"
                        value="{{$prob_data->appoinmentid}}" />

                    <textarea class="form-control" id="vitalsigns" name="vitalsigns">{{$vitalsigns->vitalsign}}</textarea>


                    <!-- <div class="saveBtn">
                    <button type="button" onclick = "submitvitalsigns({{$prob_data->id}})" class="btn formBtn" style="background: #5FB35E;">Save</button>
                </div> -->
                </div>


                <div class="medication p-4">
                    <h5 class="mb-4">Medication</h5>
                    <div class="medicationhead">
                        <div class="row">
                            <div class="col-md-2"><span>Drug</span></div>
                            <div class="col-md-2"><span>Dosage</span></div>
                            <div class="col-md-2"><span>Frequency</span></div>
                            <div class="col-md-2"><span>Duration</span></div>
                            <div class="col-md-3"><span>Instructions</span></div>
                        </div>
                    </div>
                            @foreach ($prescription_summary as $prescription_summarys)
                            <div class="row mt-4" id="pres">
                                <div class="col-md-2">
                                    <input type="text" class="form-control" name="drug0" id="drug0" value="{{ $prescription_summarys->drug }}" required />
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control" name="dosage0" value="{{ $prescription_summarys->dosage }}" required />
                                </div>
                                <div class="col-md-2">
                                    <select class="form-control" name="frequency0" value="{{ $prescription_summarys->frequency }}" required>
                                        <option value=''>Select</option>
                                        <option value="once a day"{{$prescription_summarys->frequency == 'once a day' ? 'selected' : ''}}>once a day</option>
                                        <option value="twice a day"{{$prescription_summarys->frequency == 'twice a day' ? 'selected' : ''}}>twice a day</option>
                                        <option value="three times a day"{{$prescription_summarys->frequency == 'three times a day' ? 'selected' : ''}}>Three times a day</option>
                                        <option value="before breakfast"{{$prescription_summarys->frequency == 'before breakfast' ? 'selected' : ''}}>before Breakfast</option>
                                        <option value="after meals"{{$prescription_summarys->frequency == 'after meals' ? 'selected' : ''}}>After Meals</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control" name="duration0" value="{{ $prescription_summarys->duration }}"  required />
                                </div>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" name="instructions0" value="{{ $prescription_summarys->instructions }}" required />
                                </div>
                                <div class="deletemedication">
                                    <img src="{{ asset('images/wrong.png') }}" />
                                </div>
                            </div>
                            @endforeach


                    <div class="medicationsub"></div>

                    <div class="addMedication">
                        <img src="{{ asset('images/add.png') }}" />
                    </div>
                </div>
                <div class="labtests p-4" id="labtests">
                    <h5 class="mb-4">Lab Tests</h5>
                    <div class="medicationhead">
                        <div class="col-md-7"><span>Test Name</span></div>
                    </div>

                    @foreach ($labreports as $labreport)
                    <div class="row mt-4">

                            <div class="col-md-11">
                                <input type="text" class="form-control col-md-6" name="labtest0" id="labtest0" value="{{ $labreport->labreports }}" />
                            </div>



                        <div class="deletelabtest">
                            <img src="{{ asset('images/wrong.png') }}" />
                        </div>
                    </div>
                    @endforeach
                    <div class="labtestssub"></div>

                    <div class="addlabtest">
                        <img src="{{ asset('images/add.png') }}" />
                    </div>


                </div>

                <div class="doctor_advice p-4">
                    <h5 class="mb-4">Doctor Advice</h5>
                    <input type="hidden" class="form-control" name="pid" id="pid" value="{{$prob_data->id}}" />
                    {{-- <input type="hidden" class="form-control" name="appoinment_id" id="appoinment_id"
                        value="{{$prob_data->appoinmentid}}" /> --}}
                    <textarea class="form-control" id="doctor_advice" name="doctor_advice">{{ $vitalsigns->doctor_advice }}</textarea>
                </div>


                <div id="prescription_data"></div>

            </div>
    </form>


<div id="patientinfo" class="tab-pane fade">
    <div id="main">
            <div class="accordion" id="faq">
                <div class="card">
                    <div class="card-header" id="faqhead1">
                        <a href="#" class="btn btn-header-link" data-toggle="collapse" data-target="#faq1"
                            aria-expanded="true" aria-controls="faq1">General Info</a>
                    </div>
                    <div id="faq1" class="collapse show" aria-labelledby="faqhead1" data-parent="#faq">
                        <div class="card-body">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th width="25%"></th>
                                        <th width="25%">Chest Reading</th>
                                        <th>Past History</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <label>Height :</label>
                                            <span>{{(isset($generalinfo->Height) ? $generalinfo->Height:false)}}</span>
                                        </td>
                                        <td>
                                            <label>Expi (cms) :</label>
                                            <span>{{(isset($generalinfo->Expi) ? $generalinfo->Expi:false)}}</span>
                                        </td>
                                        <td>
                                            <span class="gen_info_past_history">{{(isset($generalinfo->PastHistory) ?
                                                $generalinfo->PastHistory:false)}}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label>Weight :</label>
                                            <span>{{(isset($generalinfo->Weight) ? $generalinfo->Weight:false)}}</span>
                                        </td>
                                        <td>
                                            <label>insp (cms) :</label>
                                            <span>{{(isset($generalinfo->Ins) ? $generalinfo->Ins:false)}}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>
                                            <label>Expansion (cms) :</label>
                                            <span>{{(isset($generalinfo->Expansion)?
                                                $generalinfo->Expansion:false)}}</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!--Generan Ifo-->
                <div class="card">
                    <div class="card-header" id="faqhead2">
                        <a href="#" class="btn btn-header-link collapsed" data-toggle="collapse" data-target="#faq2"
                            aria-expanded="true" aria-controls="faq2">Family History</a>
                    </div>

                    <div id="faq2" class="collapse" aria-labelledby="faqhead2" data-parent="#faq">
                        <div class="card-body">
                            <div class="row no-gutters">
                                <div class="col-md-4">
                                    <div class="familyhistory">
                                        <div class="form-group">
                                            <label>Diabetes :</label>
                                            <span>
                                                @if(isset($familyhistory->Diabetes))
                                                @if(($familyhistory->Diabetes) == '0')
                                                <img src="{{ asset('images/wrong.png') }}" />
                                                @else
                                                <img src="{{ asset('images/tick.png') }}" />
                                                @endif
                                                @endif
                                            </span>
                                        </div>
                                        <div class="form-group">
                                            <label>Heart Diseases :</label>
                                            <span>
                                                @if(isset($familyhistory->HeartDiseases))
                                                @if(($familyhistory->HeartDiseases) == '0')
                                                <img src="{{ asset('images/wrong.png') }}" />
                                                @else
                                                <img src="{{ asset('images/tick.png') }}" />
                                                @endif
                                                @endif
                                            </span>
                                        </div>
                                        <div class="form-group">
                                            <label>Migraine :</label>
                                            <span>
                                                @if(isset($familyhistory->Migrane))
                                                @if(($familyhistory->Migrane) == '0')
                                                <img src="{{ asset('images/wrong.png') }}" />
                                                @else
                                                <img src="{{ asset('images/tick.png') }}" />
                                                @endif
                                                @endif
                                            </span>
                                        </div>
                                        <div class="form-group">
                                            <label>Epilepsy :</label>
                                            <span>
                                                @if(isset($familyhistory->Epilepsy))
                                                @if(($familyhistory->Epilepsy) == '0')
                                                <img src="{{ asset('images/wrong.png') }}" />
                                                @else
                                                <img src="{{ asset('images/tick.png') }}" />
                                                @endif
                                                @endif
                                            </span>
                                        </div>
                                        <div class="form-group">
                                            <label>Allergy :</label>
                                            <span>
                                                @if(isset($familyhistory->Allergy))
                                                @if(($familyhistory->Allergy) == '0')
                                                <img src="{{ asset('images/wrong.png') }}" />
                                                @else
                                                <img src="{{ asset('images/tick.png') }}" />
                                                @endif
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4"></div>
                                <div class="col-md-4">
                                    <h6 class="mb-4"><b>Personal History</b></h6>
                                    <div class="familyhistory">
                                        <div class="form-group">
                                            <label>Smoking :</label>
                                            <span>
                                                @if(isset($familyhistory->Smoking))
                                                @if(($familyhistory->Smoking) == '0')
                                                <img src="{{ asset('images/wrong.png') }}" />
                                                @else
                                                <img src="{{ asset('images/tick.png') }}" />
                                                @endif
                                                @endif
                                            </span>
                                        </div>
                                        <div class="form-group">
                                            <label>Alcohol :</label>
                                            <span>
                                                @if(isset($familyhistory->Alchohol))
                                                @if(($familyhistory->Alchohol) == '0')
                                                <img src="{{ asset('images/wrong.png') }}" />
                                                @else
                                                <img src="{{ asset('images/tick.png') }}" />
                                                @endif
                                                @endif
                                            </span>
                                        </div>
                                        <div class="form-group">
                                            <label>Veg :</label>
                                            <span>
                                                @if(isset($familyhistory->Veg))
                                                @if(($familyhistory->Veg) == '0')
                                                <img src="{{ asset('images/wrong.png') }}" />
                                                @else
                                                <img src="{{ asset('images/tick.png') }}" />
                                                @endif
                                                @endif
                                            </span>
                                        </div>
                                        <div class="form-group">
                                            <label>Non-Veg :</label>
                                            <span>
                                                @if(isset($familyhistory->NonVeg))
                                                @if(($familyhistory->NonVeg) == '0')
                                                <img src="{{ asset('images/wrong.png') }}" />
                                                @else
                                                <img src="{{ asset('images/tick.png') }}" />
                                                @endif
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Family History-->
                <div class="card">
                    <div class="card-header" id="faqhead3">
                        <a href="#" class="btn btn-header-link collapsed" data-toggle="collapse" data-target="#faq3"
                            aria-expanded="true" aria-controls="faq2">Physical Examination</a>
                    </div>

                    <div id="faq3" class="collapse" aria-labelledby="faqhead2" data-parent="#faq">
                        <div class="card-body">
                            <div class="row no-gutters">
                                <div class="col-md-5 physicalbasicdetails">
                                    <div class="form-group">
                                        <label>Blood Pressure :</label>
                                        <span>{{(isset($physicalinvestigation->Bloodpressure) ?
                                            $physicalinvestigation->Bloodpressure:false)}}</span>
                                    </div>
                                    <div class="form-group">
                                        <label>Pulse :</label>
                                        <span>{{(isset($physicalinvestigation->Pulse) ?
                                            $physicalinvestigation->Pulse:false)}}</span>
                                    </div>
                                    <div class="form-group">
                                        <label>ENT :</label>
                                        <span>{{(isset($physicalinvestigation->Ent) ?
                                            $physicalinvestigation->Ent:false)}}</span>
                                    </div>
                                    <div class="form-group">
                                        <label>Dental Examination :</label>
                                        <span>{{(isset($physicalinvestigation->Dental) ?
                                            $physicalinvestigation->Dental:false)}}</span>
                                    </div>
                                    <div class="form-group">
                                        <label>Heart :</label>
                                        <span>{{(isset($physicalinvestigation->Heart) ?
                                            $physicalinvestigation->Heart:false)}}</span>
                                    </div>
                                    <div class="form-group">
                                        <label>Lungs :</label>
                                        <span>{{(isset($physicalinvestigation->Lungs) ?
                                            $physicalinvestigation->Lungs:false)}}</span>
                                    </div>
                                    <div class="form-group">
                                        <label>Abdomen :</label>
                                        <span>{{(isset($physicalinvestigation->Abdomen) ?
                                            $physicalinvestigation->Abdomen:false)}}</span>
                                    </div>
                                </div>
                                <div class="col-md-2"></div>
                                <div class="col-md-5">
                                    <h6><b>Eye Sight</b></h6>
                                    <h6><b>With Glasses</b></h6>
                                    <div class="eyesight mgn_left_position">
                                        <label>Left : {{(isset($physicalinvestigation->Eyewithleft) ?
                                            $physicalinvestigation->Eyewithleft:false)}}</label> &nbsp;&nbsp;&nbsp;&nbsp;

                                        <label>Right : {{(isset($physicalinvestigation->Eyewithright) ?
                                            $physicalinvestigation->Eyewithright:false)}}</label>

                                    </div>
                                    <h6><b>Without Glasses</b></h6>
                                    <div class="eyesight mgn_left_position">
                                        <label>Left : {{(isset($physicalinvestigation->Eyewithoutleft) ?
                                            $physicalinvestigation->Eyewithoutleft:false)}}</label> &nbsp;&nbsp;&nbsp;&nbsp;
                                        <label>Right : {{(isset($physicalinvestigation->Eyewithoutright) ?
                                            $physicalinvestigation->Eyewithoutright:false)}}</label>
                                    </div>
                                    <div class="physicalexam">
                                        <div class="form-group">
                                            <label>Urological System :</label>
                                            <span>{{(isset($physicalinvestigation->Urological) ?
                                                $physicalinvestigation->Urological:false)}}</span>
                                        </div>
                                        <div class="form-group">
                                            <label>Athlete/Non-Athlete :</label>
                                            <span>{{(isset($physicalinvestigation->Athlete) ?
                                                $physicalinvestigation->Athlete:false)}}</span>
                                        </div>
                                        <div class="form-group">
                                            <label>Any defect or Deformity :</label>
                                            <span>{{(isset($physicalinvestigation->Defectordeformity) ?
                                                $physicalinvestigation->Defectordeformity:false)}}</span>
                                        </div>
                                        <div class="form-group" style="display:flex;">
                                            <label>Any scars of operation :</label>
                                            <span class="gen_info_past_history">{{(isset($physicalinvestigation->Scarsoperation) ?
                                                $physicalinvestigation->Scarsoperation:false)}}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!--Physical examination-->
                <div class="card">
                    <div class="card-header" id="faqhead4">
                        <a href="#" class="btn btn-header-link collapsed" data-toggle="collapse" data-target="#faq4"
                            aria-expanded="true" aria-controls="faq2"> Medical Exam</a>
                    </div>

                    <div id="faq4" class="collapse" aria-labelledby="faqhead4" data-parent="#faq">
                        <div class="card-body">
                            <div class="row no-gutters">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label>Temperature :</label>
                                        <span>{{(isset($medicalexam->temperature) ?
                                            $medicalexam->temperature:false)}}</span>
                                    </div>
                                    <div class="form-group">
                                        <label>Antigen test :</label>
                                        <span>{{(isset($medicalexam->antigentest) ?
                                            $medicalexam->antigentest:false)}}</span>
                                    </div>
                                    <div class="form-group">
                                        <label>RTPCR :</label>
                                        <span>{{(isset($medicalexam->rtpcr) ? $medicalexam->rtpcr:false)}}</span>
                                    </div>
                                    <div class="form-group">
                                        <label>Haemoglobin :</label>
                                        <span>{{(isset($medicalexam->haemoglobin) ?
                                            $medicalexam->haemoglobin:false)}}</span>
                                    </div>
                                    <div class="form-group">
                                        <label>Calcium :</label>
                                        <span>{{(isset($medicalexam->calcium) ? $medicalexam->calcium:false)}}</span>
                                    </div>
                                </div>

                                <div class="col-md-2"></div>
                                <div class="col-md-5">

                                    <div class="physicalexam">
                                        <div class="form-group">
                                            <label>Vitamin D :</label>
                                            <span>{{(isset($medicalexam->vitamind) ?
                                                $medicalexam->vitamind:false)}}</span>
                                        </div>
                                        <div class="form-group">
                                            <label>Vitamin B12 :</label>
                                            <span>{{(isset($medicalexam->vitaminb12) ?
                                                $medicalexam->vitaminb12:false)}}</span>
                                        </div>
                                        <div class="form-group">
                                            <label>Pre-existing injury :</label>
                                            <span>{{(isset($medicalexam->preexistinginjury) ?
                                                $medicalexam->preexistinginjury:false)}}</span>
                                        </div>
                                        <div class="form-group">
                                            <label>Family members ever tested Covid +ve :</label>
                                            <span>{{(isset($medicalexam->covid) ? $medicalexam->covid:false)}}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Medical Exam-->

                <div class="card">
                    <div class="card-header" id="faqhead5">
                        <a href="#" class="btn btn-header-link collapsed" data-toggle="collapse" data-target="#faq5"
                            aria-expanded="true" aria-controls="faq2"> Investigation</a>
                    </div>

                    <div id="faq5" class="collapse" aria-labelledby="faqhead5" data-parent="#faq">
                        <div class="card-body">
                            <div class="row no-gutters">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label>Urine Examination :</label>
                                        <span>{{(isset($investigation->Urine) ? $investigation->Urine:false)}}</span>
                                    </div>
                                    <div class="form-group">
                                        <label>Blood Group :</label>
                                        <span>{{(isset($investigation->Bloodgroup) ?
                                            $investigation->Bloodgroup:false)}}</span>
                                    </div>
                                    <div class="form-group">
                                        <label>RH Factor :</label>
                                        <span>{{(isset($investigation->Rhfactor) ?
                                            $investigation->Rhfactor:false)}}</span>
                                    </div>
                                    <div class="form-group">
                                        <label>X-Ray Chest PA View :</label>
                                        <span>{{(isset($investigation->Xray) ? $investigation->Xray:false)}}</span>
                                    </div>
                                </div>

                                <div class="col-md-2"></div>
                                <div class="col-md-5">

                                    <h6>Immunization :</h6>
                                    <span>{{(isset($investigation->Immunization) ?
                                        $investigation->Immunization:false)}}</span>

                                    <div class="form-group d-flex">
                                        <div>
                                            <label>Tetanus Oxide :</label>
                                        </div>
                                        <div class="list_mgn_left">

                                        <p>{{(isset($investigation->Tetanus1) ?
                                            $investigation->Tetanus1:false)}}</p>
                                        <p>{{(isset($investigation->Tetanus2) ?
                                            $investigation->Tetanus2:false)}}</p>
                                        <p>{{(isset($investigation->Tetanus3) ?
                                            $investigation->Tetanus3:false)}}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Investigation-->
            </div>
        </div>
</div>

 <div id="prescriptionshistory" class="tab-pane fade prescriptionreports">
<div id="main">
    <div class="accordion" id="faq">
        @if(!empty($probationer_details))
        @foreach($probationer_details as $probationer_detail)
        <div class="card">

            <div class="card-header" id="faqhead44{{ $loop -> iteration}}">
                <a href="#" class="btn btn-header-link" data-toggle="collapse" data-target="#faq44{{ $loop -> iteration}}" aria-expanded="true" aria-controls="faq1">
                  <div class="accordian_time_info">
                            <div>
                                <span>Date:</span> &nbsp; <span class="date_label">{{(isset($probationer_detail['date']) ? date('d-m-Y',
                                strtotime($probationer_detail['date'])) :false)}}</span>
                            </div>
                             <div>
                                <span>Time:</span> &nbsp; <span class="date_label">{{(isset($probationer_detail['date']) ? date('h:i A',
                                strtotime($probationer_detail['date'])) :false)}}</span>
                            </div>
                        </div>
                </a>
            </div>

            <div id="faq44{{ $loop -> iteration}}" class="collapse" aria-labelledby="faqhead1" data-parent="#faq{{ $loop -> iteration}}">
                <div class="card-body">
                  <ul class="mt-4">
                <li data-toggle="collapse" data-target="#prescriptiondetails">
                    <div class="row">
                        <div class="col-md-3 form-group">

                        </div>

                        <div class="col-md-6"></div>
                        <div class="col-md-3">
                            <div class="downloadBtn">
                                <!-- <a href="#" class="mr-3"><img src="{{ asset('images/download1.png') }}" /></a>
                                        <a href="#"><img src="{{ asset('images/print1.png') }}" /></a> -->
                            </div>
                        </div>
                    </div>
                </li>
                    <div class="vitalsigns p-4">
                        <h5>Vital Signs</h5>
                        @foreach($probationer_detail['vitalsigns'] as $vitalsign)
                        <p>{{(isset($vitalsign->vitalsign) ? $vitalsign->vitalsign:false)}}</p>
                        @endforeach
                    </div>
                    <div class="medications p-3">
                        <h5>Medication</h5>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Drug</th>
                                    <th>Dosage</th>
                                    <th>Frequency</th>
                                    <th>Duration</th>
                                    <th>Instructions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($probationer_detail['prescription'] as $prescriptions)
                                <tr>
                                    <td>{{$prescriptions->drug}}</td>
                                    <td>{{$prescriptions->dosage}}</td>
                                    <td>{{$prescriptions->frequency}}</td>
                                    <td>{{$prescriptions->duration}}</td>
                                    <td>{{$prescriptions->instructions}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="labtestsinfo p-4">
                        <h5>Lab Tests</h5>
                        @foreach($probationer_detail['labreports'] as $labreport)
                        <div class="row">
                            <div class="col-md-3">
                                <p>{{$labreport->labreports}}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="doctoradvice p-4">
                        <h5>Doctor Advice</h5>
                        @foreach($probationer_detail['doctor_advice'] as $doctor_advices)
                        <div class="row">
                            <div class="col-md-3">
                                <p>{{$doctor_advices->doctor_advice}}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>

            </ul>
                </div>
            </div>
        </div>
         @endforeach
        @endif
    </div>
</div>
</div>

    <div id="patientreport" class="tab-pane fade">
        <table>
            <tbody>
                <tr>
                    <td>

                    </td>
                </tr>
            </tbody>
        </table>
        {{-- <ul class="mt-4">
            <li>
                @foreach($labuploads as $labrepot)
                <div class="row">
                    <div class="col-md-2 form-group">
                        <label>Date</label>
                        <p>{{date('d-m-Y', strtotime($labrepot->created_at))}}</p>
                    </div>
                    <div class="col-md-2 form-group">
                        <label>Test Name</label>
                        <p>{{$labrepot->ReportName}}</p>
                    </div>
                    <div class="col-md-4"></div>
                    <div class="col-md-2">
                        <div class="downloadBtn">
                            <a href="{{route('download',$labrepot->FileDirectory)}}" class="mr-3"><img
                                    src="{{ asset('images/download1.png') }}" />
                                </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </li>
        </ul> --}}
    </div>
    </div>
    </div>
    </div>
</section>

@endsection
@section('scripts')
<script>

   $("#faqhead442").click(function(){
       $("#faq441").removeClass("show")
   })
   $("#faqhead441").click(function(){
       $("#faq442").removeClass("show")
   })
    var rowNum = 0;
    $(".addMedication").click(function () {
        rowNum++;
        $(".medication .medicationsub").append(
            "<div class='row mt-4'><div class='col-md-2'><input type='text' class='form-control' name='drug" + rowNum + "' id='drug0' required/></div><div class='col-md-2'><input type='text' class='form-control' name='dosage" + rowNum + "' required/></div><div class='col-md-2'><select class='form-control' name='frequency" + rowNum + "' required><option value=''>Select</option><option>once a day</option><option>twice a day</option><option>Three times a day</option><option>before Breakfast</option><option>After Meals</option></select></div><div class='col-md-2'><input type='text' class='form-control' name='duration" + rowNum + "' required/></div><div class='col-md-3'><input type='text' class='form-control' name='instructions" + rowNum + "' required/></div><div class='deletemedication'><img src='{{ asset('images/wrong.png') }}' /></div></div>"
        );

    });
    $(".addlabtest").click(function () {
        rowNum++;
        $('.labtests .labtestssub').append(
            `<div class="row mt-4">
          <div class="col-md-11">
              <input type="text" class="form-control col-md-6" name="labtest`+ rowNum + `" id="labtest0" />
              </div>
              <div class="deletelabtest">
                  <img src="{{ asset('images/wrong.png') }}" />
              </div>
          </div>
        `
        )
    })


    $(function () {
        $(".medication").on("click", '.deletemedication', function () {

            $(this)
                .parent()
                .remove();
        });
    });

    $(function () {
        $(".labtests").on("click", '.deletelabtest', function () {

            $(this)
                .parent()
                .remove();
        });
    });

    function submitvitalsigns(id) {

        var id = id;
        var vitalsign = $('#vitalsigns').val();
        $.ajax({
            url: '/insertvitalsign',
            type: "POST",
            data: {
                "_token": "{{ csrf_token() }}",
                "id": id,
                "vitalsign": vitalsign
            },
            success: function (data) {

                if (data == '1') {
                    $('#vitalsigns').val('');
                    alert("sucesssfully updated")
                }
            }
        })
    }

    // function submitprescription(id)
    // {
    //   var id = id;
    //   var formData = $('#prescription').serializeArray();
    //   $.ajax({
    //        url: '/insertvitalsign',
    //        type: "POST",
    //        data:{
    //            "_token": "{{ csrf_token() }}",
    //             "id":id,
    //             "vitalsign":vitalsign
    //            },
    //        success: function(data){
    //         if(data == '1')
    //         {
    //           $('#vitalsigns').val('');
    //           alert("sucesssfully updated")
    //         }
    //        }
    //    })
    // }

    $('#prescription1').click(function () {debugger

        var statusDiv = $("#prescription_data");
        var inputs = $('#prescription, #pres').serializeArray();
        $.ajax({
            url: '/updatepresciption',
            type: "POST",
            data: {
                "_token": "{{ csrf_token() }}",
                "inputs": JSON.stringify(inputs),
                processData: false,
                contentType: false,
            },
            beforeSend: function (xhr) {debugger;
                window.loadingScreen("show");
                statusDiv.html(
                    '<p class="text-info">Please wait while processing your request</p>'
                );
            },
            success: function (data) {debugger;

                window.location.href = "/doctor";
            }
        })

    });



    $('#admit_hospital').click(function () {

        var statusDiv = $("#prescription_data");
        var patient_id = $('#pid').val();
        var appointment_id = $('#appoinment_id').val();
        $.ajax({
            url: '/admitpatient',
            type: "POST",
            data: {
                "_token": "{{ csrf_token() }}",
                "pid": patient_id,
                "appointment": appointment_id,
            },
            beforeSend: function (xhr) {
                window.loadingScreen("show");
                statusDiv.html(
                    '<p class="text-info">Please wait while processing your request</p>'
                );
            },
            success: function (data) {

                window.location.href = "/doctor";
            }
        })
});



// $('#admit_hospital').click(function () {
//         var statusDiv = $("#prescription_data");
//         var patient_id = $('#pid').val();
//         var inputs = $('#prescription, #pres').serializeArray();
//         $.ajax({
//             url: '/insertinpatientprescription',
//             type: "POST",
//             data: {
//                 "_token": "{{ csrf_token() }}",
//                // "inputs": JSON.stringify(inputs),
//                 processData: false,
//                 contentType: false,
//             },
//             beforeSend: function (xhr) {
//                 window.loadingScreen("show");
//                 statusDiv.html(
//                     '<p class="text-info">Please wait while processing your request</p>'
//                 );
//             },
//             success: function (data) {
//                 window.location.reload();
//             }
//         })
//     });


$(document).on('keydown.autocomplete', '#drug0', function() {
    $(this).autocomplete({
        source: "{{ route('autocomplete') }}",
      minLength: 1,
      select:function(event,ui) {
      }
    });
});


$(document).on('keydown.autocomplete', '#labtest0', function() {
    $(this).autocomplete({
        source: "{{ route('labreportautocomplete') }}",
      minLength: 1,
      select:function(event,ui) {
      }
    });
});

</script>
@endsection
