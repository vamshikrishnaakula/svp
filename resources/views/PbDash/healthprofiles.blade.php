{{-- Extends Pb Dashboard Template --}}
@extends('layouts.pbdash.template')

{{-- Content --}}
@section('content')

<section id="userhealthprofiles" class="content-wrapper_sub tab-content">
    <div class="user_manage">

        <div class="row">
            <div class="col-md-6">
                <h4>Health Profiles</h4>
            </div>
        </div>

        <ul class="nav nav-tabs nav-fill mt-3 healthprofiles-tab">
            <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#familyinfo">Family Info</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#heathprofile-generalinfo">General Info</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#heathprofile-familyhistory">Family History</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#heathprofile-physical">Physical Examination</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#heathprofile-investigation">Investigation</a></li>
        </ul>
 <?php
                            $user_id = Auth::id();
                            $pb_id = \App\Models\probationer::where('user_id', $user_id)->value('id');
                            $pb_dependents = \App\Models\FamilyDependent::where('Probationer_Id', $pb_id)->get();
                        ?>
        <div class="tab-content">
            <div id="familyinfo" class="tab-pane fade in mt-5 active show">
                <div class="familyinfo-heading">
                    <h4 class="ml-4">Dependents</h4>
                </div>
                <table class="table tableinfo table-bordered">
                    <thead>
                        <tr>
                          @if (count($pb_dependents)>0)
                            <th>Name</th>
                            <th>Age</th>
                            <th>Gender</th>
                            <th>Relationship</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                       
                        @if (count($pb_dependents)>0)
                        @foreach ($pb_dependents as $pb_dependent)
                            <tr>
                                <td>{{ $pb_dependent->DependentName }}</td>
                                <td>{{ $pb_dependent->DependentAge }}</td>
                                <td>{{ $pb_dependent->DependentGender }}</td>
                                <td>{{ $pb_dependent->DependentRelationship }}</td>
                            </tr>
                        @endforeach
                        @else <p class="text-center">You dont have any family info record.</p>
                        @endif
                    </tbody>
                </table>
            </div>


            <div id="heathprofile-generalinfo" class="tab-pane fade">
                <div class="row mt-4">

                    <?php
                        $user_id = Auth::id();
                        $probationers = \App\Models\probationer::where('user_id', $user_id)->select('id', 'Name')->first();
                        $pb_id = $probationers->id;
                        $pb_name = $probationers->Name;
                        $pb_generalinfo = DB::table('probationer_general_info')->where('Probationer_Id', $pb_id)->first();

                    ?>
                    <div class="col-md-1"></div>
                    <div class="col-md-4">
                        <div class="generalinfo mt-5">
                            <div class="form-group row">
                                <label for="" class="col-6">Name of the Probationer:</label>
                                <span class="col-6">{{ $pb_name }}</span>
                            </div>
                            <div class="form-group row">
                                <label for="" class="col-6">Height(cms) :</label>
                                <span class="col-6"> {{(isset($pb_generalinfo->Height) ? $pb_generalinfo->Height:false)}}</span>
                            </div>
                            <div class="form-group row">
                                <label for="" class="col-6">Weight( kgs ) :</label>
                                <span class="col-6">{{(isset($pb_generalinfo->Weight) ? $pb_generalinfo->Weight:false)}}</span>
                            </div>
                            <div class="form-group row">
                                <label for="" class="col-6">Past History :</label>
                                <span class="col-6">{{(isset($pb_generalinfo->PastHistory) ? $pb_generalinfo->PastHistory:false)}}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1"></div>
                    <div class="col-md-6">
                        <div class="readings">
                            <h6><b>Chest Reading</b></h6>
                            <div class="form-group row mt-4">
                                <label for="" class="col-sm-4">Expi(cms) :</label>
                                <span class="col-sm-8">{{(isset($pb_generalinfo->Expi) ? $pb_generalinfo->Expi:false)}}</span>
                            </div>
                            <div class="form-group row">
                                <label for="" class="col-sm-4">Ins(cms) :</label>
                                <span class="col-sm-8">{{(isset($pb_generalinfo->Ins) ? $pb_generalinfo->Ins:false)}}</span>
                            </div>
                            <div class="form-group row">
                                <label for="" class="col-sm-5">Expansion(cms):</label>
                                <span class="col-sm-7 list_mgn_left">{{(isset($pb_generalinfo->Expansion) ? $pb_generalinfo->Expansion:false)}}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div id="heathprofile-familyhistory" class="tab-pane fade">
                <div class="row mt-5">
                    <?php
                     
                    ?>

                    <div class="col-md-2"></div>
                    <div class="col-md-5 familyhistory">
                        <div class="form-group">
                            <label class="col-6">Diabetes :</label>
                            <span class="col-6">
                            @if(isset($pb_familyhist->Diabetes))  
                                      @if(($pb_familyhist->Diabetes) == '0')
                                      <img src="{{ asset('images/wrong.png') }}" />
                                      @else
                                      <img src="{{ asset('images/tick.png') }}" />
                                      @endif
                                      @endif
                            </span>
                        </div>
                        <div class="form-group">
                            <label class="col-6">Heart Diseases :</label>
                            <span class="col-6">
                                @if(isset($pb_familyhist->HeartDiseases))  
                                      @if(($pb_familyhist->HeartDiseases) == '0')
                                      <img src="{{ asset('images/wrong.png') }}" />
                                      @else
                                      <img src="{{ asset('images/tick.png') }}" />
                                      @endif
                                      @endif
                            </span>
                        </div>
                        <div class="form-group">
                            <label class="col-6">Migrane :</label>
                            <span class="col-6">
                                @if(isset($pb_familyhist->Migrane))  
                                      @if(($pb_familyhist->Migrane) == '0')
                                      <img src="{{ asset('images/wrong.png') }}" />
                                      @else
                                      <img src="{{ asset('images/tick.png') }}" />
                                      @endif
                                 @endif
                            </span>
                        </div>
                        <div class="form-group">
                            <label class="col-6">Epilepsy :</label>
                            <span class="col-6">
                                @if(isset($pb_familyhist->Epilepsy))  
                                      @if(($pb_familyhist->Epilepsy) == '0')
                                      <img src="{{ asset('images/wrong.png') }}" />
                                      @else
                                      <img src="{{ asset('images/tick.png') }}" />
                                      @endif
                                 @endif
                            </span>
                        </div>
                        <div class="form-group">
                            <label class="col-6">Allergy :</label>
                            <span class="col-6">
                            @if(isset($pb_familyhist->Allergy))  
                                      @if(($pb_familyhist->Allergy) == '0')
                                      <img src="{{ asset('images/wrong.png') }}" />
                                      @else
                                      <img src="{{ asset('images/tick.png') }}" />
                                      @endif
                                 @endif
                            </span>
                        </div>
                    </div>
                    <div class="col-md-5 personal-history">
                        <h5>Personal History</h5>
                        <div class="familyhistory mt-3">
                            <div class="form-group">
                                <label class="col-6">Smoking :</label>
                                <span class="col-6">
                                @if(isset($pb_familyhist->Smoking))  
                                      @if(($pb_familyhist->Smoking) == '0')
                                      <img src="{{ asset('images/wrong.png') }}" />
                                      @else
                                      <img src="{{ asset('images/tick.png') }}" />
                                      @endif
                                 @endif
                                </span>
                            </div>
                            <div class="form-group">
                                <label class="col-6">Alcohol :</label>
                                <span class="col-6">
                                @if(isset($pb_familyhist->Alchohol))  
                                      @if(($pb_familyhist->Alchohol) == '0')
                                      <img src="{{ asset('images/wrong.png') }}" />
                                      @else
                                      <img src="{{ asset('images/tick.png') }}" />
                                      @endif
                                 @endif
                                </span>
                            </div>
                            <div class="form-group">
                                <label class="col-6">Veg :</label>
                                <span class="col-6">
                                @if(isset($pb_familyhist->Veg))  
                                      @if(($pb_familyhist->Veg) == '0')
                                      <img src="{{ asset('images/wrong.png') }}" />
                                      @else
                                      <img src="{{ asset('images/tick.png') }}" />
                                      @endif
                                 @endif
                                </span>
                            </div>
                            <div class="form-group">
                                <label class="col-6">Non-Veg :</label>
                                <span class="col-6">
                                @if(isset($pb_familyhist->NonVeg))  
                                      @if(($pb_familyhist->NonVeg) == '0')
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


            <div id="heathprofile-physical" class="tab-pane fade">

                <div class="row mt-5">

                    <?php
                        $user_id = Auth::id();
                        $pb_id = \App\Models\probationer::where('user_id', $user_id)->value('id');
                        $pb_phyexam = DB::table('probationer_physical_examination')->where('Probationer_Id', $pb_id)->first();

                    ?>

                    <div class="col-md-6">
                        <div class="physical-test-group-1">
                            <div class="form-group row">
                                <label for="" class="col-6">Blood Pressure :</label>
                                <span class="col-4">{{(isset($pb_phyexam->Bloodpressure) ? $pb_phyexam->Bloodpressure:false)}}</span>
                            </div>
                            <div class="form-group row">
                                <label class="col-6">Pulse :</label>
                                <span class="col-4">{{(isset($pb_phyexam->Pulse) ? $pb_phyexam->Pulse:false)}}</span>
                                
                            </div>
                            <div class="form-group row">
                                <label class="col-6">ENT :</label>
                                <span class="col-4">{{(isset($pb_phyexam->Ent) ? $pb_phyexam->Ent:false)}}</span>
                            </div>
                            <div class="form-group row">
                                <label class="col-6">Dental Examination :</label>
                                <span class="col-4">{{(isset($pb_phyexam->Dental) ? $pb_phyexam->Dental:false)}}</span>
                            </div>
                            <div class="form-group row">
                                <label class="col-6">Heart :</label>
                                <span class="col-4">{{(isset($pb_phyexam->Heart) ? $pb_phyexam->Heart:false)}}</span>
                            </div>
                            <div class="form-group row">
                                <label class="col-6">Lungs :</label>
                                <span class="col-4">{{(isset($pb_phyexam->Lungs) ? $pb_phyexam->Lungs:false)}}</span>
                            </div>
                            <div class="form-group row">
                                <label class="col-6">Abdomen :</label>
                                <span class="col-4">{{(isset($pb_phyexam->Abdomen) ? $pb_phyexam->Abdomen:false)}}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="physical-test-group-2">
                            <div class="eyesight mb-4">
                                <h6><b>Eye Sight</b></h6>
                                <p>With Glasses</p>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group px-4">
                                            <label>Left :</label>
                                            <span class="col-4">{{(isset($pb_phyexam->Eyewithleft) ? $pb_phyexam->Eyewithleft:false)}}</span>
                                        </div>
                                    </div>
                                    <div class="col-6 px-4">
                                        <div class="form-group">
                                            <label >Right :</label>
                                            <span class="col-4">{{(isset($pb_phyexam->Eyewithright) ? $pb_phyexam->Eyewithright:false)}}</span>

                                        </div>
                                    </div>
                                </div>
                                <p>Without Glasses</p>
                                <div class="row">
                                    <div class="col-6 px-4">
                                        <div class="form-group">
                                            <label >Left :</label>
                                            <span class="col-4">{{(isset($pb_phyexam->Eyewithoutleft) ? $pb_phyexam->Eyewithoutleft:false)}}</span>
                                        </div>
                                    </div>
                                    <div class="col-6 px-4">
                                        <div class="form-group">
                                            <label >Right :</label>
                                            <span class="col-4">{{(isset($pb_phyexam->Eyewithoutright) ? $pb_phyexam->Eyewithoutright:false)}}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="physical-test-group-2-sub">
                                <div class="form-group row">
                                    <label class="col-6">Urological System :</label>
                                    <span class="col-4">{{(isset($pb_phyexam->Urological) ? $pb_phyexam->Urological:false)}}</span>
                                </div>
                                <div class="form-group row">
                                    <label class="col-6">Athlete/Non Athlete :</label>
                                    <span class="col-4">{{(isset($pb_phyexam->Athlete) ? $pb_phyexam->Athlete:false)}}</span>
                                </div>
                                <div class="form-group row">
                                    <label class="col-6">Any Defect or Deformity :</label>
                                    <span class="col-4">{{(isset($pb_phyexam->Defectordeformity) ? $pb_phyexam->Defectordeformity:false)}}</span>
                                </div>
                                <div class="form-group row">
                                    <label class="col-6">Any scars of operation :</label>
                                    <span class="col-4">{{(isset($pb_phyexam->Scarsoperation) ? $pb_phyexam->Scarsoperation:false)}}</span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>


            <div id="heathprofile-investigation" class="tab-pane fade">

                <div class="row">
                    <?php
                        $user_id = Auth::id();
                        $pb_id = \App\Models\probationer::where('user_id', $user_id)->value('id');
                        $pb_investigation = DB::table('probationer_investigation')->where('Probationer_Id', $pb_id)->first();
                    ?>

                    <div class="col-md-6 p-4">
                        <div class="healthinvestigation-group-1">
                            <div class="form-group row">
                                <label class="col-6">Urine Examination :</label>
                                <span class="col-4">{{(isset($pb_investigation->Urine) ? $pb_investigation->Urine:false)}}</span>
                            </div>
                            <div class="form-group row">
                                <label class="col-6">Blood Group :</label>
                                <span class="col-4">{{(isset($pb_investigation->Bloodgroup) ? $pb_investigation->Bloodgroup:false)}}</span>
                            </div>
                            <div class="form-group row">
                                <label class="col-6">RH Factor :</label>
                                <span class="col-4">{{(isset($pb_investigation->Rhfactor) ? $pb_investigation->Rhfactor:false)}}</span>
                            </div>
                            <div class="form-group row">
                                <label class="col-6">Xray Chest PA view :</label>
                                <span class="col-4">{{(isset($pb_investigation->Xray) ? $pb_investigation->Xray:false)}}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="immunization">
                            <h6><b>Immunization</b></h6>
                            <div class="form-group mt-3 pr-5">
                                <label >Tetanus Oxide</label>
                                <div class="form-group">
                                    <div class="px-3">
                                        <span class="col-4">{{(isset($pb_investigation->Tetanus1) ? $pb_investigation->Tetanus1:false)}}</span><br>
                                        <span class="col-4">{{(isset($pb_investigation->Tetanus2) ? $pb_investigation->Tetanus2:false)}}</span><br>
                                        <span class="col-4">{{(isset($pb_investigation->Tetanus3) ? $pb_investigation->Tetanus3:false)}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
