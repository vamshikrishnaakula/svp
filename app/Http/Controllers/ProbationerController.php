<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\probationer;
use App\Models\User;
use App\Models\Squad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ProbationerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $batch = Batch::all();
        return view('probationer.probationers', compact('batch'));
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
        try
        {
            $dob_date = date('Y-m-d', strtotime($request->Dob));
            $dob_pass = date('d-m-Y', strtotime($request->Dob));
            $uid = substr($request->Email, 0, strpos($request->Email, "@"));




            $verify_rollnumber = Probationer::where('batch_id', $request->batch_id)->where('RollNumber', $request->Rollnumber)->count();


            if ($verify_rollnumber != '0') {
                return redirect()->route('probationers.index')
                    ->with('delete', 'Probationer With this Roll Number Already Registered.');
            }



            $lastuserinserted = User::create([
                'name' => $request->Name,
                'email' => $request->Email,
                'password' => Hash::make("Svpnpa@123"),
                'username' => $uid,
                //'force_password_change' => 1,
                'Dob' => $dob_date,
                'MobileNumber' => $request->MobileContactNumber,
                'role' => "probationer",
            ]);

            $insertedId = $lastuserinserted->id;

            Probationer::create([
                'batch_id' => $request->batch_id,
                'RollNumber' => $request->Rollnumber,
                'Cadre' => $request->Cadre,
                'Name' => $request->Name,
                'Email' => $request->Email,
                'Dob' => $dob_date,
                'MobileNumber' => $request->MobileContactNumber,
                'gender' => $request->Gender,
                'user_id' => $insertedId,
                'squad_id' => null,
            ]);


            return redirect()->route('probationers.index')
                ->with('success', 'Probationer created successfully.');
        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->errorInfo[1];
            if ($errorCode == '1062') {
                return redirect()->route('probationers.index')
                    ->with('delete', 'Probationer Email or Mobile Number Already Registered.');
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\probationer  $probationer
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        // $batches = Batch::all();
        // $batch_id = current_batch();
        // $probationers = Probationer::where('probationers.batch_id', $batch_id)
        // ->leftJoin('batches', 'batches.id', '=', 'probationers.batch_id')
        // ->leftJoin('users','squads.DrillInspector_Id','=','users.id')
        // ->select('probationers.id', 'probationers.Name', 'probationers.Email', 'probationers.MobileNumber', 'probationers.Dob', 'batches.BatchName')
        // ->get();
        // return view('probationer.probationer-list', compact('batches', 'probationers'));

        $batches = Batch::all();
        $batch_id = current_batch();
        $probationers = Probationer::leftJoin('batches', 'batches.id', '=', 'probationers.batch_id')
        ->leftJoin('squads', 'squads.id', '=',  'probationers.squad_id')
        //->leftJoin('users', 'squads.DrillInspector_Id', '=', 'users.id')
        ->where('probationers.batch_id', $batch_id)
        ->select('probationers.id', 'probationers.Name', 'probationers.Email', 'probationers.MobileNumber', 'probationers.Dob', 'batches.BatchName','squads.SquadNumber')
        ->get();
        //echo $probationers;exit;
        return view('probationer.probationer-list', compact('batches', 'probationers', 'batch_id'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\probationer  $probationer
     * @return \Illuminate\Http\Response
     */
    public function edit(probationer $probationer)
    {
        $probationer = Probationer::where('probationers.id', $probationer->id)->leftJoin('batches', 'batches.id', '=', 'probationers.batch_id')->select('probationers.*', 'batches.BatchName')->first();
        $batches = Batch::all();
        return view('probationer.editprobationer', compact('probationer', 'batches'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\probationer  $probationer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Probationer $probationer)
    {
        try
        {
            $dob_date = date('Y-m-d', strtotime($request->Dob));
            $dob_pass = date('d-m-Y', strtotime($request->Dob));
            $uid = substr($request->Email, 0, strpos($request->Email, "@"));
            $probationer->update($request->all());

            $password = trim($request->password);
            $cPassword = trim($request->confirm_password);

            if($password != $cPassword)
            {
                return redirect()->back()->with('delete','Password and Confirm password should be same');
            }

            User::where('id', $request->pid)->update([
                'name' => $request->Name,
                'email' => $request->Email,
                'username' => $uid,
                'password' => Hash::make($password),
                'Dob' => $request->Dob,
                'MobileNumber' => $request->MobileContactNumber,
                'role' => "Probationer",
            ]);

            Probationer::where('user_id', $request->pid)->update([
                'batch_id' => $request->batch_id,
                'RollNumber' => $request->Rollnumber,
                'Cadre' => $request->Cadre,
                'Name' => $request->Name,
                'Email' => $request->Email,
                'Dob' => $dob_date,
                'MobileNumber' => $request->MobileContactNumber,
                'gender' => $request->Gender,
            ]);

            return redirect('/probationerlist')->with('success', 'Probationer updated successfully');
        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->errorInfo[1];
            if ($errorCode == '1062') {
                return redirect()->route('probationers.index')
                    ->with('delete', 'Probationer Email or Mobile Number Already Registered.');
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\probationer  $probationer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Probationer $probationer)
    {
       // print_r($pro)
        $probationer->delete();
        User::where('email', $probationer->Email)->delete();
        return redirect('/probationerlist')->with('delete', 'Probationer deleted successfully');
    }

    public function profileview(Request $request)
    {
        $get_probationer = Probationer::where('id', $request->id)->first();
        return view('probationer.viewprobationer', compact('get_probationer'));
    }

    public function batchwiseprob(Request $request)
    {
        if($request->requestname == 'probationerlist')
        {
            // $get_probationer = Probationer::where('batch_id', $request->id)
            // ->join('batches', 'batches.id', '=', 'probationers.batch_id')
            // ->join('squads', 'squads.id', '=',  'probationers.squad_id')
            // ->select('BatchName','RollNumber','Name','probationers.id','Dob', 'MobileNumber', 'Email','squads.SquadNumber')->get();

            $get_probationer = Probationer::where('probationers.batch_id',$request->id)
            ->leftJoin('squads', 'squads.id', '=', 'probationers.squad_id')
            ->select('probationers.id','probationers.Name', 'probationers.Email', 'probationers.MobileNumber', 'probationers.Dob', 'squads.SquadNumber')
            ->get();

        }
        else
        {
            $get_probationer = Probationer::where('batch_id', $request->id)
            ->whereNull('squad_id')
            ->join('batches', 'batches.id', '=', 'probationers.batch_id')
            ->select('BatchName','RollNumber','Name','probationers.id','Dob', 'MobileNumber', 'Email')
            ->get();
        }

        return $get_probationer;
    }

    public function prob_autosuggestion(Request $request)
    {
        $term = $request->get('term');
        $data = Probationer::where("Name", "LIKE", "%$term%")->orWhere("RollNumber", "LIKE", "%$term%")->select('Name as value', 'id')->get();
        return response()->json($data);
    }

    public function delete_probationer(Request $request)
    {
        Probationer::where('id',$request->id)->delete();
        return true;
    }

    public function ajax(Request $request)
    {
        $requestName = $request->requestName;
        if ($requestName === "get_probationerImport_modal") {

            return view('probationer.import-probationer-modal', ['request' => $request]);
        }

        if ($requestName === "download_probationerDatasheet") {

            $batch_id = intval($request->data_batch_id);
            $squad_id = intval(isset($request->data_squad_id) ? $request->data_squad_id : '0');
            $errors = [];
            if (empty($batch_id)) {
                $errors[] = "Select Batch.";
            }


            if (empty($squad_id)) {
                $squads = \App\Models\Squad::where('Batch_Id', $batch_id)->orderBy('SquadNumber', 'asc')->pluck('id')->toArray();
               // print_r(json_encode($squads));exit;
                if(empty($squads)) {
                    $errors[]   = "No squads available for the selected Batch.";
                }
            } else {
                $squads = [$squad_id];
            }

            // $count = Probationer::where('batch_id', $batch_id)->where('squad_id', $squad_id)->count();

            // if ($count === 0) {
            //     return json_encode([
            //         'status' => "error",
            //         'message' => "Empty probationers",
            //     ]);
            // }

            if (empty($errors)) {

                $data_request = [
                    'batch_id' => $batch_id,
                    'squad_id' => $squads,
                ];
                $data_request = data_crypt(serialize($data_request));

                $datasheet_url = url("/probationers/download-probationer-datasheet/{$data_request}");
                return json_encode([
                    'status' => "success",
                    'datasheet_url' => $datasheet_url,
                ]);
            } else {
                return json_encode([
                    'status' => "error",
                    'message' => "Error: " . implode('<br />', $errors),
                ]);
            }

        }

        if ($requestName === "get_probationerImport_sample") {

            $batch_id = intval($request->data_batch_id);
            $errors = [];
            if (empty($batch_id)) {
                $errors[] = "Select Batch.";
            }

            if (empty($errors)) {

                $data_request = [
                    'batch_id' => $batch_id,
                ];
                $data_request = data_crypt(serialize($data_request));

                $datasheet_url = url("/probationers/sample-download-probationer-datasheet/{$data_request}");
                return json_encode([
                    'status' => "success",
                    'datasheet_url' => $datasheet_url,
                ]);
            } else {
                return json_encode([
                    'status' => "error",
                    'message' => "Error: " . implode('<br />', $errors),
                ]);
            }

        }
        if ($requestName === "import_Probationer_DataSheet") {

            if ($request->hasFile('probationer_csv') && $request->file('probationer_csv')->isValid()) {
                $errorMsg = [];
                $dataRowError = [];
                $result = [];
                

                $original_filename = $request->probationer_csv->getClientOriginalName();
                $ext = pathinfo($original_filename, PATHINFO_EXTENSION);

                if ($ext !== 'csv') {
                    $result['status'] = 'error';
                    $result['message'] = "Please upload a file with a .csv extension.";
                    return json_encode($result);
                }

                $fileName = time() . '-' . $original_filename;
                $request->probationer_csv->storeAs('csv_files', $fileName);

                $rowsToSkip = 0;
                $filePath = storage_path("app/public/csv_files/{$fileName}");
                $fileData = csvToArray($filePath, ',', $rowsToSkip);

                if (count($fileData) > 0) {
                    $valid_data_keys = [
                        'S.NO',
                        'batch_name',
                        'roll_number',
                        'cadre',
                        'name',
                        'email',
                        'date_of_birth(YYYY-MM-DD)',
                        'gender',
                        'mobile_number',
                    ];

                    $probationerData = [];
                    $i = 1;

                    foreach ($fileData as $key => $data) {

                        $row_num = $i++ + ($rowsToSkip + 1);

                        if ($i === 2) {
                            $data_keys = array_keys($data);
                            $diff_keys = array_diff($data_keys, $valid_data_keys);
                            if (count($diff_keys) > '1') {
                                $errorMsg = "This file contains invalid data.";
                                $result['message'] = $errorMsg;
                                $result['status'] = 'error';
                                return json_encode($result);
                            }
                        }
                        $batch_name = trim($data["batch_name"]);
                        $roll_number = trim($data["roll_number"]);
                        $cadre = trim($data["cadre"]);
                        $name = trim($data["name"]);
                        $email = trim($data["email"]);
                        $date_of_birth = trim($data["date_of_birth(YYYY-MM-DD)"]);
                        $gender = trim($data["gender"]);
                        $mobile_number = trim($data["mobile_number"]);

                        if (empty($batch_name)) {
                            $dataRowError[] = "Row #{$row_num}: Empty Batch.";
                        } else {
                            $check_batch = Batch::where('BatchName', $batch_name)->count();
                            if ($check_batch !== 1) {
                                $dataRowError[] = "Row #{$row_num}: Invalid Batch.";
                            }
                        }

                        if (empty($roll_number)) {
                            //$dataRowError[] = "Row #{$row_num}: Empty Roll Number";
                        } else {
                            $check_rollnumber = Probationer::where('RollNumber', $roll_number)->count();
                            if ($check_rollnumber >= 1) {
                                $dataRowError[] = "Row #{$row_num}: Roll Number Already Registered.";
                            }
                        }
                        if (empty($name)) {
                            $dataRowError[] = "Row #{$row_num}: Empty Name ";
                        }
                        $dob ='';
                        if (empty($email)) {
                            $dataRowError[] = "Row #{$row_num}: Empty Email ";
                        } else {
                            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                $dataRowError[] = "Row #{$row_num}: Invalid email ";
                            } else {
                                $check_email = Probationer::where('Email', $email)->count();
                                if ($check_email >= 1) {
                                    $dataRowError[] = "Row #{$row_num}: Email Id Already Registered.";
                                }
                            }

                        }
                        if (empty($date_of_birth)) {
                            //$dataRowError[] = "Row #{$row_num}: Empty Date of Birth ";
                        } else {
                            if (isValidDate($date_of_birth, 'Y-m-d') === true || isValidDate($date_of_birth, 'd-m-Y') === true) {
                                $dob = date('Y-m-d', strtotime($date_of_birth));
                            } else {
                                $dataRowError[] = "Row #{$row_num}: Invalid date format (expected 'YYYY-MM-DD').";
                            }
                        }

                            if (empty($gender)) {
                            $dataRowError[] = "Row #{$row_num}: Empty Gender ";
                        } else {
                            if (strtolower($gender) === 'male' || strtolower($gender) === 'female' || strtolower($gender) === 'others') {

                            } else {
                                $dataRowError[] = "Row #{$row_num}: Invalid Gender";
                            }
                        }
                        if (empty($mobile_number)) {
                          //  $dataRowError[] = "Row #{$row_num}: Empty mobile Number ";
                        } else {
                            if (!preg_match('/^[0-9]{10}+$/', $mobile_number)) {
                                $dataRowError[] = "Row #{$row_num}: Invalid mobile Number ";
                            } else {
                                $check_mobile_number = Probationer::where('MobileNumber', $mobile_number)->count();
                                if ($check_mobile_number >= 1) {
                                    $dataRowError[] = "Row #{$row_num}: Mobile Number Already Registered.";
                                }
                            }
                        }

                        $probationerDatas[] = [
                            'batch_id' => $batch_name,
                            'RollNumber' => $roll_number,
                            'Cadre' => $cadre,
                            'Name' => $name,
                            'Email' => $email,
                            'Dob' => $dob,
                            'gender' => ucfirst(strtolower($gender)),
                            'MobileNumber' => $mobile_number,

                        ];
                    }
                    if (empty($dataRowError)) {

                        try
                        {
                            DB::beginTransaction();
                            foreach ($probationerDatas as $probationerData) {
                                $dob_pass = date('d-m-Y', strtotime($probationerData['Dob']));

                                $lastuserinserted = User::create([
                                    'name' => $probationerData['Name'],
                                    'email' => $probationerData['Email'],
                                    'password' => Hash::make("Svpnpa@123"),
                                    'Dob' => $probationerData['Dob'],
                                    'MobileNumber' => $probationerData['MobileNumber'],
                                    'role' => "probationer",
                                ]);

                                $insertedId = $lastuserinserted->id;

                                Probationer::create([
                                    'batch_id' => get_batch_id($probationerData['batch_id']),
                                    'RollNumber' => $probationerData['RollNumber'],
                                    'Cadre' => $probationerData['Cadre'],
                                    'Name' => $probationerData['Name'],
                                    'Email' => $probationerData['Email'],
                                    'Dob' => $probationerData['Dob'],
                                    'MobileNumber' => $probationerData['MobileNumber'],
                                    'gender' => $probationerData['gender'],
                                    'user_id' => $insertedId,
                                    'squad_id' => null,
                                ]);
                            }
                            $result['status'] = 'success';
                            $result['message'] = 'Data Uploaded Succesfully';
                            DB::commit();
                        } catch (\Illuminate\Database\QueryException $e) {
                            DB::rollback();
                            $errorCode = $e->errorInfo[1];
                            if ($errorCode == '1062') {
                                $duplicate = "Roll Number or Email or Mobile Number duplicates fields present in Sheet";
                                $result['error'] = 'error';
                                $result['message'] = $duplicate;
                            }
                           // return json_encode($e);
                        }
                        return json_encode($result);
                    } else {
                        $dataErrorMsg1 = "<ul class=\"list-style\">";
                        for ($eii = 0; $eii < count($dataRowError); $eii++) {
                            $dataErrorMsg1 .= "<li>" . $dataRowError[$eii] . "</li>";
                        }
                        $dataErrorMsg1 .= "</ul>";

                        $errorMsg[] = $dataErrorMsg1;

                        $result['status'] = 'error';
                        $message = implode('<br />', $errorMsg);
                        $result['message'] = $message;
                        return json_encode($result);
                    }
                }

            }
        }

    }
    public function probationer_datasheet($data_request)
    {
        $data = unserialize(data_crypt($data_request, 'd'));
        $batch_id = isset($data["batch_id"]) ? $data["batch_id"] : 0;
        $squad_id = isset($data["squad_id"]) ? $data["squad_id"] : 0;
        $aa = count($squad_id);
        //echo $aa;exit;
       
        $spreadsheet = new Spreadsheet();
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Probationers');

        // $bProbationers = Probationer::where('probationers.batch_id', $batch_id)
        //                             ->where('squads.id',$squad_id)
        //                             ->leftJoin('batches', 'batches.id', '=', 'probationers.batch_id')
        //                             ->leftJoin('squads', 'squads.id', '=', 'probationers.squad_id')
        //                             ->select('probationers.*', 'batches.BatchName', 'squads.SquadNumber')
        //                             ->get();
        //    print_r(json_encode($bProbationers));exit;

        //     if($squad_id != '0')
        //     {
        //         $bProbationers->where('probationers.squad_id', $squad_id);
        //     }
        //     $batch_probationers = $bProbationers->orderBy('squads.SquadNumber', 'ASC')->get(); 

        if($aa == 1)
        {
            //echo "hi";exit;
            $batch_probationer = Probationer::where('probationers.batch_id', $batch_id)
                                    ->where('squads.id',$squad_id)
                                    ->leftJoin('batches', 'batches.id', '=', 'probationers.batch_id')
                                    ->leftJoin('squads', 'squads.id', '=', 'probationers.squad_id')
                                    ->select('probationers.*', 'batches.BatchName', 'squads.SquadNumber')
                                    ->get();
                                    // print_r(json_encode($squad_data));exit;
        }
        else
        {
            //echo "hello";exit;
            $batch_probationer = Probationer::where('probationers.batch_id', $batch_id)
                                    ->leftJoin('batches', 'batches.id', '=', 'probationers.batch_id')
                                    ->leftJoin('squads', 'squads.id', '=', 'probationers.squad_id')
                                    ->select('probationers.*', 'batches.BatchName', 'squads.SquadNumber')
                                    ->orderBy('squads.SquadNumber', 'ASC')
                                    ->get();
                                    //print_r(json_encode($squad_data));exit;
        }

         $i = '2';
        $cell = 'A';

        $header[] = ['Batch', 'Squad', 'Cadre', 'Name', 'Roll Number', 'Email', 'Mobile Number', 'Date Of Birth', 'Gender', 'Religion', 'Category', 'Martial Status', 'Mother Name', 'Father Name', 'Mother Occupation', 'Father Occupation', 'State of Domilcile', 'Home Address', 'District', 'State', 'Pincode', 'Emergency Name', 'Emergency Phone Number', 'Emergency Email Id', 'Emergency Address'];
        $sheet1->fromArray($header, '', 'A1');

        foreach ($batch_probationer as $batch_probationer) {
                $sheet1->setCellValue($cell++ . $i, $batch_probationer->BatchName);
                $sheet1->setCellValue($cell++ . $i, $batch_probationer->SquadNumber);
                $sheet1->setCellValue($cell++ . $i, $batch_probationer->Cadre);
                $sheet1->setCellValue($cell++ . $i, $batch_probationer->Name);
                $sheet1->setCellValue($cell++ . $i, $batch_probationer->RollNumber);
                $sheet1->setCellValue($cell++ . $i, $batch_probationer->Email);
                $sheet1->setCellValue($cell++ . $i, $batch_probationer->MobileNumber);
                $sheet1->setCellValue($cell++ . $i, $batch_probationer->Dob);
                $sheet1->setCellValue($cell++ . $i, $batch_probationer->gender);
                $sheet1->setCellValue($cell++ . $i, $batch_probationer->Religion);
                $sheet1->setCellValue($cell++ . $i, $batch_probationer->Category);
                $sheet1->setCellValue($cell++ . $i, $batch_probationer->MartialStatus);
                $sheet1->setCellValue($cell++ . $i, $batch_probationer->MotherName);
                $sheet1->setCellValue($cell++ . $i, $batch_probationer->FatherName);
                $sheet1->setCellValue($cell++ . $i, $batch_probationer->Moccupation);
                $sheet1->setCellValue($cell++ . $i, $batch_probationer->Foccupation);
                $sheet1->setCellValue($cell++ . $i, $batch_probationer->Stateofdomicile);
                $sheet1->setCellValue($cell++ . $i, $batch_probationer->HomeAddress);
                $sheet1->setCellValue($cell++ . $i, $batch_probationer->District);
                $sheet1->setCellValue($cell++ . $i, $batch_probationer->State);
                $sheet1->setCellValue($cell++ . $i, $batch_probationer->Pincode);
                $sheet1->setCellValue($cell++ . $i, $batch_probationer->EmergencyName);
                $sheet1->setCellValue($cell++ . $i, $batch_probationer->EmergencyPhone);
                $sheet1->setCellValue($cell++ . $i, $batch_probationer->EmergencyEmailId);
                $sheet1->setCellValue($cell++ . $i, $batch_probationer->EmergencyAddress);
                $i++;
                $cell = 'A';
        }
        $fileName = "Probationer_Datasheet_{$batch_id}" . date('Ymd-hia') . ".xlsx";
        spreadsheet_header($fileName);
        ob_end_clean();
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

        $writer->save('php://output');
        die();
    }

    public function probationer_sample_datasheet($data_request)
    {
        $data = unserialize(data_crypt($data_request, 'd'));
        $batch_id = isset($data["batch_id"]) ? $data["batch_id"] : 0;
        $spreadsheet = new Spreadsheet();
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Probationers');
        $header[] = ['S.NO', 'batch_name', 'roll_number', 'cadre', 'name', 'email', 'date_of_birth(YYYY-MM-DD)', 'gender', 'mobile_number'];
        $sheet1->fromArray($header, '', 'A1');
        $sheet1->setCellValue('B2', batch_name($batch_id));

        $fileName = "Probationer_Sample_Datasheet_" . batch_name($batch_id) . ".xlsx";
        spreadsheet_header($fileName);
        ob_end_clean();
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        die();
    }
}
