<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Timetable;
use App\Models\Squad;
use App\Models\Hoilday;
use App\Models\ProbationersDailyactivityData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

use Exception;
use DateTime;

class TimetableController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        // $ids = Timetable::where('batch_id', '54')
        //     ->whereNotNull('activity_id')->get();
        // foreach($ids as $id)
        // {

        // //   $timetable = Timetable::where('id', $id['timetable_id'])->first();
        //    $timetables = ProbationersDailyactivityData::where('timetable_id', $id['id'])->get();
        //     foreach($timetables as $timetable)
        //     {
        //         if(empty($timetable['subactivity_id']))
        //         {
        //             // echo $timetable['timetable_id'];
        //             // echo "<br/>";
        //          ProbationersDailyactivityData::where('id', $timetable['id'])->update([
        //                  'subactivity_id' => $id['subactivity_id'],
        //              ]);
        //         }
        //     }

        // }

        //   $lists = Hoilday::select('timetables.activity_id', 'timetables.subactivity_id', 'timetables.date', 'hoildays.date', 'timetables.session_number', 'timetables.id', 'timetables.squad_id')
        //             ->whereColumn('hoildays.date', 'timetables.date')
        //             ->whereNotNull('activity_id')
        //             ->leftJoin('timetables', 'timetables.batch_id', '=', 'hoildays.batch_id')->get();



        //     foreach($lists as $list)
        //     {
        // Timetable::where('id', $list['id'])->update([
        //          'activity_id' => null,
        //          'subactivity_id' => null,
        //      ]);
        //  }


        //  exit;
        return view('timetable.viewtimetable');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('timetable.create-timetable');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Timetable  $timetable
     * @return \Illuminate\Http\Response
     */
    public function show(Timetable $timetable)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Timetable  $timetable
     * @return \Illuminate\Http\Response
     */
    public function edit(Timetable $timetable)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Timetable  $timetable
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Timetable $timetable)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Timetable  $timetable
     * @return \Illuminate\Http\Response
     */
    public function destroy(Timetable $timetable)
    {
        //
    }

    /**
     * Download timetable datasheet
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function timetable_datasheet($data_request)
    {
        $data   = unserialize(data_crypt($data_request, 'd'));

        // print_r($data);

        $batch_id   = isset($data["batch_id"]) ? $data["batch_id"] : 0;
        $squad_ids  = isset($data["squad_ids"]) ? $data["squad_ids"] : 0;
        // print_r($squad_ids);exit;
        $date_from  = isset($data["date_from"]) ? $data["date_from"] : "";
        $date_to    = isset($data["date_to"]) ? $data["date_to"] : "";
        $sessionCount   = isset($data["sessions_count"]) ? $data["sessions_count"] : 6;

        if (empty($batch_id) || empty($squad_ids) || empty($date_from) || empty($date_to)) {
            return "Invalid data request.";
        }

        $batch_name = batch_name($batch_id);

        // get date diff
        $date1 = new DateTime($date_from);
        $date2 = new DateTime($date_to);

        $diff = $date1->diff($date2);
        if ($diff->invert === 1) {
            echo "Invalid date range selected";
            return;
        }
        $days = $diff->days;
        // ---------------------------------------------------------
        // Initialize Spreadsheet with 1st sheet as Timetables
        // ---------------------------------------------------------
        $spreadsheet = new Spreadsheet();
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Timetables');

        // Create 2nd sheet for the Activities and Subactivities list for data validation
        $spreadsheet->createSheet();
        // $sheet2 = $spreadsheet->getActiveSheet();
        $sheet2 = $spreadsheet->getSheet(1);
        $sheet2->setTitle('ActivityList');

        // get activities and sub activities
        $activities = Activity::where('batch_id', $batch_id)
            ->where('type', 'activity')
            ->select('id', 'name')
            ->orderBy('id')
            ->get();

        // List out the activities in Activity List sheet
        $sheet2->setCellValue('A1', 'Acitivities');  // Heading

        $activityRow = 2;
        foreach ($activities as $activity) {
            $activityData = "$activity->name [{$activity->id}]";
            $sheet2->setCellValue("A{$activityRow}", $activityData);
            $activityRow++;
        }

        // $activityList   = "";
        // if(!empty($activityArray)) {
        //     $activityList   = implode(',', $activityArray);
        // }

        $subActivities = Activity::where('batch_id', $batch_id)
            ->where('type', 'subactivity')
            ->select('parent_id', 'name')
            ->orderBy('parent_id')
            ->get();

        // List out the sub activities in Activity List sheet
        $sheet2->setCellValue('B1', 'Sub acitivities');  // Heading

        $subActivityRow = 2;
        foreach ($subActivities as $subActivity) {
            if (!empty($subActivity->name)) {
                $subActivityData = "$subActivity->name [{$subActivity->parent_id}]";
                $sheet2->setCellValue("B{$subActivityRow}", $subActivityData);
                $subActivityRow++;
            }
        }
        // Hide ComponentList Sheet
        $sheet2->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_HIDDEN);

        // $subActivityList   = "";
        // if(!empty($subActivityArray)) {
        //     $subActivityList   = implode(',', $subActivityArray);
        // }

        // Get first sheet
        // $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1 = $spreadsheet->getSheet(0);

        // Header row
        $sheet1->setCellValue('A1', 'batch_name');
        $sheet1->setCellValue('B1', 'squad_number');
        $sheet1->setCellValue('C1', 'date');
        $sheet1->setCellValue('D1', 'session_number');
        $sheet1->setCellValue('E1', 'activity_name');
        $sheet1->setCellValue('F1', 'subactivity_name');
        $sheet1->setCellValue('G1', 'time_start');
        $sheet1->setCellValue('H1', 'time_end');

        $row = 2;
        foreach ($squad_ids as $squad_id) {
            $squad_name = squad_number($squad_id);

            for ($i = 0; $i <= $days; $i++) {
                $date       = date('Y-m-d', strtotime($date_from . ' + ' . $i . ' days'));

                $timetables = Timetable::where('squad_id', $squad_id)
                    ->whereDate('date', $date)
                    ->where('session_type', 'regular')
                    ->orderBy('session_number', 'asc')->get()->toArray();
                //    return json_encode($timetables);

                $timeTableData   = range(1, $sessionCount);

                if (!empty($timetables)) {
                    foreach ($timetables as $timetable) {
                        for ($si = 0; $si < $sessionCount; $si++) {
                            $sn = $si + 1;
                            if ($sn === intval($timetable["session_number"])) {
                                $timeTableData[$si]  = $timetable;
                            }
                        }
                    }
                }

                //   return json_encode($timeTableData);

                $activityRangeEnd    = $activityRow - 1;
                $subActivityRangeEnd = $subActivityRow - 1;

                $ti = 1;
                foreach ($timeTableData as $sessionData) {
                    $activity_name      = "";
                    $subactivity_name   = "";
                    $session_no     = $ti;

                    $session_time_start = "";
                    $session_time_end   = "";

                    if (is_array($sessionData)) {
                        $tt_id              = $sessionData["id"];
                        $tt_activity_id     = $sessionData["activity_id"];
                        $tt_subactivity_id  = $sessionData["subactivity_id"];

                        if (!empty($sessionData["session_number"])) {
                            $session_no = $sessionData["session_number"];
                        }

                        $tt_start    = $sessionData["session_start"];
                        $tt_end      = $sessionData["session_end"];

                        if (!empty($tt_start) && !empty($tt_end)) {
                            $session_time_start  = date('H:i', $tt_start);
                            $session_time_end    = date('H:i', $tt_end);
                        }

                        if (!empty($tt_activity_id)) {
                            $activity_name = activity_name($tt_activity_id);
                            $activity_name = "$activity_name [{$tt_activity_id}]";
                        }
                        if (!empty($tt_subactivity_id)) {
                            $subactivity_name = activity_name($tt_subactivity_id);
                            $subactivity_name = "$subactivity_name [{$tt_activity_id}]";
                        }
                    }

                    // SET data cell values
                    $sheet1->setCellValue("A{$row}", $batch_name);
                    $sheet1->setCellValue("B{$row}", $squad_name);
                    $sheet1->setCellValue("C{$row}", $date);
                    $sheet1->setCellValue("D{$row}", $session_no);
                    $sheet1->setCellValue("E{$row}", $activity_name);
                    $sheet1->setCellValue("F{$row}", $subactivity_name);
                    $sheet1->setCellValue("G{$row}", $session_time_start);
                    $sheet1->setCellValue("H{$row}", $session_time_end);

                    // SET cell Data validation for dropdowns

                    // Activity
                    // $validation1 = $sheet1->getCell("E{$row}")->getDataValidation(); // GET the cell for Data validation
                    // $validation1->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST); // set the validation type to 'List'
                    // $validation1->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION); // set the validation type to 'List'
                    // $validation1->setShowDropDown(true);    // Show dropdowns
                    // $validation1->setAllowBlank(false); // Do not allow empty value for activity
                    // $validation1->setShowInputMessage(true);
                    // $validation1->setPromptTitle('Pick from list');
                    // $validation1->setPrompt('Please pick a value from the drop-down list.');
                    // $validation1->setFormula1('=ActivityList!$A$2:$A$'.$activityRangeEnd); // Set drop down options

                    $validation1 = $sheet1->getCell("E{$row}")->getDataValidation(); // GET the cell for Data validation
                    $validation1->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST) // set the validation type to 'List'
                        ->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION) // set the validation type to 'List'
                        ->setShowDropDown(true)
                        ->setAllowBlank(false) // Do not allow empty value for activity
                        ->setShowInputMessage(true)
                        ->setPromptTitle('Pick from list')
                        ->setPrompt('Please pick a value from the drop-down list.')
                        ->setFormula1('=ActivityList!$A$2:$A$' . $activityRangeEnd); // Set drop down options

                    // SET cell Data validation for dropdowns
                    // Sub activity
                    if (count($subActivities) > 0) {
                        $validation2 = $sheet1->getCell("F{$row}")->getDataValidation(); // subactivity
                        $validation2->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST); // set the validation type to 'List'
                        $validation2->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION); // set the validation type to 'List'
                        $validation2->setShowDropDown(true);    // Show dropdowns
                        $validation2->setAllowBlank(false); // Do not allow empty value for activity
                        $validation2->setShowInputMessage(true);
                        $validation2->setPromptTitle('Pick from list');
                        $validation2->setPrompt('Please pick a value from the drop-down list.');
                        $validation2->setFormula1('=ActivityList!$B$2:$B$' . $subActivityRangeEnd); // Set drop down options
                    }

                    $ti++;
                    $row++;
                }
            }
        }

        $row    = $row - 1;
        foreach (range("A{$row}", "H{$row}") as $columnID) {
            $sheet1->getColumnDimension($columnID)->setAutoSize(true);
        }


        $fileName   = "Timetables_Datasheet_{$batch_id}_{$date_from}_to_{$date_to}_" . date('Ymd-hia') . ".xlsx";

        // Spreadsheet Document Header
        spreadsheet_header($fileName);

        // Save Spreadsheet
        // $writer = new Xlsx($spreadsheet);

        ob_end_clean();
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

        $writer->save('php://output');
        die();
    }

    /**
     * Process ajax requests.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function ajax2()
    {
        //return 'Hii';
        return view('timetable.copysquad');
    }
    public function ajax1(Request $request)
    {
        //return $request->all();
        // $data = Timetable::where('batch_id','=','77')->where('squad_id','=','160')->get();
        // return $data;
        $session_time_start  = $request->input('session_time_start');
        //return $session_time_start;
        $session_time_end  = $request->input('session_time_end');
        //return $session_time_end;
        $destinationSquadId = $request->squad_2;
        //return gettype($destinationSquadId);
        $timetables = Timetable::where('batch_id', $request->BATCH_ID)
            ->where('squad_id', $request->squad_1)
            ->whereBetween('date', [$session_time_start, $session_time_end])->whereNotNull('activity_id')
            ->get();
        $time_array = json_decode($timetables, true);
        //return $timetables;
        // foreach($time_array as $array)
        // {
        //     return $array;
        // }
        // // }
        // exit;
        ////for single squad copy
        // foreach ($timetables as $timetable) {
        //         //dd($timetable);
        //     Timetable::updateOrCreate([
        //         'batch_id' => $request->BATCH_ID,
        //         // 'squad_id' => $timetable->squad_id,
        //         'squad_id' => $request->squad_2,
        //         'activity_id' => $timetable->activity_id,
        //         'subactivity_id' => $timetable->subactivity_id,
        //         'session_number' => $timetable->session_number,
        //         'date' => $timetable->date,
        //         'session_start' => $timetable->session_start,
        //         'session_end' => $timetable->session_end,
        //     ]);
        //}
        //echo $timetable->activity_id;
        //exit;

        foreach ($timetables as $timetable) {
            foreach ($destinationSquadId as $SquadId) {
                //return $destinationSquadId;
                Timetable::updateOrCreate([
                    'batch_id' => $request->BATCH_ID,
                    'squad_id' => $SquadId,
                    'activity_id' => $timetable->activity_id,
                    'subactivity_id' => $timetable->subactivity_id,
                    'session_number' => $timetable->session_number,
                    'date' => $timetable->date,
                    'session_start' => $timetable->session_start,
                    'session_end' => $timetable->session_end,
                ]);
            }
        }
        return view('timetable.viewtimetable');
    }
    public function ajax(Request $request)
    {

        //return $request->all();
        //return 'Hii';
        $requestName    = $request->requestName;

        // Get Timetable Create Form
        if ($requestName === "get_timetableView") {
            //return 'Hello';
            return view('timetable.get-timetable-view', ['request' => $request]);
        }

        // Get Timetable Update Form
        if ($requestName === "get_timetableUpdateForm") {
            //return 'Hii';
            return view('timetable.form-update-timetable', ['request' => $request]);
        }

        // Submit Timetable Update Form
        if ($requestName === "submit_timetableUpdateForm") {
            //return 'Hio';
             
            $squads = $request->squad_2;
            $data = $request->squad_id;
            //$sqdata=  $squads + $data;
            //$sqldata = [];
            array_push($squads,$data);
            //echo $sdata;exit;
            $errors = [];
            $result = [];
            foreach ($squads as $copySquadsId) {
                $timestamp  = date('Y-m-d H:i:s');
                $batch_id    = intval($request->input('batch_id'));
                //return $batch_id;
                $squad_id    = $copySquadsId;
                $session_type   = $request->input('session_type');
                if (empty($session_type)) {
                    $session_type   = "regular";
                }
                $activities    = $request->input('activity_id');
                $subactivities    = $request->input('subactivity_id');
                $activity_date  = $request->input('activity_date');
                $session_num    = $request->input('session_number');
                // $activity_time  = $request->input('activity_time');
                $session_time_start  = $request->input('session_time_start');
                $session_time_end  = $request->input('session_time_end');
                $session_hoildays  = $request->input('mark_hoilday');
                if (empty($session_hoildays)) {
                    $session_hoildays = [];
                }
                //                  echo "";
                //             print_r(json_encode($session_hoildays));
                //         echo "<br />";

                // exit;

                foreach ($session_hoildays as $key => $session_hoilday) {

                    Hoilday::where('batch_id', $batch_id)->where('squad_id', $copySquadsId)->whereDate('date', $key)->delete();
                    Hoilday::Create(
                        [
                            'batch_id' => $batch_id,
                            'squad_id' => $copySquadsId,
                            'date' => $key,
                            'session_number' => $session_num,
                            'created_at' => $timestamp,
                            'updated_at' => $timestamp,
                        ]
                    );
                    // $Timetable  = Timetable::updateOrCreate(
                    //     [
                    //         'batch_id'  => $batch_id,
                    //         'squad_id'  => $squad_id,
                    //         'date'      => $key,
                    //     ],
                    //     [
                    //         'activity_id'  => null,
                    //         'subactivity_id'  => null,
                    //         'session_start'  => null,
                    //         'session_end'  => null,
                    //     ]
                    // );

                    $Timetable = Timetable::where('batch_id', $batch_id)->where('squad_id', $copySquadsId)->where('date', $key)->update([
                        'activity_id'  => null,
                        'subactivity_id'  => null,
                    ]);
                }



                $counter    = 0;
                $timetableData  = [];

                $i  = 0;
                if (is_array($session_num)) {
                    foreach ($session_num as $date => $data) {

                        $maxSessionNum  = 0;
                        foreach ($data as $key => $num) {
                            // echo "-->";
                            // print_r($num);
                            // echo "<--<br />";

                            $session_start  = 0;
                            $session_end    = 0;

                            $session_date   = isset($activity_date[$date][$key]) ? $activity_date[$date][$key] : 0;
                            $session_number = isset($session_num[$date][$key]) ? $session_num[$date][$key] : 0;
                            $activity_id    = isset($activities[$date][$key]) ? $activities[$date][$key] : 0;

                            $subactivity_id    = isset($subactivities[$date][$key]) ? $subactivities[$date][$key] : 0;
                            //  $hoilday[]    = isset($session_hoilday[$date][$key]) ? $session_hoilday[$date][$key] : 0;

                            // echo 'subactivity_id: '. $subactivity_id .'<br />';

                            if (isset($session_time_start[$date][$key])) {
                                $session_start  = strtotime($session_date . ' ' . $session_time_start[$date][$key]);
                                if (!empty($session_start) && ($maxSessionNum < $session_number)) {
                                    $maxSessionNum  = $session_number;
                                }
                            }
                            if (isset($session_time_end[$date][$key])) {
                                $session_end  = strtotime($session_date . ' ' . $session_time_end[$date][$key]);
                            }

                            if (!empty($session_start) && !empty($session_end)) {
                                if ($session_start >= $session_end) {
                                    $errors[] = "[DT {$session_date}][Session {$session_number}] - Time (End) should be greater than Time (Start).";
                                }
                            }

                            if (empty($subactivity_id)) {
                                if (!empty($activity_id)) {
                                    if (check_sub_activity($activity_id) !== 0) {
                                        $errors[] = "[{$session_date}][Session {$session_number}] - Please Select SubActivity.";
                                    }
                                }
                            }


                            if (!empty($session_date) && !empty($session_number) && ($session_number <= $maxSessionNum)) {

                                $timetableData[]    = [
                                    'batch_id'  => $batch_id,
                                    'squad_id'  => $copySquadsId,
                                    'date'      => $session_date,
                                    'session_number'  => $session_number,
                                    'session_type' => $session_type,
                                    'activity_id'  => $activity_id,
                                    'subactivity_id'  => $subactivity_id,
                                    'session_start'  => $session_start,
                                    'session_end'  => $session_end,
                                    'updated_at' => $timestamp,
                                ];
                            }

                            $i++;
                        }
                    }
                }


                if (empty($errors)) {
                    // if ( count($timetableData) > 0) {
                    // Get all the squad for the Batch
                    $squads = \App\Models\Squad::where('Batch_Id', $batch_id)->pluck('id')->toArray();

                    try {
                        foreach ($timetableData as $index => $timetable) {
                            $Timetable  = Timetable::updateOrCreate(
                                [
                                    'batch_id'  => $batch_id,
                                    // 'squad_id'  => $timetable['squad_id'],
                                    'squad_id'  => $copySquadsId,
                                    'date'      => $timetable['date'],
                                    'session_number'  => $timetable['session_number'],
                                    'session_type' => $timetable['session_type'],
                                ],
                                [
                                    'activity_id'  => sanitize_activity_id($timetable['activity_id']),
                                    'subactivity_id'  => sanitize_activity_id($timetable['subactivity_id']),
                                    'session_start'  => $timetable['session_start'],
                                    'session_end'  => $timetable['session_end'],
                                ]
                            );

                            // Delete hoilday date in db
                            Hoilday::where('batch_id', $batch_id)->where('squad_id', $copySquadsId)->whereDate('date', $timetable['date'])->delete();



                            // Update same timing for other squads (for the first time only)
                            foreach ($squads as $squadId) {
                                if ($squad_id === intval($squadId)) {
                                    continue;
                                }
                                $Timetable  = Timetable::where('batch_id', $batch_id)
                                    ->where('squad_id', $copySquadsId)
                                    ->whereDate('date', $timetable['date'])
                                    ->where('session_number', $timetable['session_number'])
                                    ->where('session_type', $timetable['session_type'])
                                    ->whereNotNull('activity_id')
                                    ->first();

                                //echo $Timetable;

                                if (empty($Timetable)) {
                                    $Timetable  = Timetable::updateOrCreate(
                                        [
                                            'batch_id'  => $timetable['batch_id'],
                                            'squad_id'  => $copySquadsId,
                                            'date'      => $timetable['date'],
                                            'session_number'  => $timetable['session_number'],
                                            'session_type' => $timetable['session_type'],
                                        ],
                                        [
                                            'activity_id'       => null,
                                            'subactivity_id'    => null,
                                            'session_start' => $timetable['session_start'],
                                            'session_end'   => $timetable['session_end'],
                                        ]
                                    );
                                }
                            }

                            // Update same timing for next week for all the squads (for the first time only)
                            foreach ($squads as $squadId) {
                                $nextDate   = date("Y-m-d", strtotime('+ 7 days', strtotime($timetable['date'])));

                                $Timetable  = Timetable::where('batch_id', $batch_id)
                                    ->where('squad_id', $copySquadsId)
                                    ->whereDate('date', $nextDate)
                                    ->where('session_number', $timetable['session_number'])
                                    ->where('session_type', $timetable['session_type'])
                                    ->whereNotNull('activity_id')
                                    ->first();

                                if (empty($Timetable)) {
                                    $Timetable  = Timetable::updateOrCreate(
                                        [
                                            'batch_id'  => $timetable['batch_id'],
                                            'squad_id'  => $copySquadsId,
                                            'date'      => $nextDate,
                                            'session_number'  => $timetable['session_number'],
                                            'session_type' => $timetable['session_type'],
                                        ],
                                        [
                                            'activity_id'       => null,
                                            'subactivity_id'    => null,
                                            'session_start' => $timetable['session_start'],
                                            'session_end'   => $timetable['session_end'],
                                        ]
                                    );
                                }
                            }
                        }

                    } catch (Exception $e) {
                        $result['status']   = "error";
                        $result['message']  = "ERROR: Something going wrong.<br />" . $e->getMessage();
                    }
                    // } else {
                    //     $result['status']   = "error";
                    //     $result['message']  = "Invalid data submitted, please try again.";
                    // }
                } else {
                    $result['status']   = "error";
                    $result['message']  = "INVALID DATA SUBMITTED.<br /><br />" . implode('<br />', $errors);
                }
            }

            
                        return json_encode([
                            'status'    => "success",
                            'message'   => "Timetable updated successfully.",
                        ]);

            return json_encode($result);
        }

        if ($requestName === "timetableSubactivities") {
            $activity_id   = $request->input('activity_id');
            $timetable_id   = $request->input('timetable_id');
            $timetable_date = $request->input('timetable_date');
            $sequence_id = $request->input('sequence_id');

            if (!empty($activity_id)) {
                $activitySelect = "";

                $subActivities = Activity::where('parent_id', $activity_id)->get();

                if (count($subActivities) > 0) {

                    $activitySelect = "<select name=\"subactivity_id[{$timetable_date}][{$sequence_id}]\" class=\"form-control reqField\">";

                    $activitySelect .= "<option value=\"\">Select Subactivity...</option>";

                    foreach ($subActivities as $subActivity) {
                        $subActivity_id = $subActivity->id;
                        $subActivity_name = $subActivity->name;

                        $selected   = "";
                        if (!empty($timetable_id)) {
                            $timetable = Timetable::where('id', $timetable_id)->first();

                            if (!empty($timetable)) {
                                $tt_subactivity_id  = $timetable->subactivity_id;
                                if ($tt_subactivity_id === $subActivity_id) {
                                    $selected   = "selected";
                                }
                            }
                        }

                        $activitySelect .= "<option value=\"{$subActivity_id}\" {$selected}>{$subActivity_name}</option>";
                    }

                    $activitySelect .= "</select>";
                }

                echo $activitySelect;
            }

            return false;
        }

        // Get Timetable Import Modal
        if ($requestName === "get_timetableImport_modal") {

            return view('timetable.import-timetable-modal', ['request' => $request]);
        }

        // Download Timetable Datasheet
        if ($requestName === "download_timetableDatasheet") {
            $result = [];
            $errors = [];

            // print_r($request->all());
            $batch_id   = intval($request->data_batch_id);
            $squad_id   = intval($request->data_squad_id);
            $date_from  = $request->data_from_date;
            $date_to    = $request->data_to_date;
            $sessions_count    = intval($request->sessions_per_day);

            if (empty($batch_id)) {
                $errors[]   = "Select Batch.";
            }
            if (empty($date_from) || !isValidDate($date_from, 'Y-m-d')) {
                $errors[]   = "Select a valid date (from).";
            }
            if (empty($date_to) || !isValidDate($date_to, 'Y-m-d')) {
                $errors[]   = "Select a valid date (to).";
            } else {
                if (strtotime($date_from) > strtotime($date_to)) {
                    $errors[]   = "Invalid date range selected";
                }
            }

            if (empty($squad_id)) {
                $squads = \App\Models\Squad::where('Batch_Id', $batch_id)->orderBy('SquadNumber', 'asc')->pluck('id')->toArray();
                if (empty($squads)) {
                    $errors[]   = "No squads available for the selected Batch.";
                }
            } else {
                $squads = [$squad_id];
            }

            if (empty($errors)) {
                if (empty($sessions_count)) {
                    $sessions_count   = 6;
                }

                $data_request   = [
                    'batch_id'  => $batch_id,
                    'squad_ids' => $squads,
                    'date_from' => $date_from,
                    'date_to'   => $date_to,
                    'sessions_count'  => $sessions_count,
                ];
                $data_request   = data_crypt(serialize($data_request));

                $datasheet_url = url("/timetables/download-timetable-datasheet/{$data_request}");
                return json_encode([
                    'status'            => "success",
                    'datasheet_url'    => $datasheet_url
                ]);
            } else {
                return json_encode([
                    'status'    => "error",
                    'message'   => "Error: " . implode('<br />', $errors)
                ]);
            }
        }

        // Import timetable
        if ($requestName === "import_Timetable") {
            if ($request->hasFile('timetable_csv') && $request->file('timetable_csv')->isValid()) {
                $errorMsg       = [];
                $dataRowError   = [];

                $result     = [];

                // print_r($request->timetable_csv);
                // echo $request->timetable_csv->getClientOriginalName();

                $original_filename  = $request->timetable_csv->getClientOriginalName();
                $ext = pathinfo($original_filename, PATHINFO_EXTENSION);

                if ($ext !== 'csv') {
                    $result['status']  = 'error';
                    $result['message']  = "Please upload a file with a .csv extension.";

                    return json_encode($result);
                }

                $fileName   = time() . '-' . $original_filename;
                $request->timetable_csv->storeAs('csv_files', $fileName);

                $rowsToSkip = 0;
                $filePath   = storage_path("app/public/csv_files/{$fileName}");
                $fileData   = csvToArray($filePath, ',', $rowsToSkip);

                if (count($fileData) > 0) {
                    $valid_data_keys  = [
                        'batch_name',
                        'squad_number',
                        'date',
                        'session_number',
                        'activity_name',
                        'subactivity_name',
                        'time_start',
                        'time_end'
                    ];

                    $timetableData  = [];

                    $i = 1;

                    foreach ($fileData as $key => $data) {
                        $row_num    = $i + ($rowsToSkip + 1);

                        if ($i === 1) {
                            $data_keys  = array_keys($data);
                            if (empty(array_intersect($data_keys, $valid_data_keys))) {
                                $errorMsg[] = "This file contains invalid data.";

                                break;
                            }
                        }

                        $batch_name     = trim($data["batch_name"]);
                        $squad_number   = trim($data["squad_number"]);
                        $date           = trim($data["date"]);
                        $session_number = trim($data["session_number"]);
                        $activity_name      = trim($data["activity_name"]);
                        $subactivity_name   = trim($data["subactivity_name"]);
                        $time_start = trim($data["time_start"]);
                        $time_end   = trim($data["time_end"]);

                        $batch_id   = 0;
                        $squad_id   = 0;
                        $activity_id    = 0;
                        $subactivity_id = 0;

                        $session_date   = "";
                        $session_start  = "";
                        $session_end    = "";

                        if (!empty($batch_name) && !empty($squad_number)) {
                            // Get batch id
                            $getBatch   = DB::table('batches')->where('BatchName', $batch_name)->first();
                            if (empty($getBatch)) {
                                $dataRowError[] = "Row #{$row_num}: Invalid Batch.";
                            } else {
                                $batch_id   = $getBatch->id;

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

                                // Get Activity id
                                if (!empty($activity_name)) {
                                    $activity  = explode(' [', $activity_name);
                                    if (isset($activity[1]) && !empty($activity[1])) {
                                        $activity_id    = substr($activity[1], 0, -1);
                                    } else {
                                        $dataRowError[] = "Row #{$row_num}: Invalid Activity.";
                                    }

                                    if (!empty($activity_id)) {

                                        // Check Subactivity
                                        if (!empty($subactivity_name)) {
                                            $subactivity  = explode(' [', $subactivity_name);
                                            if (isset($subactivity[0]) && !empty($subactivity[0])) {
                                                $subactivity_name    = $subactivity[0];

                                                // Get Subctivity id
                                                $getSubactivity   = Activity::where('name', $subactivity_name)
                                                    ->where('type', 'subactivity')
                                                    ->where('parent_id', $activity_id)
                                                    ->first();

                                                if (empty($getSubactivity)) {
                                                    $dataRowError[] = "Row #{$row_num}: Invalid Subactivity.";
                                                } else {
                                                    $subactivity_id   = $getSubactivity->id;
                                                }
                                            } else {
                                                $dataRowError[] = "Row #{$row_num}: Invalid Subactivity.";
                                            }
                                        } else {
                                            // Check if the Activity has subactivity
                                            $getSubactivityCount   = Activity::where('parent_id', $activity_id)
                                                ->where('type', 'subactivity')
                                                ->count();
                                            if ($getSubactivityCount > 0) {
                                                $dataRowError[] = "Row #{$row_num}: Subactivity is required for the activity <b>{$activity_name}</b>.";
                                            }
                                        }
                                    }
                                }

                                // Validate date
                                if (isValidDate($date, 'Y-m-d') === true) {
                                    $session_date   = date('Y-m-d', strtotime($date));

                                    if (!empty($time_start)) {
                                        $session_start  = strtotime($session_date . ' ' . $time_start);
                                        $session_end    = strtotime($session_date . ' ' . $time_end);

                                        if (!$session_start) {
                                            $dataRowError[] = "Row #{$row_num}: Invalid Time (Start).";
                                        }
                                        if (!$session_end) {
                                            $dataRowError[] = "Row #{$row_num}: Invalid Time (End).";
                                        }
                                        if ($session_start >= $session_end) {
                                            $dataRowError[] = "Row #{$row_num}: Time (End) should be greater than Time (Start).";
                                        }
                                    }
                                } else {
                                    $dataRowError[] = "Row #{$row_num}: Invalid date format (expected 'YYYY-MM-DD').";
                                }

                                $session_number = intval($session_number);
                                if (($session_number < 1) || ($session_number > 20)) {
                                    $dataRowError[] = "Row #{$row_num}: Invalid Session Number, it must be between 1 - 20";
                                }
                            }


                            $timetableData[]    = [
                                'batch_id'  => $batch_id,
                                'squad_id'  => $squad_id,
                                'activity_id'  => $activity_id,
                                'subactivity_id'  => $subactivity_id,
                                'date'  => $session_date,
                                'session_number'  => $session_number,
                                'session_start'  => $session_start,
                                'session_end'  => $session_end,
                            ];
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
                        /* --------------- Check duplicate rows --------------- */
                        $row   = [];

                        $rowNum = $rowsToSkip + 2;
                        $dataRowError1 = [];
                        foreach ($timetableData as $index => $timetables) {
                            $batch_id   = $timetables["batch_id"];
                            $squad_id   = $timetables["squad_id"];
                            $date   = $timetables["date"];
                            $session_num   = $timetables["session_number"];

                            $rowKey    = $batch_id . '.' . $squad_id . '.' . $date . '.' . $session_num;
                            if (isset($row[$rowKey])) {
                                $prevRow    =  $row[$rowKey];
                                $dataRowError1[]    = "Duplicate session number on row {$prevRow} &amp; {$rowNum}.";
                            } else {
                                $row[$rowKey]   = $rowNum;
                            }

                            $rowNum++;
                        }

                        if (count($dataRowError1) > 0) {
                            // $errorMsg[]   = implode('<br />', $dataRowError1);
                            $dataErrorMsg1   = "<ul class=\"list-style\">";
                            for ($eii = 0; $eii < count($dataRowError1); $eii++) {
                                $dataErrorMsg1 .= "<li>" . $dataRowError1[$eii] . "</li>";
                            }
                            $dataErrorMsg1 .= "</ul>";

                            $errorMsg[]   = $dataErrorMsg1;
                        } else {
                            /* --------------- Check session time overlaping --------------- */

                            $rowNum = $rowsToSkip + 2;
                            $dataRowError2 = [];

                            $dataRows = [];
                            foreach ($timetableData as $index => $timetables) {
                                $batch_id   = $timetables["batch_id"];
                                $squad_id   = $timetables["squad_id"];
                                $date   = $timetables["date"];

                                $dataKey    = $batch_id . '.' . $squad_id . '.' . $date;

                                $dataRows[$dataKey][]  = [
                                    'batch_id'  => $batch_id,
                                    'squad_id'  => $squad_id,
                                    'date'      => $date,
                                    'session_start'  => $timetables["session_start"],
                                    'session_end'  => $timetables["session_end"],
                                    'row_num'   => $rowNum,
                                ];

                                $rowNum++;
                            }

                            $i = 0;
                            foreach ($dataRows as $key => $dataRow) {
                                $dataRowCount   = count($dataRow);

                                // $isValidTime  = true;
                                for ($i = 0; $i < $dataRowCount; $i++) {
                                    $startTime  = $dataRow[$i]['session_start'];
                                    $endTime    = $dataRow[$i]['session_end'];

                                    $row_num    = $dataRow[$i]['row_num'];

                                    $isValidTime  = true;
                                    for ($j = 0; $j < $dataRowCount; $j++) {
                                        if ($i !== $j) {
                                            if (!empty($dataRow[$j]['session_start'])) {
                                                $startTime1  = $dataRow[$j]['session_start'];
                                                $endTime1    = $dataRow[$j]['session_end'];

                                                if ((($startTime >= $startTime1) && ($startTime <= $endTime1)) || (($endTime >= $startTime1) && ($endTime <= $endTime1))) {
                                                    $isValidTime  = false;
                                                } elseif (($startTime <= $startTime1) && ($endTime >= $endTime1)) {
                                                    $isValidTime  = false;
                                                } elseif (($startTime == $startTime1) || ($endTime == $endTime1)) {
                                                    $isValidTime  = false;
                                                }
                                            }
                                        }
                                    }

                                    if (($isValidTime !== true) && !isset($dataRowError2[$row_num])) {
                                        $dataRowError2[$row_num]    = "Row #{$row_num}: The time range has already been assigned.";
                                    }
                                }
                            }

                            if (count($dataRowError2) > 0) {
                                // $errorMsg[]   = implode('<br />', $dataRowError2);
                                ksort($dataRowError2);

                                $dataErrorMsg2   = "<ul class=\"list-style\">";
                                foreach ($dataRowError2 as $key => $val) {
                                    $dataErrorMsg2 .= "<li>" . $val . "</li>";
                                }
                                $dataErrorMsg2 .= "</ul>";

                                $errorMsg[]   = $dataErrorMsg2;
                            }

                            // echo '<pre>';
                            // print_r($errorMsg);
                            // echo '</pre>';
                            // return;
                        }
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
            } else {

                // echo '<pre>Timetable Data:<br />';
                // print_r($timetableData);
                // echo '</pre>';
                // return;

                $timestamp  = date('Y-m-d H:i:s');

                try {
                    $tt_count   = 0;
                    $dateOverride   = [];
                    foreach ($timetableData as $timetables) {
                        $batch_id   = $timetables["batch_id"];
                        $squad_id   = $timetables["squad_id"];
                        $date   = $timetables["date"];

                        $dataKey    = $batch_id . '.' . $squad_id . '.' . $date;

                        // Remove existing timetable for the dates
                        if (!in_array($dataKey, $dateOverride)) {
                            Timetable::where(
                                [
                                    ['batch_id', '=', $batch_id],
                                    ['squad_id', '=', $squad_id],
                                    ['date', '=', $date],
                                    ['session_type', '=', 'regular'],
                                ]
                            )->update(
                                [
                                    'activity_id'  => null,
                                    'subactivity_id'  => null,
                                    'session_start'  => 0,
                                    'session_end'  => 0,
                                ]
                            );

                            $dateOverride[] = $dataKey;
                        }

                        Timetable::updateOrCreate(
                            [
                                'batch_id'  => $batch_id,
                                'squad_id'  => $squad_id,
                                'date'      => $date,
                                'session_number'  => $timetables["session_number"],
                                'session_type' => "regular",
                            ],
                            [
                                'activity_id'  => sanitize_activity_id($timetables["activity_id"]),
                                'subactivity_id'  => sanitize_activity_id($timetables["subactivity_id"]),
                                'session_start'  => $timetables["session_start"],
                                'session_end'  => $timetables["session_end"],
                                'updated_at' => $timestamp,
                            ]
                        );
                    }

                    $result['status']  = 'success';
                    $result['message']  = "Timetables created successfully";
                    $result['tt_count']  = $tt_count;
                } catch (Exception $e) {
                    $result['status']  = 'error';
                    $result['message']  = "Unable to process. " . $e->getMessage();
                }
            }

            return json_encode($result);
        }

        // Get Activities Dorpdown
        if ($requestName === "get_activities_dropdown") {
            $batch_id   = $request->batch_id;
            if (!empty($batch_id)) {
                $Activities = Activity::where('batch_id', $batch_id)
                    ->where('type', 'activity')->get();
                if (count($Activities) > 0) {
                    echo "<option value=\"\">Select Activity...</option>";
                    foreach ($Activities as $Activity) {
                        echo "<option value=\"$Activity->id\">$Activity->name</option>";
                    }
                } else {
                    echo "<option value=\"\">-- No Activity --</option>";
                }
            } else {
                echo "<option value=\"\">Select...</option>";
            }

            return;
        }

        // Get Sub Activities Dorpdown
        if ($requestName === "get_subactivities_dropdown") {
            $result = [];
            $dropdowns  = '';
            $counter  = 0;

            $activity_id   = $request->activity_id;
            if (!empty($activity_id)) {
                $SubActivities = Activity::where('parent_id', $activity_id)->where('type', 'subactivity')->get();
                if (count($SubActivities) > 0) {
                    $dropdowns  .= "<option value=\"\">Select Sub Activity...</option>";

                    foreach ($SubActivities as $SubActivity) {
                        $dropdowns  .= "<option value=\"$SubActivity->id\">$SubActivity->name</option>";

                        $counter++;
                    }
                } else {
                    $dropdowns  .= "<option value=\"\">-- No Sub Activity --</option>";
                }
            } else {
                $dropdowns  .= "<option value=\"\">Select...</option>";
            }

            return json_encode([
                'status'    => 'success',
                'data'    => $dropdowns,
                'count'    => $counter,
            ]);
        }
    }

    public function batch_timetable(Request $request)
    {
        $batch_id = $request->batch_id;
        $ldate = date('Y-m-d');
        $data   = "";
        $dt = '';
        $squads = \App\Models\Squad::where('Batch_Id', '=', $batch_id)->orderBy('SquadNumber', 'asc')->get();
        if (count($squads) !== 0) {
            echo <<<EOL
                 <div class="tab">
            EOL;
            foreach ($squads as $index => $squad) {
                $squadid = $squad->id;
                $squad_no = $squad->SquadNumber;
                if ($index == '0') {
                    echo <<<EOL
                                 <button class="tablinks active"  id = "defaultOpen" onclick="openCity(event, $squadid)">$squad_no</button>
                             EOL;
                } else {
                    echo <<<EOL
                                <button class="tablinks"  id = "defaultOpen" onclick="openCity(event, $squadid)">$squad_no</button>
                             EOL;
                }
            }
            echo <<<EOL
                             </div>
                      EOL;
            foreach ($squads as $index => $squad) {
                $display = ($index === 0) ? "block" : "none";
                $Timetables  = \App\Models\Timetable::whereDate('timetables.date', '=', $ldate)->where('squad_id', $squad->id)
                    ->select('activities.name', 'squads.SquadNumber', 'subactivity_id', 'timetables.session_number')
                    ->leftJoin('squads', 'timetables.squad_id', '=', 'squads.id')
                    ->leftJoin('activities', 'timetables.activity_id', '=', 'activities.id')
                    ->orderBy('session_number', 'asc')
                    ->get();
                $squadid = $squad->id;
                $squad_number = isset($Timetables[0]->SquadNumber) ? $Timetables[0]->SquadNumber : '';
                echo <<<EOL
                         <div id="$squadid" class="tabcontent" style="display: {$display};">
                         <h3>$squad_number</h3>
                    EOL;
                if (count($Timetables) != 0) {
                    foreach ($Timetables as $Timetable) {
                        $session_number = isset($Timetable->session_number) ? $Timetable->session_number : '';
                        $activity_name = isset($Timetable->name) ? $Timetable->name : '';
                        if ($Timetable->name != '') {
                            echo <<<EOL
                                  <p> Session $session_number : $activity_name  </p>
                                  EOL;
                        } else {
                        }
                    }
                } else {
                    echo <<<EOL
                        <p> No sessions  </p>
                        EOL;
                }
                echo <<<EOL
                             </div>
                    EOL;
            }
        } else {
            echo <<<EOL
            <tr>
                <td colspan="7" class="text-center">No Squad data </td>
            </tr>
        EOL;
        }
        return;
    }
}
