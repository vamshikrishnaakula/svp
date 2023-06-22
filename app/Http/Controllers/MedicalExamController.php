<?php

namespace App\Http\Controllers;

use App\Models\probationer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;


class MedicalExamController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        return view('hospitalization.medicalexamination');
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

           $medicalinfo = DB::table('probationer_medical_exam')->updateOrInsert([
                'Probationer_Id' => $request->pid_medical,
                'date' => $request->date],

                ['temperature' => $request->temperature,
                'antigentest' => $request->antigentest,
                'rtpcr' => $request->rtpcr,
                'haemoglobin' => $request->haemoglobin,
                'calcium' => $request->calcium,
                'vitamind' => $request->vitamind,
                'vitaminb12' => $request->vitaminb12,
                'preexistinginjury' => $request->preexistinginjury,
                'covid' => $request->covid,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);
        if($medicalinfo)
        {
            return json_encode([
                'status'    => "success",
                'message'   => "Medical Test Date Added Successfully",
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $prob_data = Probationer::where('probationers.id', $request->id)->leftJoin('probationer_medical_exam', 'probationer_medical_exam.Probationer_Id', '=', 'probationers.id')->select('probationers.id as pid', 'probationers.Name', 'probationers.Dob', 'probationers.gender', 'probationer_medical_exam.*')->orderBy('probationer_medical_exam.id', 'DESC')->first();
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //get_prob_details
    }
    public function view_medical_exam(Request $request)
    {
        $error = '';
        $date = explode("/", $request->date);

        if (count($date) === 2) {
            list($month, $year) = $date;
            if (checkdate($month, 01, $year)) {

                $prob_data = Probationer::where('probationers.id', $request->id)
                    ->whereMonth('probationer_medical_exam.date', $month)
                    ->whereYear('probationer_medical_exam.date', $year)
                    ->leftJoin('probationer_medical_exam', 'probationer_medical_exam.Probationer_Id', '=', 'probationers.id')->select('probationers.id as pid', 'probationers.Name', 'probationers.Dob', 'probationers.gender', 'probationer_medical_exam.*')->orderBy('probationer_medical_exam.date', 'DESC')->first();

                if (!empty($prob_data)) {
                    return json_encode([
                        'status' => 'success',
                        'data' => $prob_data,
                    ]);
                } else {
                    $error = 'No data found for the given Month and Roll Number.';
                }
            } else {
                $error = 'Please select a month.';
            }
        } else {
            $error = 'Please select a month.';
        }

        return json_encode([
            'status' => 'error',
            'message' => $error,
        ]);

    }


    public function download_medical_testdata(Request $request)
    {
        $date = $request->date;
        $roll_no = $request->roll_no;

        $date_explode = explode("/",$date);

        $count = DB::table('probationer_medical_exam')->where('Probationer_Id',$roll_no)->whereMonth('date',$date_explode[0])->count();

        if(empty($date))
        {
            $errors[] = "Select Date";
        }

        if ($count === 0) {
            return json_encode([
                'status' => "error",
                'message' => "No data found for the given Month and Roll Number..",
            ]);
        }

        $get_probationer = Probationer::where('id',$request->roll_no)
                                       ->select('Name')
                                       ->get();
              $headers = [
                'rollNumber' => $roll_no,
                'date' => $date,
             ];

              $data_request   = data_crypt(serialize($headers) );

              $datasheet_url = url("/download_medicalexamnation_test_data/{$data_request}");
              return json_encode([
                  'status'            => "success",
                  'datasheet_url'    => $datasheet_url
              ]);


    }

    public function medicalexamination_datasheet($data_request)
    {


        $data = unserialize(data_crypt($data_request, 'd'));
        $rollNumber = isset($data["rollNumber"]) ? $data["rollNumber"] : 0;
        $date = isset($data["date"]) ? $data["date"] : 0;


        $date_explode = explode("/",$date);



        $spreadsheet = new Spreadsheet();
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Medical Examination');

        $get_medical_examination_data = DB::table('probationer_medical_exam')
                                          ->where('Probationer_Id',$rollNumber)
                                          ->whereMonth('date',$date_explode[0])
                                          ->whereYear('date',$date_explode[1])
                                          ->leftJoin('probationers','probationer_medical_exam.Probationer_Id' , '=' ,'probationers.id')
                                          ->select('probationers.Name','temperature','antigentest','rtpcr','haemoglobin','calcium','vitamind','vitaminb12','preexistinginjury','covid','date')
                                          ->get();

                                          $i = 4;
                                          $cell = 'B';

                                          $squad_id  = squad_id($rollNumber);

                                          $sheet1->setCellValue('A1','Probationer Name');
                                          $sheet1->setCellValue('A2','Squad Number');
                                          $sheet1->setCellValue('B1', probationer_name((int) $rollNumber));
                                          $sheet1->setCellValue('B2',squad_number($squad_id));


                                          $sheet1->setCellValue('A5','temperature');
                                          $sheet1->setCellValue('A6','antigentest');
                                          $sheet1->setCellValue('A7','rtpcr');
                                          $sheet1->setCellValue('A8','haemoglobin');
                                          $sheet1->setCellValue('A9','calcium');
                                          $sheet1->setCellValue('A10','vitamind');
                                          $sheet1->setCellValue('A11','vitaminb12');
                                          $sheet1->setCellValue('A12','preexistinginjury');
                                          $sheet1->setCellValue('A13','covid');

                                          foreach($get_medical_examination_data as $get_medical_examination_datas)
                                          {
                                            $sheet1->setCellValue($cell .$i++,$get_medical_examination_datas->date);
                                             $sheet1->setCellValue($cell .$i++,$get_medical_examination_datas->temperature);
                                             $sheet1->setCellValue($cell .$i++,$get_medical_examination_datas->antigentest);
                                             $sheet1->setCellValue($cell .$i++,$get_medical_examination_datas->rtpcr);
                                             $sheet1->setCellValue($cell .$i++,$get_medical_examination_datas->haemoglobin);
                                             $sheet1->setCellValue($cell .$i++,$get_medical_examination_datas->calcium);
                                             $sheet1->setCellValue($cell .$i++,$get_medical_examination_datas->vitamind);
                                             $sheet1->setCellValue($cell .$i++,$get_medical_examination_datas->vitaminb12);
                                             $sheet1->setCellValue($cell .$i++,$get_medical_examination_datas->preexistinginjury);
                                             $sheet1->setCellValue($cell .$i,$get_medical_examination_datas->covid);
                                            $i =4;
                                             $cell++;
                                          }

                                          $fileName = "Medical_Examination_data{$rollNumber}" . date('Ymd-hia') . ".xlsx";
                                          spreadsheet_header($fileName);
                                          ob_end_clean();
                                          $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

                                          $writer->save('php://output');
                                          die();


    }

    public function sickreports()
    {
        return view('hospitalization.sickreports');
    }

    public function get_sick_reports(Request $request)
    {

        $result = [];
        $errors = [];

        $batch_id = $request->batch_id;
        $squad_id = $request->squad_id;
        $probationer_id = $request->probationer_id;

        if (empty($batch_id)) {
            $errors[] = "Select a batch.";
        }
        if (empty($squad_id)) {
            $errors[] = "Select a squad.";
        }
        if (!empty($errors)) {
            return '<p class="msg msg-danger msg-full">ERRORS:<br />' . implode("<br />", $errors) . '</p>';
        }

        // $Notes  = PersonalNote::where('user_id', $user->id)
        //     ->where('reference', $reference)
        //     ->where('reference_id', $reference_id)
        //     ->orderBy('updated_at', 'desc')
        //     ->get();

        $sickreports = DB::table('probationer_sickreports')->where('Probationer_Id', $probationer_id)->orderBy('id', 'DESC')->get();

        $sickreports_count = $sickreports->count();

        $data = <<<EOL
            <div class="row g-1">
                <div class="col-6 pt-2">{$sickreports_count} Note(s)</div>
            </div>
            <hr class="mt-2" />
        EOL;

        if ($sickreports_count > 0) {

            foreach ($sickreports as $sickreports) {
                $sickreports_id = $sickreports->id;

                $createdAt = $sickreports->created_at;
                $createdAt = date('d F Y, H:i', strtotime($createdAt));

                $updatedAt = $sickreports->updated_at;
                $updatedAt = date('d F Y, H:i', strtotime($updatedAt));

                $data .= <<<EOL

                        <div class="note-item read-note" data-note-id="{$sickreports_id}">
                            <div class="note-title-bar">
                                <h5 class="note-title">{$sickreports->sickreport}</h5>
                                <p class="note-timestamp">{$createdAt}</p>
                            </div>
                            <p class="note-metadata">
                                Last updated: {$updatedAt}
                            </p>
                        </div>
                    EOL;
            }
        } else {
            $data .= <<<EOL
                <div class="note-item">
                    <div class="msg msg-info msg-full text-left">
                        No note found
                    </div>
                </div>
                EOL;
        }

        return $data;
    }
}
