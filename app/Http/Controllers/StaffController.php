<?php

namespace App\Http\Controllers;


use App\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class StaffController extends Controller
{

    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $role = Auth::user()->role;
        return view('staff.staffs', compact('role'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('staff.staff-list');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try
        {
        $request->validate([
            'Name' => 'required',
            'Email' => 'required|email',
            'Dob' => 'required',
            'MobileNumber' => 'required',
            'Role' => 'required',
        ]);

        $dob_date    = date('Y-m-d', strtotime($request->Dob));
        $dob_pass    = date('d-m-Y', strtotime($request->Dob));
        $uid = substr($request->Email, 0, strpos($request->Email, "@"));

        User::create([
            'name' => $request->Name,
            'email' => $request->Email,
            'username' => $uid,
            'password' => Hash::make(str_replace("-", "", "{$dob_pass}")),
            //'force_password_change' => '1',
            'Dob' => $dob_date,
            'MobileNumber' => $request->MobileNumber,
            'role' => $request->Role,
        ]);

        return redirect()->route('staffs.index')
                        ->with('success','Staff created successfully.');
        }
           catch(\Illuminate\Database\QueryException $e){
            $errorCode = $e->errorInfo[1];
            if($errorCode == '1062'){
                return redirect()->route('staffs.index')
                ->with('delete','Email or Mobile Number Already Registered.');
            }
          }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $role = Auth::user()->role;
        if($role === 'superadmin')
        {
            $staffs = User::where('role', '!=', 'probationer')->where('role', '!=', 'superadmin')->where('role', '!=', 'admin')->get();
        }
        else
        {
            $staffs = User::where('role', '!=', 'probationer')->where('role', '!=', 'admin')->get();
        }
        return view('staff.staff-list',compact('staffs'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Staff $staff)
    {
        $role = Auth::user()->role;
        return view('staff.editstaff',compact('staff', 'role'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Staff $staff)
    {
        try
        {

        $validated = $request->validate([
            'Name' => 'required',
            'Email' => 'required|email',
            'Dob' => 'required',
            'MobileNumber' => 'required',
            'Role' => 'required',
        ]);

        $dateofbirth    = date('Y-m-d', strtotime($request->Dob));
        $password = trim($request->password);
        $cPassword = trim($request->confirm_password);
        $uid = substr($request->Email, 0, strpos($request->Email, "@"));

        if($password != $cPassword)
        {
            return redirect()->back()->with('delete','Password and Confirm password should be same');
        }

       $aaa =  $staff->update([
            'name' => $request->Name,
            'email' => $request->Email,
            'password' => Hash::make($password),
            'username' => $uid,
            'Dob' => $dateofbirth,
            'MobileNumber' => $request->MobileNumber,
            'role' => $request->Role,
        ]);

        return redirect('/stafflist')->with('success','Staff updated successfully');
    }
    catch(\Illuminate\Database\QueryException $e){
        $errorCode = $e->errorInfo[1];
        if($errorCode == '1062'){
            return redirect()->route('staffs.index')
                    ->with('delete','Email Already Registered.');
        }
    }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Staff $staff)
    {
        $staff->delete();

        return redirect('/stafflist')->with('delete','Staff deleted successfully');
    }
}
