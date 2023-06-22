<?php

namespace App\Http\Controllers;

use App\Models\Lab;
use App\Models\Medicines;
use Illuminate\Http\Request;

class InserthospitaldataController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('hospitalization.adddata');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {


    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (array_key_exists("LabTestName", $request->input()))
        {
            $request->validate([
                'LabTestName' => 'required',
            ]);
            Lab::create($request->all());
            return redirect()->route('addmedicinedata.index')->with('success','LabTest created successfully.');
        }
        else
        {
            $request->validate([
                'MedicineName' => 'required',
                'MedicineContent' => 'required',
                'MedicineType' => 'required',
                'MedicineDosage' => 'required',
            ]);
            Medicines::create($request->all());
            return redirect()->route('addmedicinedata.index')->with('success','Medicine created successfully.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Lab  $lab
     * @return \Illuminate\Http\Response
     */
    public function show(Lab $lab)
    {


    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Lab  $lab
     * @return \Illuminate\Http\Response
     */
    public function edit(Lab $lab)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Lab  $lab
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Lab $lab)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Lab  $lab
     * @return \Illuminate\Http\Response
     */
    public function destroy(Lab $lab)
    {
        //
    }
    public function redirect()
    {
        return view('doctor.adddata');
    }

    public function doctorstore(Request $request)
    {
        if (array_key_exists("LabTestName", $request->input()))
        {
            $request->validate([
                'LabTestName' => 'required',
            ]);
            Lab::create($request->all());
            // return redirect()->route('adddata')
            // ->with('success','LabTest created successfully.');
            return view('doctor.adddata');
        }
        else
        {
            $request->validate([
                'MedicineName' => 'required',
                'MedicineContent' => 'required',
                'MedicineType' => 'required',
                'MedicineDosage' => 'required',
            ]);
            Medicines::create($request->all());
            // return redirect()->route('adddata')
            // ->with('success','Medicine created successfully.');
            return view('doctor.adddata');
        }
    }
}
