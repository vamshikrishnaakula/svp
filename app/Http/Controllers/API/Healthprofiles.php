<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Fitness;
use App\Models\probationer;
use App\Services\PayUService\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;
use \stdClass;

class Healthprofiles extends Controller
{
    /*
    probationer Basic details
     */

    public function Probhealthprofile(Request $request)
    {
        try {

            $user_role = Auth::user()->role;
            if ($user_role === 'drillinspector' || $user_role === 'si' || $user_role === 'adi') {
                isset($request->probationer_id) ? $probationer_Id = remove_specialcharcters($request->probationer_id) : $probationer_Id = '';
            } else {
                $user_id = Auth::id();
                $probationer_Id = probationer_id($user_id);
                if ($probationer_Id != $request->probationer_id) {
                    $response = [
                        'code' => "401",
                        'message' => "Unauthorized",
                    ];
                    return response()->json($response, 401);
                }
            }

            if (!is_numeric($probationer_Id)) {
                $response = [
                    'code' => "201",
                    'status' => "success",
                    'message' => "Invalid Probationer Id",
                ];
                return response()->json($response, 200);
            }

            $family = DB::table('familydependents')->where('Probationer_Id', $probationer_Id)->get();
            $generalinfo = DB::table('probationer_general_info')->where('Probationer_Id', $probationer_Id)->first();
            $familyhistory = DB::table('probationer_family_history')->where('Probationer_Id', $probationer_Id)->first();
            $physicalexamination = DB::table('probationer_physical_examination')->where('Probationer_Id', $probationer_Id)->first();
            $investigation = DB::table('probationer_investigation')->where('Probationer_Id', $probationer_Id)->first();

            $emptyobject = new stdClass;

            $data['healthprofiles'] = array(
                'familyinfo' => isset($family) ? $family : $emptyobject,
                'generalinfo' => isset($generalinfo) ? $generalinfo : $emptyobject,
                'familyhistory' => isset($familyhistory) ? $familyhistory : $emptyobject,
                'physicalexamination' => isset($physicalexamination) ? $physicalexamination : $emptyobject,
                'investigation' => isset($investigation) ? $investigation : $emptyobject,
            );

            $response = [
                'code' => '200',
                'status' => "success",
                'data' => $data,
            ];
        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->errorInfo[1];
            $response = [
                'code' => "200",
                'status' => "failed",
                'message' => 'Something went wrong Please try again',
            ];
        }
        return response()->json($response, 200);
    }

    public function medicalexam(Request $request)
    {
        try {
            $request = (json_decode($request->getContent(), true));
            $user_role = Auth::user()->role;
            if ($user_role === 'drillinspector' || $user_role === 'si' || $user_role === 'adi') {
                isset($request['probationer_id']) ? $probationer_Id = remove_specialcharcters($request['probationer_id']) : $probationer_Id = '';
            } else {
                $user_id = Auth::id();
                $probationer_Id = probationer_id($user_id);
                if ($probationer_Id != $request['probationer_id']) {
                    $response = [
                        'code' => "401",
                        'message' => "Unauthorized",
                    ];
                    return response()->json($response, 401);
                }
            }

            // isset($request['probationer_id']) ? $probationer_Id = remove_specialcharcters($request['probationer_id']) : $probationer_Id = '';
            isset($request['month']) ? $month = remove_specialcharcters($request['month']) : $month = '';
            isset($request['year']) ? $year = remove_specialcharcters($request['year']) : $year = '';

            if (!is_numeric($probationer_Id) || !is_numeric($month) || !is_numeric($year)) {
                $response = [
                    'code' => "201",
                    'status' => "success",
                    'message' => "Invalid Details",
                ];
                return response()->json($response, 200);
            }

            $emptyobject[] = new stdClass;
            $medicalexam = DB::table('probationer_medical_exam')->where('Probationer_Id', $probationer_Id)
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->orderBy('date', 'DESC')->get();
            if (count($medicalexam) != '0') {

                foreach ($medicalexam as $medicalexams) {
                    $dt[] = $medicalexams;
                }

                $response = [
                    'code' => '200',
                    'status' => "success",
                    'data' => $dt,
                ];
            } else {
                $dt[] = array(
                    "temperature" => "-",
                    "antigentest" => "-",
                    "rtpcr" => "-",
                    "haemoglobin" => "-",
                    "calcium" => "-",
                    "vitamind" => "-",
                    "vitaminb12" => "-",
                    "preexistinginjury" => "-",
                    "covid" => "-",
                    "month" => "-",
                    "year" => "-",
                    "date" => "-",
                    "created_at" => "-",
                    "updated_at" => "-",
                );

                $response = [
                    'code' => '204',
                    'status' => "success",
                    'data' => $dt,
                ];
            }

            return response()->json($response, 200);
        } catch (Exception $e) {
            $response = [
                'code' => "401",
                'status' => "failed",
                'message' => $e->getMessage(),
            ];
            return response()->json($response, 401);
        }
    }

    public function fitness(Request $request)
    {
        DB::beginTransaction();
        try
        {
            $request = (json_decode($request->getContent(), true));
            $user_role = Auth::user()->role;
            if ($user_role === 'drillinspector' || $user_role === 'si' || $user_role === 'adi') {
                isset($request['date']) ? $date = $request['date'] : $date = '';
                isset($request['type']) ? $fitness_type = fitness_number_to_text($request['type']) : $fitness_type = '';
                $timestamp = date('Y-m-d H:i:s');
            } else {
                $response = [
                    'code' => "401",
                    'message' => "Unauthorized",
                ];
                return response()->json($response, 401);
            }
            foreach ($request['Probationers'] as $probationer) {
                if (!empty($probationer['count'])) {
                    $data = Fitness::updateOrInsert([
                        'probationer_id' => $probationer['id'],
                        'fitness_name' => $fitness_type,
                        'date' => $date],

                        ['fitness_value' => $probationer['count'],
                            'created_at' => $timestamp,
                            'updated_at' => $timestamp,
                        ]);
                }
            }
            DB::commit();
            if (empty($data)) {
                $response = [
                    'code' => '500',
                    'status' => "failed",
                ];
                return response()->json($response, 500);
            } else {
                $response = [
                    'code' => '200',
                    'status' => "success",
                ];
                return response()->json($response, 200);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            $errorCode = $e->errorInfo[1];
            $response = [
                'code' => "401",
                'status' => "failed",
                'message' => 'Something went wrong Please try again',
            ];
        }
    }

    public function fitness_data(Request $request)
    {
        try {
            $request = (json_decode($request->getContent(), true));

            $user_role = Auth::user()->role;
            if ($user_role === 'drillinspector' || $user_role === 'si' || $user_role === 'adi') {
                isset($request['date']) ? $date = $request['date'] : $date = '';
                isset($request['type']) ? $fitness_type = fitness_number_to_text($request['type']) : $fitness_type = '';
                isset($request['type']) ? $squad_id = remove_specialcharcters($request['squad_id']) : $fitness_type = '';
                $timestamp = date('Y-m-d H:i:s');
            } else {
                $response = [
                    'code' => "401",
                    'message' => "Unauthorized",
                ];
                return response()->json($response, 401);

            }
            $probationer = Probationer::where('squad_id', $squad_id)->orderBy('probationers.position_number', 'asc')->get();
            $fitness_data = Fitness::select('fitness_meta.fitness_name', 'fitness_meta.fitness_value', 'probationers.name', 'probationers.id')->where('fitness_name', $fitness_type)->where('date', $date)->where('probationers.squad_id', $squad_id)
                ->leftJoin('probationers', 'probationers.id', '=', 'fitness_meta.probationer_id')
                ->orderBy('probationers.position_number', 'asc')->get();
            if (count($fitness_data) == '0') {

                foreach ($probationer as $prob) {
                    $data[] = [
                        "fitness_name" => $fitness_type,
                        "fitness_value" => '',
                        "name" => $prob->Name,
                        "id" => $prob->id,

                    ];
                }
                $response = [
                    'code' => '200',
                    'status' => "success",
                    'message' => $data,
                ];
            } else {
                foreach ($probationer as $prob) {

                    $probationer_ftData = Fitness::select('fitness_meta.fitness_name', 'fitness_meta.fitness_value')
                        ->where('fitness_name', $fitness_type)->where('date', $date)->where('probationer_id', $prob->id)
                        ->first();
                    if (empty($probationer_ftData)) {
                        $data[] = [
                            "fitness_name" => $fitness_type,
                            "fitness_value" => '',
                            "name" => $prob->Name,
                            "id" => $prob->id,

                        ];
                    } else {
                        $data[] = [
                            "fitness_name" => $fitness_type,
                            "fitness_value" => $probationer_ftData->fitness_value,
                            "name" => $prob->Name,
                            "id" => $prob->id,
                        ];
                    }
                }

                $response = [
                    'code' => '200',
                    'status' => "success",
                    'message' => $data,
                ];
            }
            return response()->json($response, 200);
        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->errorInfo[1];
            $response = [
                'code' => "401",
                'status' => "failed",
                'message' => 'Something went wrong Please try again',
            ];
            return response()->json($response, 401);
        }

    }

    public function fitnessview(Request $request)
    {
        try {

            $request = (json_decode($request->getContent(), true));
            $user_role = Auth::user()->role;
            if ($user_role === 'drillinspector' || $user_role === 'si' || $user_role === 'adi') {
                isset($request['probationer_id']) ? $probationer_Id = $request['probationer_id'] : $date = '';
            } else {
                $user_id = Auth::id();
                $probationer_Id = probationer_id($user_id);

                if ($probationer_Id != $request['probationer_id']) {
                    $response = [
                        'code' => "401",
                        'message' => "Unauthorized",
                    ];
                    return response()->json($response, 401);
                }
            }
            $fitness = [
                'weight',
                'bmi',
                'bodyfat',
                'fitnessscore',
                'endurancegrade',
                'strengthgrade',
                'flexibilitygrade',
            ];
            foreach ($fitness as $fitnessname) {
                $fitness_data = Fitness::where('probationer_id', $probationer_Id)->where('fitness_name', $fitnessname)
                    ->orderBy('date', 'DESC')->get();

                if (count($fitness_data) != '0') {

                    foreach ($fitness_data as $f_data) {
                        $f_data['date'] = date('d-m-Y', strtotime($f_data['date']));
                        $sData[] = $f_data;
                    }

                    $fData[] = [
                        'Type' => fitness_name_to_text($fitnessname),
                        'fitness_details' => $sData,
                    ];
                }
                unset($sData);
            }
            if (empty(isset($fData))) {
                $response = [
                    'code' => 204,
                    'status' => "success",
                    'message' => "No Fitness data",
                    'data' => array(),
                ];
            } else {
                $response = [
                    'code' => 200,
                    'status' => "success",
                    'message' => "",
                    'data' => $fData,
                ];
            }
            return response()->json($response, 200);

        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->errorInfo[1];
            $response = [
                'code' => 500,
                'status' => "failed",
                'message' => 'Something went wrong Please try again',
                'data' => array(),
            ];
            return response()->json($response, 200);
        }

    }

    public function prescriptions(Request $request)
    {
        try {
            $request = (json_decode($request->getContent(), true));
            $user_role = Auth::user()->role;
            if ($user_role === 'drillinspector' || $user_role === 'si' || $user_role === 'adi') {
                isset($request['probationer_id']) ? $probationer_Id = remove_specialcharcters($request['probationer_id']) : $probationer_Id = '';
            } else {
                $user_id = Auth::id();
                $probationer_Id = probationer_id($user_id);
                if ($probationer_Id != $request['probationer_id']) {
                    $response = [
                        'code' => "401",
                        'message' => "Unauthorized",
                    ];
                    return response()->json($response, 401);
                }
            }

            if (!is_numeric($probationer_Id)) {
                $response = [
                    'code' => "201",
                    'status' => "success",
                    'message' => "Invalid Probationer Id",
                ];
                return response()->json($response, 200);
            }
            $records_push = array();
            $records_push1 = array();
            $prescriptions_date = DB::table('probationer_prescription')->where('probationer_prescription.Probationer_Id', $probationer_Id)->select('date', 'appointment_id')->groupBy('appointment_id')->orderBy('id', 'ASC')->get();
            if (count($prescriptions_date) >= 1) {
                foreach ($prescriptions_date as $dt) {
                    $datewiseprescription = DB::table('probationer_prescription')->where('probationer_prescription.Probationer_Id', $probationer_Id)->where('appointment_id', $dt->appointment_id)->get();
                    $datewisevitalsign = DB::table('probationer_vitalsign')->where('probationer_vitalsign.Probationer_Id', $probationer_Id)->where('appointment_id', $dt->appointment_id)->first();
                    foreach ($datewiseprescription as $datepres) {
                        $records_push1 = array(
                            "drug" => isset($datepres->drug) ? $datepres->drug : '-',
                            "dosage" => isset($datepres->dosage) ? $datepres->dosage : '-',
                            "frequency" => isset($datepres->frequency) ? $datepres->frequency : '-',
                            "duration" => isset($datepres->duration) ? $datepres->duration : '-',
                            "instructions" => isset($datepres->instructions) ? $datepres->instructions : '-',
                        );
                        $test[] = $records_push1;
                        unset($records_push1);
                    }

                    $records_push = array(
                        "date" => date('d-m-Y', strtotime($dt->date)),
                        "symptoms" => isset($datewisevitalsign->vitalsign) ? $datewisevitalsign->vitalsign : '',
                        "appointment_id" => $dt->appointment_id,
                        "daterecord" => $test,
                    );
                    $final_prescription[] = $records_push;
                    unset($test);
                }
                $response = [
                    'code' => '200',
                    'status' => "success",
                    'data' => $final_prescription,
                ];
            } else {
                $records_push = array(
                    "message" => "No records exits",
                );
                $response = [
                    'code' => '204',
                    'status' => "No records exits",
                ];
            }
        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->errorInfo[1];
            $response = [
                'code' => "200",
                'status' => "failed",
                'message' => 'Something went wrong Please try again',
            ];
        }
        return response()->json($response, 200);
    }

    public function labreports(Request $request)
    {
        try {

            $user_role = Auth::user()->role;
            if ($user_role === 'drillinspector' || $user_role === 'si' || $user_role === 'adi') {
                isset($request->probationer_id) ? $probationer_Id = remove_specialcharcters($request->probationer_id) : $probationer_Id = '';
            } else {
                $user_id = Auth::id();
                $probationer_Id = probationer_id($user_id);
                if ($probationer_Id != $request->probationer_id) {
                    $response = [
                        'code' => "401",
                        'message' => "Unauthorized",
                    ];
                    return response()->json($response, 401);
                }
            }
            $records_push = array();
            $records_push1 = array();
            if (!is_numeric($probationer_Id)) {
                $response = [
                    'code' => "201",
                    'status' => "success",
                    'message' => "Invalid Probationer Id",
                ];
                return response()->json($response, 200);
            }
            $baseurl = url('/uploads');
            $labreports_date = DB::table('labreports')->where('labreports.Probationer_Id', $probationer_Id)->select(DB::raw('DATE(created_at) as created_at'))->groupBy(DB::raw('Date(created_at)'))->orderBy('id', 'ASC')->get();

            if (count($labreports_date) >= 1) {
                foreach ($labreports_date as $dt) {

                    $datewisereports = DB::table('labreports')->where('labreports.Probationer_Id', $probationer_Id)->whereDate('created_at', $dt->created_at)->get();
                    foreach ($datewisereports as $datepres) {
                        $records_push1 = array(
                            "ReportName" => isset($datepres->ReportName) ? $datepres->ReportName : '-',
                            "FileDirectory" => isset($datepres->FileDirectory) ? $baseurl . '/' . $datepres->FileDirectory : '-',
                        );
                        // print_r($records_push1);exit;
                        $test[] = $records_push1;
                        unset($records_push1);
                    }

                    $records_push = array(
                        "date" => date('d-m-Y', strtotime($dt->created_at)),
                        "daterecord" => $test,
                    );
                    $final_prescription[] = $records_push;
                    unset($test);
                }
                $response = [
                    'code' => '200',
                    'status' => "success",
                    'data' => $final_prescription,
                ];
            } else {
                $records_push = array(
                    "message" => "No records exits",
                );
                $response = [
                    'code' => '204',
                    'status' => "No records exits",
                ];
            }
        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->errorInfo[1];
            $response = [
                'code' => "200",
                'status' => "failed",
                'message' => 'Something went wrong Please try again',
            ];
        }
        return response()->json($response, 200);
    }

    public function sickreports(Request $request)
    {
        try {
            $request = (json_decode($request->getContent(), true));
            //isset($request['probationer_id']) ? $probationer_Id = remove_specialcharcters($request['probationer_id']) : $probationer_Id = '';
            $user_role = Auth::user()->role;
            if ($user_role === 'drillinspector' || $user_role === 'si' || $user_role === 'adi') {
                isset($request['probationer_id']) ? $probationer_Id = remove_specialcharcters($request['probationer_id']) : $probationer_Id = '';
            } else {
                $user_id = Auth::id();
                $probationer_Id = probationer_id($user_id);
                if ($probationer_Id != $request['probationer_id']) {
                    $response = [
                        'code' => "401",
                        'message' => "Unauthorised",
                    ];
                    return response()->json($response, 401);
                }
            }
            $report = $request['report'];
            $report_id = remove_specialcharcters($request['report_id']);
            $timestamp = date('Y-m-d H:i:s');
            $date = date('d-m-Y');
            if (!is_numeric($probationer_Id)) {
                $response = [
                    'code' => "201",
                    'status' => "success",
                    'message' => "Invalid Probationer Id",
                ];
                return response()->json($response, 200);
            }
            if (!empty($probationer_Id)) {
                if ($report_id == '' || $report_id == null) {
                    $data = DB::table('probationer_sickreports')->insert([
                        'Probationer_Id' => $probationer_Id,
                        'sickreport' => $report,
                        'date' => $date,
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp,
                    ]);
                } else {
                    $data = DB::table('probationer_sickreports')->where('id', $report_id)->updateOrInsert([
                        'Probationer_Id' => $probationer_Id],

                        ['sickreport' => $report,
                            'date' => $date,
                            'created_at' => $timestamp,
                            'updated_at' => $timestamp,
                        ]);
                }

                if (empty($data)) {
                    $response = [
                        'code' => '500',
                        'status' => "failed",
                    ];
                    return response()->json($response, 500);
                } else {
                    $response = [
                        'code' => '200',
                        'status' => "success",
                    ];
                    return response()->json($response, 200);
                }
            }
        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->errorInfo[1];
            $response = [
                'code' => "200",
                'status' => "failed",
                'message' => 'Something went wrong Please try again',
            ];
        }
    }
    public function viewsickreports(Request $request)
    {
        try {
            $user_role = Auth::user()->role;
            if ($user_role === 'drillinspector' || $user_role === 'si' || $user_role === 'adi') {
                isset($request->probationer_id) ? $probationer_Id = remove_specialcharcters($request->probationer_id) : $probationer_Id = '';
            } else {
                $user_id = Auth::id();
                $probationer_Id = probationer_id($user_id);
                if ($probationer_Id != $request->probationer_id) {
                    $response = [
                        'code' => "401",
                        'message' => "Unauthorized",
                    ];
                    return response()->json($response, 401);
                }
            }
            if (!is_numeric($probationer_Id)) {
                $response = [
                    'code' => "201",
                    'status' => "success",
                    'message' => "Invalid Probationer Id",
                ];
                return response()->json($response, 200);
            }
            if (!empty($probationer_Id)) {
                $data = DB::table('probationer_sickreports')->where('Probationer_Id', $probationer_Id)->orderBy('id', 'DESC')->get();

                if (count($data) == '0') {

                    $response = [
                        'code' => '204',
                        'status' => "No records exits",
                    ];
                    return response()->json($response, 200);
                } else {
                    foreach ($data as $dt) {
                        $sickreportlist[] = array(
                            'id' => isset($dt->id) ? $dt->id : '-',
                            'Probationer_Id' => isset($dt->Probationer_Id) ? $dt->Probationer_Id : '',
                            'sickreport' => isset($dt->sickreport) ? $dt->sickreport : '',
                            'date' => isset($dt->date) ? date('d-m-Y', strtotime($dt->date)) : '',
                            'created_at' => isset($dt->created_at) ? date('d-m-Y H:i:s', strtotime($dt->created_at)) : '',
                            'updated_at' => isset($dt->updated_at) ? date('d-m-Y H:i:s', strtotime($dt->updated_at)) : '',
                        );
                    }
                    $response = [
                        'code' => '200',
                        'status' => "success",
                        'data' => $sickreportlist,
                    ];
                    return response()->json($response, 200);
                }
            } else {
                $response = [
                    'code' => '204',
                    'status' => "Probationer Id doesn't exits",
                ];
                return response()->json($response, 200);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->errorInfo[1];
            $response = [
                'code' => "200",
                'status' => "failed",
                'message' => 'Something went wrong Please try again',
            ];
        }
    }
    public function prescription_pdf(Request $request)
    {
        try {
            $request = (json_decode($request->getContent(), true));
            // isset($request['appointment_id']) ? $id = remove_specialcharcters($request['appointment_id']) : $id = '';
            $user_role = Auth::user()->role;
            if ($user_role === 'drillinspector' || $user_role === 'si' || $user_role === 'adi') {
                isset($request['appointment_id']) ? $id = remove_specialcharcters($request['appointment_id']) : $id = '';
            } else {
                $user_id = Auth::id();
                $probationer_Id = probationer_id($user_id);
                $check_appoinment = DB::table('appoinments')->where('id', $request['appointment_id'])->where('Probationer_Id', $probationer_Id)->first();
                if (!empty($check_appoinment)) {
                    isset($request['appointment_id']) ? $id = remove_specialcharcters($request['appointment_id']) : $id = '';
                } else {
                    $response = [
                        'code' => "401",
                        'message' => "Unauthorized",
                    ];
                    return response()->json($response, 401);
                }
            }
            if (!is_numeric($id)) {
                $response = [
                    'code' => "201",
                    'status' => "success",
                    'message' => "Invalid Appointment Id",
                ];
                return response()->json($response, 200);
            }
            $Probationer = DB::table('appoinments')->where('id', $id)->value('Probationer_Id');
            $Appointments = DB::table('appoinments')->where('probationer_id', $Probationer)->first();
            $Prescriptions = DB::table('probationer_prescription')
                ->where('probationer_id', $Probationer)
                ->where('appointment_id', $id)
                ->get();

            $pdf_title = "Prescription-" . $Probationer;
            $data = [
                'pdf_title' => $pdf_title,
                'Probationer' => $Probationer,
                'Appointments' => $id,
                'Prescriptions' => $Prescriptions,
                'id' => $id,
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
            $path = public_path('uploads');
            $baseurl = url('/uploads');
            $pdf = PDF::loadView('PbDash.prescription-pdf', $data, [], $config)->save('' . $path . '/' . $pdf_title . '.pdf');
            //return $pdf->stream("{$pdf_title}.pdf");
            $response = [
                'code' => '200',
                'status' => "success",
                'message' => $baseurl . '/' . $pdf_title . '.pdf',
            ];
        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->errorInfo[1];
            $response = [
                'code' => "200",
                'status' => "failed",
                'message' => 'Something went wrong Please try again',
            ];
        }
        return response()->json($response, 200);
    }
}
