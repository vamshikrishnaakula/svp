<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\probationer;
use App\Models\Medicines;
use App\Models\Appoinments;
use App\Models\Lab;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Redirect,Response;

use PDF;

class DoctorMedicalExamController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('doctor.medicalexamination');
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

        $date = explode("-",$request->data[10]['value']);
        //  print_r(json_encode($request->data));
        //  exit;
       //zqaVC4 Q32AS2ZZ return $date[0];
        // if(array_key_exists('pid_medical', array_column($request->data, 'name')))
         {

            $medicalinfo = DB::table('probationer_medical_exam')->updateOrInsert([
                'Probationer_Id' => $request->data[1]['value'],
                'year' => $date[0],
                'month' => $date[1]],

                ['temperature' => $request->data[0]['value'],
                'antigentest' => $request->data[2]['value'],
                'rtpcr' => $request->data[3]['value'],
                'haemoglobin' => $request->data[4]['value'],
                'calcium' => $request->data[5]['value'],
                'vitamind' => $request->data[6]['value'],
                'vitaminb12' => $request->data[7]['value'],
                'preexistinginjury' => $request->data[8]['value'],
                'covid' => $request->data[9]['value'],
                'date' => $request->data[10]['value'],
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ]);
       }
        return true;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $prob_data =   Probationer::where('probationers.id', $request->id) ->leftJoin('probationer_medical_exam', 'probationer_medical_exam.Probationer_Id', '=', 'probationers.id')->select('probationers.id as pid','probationers.Name','probationers.Dob', 'probationers.gender', 'probationer_medical_exam.*')->orderBy('probationer_medical_exam.id', 'DESC')->first();
        return $prob_data;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $id = isset($request->m_id) ? $request->m_id : '';
        $medicine_name = isset($request->m_name) ? $request->m_name : '';
        $medicine_content = isset($request->m_content) ? $request->m_content : '';
        $medicine_type = isset($request->m_type) ? $request->m_type : '';
        $medicine_manufacture = isset($request->m_manufacture) ? $request->m_manufacture : '';
        $medicine_dosage = isset($request->m_dosage) ? $request->m_dosage : '';

        $update_medicines = Medicines::updateOrCreate([
            'id' =>$request->id,
        ],
        [
            'MedicineName' => $medicine_name,
            'MedicineContent' => $medicine_content,
            'MedicineType' => $medicine_type,
            'MedicineManufacturer' => $medicine_manufacture,
            'MedicineDosage' => $medicine_dosage,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function view_medical_exam(Request $request)
    {
        $date = explode("/",$request->date);
        $prob_data =   Probationer::where('RollNumber', $request->id)->where('probationer_medical_exam.month', $date[0])->where('probationer_medical_exam.year', $date[1])->leftJoin('probationer_medical_exam', 'probationer_medical_exam.Probationer_Id', '=', 'probationers.id')->select('probationers.id as pid','probationers.Name','probationers.Dob', 'probationers.gender', 'probationer_medical_exam.*')->orderBy('probationer_medical_exam.id', 'DESC')->first();
        return $prob_data;
    }
    public function get_medicines_data(Request $request)
    {
        try{
        $medicine_name = isset($request['drug']) ? $request['drug'] : '';
        $medicine_type = isset($request['medicine']) ? $request['medicine'] : '';

        if($medicine_type == "")
        {
            $medicines_data = Medicines::Where('MedicineName', 'like', '%' . $medicine_name . '%')->get();
        }
        else
        {
             $medicines_data = Medicines::Where('MedicineName', 'like', '%' . $medicine_name . '%')->Where('MedicineType', $medicine_type)->get();
        }

        if(count($medicines_data) >= '1')
        {
            return $medicines_data;
        }
        else
        {
            return "1";
        }
        }
        catch (Exception $e) {
            return $e->getMessage();
        }
    }
    public function get_medicine($id)
    {
        $where = array('id' => $id);
		$Medicine = Medicines::where($where)->first();
		return Response::json($Medicine);
    }

    public function get_lab($id)
    {
        $where = array('id' => $id);
		$Medicine = Lab::where($where)->first();
		return Response::json($Medicine);
    }

    public function update_medicines(Request $request)
    {
        $id = isset($request->m_id) ? $request->m_id : '';
        $medicine_name = isset($request->m_name) ? $request->m_name : '';
        $medicine_content = isset($request->m_content) ? $request->m_content : '';
        $medicine_type = isset($request->m_type) ? $request->m_type : '';
        $medicine_manufacture = isset($request->m_manufacture) ? $request->m_manufacture : '';
        $medicine_dosage = isset($request->m_dosage) ? $request->m_dosage : '';

        $update_medicines = Medicines::updateOrCreate([
            'id' =>$id,
        ],
        [
            'MedicineName' => $medicine_name,
            'MedicineContent' => $medicine_content,
            'MedicineType' => $medicine_type,
            'MedicineManufacturer' => $medicine_manufacture,
            'MedicineDosage' => $medicine_dosage,
        ]);
        return true;
    }

    public function update_labs(Request $request)
    {
        $id = isset($request->m_id) ? $request->m_id : '';
        $lab_name = isset($request->m_name) ? $request->m_name : '';

        $update_medicines = Lab::updateOrCreate([
            'id' =>$id,
        ],
        [
            'LabTestName' => $lab_name
        ]);
        return true;
    }
    public function delete_medicine(Request $request)
    {
        $where = array('id' => $request->id);
		$Medicine = Medicines::where($where)->delete();
		return true;
    }
    public function delete_lab(Request $request)
    {
        $where = array('id' => $request->id);
		$Medicine = Lab::where($where)->delete();
		return true;
    }
    public function inpatients_history(Request $request)
    {
        $date = $request->date;
        $currentuserid = Auth::user()->id;
        $inpatients_appoinments = Appoinments::where('Doctor_Id', $currentuserid)->whereDay('appoinments.Appoinment_Time', $date)
        ->leftJoin('probationers', 'appoinments.Probationer_Id', '=', 'probationers.id')
        ->leftJoin('users', 'appoinments.Doctor_Id', '=', 'users.id')
        ->select('probationers.Name','probationers.RollNumber','probationers.id', 'appoinments.Appoinment_Time', 'users.name as user_name', 'appoinments.id as appoinmentid', 'Status')
        ->get();
        return $inpatients_appoinments;

    }

    public function appointment_summary(Request $request)
    {
        $appointment_id = $request->id;
        $prescription_summary = DB::table('probationer_prescription')->where('appointment_id', $appointment_id)->get();
        $vitalsigns = DB::table('probationer_vitalsign')
            ->where('appointment_id', $appointment_id)->orderBy('id', 'desc')
            ->get();

            $labreports = DB::table('probationer_labreports')
            ->where('appointment_id', $appointment_id)->orderBy('id', 'desc')
            ->get();
            $prob_data = Appoinments::where('appoinments.id', $request->id)
            ->leftJoin('probationers', 'appoinments.Probationer_Id', '=', 'probationers.id')
            ->select('probationers.Name','probationers.RollNumber','probationers.id', 'probationers.gender', 'appoinments.Symptoms', 'appoinments.id as appoinmentid', 'appoinments.Appoinment_Time')
            ->first();
        return view('doctor.prescriptioninfo', compact('prescription_summary', 'labreports', 'prob_data', 'vitalsigns'));
    }
    public function get_inpatients_data(Request $request)
    {
        $p_id = $request->id;
        //$roll = Probationer::
        $probationer_id = Probationer::where('id', $p_id)->first();
        //echo $probationer_id;exit;
        $in_patients_prescriptions = DB::table('in_patients')
                                    ->where('in_patients.probationer_id', $probationer_id->id)
                                    ->leftJoin('probationers', 'in_patients.Probationer_Id', '=', 'probationers.id')
                                    ->leftJoin('appoinments', 'in_patients.appointment_id', '=', 'appoinments.id')
                                    ->leftJoin('users', 'appoinments.Doctor_Id', '=', 'users.id')
                                    ->select('in_patients.id as in_pat_id', 'in_patients.appointment_id', 'in_patients.admitted_date', 'in_patients.discharge_date', 'probationers.id', 'users.name', 'probationers.RollNumber')
                                   ->get();
                                  // print_r($in_patients_prescriptions);exit;
        return $in_patients_prescriptions;
    }

    public function get_patient_history(Request $request)
    {
        $currentuserid = Auth::user()->id;

        $probationer_id = Probationer::where('id',$request->id)->first();

        // $sql = " DATEDIFF(items.date_start, '".date('Y-m-d')."' ) = ".$SomeDayDiff;

        if(DB::table('in_patients')->where('Probationer_Id', $request->id)->exists())
        {
            $get_patient_history = Appoinments::where('Doctor_Id', $currentuserid)
            ->where('appoinments.probationer_id',$probationer_id->id)
            ->leftJoin('in_patients','appoinments.Probationer_Id', '=','in_patients.probationer_id')
            ->leftJoin('users', 'appoinments.Doctor_Id', '=', 'users.id')
            ->select('in_patients.id as in_pat_id','in_patients.admitted_date', 'in_patients.discharge_date','users.name', DB::raw("DATEDIFF(discharge_date,admitted_date)AS count"))
            ->get();

        }

        return $get_patient_history;


    }

    public function get_patient_report(Request $request)
    {
        $p_id = $request->id;
        $get_patient_reports = DB::table('labreports')
                                    ->where('Probationer_Id',$p_id)
                                    ->whereIn('report_type', ['investigationreport','others'])
                                    ->select('id as in_pat_ids','labreports.ReportName','labreports.FileDirectory','labreports.created_at')
                                    ->get();
        return $get_patient_reports;
    }

    public function discharge_summary(Request $request)
    {

        $user_id = Auth::id();
        $inpatient_id = $request->id;

        $Probationer =  DB::table('in_patients')->where('id', $inpatient_id)->value('probationer_id');
        $doctor_advie =  DB::table('in_patients')->where('id', $inpatient_id)->value('doctor_advice');

        $pdf_title = "Prescription-".$Probationer;
        $data = [
            'pdf_title' => $pdf_title,
            'Probationer' => $Probationer,
            'id' => $inpatient_id,
            'doctor_advice' => $doctor_advie
        ];

        $config = [
        'title' => $pdf_title,
        'format' => 'A4',
        'orientation' => 'P',
        'margin_left' => 7,
        'margin_right' => 7,
        'margin_top' => 7,
        'margin_bottom' => 7,
        ];

        $pdf = PDF::loadView('doctor.dischargesummary-pdf', $data, [], $config);
        return $pdf->download("{$pdf_title}.pdf");
    }

    public function getDownload($id){

        $file = public_path()."/uploads/".$id;
       $headers = array(
        'Content-Type' => 'application/pdf',
        );
        ob_clean ();
        return Response::download($file, 'report.pdf', $headers);
    }




    public function insert_inpatientdischarge_medication(Request $request)
    {
        $test = json_decode($request->inputs);
        $patient_id = $request->pid;
        $in_patient_id = $request->in_patient_id;
        $doctor_advice = $request->doctor_advice;

        $timestamp  = date('Y-m-d H:i:s');
        $date  = date('Y-m-d');

        $chunked_array = array_chunk($test,5);

        foreach($chunked_array as $dt)
        {
            if(substr_replace($dt[0]->name, "", -1) == 'drug')
            {
                DB::table('probationer_inpatient_prescription')->insert([
                    'probationer_id' => $patient_id,
                    'inpatient_id' => $in_patient_id,
                    'prescription_number' => '0',
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
        DB::table('in_patients')->where('id', $in_patient_id)->update([
            'status' => 'Discharge',
            'doctor_advice' => $doctor_advice,
            'discharge_date' => $date
        ]);
        return true;
    }

    // public function get_patient_report(Request $request)
    // {
    //     $p_id = $request->id;

    //     //$probationer_id = Probationer::where('id', $p_id)->first();

    //     $get_patient_reports = DB::table('labreports')
    //                                 ->where('Probationer_Id',$p_id)
    //                                 ->select('id as in_pat_ids','labreports.ReportName','labreports.FileDirectory','labreports.created_at')
    //                                 ->get();
    //                             //   print_r($get_patient_reports);exit;
    //     return $get_patient_reports;
    // }

    // public function getDownload($id){

    //     $file = public_path()."/uploads/".$id;
    //    $headers = array(
    //     'Content-Type' => 'application/pdf',
    //     );
    //     ob_clean ();
    //     return Response::download($file, 'report.pdf', $headers);
    // }


    public function editprescription(Request $request)
    {
       //echo "hi";exit;

        $appointment_id = $request->id;
        $prescription_summary = DB::table('probationer_prescription')->where('appointment_id', $appointment_id)->get();
       // echo $prescription_summary->frequency;exit;
        $vitalsigns = DB::table('probationer_vitalsign')
            ->where('appointment_id', $appointment_id)->orderBy('id', 'desc')
            ->first();
          // echo $vitalsigns->vitalsign;exit;

            $labreports = DB::table('probationer_labreports')
            ->where('appointment_id', $appointment_id)->orderBy('id', 'desc')
            ->get();
            $prob_data = Appoinments::where('appoinments.id', $request->id)
            ->leftJoin('probationers', 'appoinments.Probationer_Id', '=', 'probationers.id')
            ->select('probationers.Name','probationers.RollNumber','probationers.id', 'probationers.gender', 'appoinments.Symptoms', 'appoinments.id as appoinmentid', 'appoinments.Appoinment_Time')
            ->first();

        return view('doctor.edit_prescription', compact('prescription_summary', 'labreports', 'prob_data', 'vitalsigns'));

    }



    public function updatepresciption(Request $request)
    {
        DB::beginTransaction();
        try
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
        if($data->name == 'appoinment_id')
        {
            $appoinment_id = $data->value;
        }
    }

    $sliced_array = (array_slice($test,3));
    $chunked_array = array_chunk($sliced_array,5);

    DB::table('probationer_prescription')->where('probationer_id',$pid)->where('appointment_id',$appoinment_id)->delete();
    foreach($chunked_array as $dt)
    {
        if(substr_replace($dt[0]->name, "", -1) == 'drug')
        {
            DB::table('probationer_prescription')->updateOrInsert([
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

    DB::table('probationer_labreports')->where('probationer_id',$pid)->where('appointment_id',$appoinment_id)->delete();

    foreach($test as $data)
    {
        if(substr_replace($data->name, "", -1) == 'labtest')
        {
            if(!empty($data->value))
            {
                DB::table('probationer_labreports')->updateOrInsert([
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

        DB::commit();
        // $response = [
        //     'code' => "200",
        //     'status' => "success",
        //     'message' => "Data saved successfully",
        // ];

    }
        }
        catch(\Illuminate\Database\QueryException $e)
        {
            DB::rollback();
            //return redirect('/doctor');
        }
    }
}
