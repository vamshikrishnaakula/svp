<?php

namespace App\Http\Controllers;

use App\Models\HealthProfile;
use App\Models\probationer;
use App\Models\FamilyDependent;
use App\Models\Labreports;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HealthProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('hospitalization.healthprofiles');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $timestamp  = date('Y-m-d H:i:s');


        if(array_search('pid_generalinfo', array_column($request->data, 'name')))
        {


            $generalinfo = DB::table('probationer_general_info')->updateOrInsert([
                'Probationer_Id' => $request->data[2]['value']],

                ['Height' => $request->data[1]['value'],
                'Weight' => $request->data[3]['value'],
                'Expi' => $request->data[5]['value'],
                'Ins' => $request->data[6]['value'],
                'Expansion' => $request->data[7]['value'],
                'PastHistory' => $request->data[4]['value'],
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ]);
            $generalinfo_data = DB::table('probationer_general_info')->where('Probationer_Id', $request->data[2]['value'])->get();
            return $generalinfo_data;
        }
        elseif (array_search('pid_physicalexam', array_column($request->data, 'name')))
        {
            $physical_examination = DB::table('probationer_physical_examination')->updateOrInsert([
                'Probationer_Id' => $request->data[2]['value']],

                ['Bloodpressure' => $request->data[1]['value'],
                'Pulse' => $request->data[3]['value'],
                'Ent' => $request->data[4]['value'],
                'Dental' => $request->data[5]['value'],
                'Heart' => $request->data[6]['value'],
                'Lungs' => $request->data[7]['value'],
                'Abdomen' => $request->data[8]['value'],
                'Eyewithleft' =>$request->data[9]['value'],
                'Eyewithright' => $request->data[10]['value'],
                'Eyewithoutleft' => $request->data[11]['value'],
                'Eyewithoutright' => $request->data[12]['value'],
                'Urological' => $request->data[13]['value'],
                'Athlete' => $request->data[14]['value'],
                'Defectordeformity' => $request->data[15]['value'],
                'Scarsoperation' => $request->data[16]['value'],
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ]);
            $physical_examination_data = DB::table('probationer_physical_examination')->where('Probationer_Id', $request->data[2]['value'])->get();
            return $physical_examination_data;
        }
        elseif (array_search('pid_investigation', array_column($request->data, 'name')))
        {
            $investigation = DB::table('probationer_investigation')->updateOrInsert([
                'Probationer_Id' => $request->data[2]['value']],

                ['Urine' => $request->data[1]['value'],
                'Bloodgroup' =>$request->data[3]['value'],
                'Rhfactor' => $request->data[4]['value'],
                'Xray' => $request->data[5]['value'],
                'Tetanus1' => $request->data[6]['value'],
                'Tetanus2' => $request->data[7]['value'],
                'Tetanus3' => $request->data[8]['value'],
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ]);
            $investigation_data = DB::table('probationer_investigation')->where('Probationer_Id', $request->data[2]['value'])->get();
            return $investigation_data;
        }
        else
         {

           if ($request->diabetes == 'true') {$diabetics ='1';} else{$diabetics ='0';}
           if ($request->heartdiseases == 'true') {$heartdiseases ='1';} else{$heartdiseases ='0';}
           if ($request->migrane == 'true') {$migrane ='1';} else{$migrane ='0';}
           if ($request->epilepsy == 'true') {$epilepsy ='1';} else{$epilepsy ='0';}
           if ($request->allergy == 'true') {$allergy ='1';} else{$allergy ='0';}
           if ($request->smoking == 'true') {$smoking ='1';} else{$smoking ='0';}
           if ($request->alchocol == 'true') {$alchocol ='1';} else{$alchocol ='0';}
           if ($request->veg == 'true') {$veg ='1';} else{$veg ='0';}
           if ($request->nonveg == 'true') {$nonveg ='1';} else{$nonveg ='0';}
            $generalinfo = DB::table('probationer_family_history')->updateOrInsert([
                'Probationer_Id' => $request->pid],

                ['Diabetes' => $diabetics,
                'HeartDiseases' => $heartdiseases,
                'Migrane' => $migrane,
                'Epilepsy' => $epilepsy,
                'Allergy' => $allergy,
                'Smoking' => $smoking,
                'Alchohol' => $alchocol,
                'Veg' => $veg,
                'NonVeg' => $nonveg,
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ]);

            $family_history_data = DB::table('probationer_family_history')->where('Probationer_Id', $request->pid)->get();
            return $family_history_data;
        }

      //return view('hospitalization.healthprofiles')->with('success','Created successfully');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\HealthProfile  $healthProfile
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {

        $pid = probationer::where('id', $request->id)->first();

         //$pid = DB::table('probationers')->where('RollNumber', $request->id)->first();
        if($request->id != '')
        {
        $prob_details = probationer::where('probationers.id', $request->id)
        ->leftJoin('familydependents', 'probationers.id', '=', 'familydependents.Probationer_Id')
        ->leftJoin('squads', 'probationers.squad_id', '=', 'squads.id')
        ->select('probationers.id as pids', 'probationers.batch_id', 'probationers.Name', 'SquadNumber', 'probationers.MobileNumber', 'probationers.profile_url', 'familydependents.*')->get();

        $prob_all_details = DB::table('probationer_general_info')->where('probationer_general_info.Probationer_Id', $request->id)
        ->leftJoin('probationer_physical_examination', 'probationer_physical_examination.Probationer_Id', '=', 'probationer_general_info.Probationer_Id')
        ->leftJoin('probationer_investigation', 'probationer_investigation.Probationer_Id', '=', 'probationer_general_info.Probationer_Id')
        ->leftJoin('probationer_medical_exam', 'probationer_medical_exam.Probationer_Id', '=', 'probationer_general_info.Probationer_Id')
        ->leftJoin('probationer_family_history', 'probationer_family_history.Probationer_Id', '=', 'probationer_general_info.Probationer_Id')->get();

        $prescriptions = DB::table('probationer_prescription')->where('probationer_prescription.probationer_id', $request->id)
                                    ->select('probationer_prescription.*', 'appoinments.*', 'users.Name as doctor_name')
                                    ->leftJoin('appoinments', 'probationer_prescription.appointment_id', '=', 'appoinments.id')
                                    ->leftJoin('users', 'users.id', '=', 'appoinments.Doctor_Id')
                                    ->groupBy('appointment_id')
                                    ->get();

        // foreach($prescriptions as $prescription)
        // {
        //     $prescriptions['hospitalName'] = 'SVPNPA';
        // }

       // echo $prescriptions['hospitalName'];exit;

        $outdoor_prescriptions = Labreports::where('Probationer_Id',$request->id)
                                            ->where('report_type','prescription')
                                            ->leftJoin('outdoor_prescriptions','labreports.id', '=', 'outdoor_prescriptions.p_id')
                                            ->select('outdoor_prescriptions.hospital_name as h_name','outdoor_prescriptions.doctor_name', 'labreports.created_at', 'labreports.FileDirectory')
                                            ->get();

//print_r(json_encode($outdoor_prescriptions));exit;


        $data = array(
            'prob_details' => $prob_details,
            'prob_all_details' => $prob_all_details,
            'prescriptions' => $prescriptions,
            'outdoor_prescriptions' => $outdoor_prescriptions,
        );
        return $data;
        }
        else
            {
                return $pid;
            }

        }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\HealthProfile  $healthProfile
     * @return \Illuminate\Http\Response
     */
    public function edit(HealthProfile $healthProfile)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\HealthProfile  $healthProfile
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, HealthProfile $healthProfile)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\HealthProfile  $healthProfile
     * @return \Illuminate\Http\Response
     */
    public function destroy(HealthProfile $healthProfile)
    {

    }

    public function deleteprob(Request $request)
    {
        if($request->id != '')
        {
        FamilyDependent::where('id', $request->id)->delete();
        return true;
      }
    else
    {
        return false;
    }
    }

    public function storedependent(Request $request)
    {
        try {
            $family =  FamilyDependent::create([
                'Probationer_Id' => $request->pid,
                'DependentName' => $request->Name,
                'DependentAge' => $request->Age,
                'DependentGender' => $request->gender,
                'DependentRelationship' => $request->relationship,
            ]);

            $data = FamilyDependent::where('id', $family->id)->get();
            return $data;

        } catch (\Throwable $e) {
            return $e;
        }

    }
}
