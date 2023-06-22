<?php

namespace App\Http\Controllers;

use App\Models\GeneralAssesment;
use App\Models\Fitness;
use App\Models\Batch;
use App\Models\probationer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

use Exception;

class FitnessController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $batches = Batch::all();
        // $valid_data_keys  = [
        //     'weight',
        //     'bmi',
        //     'bodyfat',
        //     'fitnessscore',
        //     'endurancegrade',
        //     'strengthgrade',
        //     'flexibilitygrade',
        // ];
        // $timestamp  = date('Y-m-d H:i:s');

        // foreach($valid_data_keys as $keys)
        // {
        //     $fitness  = DB::table('fitness_evaluvation')->where('date', '!=', '0000-00-00')->where($keys, '!=', '')->select("$keys as value", 'Probationer_Id', 'date')->orderBy('date', 'DESC')->get();
        //     foreach($fitness as $fit)
        //     {
        //         DB::table('fitness_meta')->insert([
        //                 'probationer_id' => $fit->Probationer_Id,
        //                 'fitness_name' => $keys,
        //                 'fitness_value' =>$fit->value,
        //                 'date' => $fit->date,
        //                 'created_at'=> $timestamp,
        //                 'updated_at'=> $timestamp,
        //             ]);
        //     }
        // }

      //  exit;


        return view('Fitnessevaluation.fitnessevaluvation',compact('batches'));
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

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Fitness  $fitness
     * @return \Illuminate\Http\Response
     */
    public function show(Fitness $fitness)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Fitness  $fitness
     * @return \Illuminate\Http\Response
     */
    public function edit(Fitness $fitness)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Fitness  $fitness
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Fitness $fitness)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Fitness  $fitness
     * @return \Illuminate\Http\Response
     */
    public function destroy(Fitness $fitness)
    {
        //
    }

    /**
     * General Assesment.
     *
     * @return \Illuminate\Http\Response
     */
    public function general_assesment()
    {
        $batches = Batch::all();
        $role   = Auth::user()->role;
        return view('Fitnessevaluation.generalassesment',compact('batches', 'role'));
    }

    public function general_assesment_data(Request $request)
    {
        $role   = Auth::user()->role;
        $Probationer = Probationer::where('id', $request->id)->first();

        $general_assesment   = DB::table('general_assesments')->where('probationer_id', $request->id)->orderBy('id', 'DESC')->first();
        $gen_dates   = DB::table('general_assesments')->select('date')->where('probationer_id', $request->id)->orderBy('id', 'DESC')->get();

        $dates[] = '';

        if(!empty($gen_dates))
        {
          foreach($gen_dates as $date)
            {
                $dates[] = $date->date;
            }
        }
        $dt = json_encode($dates);
        return view('Fitnessevaluation.generalassesment-data',compact('Probationer', 'general_assesment', 'dt','role'));
    }

    public function profileview(Request $request)
    {

        $month = date('n');
        $get_probationer = Probationer::where('id', $request->id)->first();

        $squad_probationers = Probationer::where('squad_id', $get_probationer->squad_id)->orderBy('position_number', 'asc')->get();

        $fitness_weight   = Fitness::where('probationer_id', $request->id)->where('fitness_name', '=', 'weight')->orderBy('date', 'DESC')->first();
        $fitness_bmi   = Fitness::where('probationer_id', $request->id)->where('fitness_name', '=', 'bmi')->orderBy('date', 'DESC')->first();
        $fitness_bodyfat   = Fitness::where('probationer_id', $request->id)->where('fitness_name', '=', 'bodyfat')->orderBy('date', 'DESC')->first();
        $fitness_fitnessscore   = Fitness::where('probationer_id', $request->id)->where('fitness_name', '=', 'fitnessscore')->orderBy('date', 'DESC')->first();
        $fitness_endurancegrade   = Fitness::where('probationer_id', $request->id)->where('fitness_name', '=', 'endurancegrade')->orderBy('date', 'DESC')->first();
        $fitness_strengthgrade   = Fitness::where('probationer_id', $request->id)->where('fitness_name', '=', 'strengthgrade')->orderBy('date', 'DESC')->first();
        $fitness_flexibilitygrade   = Fitness::where('probationer_id', $request->id)->where('fitness_name', '=', 'flexibilitygrade')->orderBy('date', 'DESC')->first();


        $fitness   = Fitness::where('probationer_id', $request->id)->orderBy('id', 'DESC')->first();
        $fitn_dates   = Fitness::select('date')->where('probationer_id', $request->id)->orderBy('id', 'DESC')->get();

        $dates[] = '';

        if(!empty($fitn_dates))
        {
          foreach($fitn_dates as $date)
            {
                $dates[] = $date->date;
            }
        }

        // $usersexport = DB::select("SELECT Probationer_Id, `weight`, probationers.name as 'pid', `month`, `year`, `date`  FROM `fitness_evaluvation` LEFT JOIN probationers on fitness_evaluvation.Probationer_Id = probationers.id WHERE Probationer_Id = '$request->id' order by `date`");


        $usersexport = Fitness::where('probationer_id', $request->id)->where('fitness_name', '=', 'weight')
        ->leftJoin('probationers', 'probationers.id', '=', 'fitness_meta.probationer_id')->orderBy('date', 'asc')->get();

        if(!empty($usersexport))
        {
            foreach($usersexport as $row)
            {
                $outputs[] = array(
                    'date'  => date('d-m-Y', strtotime($row->date)),
                    'count' => $row->fitness_value,
                );
            }
        }
        else
        {
            $outputs[] = '';
        }

        //$output = json_encode($outputs);
        $dt = json_encode($dates);


        $weight = isset($fitness_weight->fitness_value) ? $fitness_weight->fitness_value .' Kg' : '-';
        $bmi = isset($fitness_bmi->fitness_value) ? $fitness_bmi->fitness_value . ' kg/m2' : '-';
        $bodyfat = isset($fitness_bodyfat->fitness_value) ? $fitness_bodyfat->fitness_value : '-';
        $fitnessscore = isset($fitness_fitnessscore->fitness_value) ? $fitness_fitnessscore->fitness_value . ' Total' : '-';
        $endurancegrade = isset($fitness_endurancegrade->fitness_value) ? $fitness_endurancegrade->fitness_value . ' Grade': '-';
        $strengthgrade = isset($fitness_strengthgrade->fitness_value) ? $fitness_strengthgrade->fitness_value : '-';
        $flexibilitygrade = isset($fitness_flexibilitygrade->fitness_value) ? $fitness_flexibilitygrade->fitness_value  : '-';


        $fdata = '';
        $fdata .= <<<EOL
                    <div id='fitness_child'>
                        <h5>Fitness</h5>
                            <div class="justify-content-center fitness-evaluation-data">
                                <div class="fitness_evaluation_card_sec">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <h5 class="card-title">Weight</h5>
                                            <div class="card-text"></div>
                                            <p id="weight">$weight</p>
                                        </div>
                                    </div>
                                    <div class="card">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">BMI</h5>
                                        <div class="card-text"></div>
                                        <p id="bmi">$bmi</p>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Body Fat</h5>
                                        <div class="card-text"></div>
                                        <p id="bodyfat">$bodyfat</p>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Fitness Score</h5>
                                        <div class="card-text"></div>
                                        <p id="fitnessscore">$fitnessscore </p>
                                    </div>
                                </div>
                                  <div class="card">
                                        <div class="card-body text-center">
                                            <h5 class="card-title">Endurance</h5>
                                            <div class="card-text"></div>
                                            <p id="egrade">$endurancegrade</p>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <h5 class="card-title">Strength</h5>
                                            <div class="card-text"></div>
                                            <p id="sgrade">$strengthgrade</p>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <h5 class="card-title">Flexibility</h5>
                                            <div class="card-text"></div>
                                            <p id="fgrade">$flexibilitygrade</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </div>
        EOL;

        $data = array(
            "fData" => $fdata,
            "gData" => isset($outputs) ? $outputs : '',
            "dDates" => $dates,
        );

        return $data;
        //return view('Fitnessevaluation.fitnessanalytics',compact('get_probationer', 'fitness', 'output', 'squad_probationers', 'dt'));
    }



    public function fitnesswisechart(Request $request)
    {
        $fitness_name = $request->name;
        $usersexport = Fitness::where('probationer_id', $request->id)->where('fitness_name', '=', $fitness_name)
        ->leftJoin('probationers', 'probationers.id', '=', 'fitness_meta.probationer_id')->orderBy('date', 'DESC')->get();

        // $usersexport = DB::select("SELECT Probationer_Id, $fitness_name as 'value', probationers.name as 'pid', `month`, `year`, `date`  FROM `fitness_evaluvation` LEFT JOIN probationers on fitness_evaluvation.Probationer_Id = probationers.id WHERE Probationer_Id = '$request->id'");

        if(!empty($usersexport))
        {
            foreach($usersexport as $row)
            {
                $outputs[] = array(
                    'date'  => date('d-m-Y', strtotime($row->date)),
                    'count' => $row->fitness_value,
                );
            }
        }
        else
        {
            $outputs[] = '';
        }
        return $outputs;
    }



    public function insertfitnessevaluation(Request $request)
    {
        $timestamp  = date('Y-m-d H:i:s');

        $fitness = DB::table('fitness_evaluvation')->updateOrInsert([
            'Probationer_Id' => $request->data[2]['value'],
            'month' => $request->data[3]['value'],
            'year' => $request->data[4]['value']],

            ['weight' => $request->data[1]['value'],
            'bmi' => $request->data[5]['value'],
            'bodyfat' => $request->data[6]['value'],
            'fitnessscore' => $request->data[7]['value'],
            'endurancegrade' => $request->data[8]['value'],
            'strengthgrade' => $request->data[9]['value'],
            'flexibilitygrade' => $request->data[10]['value']
        ]);
        return true;
    }

    public function prob_month_fitness(Request $request)
    {

        if($request->date != '')
        {
            $date = date('Y-m-d', strtotime($request->date));

        }
        else
        {
            return json_encode([
                'status'    => 'error',
                'message'   => 'Please Select Month And Try Again',
            ]);
        }

        $fitness_weight   = Fitness::where('probationer_id', $request->pid)->where('fitness_name', '=', 'weight')->where('date', $date)->first();
        $fitness_bmi   = Fitness::where('probationer_id', $request->pid)->where('fitness_name', '=', 'bmi')->where('date', $date)->first();
        $fitness_bodyfat   = Fitness::where('probationer_id', $request->pid)->where('fitness_name', '=', 'bodyfat')->where('date', $date)->first();
        $fitness_fitnessscore   = Fitness::where('probationer_id', $request->pid)->where('fitness_name', '=', 'fitnessscore')->where('date', $date)->first();
        $fitness_endurancegrade   = Fitness::where('probationer_id', $request->pid)->where('fitness_name', '=', 'endurancegrade')->where('date', $date)->first();
        $fitness_strengthgrade   = Fitness::where('probationer_id', $request->pid)->where('fitness_name', '=', 'strengthgrade')->where('date', $date)->first();
        $fitness_flexibilitygrade   = Fitness::where('probationer_id', $request->pid)->where('fitness_name', '=', 'flexibilitygrade')->where('date', $date)->first();


        $weight = isset($fitness_weight->fitness_value) ? $fitness_weight->fitness_value .' Kg' : '-';
        $bmi = isset($fitness_bmi->fitness_value) ? $fitness_bmi->fitness_value . ' kg/m2' : '-';
        $bodyfat = isset($fitness_bodyfat->fitness_value) ? $fitness_bodyfat->fitness_value : '-';
        $fitnessscore = isset($fitness_fitnessscore->fitness_value) ? $fitness_fitnessscore->fitness_value . ' Total' : '-';
        $endurancegrade = isset($fitness_endurancegrade->fitness_value) ? $fitness_endurancegrade->fitness_value . ' Grade': '-';
        $strengthgrade = isset($fitness_strengthgrade->fitness_value) ? $fitness_strengthgrade->fitness_value : '-';
        $flexibilitygrade = isset($fitness_flexibilitygrade->fitness_value) ? $fitness_flexibilitygrade->fitness_value  : '-';


        $fdata = '';
        $fdata .= <<<EOL
                        <h5>Fitness</h5>
                            <div class="justify-content-center fitness-evaluation-data">
                                <div class="fitness_evaluation_card_sec">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <h5 class="card-title">Weight</h5>
                                            <div class="card-text"></div>
                                            <p id="weight">$weight</p>
                                        </div>
                                    </div>
                                    <div class="card">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">BMI</h5>
                                        <div class="card-text"></div>
                                        <p id="bmi">$bmi</p>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Body Fat</h5>
                                        <div class="card-text"></div>
                                        <p id="bodyfat">$bodyfat</p>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Fitness Score</h5>
                                        <div class="card-text"></div>
                                        <p id="fitnessscore">$fitnessscore</p>
                                    </div>
                                </div>
                                  <div class="card">
                                        <div class="card-body text-center">
                                            <h5 class="card-title">Endurance</h5>
                                            <div class="card-text"></div>
                                            <p id="egrade">$endurancegrade</p>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <h5 class="card-title">Strength</h5>
                                            <div class="card-text"></div>
                                            <p id="sgrade">$strengthgrade</p>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <h5 class="card-title">Flexibility</h5>
                                            <div class="card-text"></div>
                                            <p id="fgrade">$flexibilitygrade</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
        EOL;

        return $fdata;

    }

    /**
     * Download timetable datasheet
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function fitness_datasheet($data_request)
    {
        $data   = unserialize( data_crypt($data_request, 'd') );

        // print_r($data);

        $batch_id   = isset($data["batch_id"]) ? $data["batch_id"] : 0;
        $squad_ids  = isset($data["squad_ids"]) ? $data["squad_ids"] : 0;

        if( empty($batch_id) || empty($squad_ids) ) {
            return "Invalid data request.";
        }

        $batch_name     = batch_name($batch_id);

        // ---------------------------------------------------------
        // Initialize Spreadsheet with 1st sheet as Timetables
        // ---------------------------------------------------------
        $spreadsheet = new Spreadsheet();
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('FitnessData');

        // Header row
        $sheet1->setCellValue("A1", "batch");
        $sheet1->setCellValue("B1", "squad");
        $sheet1->setCellValue("C1", "pbid");
        $sheet1->setCellValue("D1", "probationer");
        $sheet1->setCellValue("E1", "roll_number");
        $sheet1->setCellValue("F1", "weight");
        $sheet1->setCellValue("G1", "bmi");
        $sheet1->setCellValue("H1", "bodyfat");
        $sheet1->setCellValue("I1", "fitnessscore");
        $sheet1->setCellValue("J1", "endurancegrade");
        $sheet1->setCellValue("K1", "strengthgrade");
        $sheet1->setCellValue("L1", "flexibilityscore");
        $sheet1->setCellValue("M1", "date");

        $grades = "A, B, C, D, E";
        $date   = date("Y-m-d");

        $row    = 2;
        foreach($squad_ids as $squad_id) {
            $squad_number   = squad_number($squad_id);

            $Probationers = Probationer::where('squad_id', $squad_id)
                ->orderBy('position_number')
                ->get();

            foreach($Probationers as $probationer) {
                $pbid   = $probationer->id;
                $name   = $probationer->Name;
                $roll   = $probationer->RollNumber;

                $fitness_weight   = Fitness::where('probationer_id', $pbid)->where('fitness_name', '=', 'weight')->where('date', $date)->first();
                $fitness_bmi   = Fitness::where('probationer_id', $pbid)->where('fitness_name', '=', 'bmi')->where('date', $date)->first();
                $fitness_bodyfat   = Fitness::where('probationer_id', $pbid)->where('fitness_name', '=', 'bodyfat')->where('date', $date)->first();
                $fitness_fitnessscore   = Fitness::where('probationer_id', $pbid)->where('fitness_name', '=', 'fitnessscore')->where('date', $date)->first();
                $fitness_endurancegrade   = Fitness::where('probationer_id', $pbid)->where('fitness_name', '=', 'endurancegrade')->where('date', $date)->first();
                $fitness_strengthgrade   = Fitness::where('probationer_id', $pbid)->where('fitness_name', '=', 'strengthgrade')->where('date', $date)->first();
                $fitness_flexibilitygrade   = Fitness::where('probationer_id', $pbid)->where('fitness_name', '=', 'flexibilitygrade')->where('date', $date)->first();

                $weight = isset($fitness_weight->fitness_value) ? $fitness_weight->fitness_value : '';
                $bmi = isset($fitness_bmi->fitness_value) ? $fitness_bmi->fitness_value : '';
                $bodyfat = isset($fitness_bodyfat->fitness_value) ? $fitness_bodyfat->fitness_value : '';
                $fitnessscore = isset($fitness_fitnessscore->fitness_value) ? $fitness_fitnessscore->fitness_value : '';
                $endurancegrade = isset($fitness_endurancegrade->fitness_value) ? $fitness_endurancegrade->fitness_value : '';
                $strengthgrade = isset($fitness_strengthgrade->fitness_value) ? $fitness_strengthgrade->fitness_value : '';
                $flexibilitygrade = isset($fitness_flexibilitygrade->fitness_value) ? $fitness_flexibilitygrade->fitness_value  : '';



                $sheet1->setCellValue("A{$row}", $batch_name);
                $sheet1->setCellValue("B{$row}", $squad_number);
                $sheet1->setCellValue("C{$row}", $pbid);
                $sheet1->setCellValue("D{$row}", $name);
                $sheet1->setCellValue("E{$row}", $roll);
                $sheet1->setCellValue("F{$row}", $weight);
                $sheet1->setCellValue("G{$row}", $bmi);
                $sheet1->setCellValue("H{$row}", $bodyfat);
                $sheet1->setCellValue("I{$row}", $fitnessscore);
                $sheet1->setCellValue("J{$row}", $endurancegrade);
                $sheet1->setCellValue("K{$row}", $strengthgrade);
                $sheet1->setCellValue("L{$row}", $flexibilitygrade);
                $sheet1->setCellValue("M{$row}", $date);

                //
                $celsValidations    = ["J", "K"];

                foreach($celsValidations as $celsValidation) {
                    $cell   = $celsValidation . $row;

                    $validation = $sheet1->getCell("{$cell}")->getDataValidation(); // GET the cell for Data validation
                    $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST) // set the validation type to 'List'
                        ->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION) // set the validation type to 'List'
                        ->setShowDropDown(true)
                        ->setAllowBlank(false) // Do not allow empty value for activity
                        ->setShowInputMessage(true)
                        ->setPromptTitle('Pick from list')
                        ->setPrompt('Please pick a value from the drop-down list.')
                        ->setFormula1('"'. $grades .'"'); // Set drop down options
                }

                $sheet1->getStyle("M{$row}")
                    ->getNumberFormat()
                    ->setFormatCode("YYYY-MM-DD");

                $row++;
            }
        }

        $row    = $row - 1;
        foreach (range("A1", "M{$row}") as $columnID) {
            $sheet1->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Save Spreadsheet
        // $writer = new Xlsx($spreadsheet);

        $batchName  = preg_replace("/[^\da-z]/i", "", $batch_name);

        $fileName   = "Fitness_Datasheet_{$batchName}_" . date('Ymd-hia') . ".xlsx";

        ob_end_clean();
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

        // Spreadsheet Document Header
        spreadsheet_header($fileName);

        $writer->save('php://output');
    }

    /**
     * Process ajax requests.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function ajax(Request $request)
    {
        $requestName    = $request->requestName;

        // Get Assesment Data
        if ($requestName === "get_assesment_data") {
           // $date     = date('Y-m', strtotime($request->month_year));
           // $month = date('m',strtotime($request->month_year));
            //$year = date('Y',strtotime($request->month_year));
            $probationer_id = $request->probationer_id;
            $dates = explode("-",$request->month_year);
           // $month = str_replace(' ', '', $dates[0]);
            //$mon = str_pad($month, 2, '0', STR_PAD_LEFT);
            //$mon = sprintf("%02d",$month);
            $year = str_replace(' ', '', $dates[1]);
            $month =  ((int) $dates[0]);


            if(empty($dates) || empty($probationer_id) ) {
                return json_encode([
                    'status'    => 'error',
                    'message'   => 'Please Select Date And Try Again',
                ]);
            }

            $AssesmentData   = GeneralAssesment::where('probationer_id', $probationer_id)
                ->where('month', $month)->where('year', $year)->orderBy('id', 'DESC')->first();

                    //return json_encode($AssesmentData);

            if(empty($AssesmentData)) {
                $AssesmentData  = [
                    'behaviour'         => '',
                    'commandcontrol'    => '',
                    'leadership'        => '',
                    'learningefforts'   => '',
                    'punctuality'       => '',
                    'responsibility'    => '',
                    'sportsmanship'     => '',
                    'teamspirit'        => '',
                    'month'             => $month,
                    'year'              => $year,
                    'date'              => $request->month_year,
                ];
            }

            return json_encode([
                'status'    => 'success',
                'data'      => $AssesmentData,
            ]);
        }

        // Submit Assesment Form
        if ($requestName === "submit_assesment_form") {
            $result = [];

            $form_data  = $request->all();
          //  return json_encode($form_data);

            $user  = Auth::user();
            $form_data["staff_id"]      = $user->id;
            $form_data["staff_role"]    = $user->role;

            $probationer_id = $request->probationer_id;
           // $month          = ltrim($request->month, '0');
            //$date   = date('Y-m', strtotime($request->date));
            //$month_yea = $request->month_year;
            //$month = date('m',strtotime($request->month_year));
            //$year = date('Y',strtotime($request->month_year));
           // $date = date('Y-m',strtotime($request->month,$request->year));
         //   $dates = explode("-",$request->date);


            //$month = str_replace(' ', '', $dates[0]);
           // $month = str_pad($month, 2, '0', STR_PAD_LEFT);
            // $month = sprintf("%02d", $form_data['month']);

            // return json_encode($form_data);

            try {
                GeneralAssesment::Create(
                    [
                    'punctuality' => isset($form_data['punctuality']) ? $form_data['punctuality'] : "",
                    'behaviour' => isset($form_data['behaviour']) ? $form_data['behaviour'] : "",
                    'teamspirit' => isset($form_data['teamspirit']) ? $form_data['teamspirit'] : "",
                    'learningefforts' => isset($form_data['learningefforts']) ? $form_data['learningefforts'] : "",
                    'responsibility' => isset($form_data['responsibility']) ? $form_data['responsibility'] : "",
                    'leadership' => isset($form_data['leadership']) ? $form_data['leadership'] : "",
                    'commandcontrol' => isset($form_data['commandcontrol']) ? $form_data['commandcontrol'] : "",
                    'sportsmanship' => isset($form_data['sportsmanship']) ? $form_data['sportsmanship'] : "",
                    'month' => $form_data['month'],
                    'year' => $form_data['year'],
                    'probationer_id'    => $probationer_id,
                    'date' => '',
                    'staff_id' => $user->id,
                    'staff_role' => $user->role,
                    ]
                );

                $result['status']   = 'success';
                $result['message']  = 'Details saved';
            } catch (Exception $e) {
                $result['status']   = 'error';
                $result['message']  = $e->getMessage();
            }

            return json_encode($result);
        }

        // Get Fitness Data Import Modal
        if ($requestName === "get_fitnessData_import_modal") {

            return view('Fitnessevaluation.import-fitness-data-modal', ['request' => $request]);
        }

        // Download Fitness Datasheet
        if ($requestName === "download_fitnessDatasheet") {
            $result = [];
            $errors = [];

            // print_r($request->all());
            $batch_id   = intval($request->data_batch_id);
            $squad_id   = intval($request->data_squad_id);

            if (empty($batch_id)) {
                $errors[]   = "Select Batch.";
            }

            if (empty($squad_id)) {
                $squads = \App\Models\Squad::where('Batch_Id', $batch_id)->pluck('id')->toArray();
                if(empty($squads)) {
                    $errors[]   = "No squads available for the selected Batch.";
                }
            } else {
                $squads = [$squad_id];
            }

            if(count($squads) > 0) {
                if( Probationer::whereIn('squad_id', $squads)->count() === 0 ) {
                    $errors[]   = "No probationers available for the selected Batch/Squad.";
                }
            }

            if (empty($errors)) {

                $data_request   = [
                    'batch_id'  => $batch_id,
                    'squad_ids' => $squads,
                ];
                $data_request   = data_crypt( serialize($data_request) );

                $datasheet_url = url("/fitnessanalytics/fitness-datasheet/{$data_request}");
                return json_encode([
                    'status'            => "success",
                    'datasheet_url'    => $datasheet_url
                ]);
            } else {
                return json_encode([
                    'status'    => "error",
                    'message'   => "Error: ". implode('<br />', $errors)
                ]);
            }
        }

        // Import Fitness Data
        if ($requestName === "import_FitnessData") {
            if ($request->hasFile('fitnessdata_csv') && $request->file('fitnessdata_csv')->isValid()) {
                $errorMsg       = [];
                $dataRowError   = [];

                $result     = [];

                // print_r($request->fitnessdata_csv);
                // echo $request->fitnessdata_csv->getClientOriginalName();

                $original_filename  = $request->fitnessdata_csv->getClientOriginalName();
                $ext = pathinfo($original_filename, PATHINFO_EXTENSION);

                if ($ext !== 'csv') {
                    $result['status']  = 'error';
                    $result['message']  = "Please upload a file with a .csv extension.";

                    return json_encode($result);
                }

                $fileName   = time() . '-' . $original_filename;
                $request->fitnessdata_csv->storeAs('csv_files', $fileName);

                $rowsToSkip = 0;
                $filePath   = storage_path("app/public/csv_files/{$fileName}");
                $fileData   = csvToArray($filePath, ',', $rowsToSkip);

                if (count($fileData) > 0) {
                    $valid_data_keys  = [
                        'batch',
                        'squad',
                        'pbid',
                        'probationer',
                        'roll_number',
                        'weight',
                        'bmi',
                        'bodyfat',
                        'fitnessscore',
                        'endurancegrade',
                        'strengthgrade',
                        'flexibilityscore',
                        'date',
                    ];

                    $fitnessData  = [];

                    $validGrades    = ['A', 'B', 'C', 'D', 'E'];

                    $i = 1;
                    foreach ($fileData as $key => $data) {
                        $row_num    = $i + ($rowsToSkip+1);

                        if ($i === 1) {
                            $data_keys  = array_keys($data);
                            if (empty(array_intersect($data_keys, $valid_data_keys))) {
                                $errorMsg[] = "This file contains invalid data.";

                                break;
                            }
                        }
                            if(!isset($data["batch"]))
                            {
                                continue;
                            }
                        $batch_name     = trim($data["batch"]);
                        $squad_number   = trim($data["squad"]);
                        $pbid           = trim($data["pbid"]);
                        $weight         = trim($data["weight"]);
                        $bmi            = trim($data["bmi"]);
                        $bodyfat        = trim($data["bodyfat"]);
                        $fitnessscore   = trim($data["fitnessscore"]);
                        $endurancegrade = trim($data["endurancegrade"]);
                        $strengthgrade  = trim($data["strengthgrade"]);
                        $flexibilitygrade   = trim($data["flexibilityscore"]);
                        $date           = trim($data["date"]);

                        $batch_id   = 0;
                        $squad_id   = 0;

                        $isEmptyData    = true;

                        if (!empty($batch_name) && !empty($squad_number) && !empty($pbid)) {
                            // Get batch id
                            $getBatch   = DB::table('batches')->where('BatchName', $batch_name)->first();
                            if (empty($getBatch)) {
                                $dataRowError[] = "Row #{$row_num}: Invalid Batch.";
                            } else {
                                $batch_id   = $getBatch->id;
                            }

                            // Get Squad id
                            $getSquad   = DB::table('squads')
                                ->where('Batch_ID', $batch_id)
                                ->where('SquadNumber', $squad_number)
                                ->first();

                            if (empty($getSquad)) {
                                $dataRowError[] = "Row #{$row_num}: Invalid Squad.";
                            } else {
                                $squad_id   = $getSquad->id;
                            }

                            if(!empty($squad_id)) {
                                // Verify probationer id
                                if(! Probationer::where('id', $pbid)->where('squad_id', $squad_id)->first() ) {
                                    $dataRowError[] = "Row #{$row_num}: Probationer Id does not exist, or invalid id provided.";
                                }
                            }

                            // weight
                            if(!empty($weight) && !is_numeric($weight)) {
                                $dataRowError[] = "Row #{$row_num}: Invalid weight.";
                            } else {
                                $isEmptyData    = false;
                            }

                            // bmi
                            if(!empty($bmi) && !is_numeric($bmi)) {
                                $dataRowError[] = "Row #{$row_num}: Invalid BMI.";
                            } else {
                                $isEmptyData    = false;
                            }
                            // bodyfat
                            if(!empty($bodyfat) && !is_numeric($bodyfat)) {
                                $dataRowError[] = "Row #{$row_num}: Invalid bodyfat.";
                            } else {
                                $isEmptyData    = false;
                            }
                            // fitnessscore
                            if(!empty($fitnessscore) && !is_numeric($fitnessscore)) {
                                $dataRowError[] = "Row #{$row_num}: Invalid fitnessscore.";
                            } else {
                                $isEmptyData    = false;
                            }

                            // endurancegrade
                            if(!empty($endurancegrade) && !in_array($endurancegrade, $validGrades)) {
                                $dataRowError[] = "Row #{$row_num}: Invalid endurancegrade.";
                            } else {
                                $isEmptyData    = false;
                            }
                            // strengthgrade
                            if(!empty($strengthgrade) && !in_array($strengthgrade, $validGrades)) {
                                $dataRowError[] = "Row #{$row_num}: Invalid strengthgrade.";
                            } else {
                                $isEmptyData    = false;
                            }
                            // flexibilitygrade
                            if(!empty($flexibilitygrade) && !is_numeric($flexibilitygrade)) {
                                $dataRowError[] = "Row #{$row_num}: Invalid flexibility.";
                            }

                            // Date
                            if(empty($date)) {
                                $dataRowError[] = "Row #{$row_num}: Date is empty.";
                            } else {
                                if(!isValidDate($date, 'Y-m-d')) {
                                    $dataRowError[] = "Row #{$row_num}: Invalid date format (expected 'YYYY-MM-DD').";
                                }
                                if(time() < strtotime($date)) {
                                    $dataRowError[] = "Row #{$row_num}: Future Date not allowed.";
                                }
                                if(strtotime( '2000-01-01' ) > strtotime($date)) {
                                    $dataRowError[] = "Row #{$row_num}: Invalid date.";
                                }
                            }

                            if($isEmptyData === false) {
                                $fitnessvalues =
                                [
                                    'weight'  => $weight,
                                    'bmi'  => $bmi,
                                    'bodyfat'  => $bodyfat,
                                    'fitnessscore'  => $fitnessscore,
                                    'endurancegrade'  => $endurancegrade,
                                    'strengthgrade'  => $strengthgrade,
                                    'flexibilitygrade'  => $flexibilitygrade,
                                ];

                                $fitnessData[]    = [
                                    'Probationer_Id'  => $pbid,
                                    'month' => date('m', strtotime($date)),
                                    'year'  => date('Y', strtotime($date)),
                                    'date'  => $date,
                                    'values' => $fitnessvalues,
                                ];
                                unset($fitnessvalues);
                            }
                        }

                        $i++;
                    }

                    if (count($dataRowError) > 0) {
                        // $errorMsg[]   = implode('<br />', $dataRowError);
                        $dataErrorMsg   = "<ul class=\"list-style\">";
                        for ($ei = 0; $ei < count($dataRowError); $ei++) {
                            $dataErrorMsg .= "<li>" . $dataRowError[$ei] . "</li>";
                        }
                        $dataErrorMsg .= "</ul>";

                        $errorMsg[]   = $dataErrorMsg;
                    } else {
                        try {
                            $tt_count   = 0;


                            foreach ($fitnessData as $key => $fitness) {
                                $Probationer_Id   = $fitness["Probationer_Id"];
                                $date   = $fitness["date"];
                                foreach($fitness['values'] as $key => $values)
                                {
                                    if(!empty($values))
                                    {
                                        Fitness::updateOrCreate(
                                            [
                                                'probationer_id'  => $Probationer_Id,
                                                'date'      => $date,
                                                'fitness_name' => $key,
                                            ],
                                            [
                                                'fitness_value'  => $values,
                                            ]
                                        );
                                    }
                                }
                            }

                            $result['status']  = 'success';
                            $result['message']  = "Fitness data processed successfully";
                            $result['tt_count']  = $tt_count;
                        } catch (Exception $e) {
                            $result['status']  = 'error';
                            $result['message']  = "Unable to process. ". $e->getMessage();
                        }
                    }
                } else {
                    $errorMsg[]   = "Selected file is empty. or contains invalid data.";
                }

                // Delete the file
                unlink($filePath);
            } else {
                $errorMsg[]   = "Select a valid CSV file.";
            }

            if( !empty($errorMsg)) {
                $result  = [
                    'status'    => 'error',
                    'message'    => implode('<br />', $errorMsg),
                ];
            }

            return json_encode($result);
        }
    }
}
