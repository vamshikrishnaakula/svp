<?php

namespace App\Http\Controllers;

use Response;
use App\Models\Doctor;
use App\Models\Appoinments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use \stdClass;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $currentuserid = Auth::user()->id;
        $today_appoinments = Appoinments::where('Doctor_Id', $currentuserid)->whereDay('appoinments.Appoinment_Time', now()->day)->where('appoinments.Status', 'Open')
        ->leftJoin('probationers', 'appoinments.Probationer_Id', '=', 'probationers.id')
        ->leftJoin('users', 'appoinments.Doctor_Id', '=', 'users.id')
        ->select('probationers.Name','probationers.RollNumber','probationers.id', 'appoinments.Appoinment_Time', 'users.name as user_name', 'appoinments.id as appoinmentid', 'Status')
        ->get();
        $today_appoinments_closed = Appoinments::where('Doctor_Id', $currentuserid)->whereDay('appoinments.Appoinment_Time', now()->day)->where('Status', 'Close')
        ->leftJoin('probationers', 'appoinments.Probationer_Id', '=', 'probationers.id')
        ->leftJoin('users', 'appoinments.Doctor_Id', '=', 'users.id')
        ->select('probationers.Name','probationers.RollNumber','probationers.id', 'appoinments.Appoinment_Time', 'users.name as user_name', 'appoinments.id as appoinmentid', 'Status')
        ->get();

        return view('doctor.appointments', compact('today_appoinments', 'today_appoinments_closed'));
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
       // print_r($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Doctor  $doctor
     * @return \Illuminate\Http\Response
     */
    public function show(Doctor $doctor)
    {
        return view('doctor.patientdetails');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Doctor  $doctor
     * @return \Illuminate\Http\Response
     */
    public function edit(Doctor $doctor)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Doctor  $doctor
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Doctor $doctor)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Doctor  $doctor
     * @return \Illuminate\Http\Response
     */
    public function destroy(Doctor $doctor)
    {
        //
    }
    public function get_prob_data(Request $request)
    {
        $currenturi = Route::currentRouteName();

        $probationer_details = array();
        $probationer_inpatient_detail = array();
        $prob_data = Appoinments::where('appoinments.id', $request->id)
        ->leftJoin('probationers', 'appoinments.Probationer_Id', '=', 'probationers.id')
        ->select('probationers.Name','probationers.RollNumber','probationers.id', 'probationers.gender', 'appoinments.Symptoms', 'appoinments.id as appoinmentid')
        ->first();

        $appoinments = Appoinments::where('probationer_id', $prob_data->id)
        ->where('status', 'Close')->orderBy('id', 'desc')
        ->get();

        // to know the inpatient id and count of prescriptions
        $inpatient_id = DB::table('in_patients')
        ->where('appointment_id', $request->id)
        ->first();
        // end


        foreach($appoinments as $appoinment)   // Probationer prescription, lab reports and vital signs
        {
            $vitalsigns = DB::table('probationer_vitalsign')
            ->where('appointment_id', $appoinment['id'])->orderBy('id', 'desc')
            ->get();
            $labreports = DB::table('probationer_labreports')
            ->where('appointment_id', $appoinment['id'])->orderBy('id', 'desc')
            ->get();
            $prescription = DB::table('probationer_prescription')
            ->select(DB::raw('*'))
            ->where('appointment_id', $appoinment['id'])
            ->get();
            $doctor_advice = DB::table('probationer_vitalsign')
            ->where('appointment_id', $appoinment['id'])
            ->get();
            $probationer_details[] = array(
                "date" => $appoinment['Appoinment_Time'],
                "vitalsigns" => $vitalsigns,
                "labreports" => $labreports,
                "prescription" => $prescription,
                "doctor_advice" => $doctor_advice,
            );

        }   // end


        $labuploads = DB::table('labreports')->where('Probationer_Id', $prob_data->id)->orderBy('id', 'desc')->get();
        $generalinfo = DB::table('probationer_general_info')->where('Probationer_Id', $prob_data->id)->first();
        $familyhistory = DB::table('probationer_family_history')->where('Probationer_Id', $prob_data->id)->first();
        $physicalinvestigation = DB::table('probationer_physical_examination')->where('Probationer_Id', $prob_data->id)->first();
        $medicalexam = DB::table('probationer_medical_exam')->where('Probationer_Id', $prob_data->id)->first();
        $investigation = DB::table('probationer_investigation')->where('Probationer_Id', $prob_data->id)->first();
        //$doctor_advice = DB::table('probationer_vitalsign')->where('probationer_id',$prob_data->id)->first();

       // print_r($inpatient_id->id);exit;

        if($currenturi == 'inpatientprescription')
        {
            $count1 = DB::table('probationer_inpatient_prescription')
            ->where('inpatient_id', $inpatient_id->id)
            ->where('prescription_number', '!=',  '0')
            ->groupBy('prescription_number')->get();


            for ($n = 1; $n <= count($count1); $n++) {

             $procedure = DB::table('probationer_inpatient_procedure')
            ->where('inpatient_id', $inpatient_id->id)->where('prescription_number', $n)
            ->get();
            $in_labreports = DB::table('probationer_inpatient_labreports')
            ->where('inpatient_id', $inpatient_id->id)->where('prescription_number', $n)
            ->get();
            $in_prescription = DB::table('probationer_inpatient_prescription')
            ->select(DB::raw('*'))
            ->where('inpatient_id', $inpatient_id->id)->where('prescription_number', $n)
            ->get();

            $probationer_inpatient_detail[] = array(
                "date" => $procedure[0]->created_at,
                "procedure" => $procedure,
                "labreports" => $in_labreports,
                "prescription" => $in_prescription,
            );
              }

              $probationer_inpatient_details = array_reverse($probationer_inpatient_detail);

            return view('doctor.inpatientprescritption', compact('prob_data','labuploads', 'generalinfo', 'familyhistory', 'physicalinvestigation', 'medicalexam', 'investigation', 'probationer_details', 'inpatient_id', 'probationer_inpatient_details'));
        }
        else
        {
            return view('doctor.patientdetails', compact('prob_data','labuploads', 'generalinfo', 'familyhistory', 'physicalinvestigation', 'medicalexam', 'investigation', 'probationer_details'));
        }

    }

    public function insertvitalsign(Request $request)
    {
        $timestamp  = date('Y-m-d H:i:s');
        $data  = DB::table('probationer_vitalsign')->insert([
            'probationer_id' => $request->id,
            'vitalsign' => $request->vitalsign,
            'created_at' => $timestamp,
            'updated_at' => $timestamp
        ]);
        return true;
    }

    public function insertprescription(Request $request)
    {
        $test = json_decode($request->inputs);
       // return json_encode($test);
        //$doctor_advices = $request->doctor_advice;
        $timestamp  = date('Y-m-d H:i:s');
        $date  = date('Y-m-d');
        foreach($test as $data)
        {
            if($data->name == 'pid')
            {
                $pid = $data->value;
            }
            if($data->name == 'appoinment_id')
            {
                $appoinment_id = $data->value;
            }
        }
        $sliced_array = (array_slice($test,3));
        $chunked_array = array_chunk($sliced_array,5);

        foreach($chunked_array as $dt)
        {
            if(substr_replace($dt[0]->name, "", -1) == 'drug')
            {
                DB::table('probationer_prescription')->insert([
                    'probationer_id' => $pid,
                    'appointment_id' => $appoinment_id,
                    'date' => $date,
                    'drug' => $dt[0]->value,
                    'dosage' => $dt[1]->value,
                    'frequency' => $dt[2]->value,
                    'duration' => $dt[3]->value,
                    'instructions' => $dt[4]->value,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp
                ]);
            }
        }
        DB::table('appoinments')->where('id',$appoinment_id)->update([
            'Status' => 'Close'
        ]);


        foreach($test as $data)
        {
            if(substr_replace($data->name, "", -1) == 'labtest')
            {
                if(!empty($data->value))
                {
                    DB::table('probationer_labreports')->insert([
                        'probationer_id' => $pid,
                        'appointment_id' => $appoinment_id,
                        'date' => $date,
                        'labreports' => $data->value,
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp
                    ]);
                }
            }
            elseif($data->name == 'vitalsigns')
            {

                DB::table('probationer_vitalsign')->updateOrInsert([
                    'probationer_id' => $pid,
                    'appointment_id' => $appoinment_id],
                    ['date' => $date,
                    'vitalsign' => $data->value,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp
                ]);
            }
            elseif($data->name == 'doctor_advice')
            {

                DB::table('probationer_vitalsign')->updateOrInsert([
                    'probationer_id' => $pid,
                    'appointment_id' => $appoinment_id],
                    ['date' => $date,
                    'doctor_advice' => $data->value,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp
                ]);
            }

        }
    }

    public function insertinpatientprescription(Request $request)
    {
        $test = json_decode($request->inputs);
        $timestamp  = date('Y-m-d H:i:s');
        $date  = date('Y-m-d');
        foreach($test as $data)
        {
            if($data->name == 'pid')
            {
                $pid = $data->value;
            }
            if($data->name == 'admit')
            {
                $patient_id ='0';
            }
        }

        $sliced_array = (array_slice($test,3));
        $chunked_array = array_chunk($sliced_array,5);
        if($patient_id == '0')
        {
            $in_patient_id = DB::table('in_patients')->where('probationer_id', $pid)->where('status', 'Open')->orderBy('id', 'desc')->first();
            $lastrow = DB::table('probationer_inpatient_prescription')->where('inpatient_id', $in_patient_id->id)->orderBy('id', 'desc')->first();
            $patient_id = $in_patient_id->id;
        }
        else
        {
            $lastrow = DB::table('probationer_inpatient_prescription')->where('inpatient_id', $patient_id)->orderBy('id', 'desc')->first();
        }


       ((isset($lastrow->prescription_number) == '') ? $count = '0' : $count = $lastrow->prescription_number);

       $count++;
        foreach($chunked_array as $dt)
        {
            if(substr_replace($dt[0]->name, "", -1) == 'drug')
            {
                DB::table('probationer_inpatient_prescription')->insert([
                    'probationer_id' => $pid,
                    'inpatient_id' => $patient_id,
                    'prescription_number' => $count,
                    'date' => $date,
                    'drug' => $dt[0]->value,
                    'dosage' => $dt[1]->value,
                    'frequency' => $dt[2]->value,
                    'duration' => $dt[3]->value,
                    'instructions' => $dt[4]->value,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp
                ]);
            }
        }
        foreach($test as $data)
        {
            if(substr_replace($data->name, "", -1) == 'labtest')
            {
                if(!empty($data->value))
                {
                    DB::table('probationer_inpatient_labreports')->insert([
                        'probationer_id' => $pid,
                        'inpatient_id' => $patient_id,
                        'prescription_number' => $count,
                        'date' => $date,
                        'labreports' => $data->value,
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp
                    ]);
                }
            }
            elseif($data->name == 'vitalsigns')
            {
                DB::table('probationer_inpatient_procedure')->insert([
                    'probationer_id' => $pid,
                    'inpatient_id' => $patient_id,
                    'prescription_number' => $count,
                    'date' => $date,
                    'procedure' => $data->value,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp
                ]);
            }
        }

    }


    public function getDownload($id){

        $file = public_path()."/uploads/".$id;
       $headers = array(
        'Content-Type' => 'application/pdf',
        );
        ob_clean ();
        return Response::download($file, 'report.pdf', $headers);

    }

    public function admitpatient(Request $request)
    {
        $timestamp  = date('Y-m-d H:i:s');
        $date  = date('Y-m-d');
        DB::table('in_patients')->insert([
            'probationer_id' => $request->pid,
            'appointment_id' => $request->appointment,
            'admitted_date' => $date,
            'status' => 'Open',
            'created_at' => $timestamp,
            'updated_at' => $timestamp
        ]);

        Appoinments::where('id',$request->appointment)->update([
            'Status' => 'Admitted'
        ]);
    }

    public function dischargepatient(Request $request)
    {
        $date  = date('Y-m-d');
        DB::table('in_patients')->where('id', $request->in_pat_id)->update([
            'status' => 'Discharge',
            'discharge_date' => $date
        ]);
    }

    public function inpatientlist(Request $request)
    {
        //echo "hii";exit;
        $patientslist = DB::table('in_patients')
            ->leftJoin('probationers', 'in_patients.probationer_id', '=', 'probationers.id')
            ->leftJoin('appoinments', 'appoinments.id', '=', 'in_patients.appointment_id')
            ->select('probationers.Name','probationers.RollNumber','probationers.id', 'probationers.gender', 'in_patients.admitted_date', 'in_patients.id', 'in_patients.status', 'appoinments.id as appoinmentid')->get();
       // print_r(json_encode($patientslist));exit;
        return view('doctor.inpatientlist', compact('patientslist'));
    }

    public function autocomplete(Request $request)
    {

        $term = $request->get('term');
        $data = DB::table('medicines')->where("MedicineName","LIKE","%$term%")->select('MedicineName as value')->get();
        return response()->json($data);

    }

    public function labreportautocomplete(Request $request)
    {

        $term = $request->get('term');
        $data = DB::table('labs')->where("LabTestName","LIKE","%$term%")->select('LabTestName as value')->get();
        return response()->json($data);

    }
}
