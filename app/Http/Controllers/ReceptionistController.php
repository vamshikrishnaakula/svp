<?php

namespace App\Http\Controllers;


use App\Staff;
use App\Models\User;
use App\Models\Receptionist;
use App\Models\probationer;
use App\Models\Appoinments;
use App\Models\Labreports;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReceptionistController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $staffs = User::where('role', 'doctor')->select('name', 'id')->get();

        $today_appoinment = Appoinments::whereDay('Appoinment_Time', now()->day)
        ->leftJoin('probationers', 'appoinments.Probationer_Id', '=', 'probationers.id')
        ->leftJoin('users', 'appoinments.Doctor_Id', '=', 'users.id')
        ->select('probationers.Name','probationers.RollNumber','probationers.id', 'appoinments.Appoinment_Time', 'users.name', 'appoinments.status')
        ->orderBy('id')
        ->get();

        $Inpatientslist = DB::table('in_patients')
        ->leftJoin('probationers', 'in_patients.probationer_id', '=', 'probationers.id')
        ->select('probationers.Name','probationers.RollNumber','probationers.id', 'in_patients.id', 'in_patients.status', 'in_patients.admitted_date')
        ->get();
      //  print_r($today_appoinment);exit;
        return view('receptionist.receptionist', compact('staffs', 'today_appoinment', 'Inpatientslist'));
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


        try{
        $appoinment_date = date('Y-m-d H:i:s', strtotime($request->Appoinment_Time));
        Appoinments::create([
            'Probationer_Id' => $request->prob_id,
            'Doctor_Id' => $request->Doctor_Id,
            'Symptoms' => $request->Symptoms,
            'Appoinment_Time' => $appoinment_date,
            'Status' => 'Open',
        ]);
        return redirect()->route('receptionist.index')->with('success','Appointment created successfully.');
   }
        catch(\Illuminate\Database\QueryException $e){
            $errorCode = $e->errorInfo[1];
            if($errorCode == '1048'){
                return redirect()->route('receptionist.index')
                        ->with('delete','Please get data for that probationer');
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Receptionist  $receptionist
     * @return \Illuminate\Http\Response
     */
    public function show(Receptionist $receptionist)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Receptionist  $receptionist
     * @return \Illuminate\Http\Response
     */
    public function edit(Receptionist $receptionist)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Receptionist  $receptionist
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Receptionist $receptionist)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Receptionist  $receptionist
     * @return \Illuminate\Http\Response
     */
    public function destroy(Receptionist $receptionist)
    {
        //
    }

    public function get_prob_data(Request $request)
    {
      // print_r($request->all());exit;
        $prob_data = probationer::where('id', $request->id)
        ->select('Name', 'id', 'Dob', 'gender')->first();
        return $prob_data;
    }

    public function labreports(Request $request)
    {
        return view('receptionist.labreports');
    }

    public function fileUploadPost(Request $request)
    {

        $request->validate([
            'file' => 'required|mimes:pdf,xlx,csv|max:2048',
            'prob_id' => 'required'
        ]);
        $fileName = $request->ReportName . time() . '.'.$request->file->extension();
        $request->file->move(public_path('uploads'), $fileName);

        // $aa = $request->all;
        // print_r("hiii");
        // exit;
        if($request->doctype === "prescription")
        {
            $report_name = 'outdoor_prescriptions';
        }
        else
        {
            $report_name = $request->ReportName;
        }

   $lastinsertId = Labreports::create([
            'ReportName' => $report_name,
            'Probationer_Id' => $request->prob_id,
            'report_type' => $request->doctype,
            'FileDirectory' => $fileName,
        ]);

        if($request->doctype === "prescription")
        {
            DB::table('outdoor_prescriptions')->insert([
                 'p_id' => $lastinsertId->id,
                 'doctor_name' => $request->doc_name,
                 'hospital_name' => $request->hos_name,
                 'report_name' => $report_name
            ]);
        }


        return back()
            ->with('success','File Uploaded Successfully.')
            ->with('file',$fileName);

    }
}

