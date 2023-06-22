<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Batch;
use App\Models\Event;
use App\Models\probationer;
use App\Models\EventScheduler;
use App\Models\ProbationereventScheduler;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class EventsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

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

        $gender = isset($request->gender) ? $request->gender : '';
        //return $gender;
        $inputs = [
            'batch_id' => $request->batch,
            'competition' => $request->event_competition,
            'category' => $request->event_category,
            'event_name' => $request->event_name,
            'events_rounds' => $request->event_competition,
            'units' => $request->units,
            'gender' => $gender,
        ];

        $verfiy = Event::create($inputs);
        if($verfiy)
        {
            return json_encode([
                'status'    => 'success',
                'message'      => "Event Created succesfully",
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        //
    }
    public function event_list(Request $request)
    {
        $batches = Batch::all();
        $events = Event::get();
        return view('events.eventlist',compact('batches', 'events'));   
    }

    public function addSchedule(Request $request, $id)
    {
        $event = Event::where('id', $id)->first();
        $probationers = probationer::select('id', 'RollNumber', 'squad_id', 'Name')->where('batch_id', $event->batch_id)->orderBy('squad_id', 'ASC')->get();
        $check_event_scheduler = EventScheduler::where('event_id', $id)->first();
        return view('events.addSchedule', compact('event','probationers', 'check_event_scheduler'));
    }


    public function editSchedule(Request $request, $id)
    {
        $event = Event::where('id', $id)->first();
        $probationers = probationer::select('id', 'RollNumber', 'squad_id', 'Name')->where('batch_id', $event->batch_id)->orderBy('squad_id', 'ASC')->get();
        $check_event_scheduler = EventScheduler::where('event_id', $id)->first();
        $scheduled_probationers = ProbationereventScheduler::where('event_scheduler_id', $check_event_scheduler->id)->pluck('probationers_id')->toArray();
        return view('events.editSchedule', compact('event','probationers', 'check_event_scheduler', 'scheduled_probationers'));
    }


    public function viewscheduler(Request $request, $id)
    {
        $event = Event::where('id', $id)->first();
        $probationers = probationer::select('id', 'RollNumber', 'squad_id', 'Name')->where('batch_id', $event->batch_id)->orderBy('squad_id', 'ASC')->get();
        $check_event_scheduler = EventScheduler::where('event_id', $id)->first();
        $scheduled_probationers = ProbationereventScheduler::where('event_scheduler_id', $check_event_scheduler->id)->get();

        return view('events.schedularview', compact('event','probationers', 'check_event_scheduler', 'scheduled_probationers'));
    }



    public function updateSchedule(Request $request)
    {

        $event_id = EventScheduler::where('event_id',$request->event_id)->first('id');

        $verify = EventScheduler::where('id',$request->event_scheduler_id)
        ->update([
                    'roundno' => $request->round,
                    'venue' => $request->venue,
                    'date' => strtotime($request->venue_Time)
        ]);

          $probationers_checked_array = $request->probationers;
          $probationers_unchecked_array = $request->unchecked_probationers;

        $probationers_checked = explode(',', $probationers_checked_array);
        $probationers_unchecked = explode(',', $probationers_unchecked_array);

        foreach($probationers_checked as $probationer_checked)
        {
            ProbationereventScheduler::where('event_scheduler_id',$request->event_scheduler_id)->where('probationers_id', $probationer_checked)
            ->updateOrCreate([
                "event_scheduler_id"  => $request->event_scheduler_id,


                "probationers_id" => $probationer_checked
            ]);
        }

        foreach($probationers_unchecked as $probationer_unchecked)
        {
            ProbationereventScheduler::where('event_scheduler_id',$request->event_scheduler_id)->where('probationers_id', $probationer_unchecked)
            ->delete();
        }

        if($verify)
        {
            return json_encode([
                'status'    => 'success',
                'message'      => "Event Schedule Updated succesfully",
            ]);
        }

     }



    public function scheduler(Request $request)
    {
        $inputs = [
            'event_id' => $request->event_id,
            'roundno' => $request->round,
            'venue' => $request->venue,
            'date' => strtotime($request->venue_Time),
        ];
        $probationers_array = $request->probationers;

        $verify = EventScheduler::create($inputs);

        $insertedId = $verify->id;
        $probationers = explode(',', $probationers_array);
        foreach($probationers as $probationer)
        {
            ProbationereventScheduler::create([
                "event_scheduler_id"  => $insertedId,
                "probationers_id" => $probationer
            ]);
        }
        if($verify)
        {
            return json_encode([
                'status'    => 'success',
                'message'      => "Event Scheduled Created succesfully",
            ]);
        }
    }
    public function get_scheduled_events(Request $request)
    {
        $scheduled_events = Event::join("events_scheduler as es", "es.event_id", "=", "events.id")->select("competition", "category", "event_name", "events.id as event_id", "es.id as event_scheduled_id")->get();
        return view('events.result', compact('scheduled_events'));
    }

    public function upload_results(Request $request, $id)
    {
        $event_scheduled_data = Event::join("events_scheduler as es", "es.event_id", "=", "events.id")->select("batch_id", "roundno", "competition", "category", "event_name", "events.id as event_id", "es.id as event_scheduled_id", "es.date")->where('es.id', $id)->first();
        return view('events.uploadresult', compact('event_scheduled_data'));
    }

    public function viewresults(Request $request, $id)
    {
        $event_scheduled_data = Event::join("events_scheduler as es", "es.event_id", "=", "events.id")->select("batch_id", "roundno", "competition", "category", "event_name", "events.id as event_id", "es.id as event_scheduled_id", "es.date")->where('es.id', $id)->first();
        $event_probationers_data = ProbationereventScheduler::where('event_scheduler_id', $id)->get();
        return view('events.resultview', compact('event_scheduled_data', 'event_probationers_data'));
    }

    public function ajax(Request $request)
    {
        //echo "hi";exit;

        $requestName = $request->request_name;

        if ($requestName === "sample_scheduled_data") {
            $result = [];
            $errors  = [];

            //print_r($request->all());
            $scheduled_id   = $request->scheduled_id;
            if (empty($errors)) {

                $data_request   = [
                    'scheduled_id'  => $scheduled_id,
                ];
                $data_request   = data_crypt( serialize($data_request) );

                $datasheet_url = url("/events/download-scheduled-data/{$data_request}");
                return json_encode([
                    'status'            => "success",
                    'datasheet_url'    => $datasheet_url
                ]);
            } else {
                return json_encode([
                    'status'    => 'error',
                    'message'   => implode('<br />', $errors),  
                ]);
            }
            return;
        }

        if ($requestName === "import_DataSheet") {

            if ($request->hasFile('data_csv') && $request->file('data_csv')->isValid()) {

                $errorMsg       = [];
                $dataRowError   = [];

                $result     = [];
                $tt_count   = 0;



                $original_filename  = $request->data_csv->getClientOriginalName();
                $ext = pathinfo($original_filename, PATHINFO_EXTENSION);

                if ($ext !== 'csv') {
                    $result['status']  = 'error';
                    $result['message']  = "Please upload a file with .csv extension.";

                    return json_encode($result);
                }

                $fileName   = time() . '-' . $original_filename;
                $request->data_csv->storeAs('csv_files', $fileName);

                $filePath   = storage_path("app/public/csv_files/{$fileName}");
                $fileData   = csvToArray($filePath, ',');


                if ( is_array($fileData) && count($fileData) > 0) {
                    $valid_data_keys  = [
                        'key',
                        'Event_Name',
                        'Round_Number',
                        'Probationer_Name',
                        'Squad_Number',
                        'result',
                        'status',
                    ];

                    $attnData  = [];

                    $i = 1;

                    foreach ($fileData as $key => $data) {
                        $row_num    = $i + 1;


                        if ($i === 1) {
                            $data_keys  = array_keys($data);
                            if (count($valid_data_keys) !== count(array_intersect($valid_data_keys, $data_keys))) {
                                $dataRowError[] = "This file contains invalid data. The file should contain [". implode(', ', $valid_data_keys) ."] parameters on the Row# 1";

                                break;
                            }
                        }

                        $composite_key  = trim($data["key"]);
                        $result  = trim($data["result"]);
                        $status     = strtoupper(trim($data["status"]));

                        if (!empty($composite_key)) {

                            $keyData    = explode('-', $composite_key);
                            if(is_array($keyData) && count($keyData) === 3) {
                                $event_id       = $keyData[0];
                                $event_scheduled_id   = $keyData[1];
                                $probationer_id = $keyData[2];
                            }

                            ProbationereventScheduler::where("event_scheduler_id", $event_scheduled_id)->where("probationers_id", $probationer_id)->update([
                                "result"  => $result,
                                "status" => $status
                            ]);
                        }
                        $tt_count++;
                    }

                    if(empty($dataRowError)) {
                        if(empty($createTxnError)) {
                            return json_encode([
                                'status'    => 'success',
                                'message'   => 'Data sheet uploaded successfully.',
                                'tt_count'  => $tt_count,
                            ]);
                        } else {
                            return json_encode([
                                'status'    => 'error',
                                'message'   => implode('<br />', $createTxnError),
                            ]);
                        }
                    } else {
                        $errorMsg[] = implode('<br />', $dataRowError);
                    }
                } else {
                    $errorMsg[]   = "Selected file is empty. or contains invalid data.";
                }

                // echo $filePath;
                // Delete the file from storage
                unlink($filePath);
            } else {
                $errorMsg[]   = "Select a valid CSV file.";
            }

            if (!empty($errorMsg)) {
                $result['status']  = 'error';
                $message  = implode('<br />', $errorMsg);
                $result['message']  = $message;
            }

            return json_encode($result);
        }
    }

    public function download_scheduled_sample_datasheet($data_request)
    {
        $data   = unserialize( data_crypt($data_request, 'd') );

        $scheduled_id   = isset($data["scheduled_id"]) ? $data["scheduled_id"] : 0;

        $scheduled_datas =  EventScheduler::select('events.id as event_id', 'events_scheduler.id as event_sch_id', 'events_scheduler_probationers.id as event_probationer_id', 'roundno', 'events.event_name', 'events_scheduler_probationers.probationers_id', 'events_scheduler_probationers.result', 'events_scheduler_probationers.status')->leftjoin("events", "events.id", "=", "events_scheduler.event_id")->leftjoin("events_scheduler_probationers", "events_scheduler_probationers.event_scheduler_id", "=", "events_scheduler.id")->where('events_scheduler.id', $scheduled_id)->get();

        // ---------------------------------------------------------
            // Initialize Spreadsheet with 1st sheet as Timetables
            // ---------------------------------------------------------
            $spreadsheet = new Spreadsheet();
            $sheet1 = $spreadsheet->getActiveSheet();
            $sheet1->setTitle('Scheduled_event_sheet');


            // Header row
            $sheet1->setCellValue('A1', 'key');
            $sheet1->setCellValue('B1', 'Event_Name');
            $sheet1->setCellValue('C1', 'Round_Number');
            $sheet1->setCellValue('D1', 'Probationer_Name');
            $sheet1->setCellValue('E1', 'Squad_Number');
            $sheet1->setCellValue('F1', 'result');
            $sheet1->setCellValue('G1', 'status');
            $ttRow = '2';
            foreach($scheduled_datas as $data)
            {
                $event_id = $data->event_id;
                $event_sch_id = $data->event_sch_id;
                $event_probationer_id = $data->event_probationer_id;
                $roundno = $data->roundno;
                $event_name = $data->event_name;
                $probationer_id = $data->probationers_id;
                $result = $data->result;
                $status = $data->status;

                $squad_id = squad_id($probationer_id);
                //print_r($probationer_id);exit;

                $composite_key  = "{$event_id}-{$event_sch_id}-{$probationer_id}";
                $sheet1->setCellValue("A{$ttRow}", $composite_key);
                $sheet1->setCellValue("B{$ttRow}", $event_name);
                $sheet1->setCellValue("C{$ttRow}", $roundno);
                $sheet1->setCellValue("D{$ttRow}", probationer_name($probationer_id));
                $sheet1->setCellValue("E{$ttRow}", squad_number($squad_id));
                $sheet1->setCellValue("F{$ttRow}", $result);
                $sheet1->setCellValue("G{$ttRow}", $status);
                $ttRow++;
            }

            $timeNow    = date('Y-m-d-His');
            $fileName   = "Events_scheduled-Data-{$timeNow}.xlsx";
            $fileName   = str_replace(' ', '-', $fileName);
            spreadsheet_header($fileName);
            ob_end_clean();
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

            $writer->save('php://output');
            return;

    }

}
