<?php

namespace App\Http\Controllers;

use App\Models\ExtraSession;
use App\Models\Activity;
use App\Models\Timetable;
use Illuminate\Http\Request;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

use Exception;

class ExtraSessionController extends Controller
{
    /**
     * View Extra Sessions
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function extra_sessions(Request $request)
    {
        return view('timetable.extra-sessions');
    }

    /**
     * Create Extra Session View
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function create_extra_session(Request $request)
    {
        $batches    = \App\Models\Batch::all();
        $get_DI     = get_staffs();

        return view('timetable.create-extra-session', compact('batches', 'get_DI'));
    }

    /**
     * Download timetable datasheet
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function extrasession_datasheet($data_request)
    {
        $data   = unserialize( data_crypt($data_request, 'd') );

        // print_r($data);

        $session_id   = isset($data["session_id"]) ? $data["session_id"] : 0;

        if( empty($session_id) ) {
            return "Invalid data request.";
        }

        $ExtraSession   = \App\Models\ExtraSession::find($session_id);
        if(!$ExtraSession) {
            return "Session not exist, or invalid data request.";
        }

        // Get Session Meta
        $SessionMetas   = $ExtraSession->metas;
        if (count($SessionMetas) === 0) {
            return "Probationer data not available for this session.";
        }

        $batch_id   = $ExtraSession->batch_id;
        $batch_name = batch_name($batch_id);

        $activity_id     = $ExtraSession->activity_id;
        $activity        = activity_name($activity_id);

        $subActivity_id = $ExtraSession->subactivity_id;
        $subactivity    = "";
        if (!empty($subActivity_id)) {
            $subactivity   = activity_name($subActivity_id);
        }

        $di_id      = $ExtraSession->drillinspector_id;
        $di_name    = user_name($di_id);

        $date     = $ExtraSession->date;
        $session_start  = $ExtraSession->session_start;
        $session_start  = date('h:i A', $session_start);

        $session_end    = $ExtraSession->session_end;
        $session_end    = date('h:i A', $session_end);

        // ---------------------------------------------------------
        // Initialize Spreadsheet with 1st sheet as ExtraSessionData
        // ---------------------------------------------------------
        $spreadsheet = new Spreadsheet();
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('ExtraSessionData');

        // Header row
        $sheet1->setCellValue('A1', 'key');
        $sheet1->setCellValue('B1', 'batch_name');
        $sheet1->setCellValue('C1', 'squad');
        $sheet1->setCellValue('D1', 'probationer');
        $sheet1->setCellValue('E1', 'date');
        $sheet1->setCellValue('F1', 'time');
        $sheet1->setCellValue('G1', 'staff');
        $sheet1->setCellValue('H1', 'activity');
        $sheet1->setCellValue('I1', 'sub_activity');
        $sheet1->setCellValue('J1', 'component');
        $sheet1->setCellValue('K1', 'attendance');
        $sheet1->setCellValue('L1', 'grade');
        $sheet1->setCellValue('M1', 'count');
        $sheet1->setCellValue('N1', 'qualified');

        // Create 2nd sheet for the Components list for data validation

        $components = [];
        if (!empty($subActivity_id)) {
            $components   = \App\Models\Activity::where('parent_id', $subActivity_id)->where('type', 'component')->pluck('name')->toArray();
        }

        $cRow   = 2;
        $dataListRange  = [2,2];
        if(!empty($components)) {
            $spreadsheet->createSheet();
            $sheet2 = $spreadsheet->getSheet(1);
            $sheet2->setTitle('DataLists');

            $sheet2->setCellValue("A1", "Components");

            foreach($components as $component) {
                $sheet2->setCellValue("A{$cRow}", $component);
                $cRow++;
            }
            $dataListRange[1]   = $cRow - 1;

            // Hide DataLists Sheet
            $sheet2->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_HIDDEN);
        }

        // Populate date in ExtraSessionData
        $sheet1 = $spreadsheet->getSheet(0);

        $validAttendances    = valid_attendances();
        $validAttendances    = implode(', ', $validAttendances);

        $grades = "A, B, C, D, E";

        $qualifyValues  = qualified_values(null, false);
        $qualifyValues1  = [];
        foreach($qualifyValues as $key => $val) {
            $qualifyValues1[]   = $key .' - '. $val;
        }
        $qualifyValues    = implode(', ', $qualifyValues1);

        $row = 2;
        foreach ($SessionMetas as $Sessionmeta) {
            $pb_id      = $Sessionmeta->probationer_id;

            $probationer   = \App\Models\probationer::find($pb_id);
            if(!$probationer) {
                continue;
            }
            $pb_name    = $probationer->Name;
            $squad_id   = $probationer->squad_id;
            $squad_num  = squad_number($squad_id);

            $attendance = $Sessionmeta->attendance;
            $grade      = $Sessionmeta->grade;
            $count      = $Sessionmeta->count;
            $qualified  = $Sessionmeta->qualified;
            if($qualified !== null && $qualified !== "") {
                $qualified  = qualified_values($qualified);
            }

            $composite_key    = "{$session_id}-{$squad_id}-{$pb_id}";

            // SET data cell values
            $sheet1->setCellValue("A{$row}", $composite_key);
            $sheet1->setCellValue("B{$row}", $batch_name);
            $sheet1->setCellValue("C{$row}", $squad_num);
            $sheet1->setCellValue("D{$row}", $pb_name);
            $sheet1->setCellValue("E{$row}", $date);
            $sheet1->setCellValue("F{$row}", $session_start .' - '. $session_end);
            $sheet1->setCellValue("G{$row}", $di_name);
            $sheet1->setCellValue("H{$row}", $activity);
            $sheet1->setCellValue("I{$row}", $subactivity);
            $sheet1->setCellValue("J{$row}", "");
            $sheet1->setCellValue("K{$row}", $attendance);
            $sheet1->setCellValue("L{$row}", $grade);
            $sheet1->setCellValue("M{$row}", $count);
            $sheet1->setCellValue("N{$row}", $qualified);

            // Cell validation dropdown list

            // Cell validation dropdown list for Components
            if(!empty($components)) {
                $formula1   = '=DataLists!$A$'. $dataListRange[0] .':$A$'. $dataListRange[1];

                $validation1 = $sheet1->getCell("J{$row}")->getDataValidation(); // GET the cell for Data validation
                $validation1->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST) // set the validation type to 'List'
                    ->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION) // set the validation type to 'List'
                    ->setShowDropDown(true)
                    ->setAllowBlank(false) // Do not allow empty value for activity
                    ->setShowInputMessage(true)
                    ->setPromptTitle('Pick from list')
                    ->setPrompt('Please pick a value from the drop-down list.')
                    ->setFormula1($formula1); // Set drop down options
            }

            // Cell validation dropdown list for Attendance
            $validatio2 = $sheet1->getCell("K{$row}")->getDataValidation(); // GET the cell for Data validation
            $validatio2->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST) // set the validation type to 'List'
                ->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION) // set the validation type to 'List'
                ->setShowDropDown(true)
                ->setAllowBlank(false) // Do not allow empty value for activity
                ->setShowInputMessage(true)
                ->setPromptTitle('Pick from list')
                ->setPrompt('Please pick a value from the drop-down list.')
                ->setFormula1('"'. $validAttendances .'"'); // Set attendance drop down options


            // Cell validation dropdown list for Grades
            $validation3 = $sheet1->getCell("L{$row}")->getDataValidation(); // GET the cell for Data validation
            $validation3->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST) // set the validation type to 'List'
                ->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION) // set the validation type to 'List'
                ->setShowDropDown(true)
                ->setAllowBlank(false) // Do not allow empty value for activity
                ->setShowInputMessage(true)
                ->setPromptTitle('Pick from list')
                ->setPrompt('Please pick a value from the drop-down list.')
                ->setFormula1('"'. $grades .'"'); // Set drop down options

            // Cell validation dropdown list for Qualified
            $validation3 = $sheet1->getCell("N{$row}")->getDataValidation(); // GET the cell for Data validation
            $validation3->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST) // set the validation type to 'List'
                ->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION) // set the validation type to 'List'
                ->setShowDropDown(true)
                ->setAllowBlank(false) // Do not allow empty value for activity
                ->setShowInputMessage(true)
                ->setPromptTitle('Pick from list')
                ->setPrompt('Please pick a value from the drop-down list.')
                ->setFormula1('"'. $qualifyValues .'"'); // Set drop down options

            $row++;
        }

        $row    = $row - 1;
        foreach (range("A{$row}", "N{$row}") as $columnID) {
            $sheet1->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Save Spreadsheet
        // $writer = new Xlsx($spreadsheet);

        $fileName   = "Extra_Session_Datasheet_{$batch_id}_{$date}_{$session_id}_" . date('Ymd-hia') . ".xlsx";

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

        // get extrasessions
        if ($requestName === "get_extrasessions") {
            $result = [];
            $errors  = [];

            // print_r($request->all());
            $batch_id   = intval($request->batch_id);
            $date_from  = $request->date_from;
            $date_to    = $request->date_to;

            if (empty($batch_id)) {
                $errors[]   = "Select Batch.";
            } else {
                set_current_batch($batch_id);
            }

            if (empty($date_from) || !isValidDate($date_from, 'Y-m-d')) {
                $errors[]   = "Select a valid date (from).";
            }

            if ( !empty($date_to) ) {
                if ( !isValidDate($date_to, 'Y-m-d') ) {
                    $errors[]   = "Select a valid date (to).";
                } elseif(strtotime($date_from) > strtotime($date_to) ) {
                    $errors[]   = "Invalid date range selected";
                }
            } else {
                $date_to    = date("Y-m-d");
            }

            if (empty($errors)) {
                $dataQuery   = \App\Models\ExtraSession::query()
                    ->where('batch_id', $batch_id)
                    ->whereNotNull('activity_id');

                if ( !empty($date_to) ) {
                    $dataQuery->whereDate('date', '>=', $date_from)->whereDate('date', '<=', $date_to);
                } else {
                    $dataQuery->whereDate('date', $date_from);
                }

                $ExtraSessions   = $dataQuery->where('session_start', '>', 0)
                    ->orderBy('session_start', 'desc')->get();

                if (count($ExtraSessions) > 0) {

                    $data   = "";
                    foreach ($ExtraSessions as $ExtraSession) {

                        $sessionId   = $ExtraSession->id;
                        $batch_id   = $ExtraSession->batch_id;
                        $batch      = batch_name($batch_id);

                        $activity_id     = $ExtraSession->activity_id;
                        $activity        = activity_name($activity_id);

                        $subActivity_id  = $ExtraSession->subactivity_id;
                        $subActivity     = "";
                        if (!empty($subActivity_id)) {
                            $subActivity     = activity_name($subActivity_id);
                        }

                        $di_id      = $ExtraSession->drillinspector_id;
                        $di_name    = user_name($di_id);

                        $date     = $ExtraSession->date;
                        $session_start  = $ExtraSession->session_start;
                        $session_start  = date('h:i A', $session_start);

                        $session_end    = $ExtraSession->session_end;
                        $session_end    = date('h:i A', $session_end);

                        $downloadIcon   = asset('/images/download.svg');
                        $editIcon   = asset('/images/edit.png');
                        $viewIcon   = asset('/images/view.png');

                        $data   .= <<<EOL
                            <tr>
                                <td>{$batch}</td>
                                <td>{$activity}</td>
                                <td>{$subActivity}</td>
                                <td>{$session_start} - {$session_end}</td>
                                <td>{$date}</td>
                                <td>{$di_name}</td>
                                <td>
                                    <a href="#" onclick="window.get_editExtraSession({$sessionId}); return false;" data-toggle="tooltip" title="Edit sessions details">
                                        <img src="{$editIcon}">
                                    </a>
                                    <a href="#" onclick="window.download_extraSessionData({$sessionId}); return false;" data-toggle="tooltip" title="Download attendance data">
                                        <img src="{$downloadIcon}" style="width:33px;">
                                    </a>
                                    <a href="#" onclick="window.get_extraSessionsMeta({$sessionId}); return false;" data-toggle="tooltip" title="View probationers">
                                        <img src="{$viewIcon}">
                                    </a>
                                </td>
                            </tr>
                        EOL;
                    }
                } else {
                    $data   = "<tr><td colspan=\"6\">No Sessions Found</td></tr>";
                }

                return json_encode([
                    'status'    => 'success',
                    'message'   => '',
                    'data'      => $data,
                ]);
            } else {
                return json_encode([
                    'status'    => 'error',
                    'message'   => implode('<br />', $errors),
                ]);
            }

            return;
        }

        // get_extra_sessions_meta
        if ($requestName === "get_extra_sessions_meta") {
            $result = [];
            $errors  = [];

            // print_r($request->all());
            $sessionId       = $request->sessionId;
            $ExtraSession   = \App\Models\ExtraSession::find($sessionId);
            $Sessionmetas   = $ExtraSession->metas;

            $sessionId   = $ExtraSession->id;
            $batch_id   = $ExtraSession->batch_id;
            $batch      = batch_name($batch_id);

            $activity_id     = $ExtraSession->activity_id;
            $activity        = activity_name($activity_id);

            $subActivity_id  = $ExtraSession->subactivity_id;
            $subActivity     = "";
            if (!empty($subActivity_id)) {
                $subActivity     = activity_name($subActivity_id);
                
                $activity_unit =  $subActivity_id;
            }
            else
            {
                $activity_unit =  $activity_id;
            }

            $check_grade = Activity::withTrashed()->where('id', $activity_unit)->first();
            ($check_grade->has_qualify !== 0) ? ($type = 'qualify') : (($check_grade->has_grading !== 0) ? $type = 'grade' : $type = 'count');
            if($type === 'count')
            {
                $unit = activity_unit($activity_unit);
                if(empty($unit))
                {
                    $unit = "No Units";
                }
            }
            else
            {
                $unit = $type;
            }


            $di_id      = $ExtraSession->drillinspector_id;
            $di_name    = user_name($di_id);

            $date     = $ExtraSession->date;
            $session_start  = $ExtraSession->session_start;
            $session_start  = date('h:i A', $session_start);

            $session_end    = $ExtraSession->session_end;
            $session_end    = date('h:i A', $session_end);

            $session_time   = $session_start .' - '. $session_end;

            // Session Details
            echo <<<EOL
                <h4 class="text-center mb-4">Session Details</h4>

                <table class="table table-stripped session-details-table w-75 mb-4">
                    <tr>
                        <td style="width:150px;">Batch</td>
                        <td>{$batch}</td>
                    </tr>
                    <tr>
                        <td>Activity</td>`
                        <td>{$activity}</td>
                    </tr>
                    <tr>
                        <td>Sub Activity</td>
                        <td>{$subActivity}</td>
                    </tr>
                    <tr>
                        <td>Session Date</td>
                        <td>{$date}</td>
                    </tr>
                    <tr>
                        <td>Session Time</td>
                        <td>{$session_time}</td>
                    </tr>
                    <tr>
                        <td>Staff / DI</td>
                        <td>{$di_name}</td>
                    </tr>
                </table>
            EOL;

            if (count($Sessionmetas) > 0) {
                echo <<<EOL
                <div class="listdetails">
                <table id="extra_sessions_table" class="table">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Probationer</th>
                            <th>Squad</th>
                            <th>Attendance</th>
                            <th>{$unit}</th>
                            <th>Regular Session</th>
                        </tr>
                    </thead>
                    <tbody>
                EOL;

                $sl = 1;
                foreach ($Sessionmetas as $Sessionmeta) {
                    $pb_id      = $Sessionmeta->probationer_id;

                    $probationer   = \App\Models\probationer::find($pb_id);
                    $pb_name    = $probationer->Name;
                    $squad_id   = $probationer->squad_id;
                    $squad_num  = squad_number($squad_id);

                    $attendance      = $Sessionmeta->attendance;
                  //  $unit_count      = $Sessionmeta->count;
                  //  $grade      = $Sessionmeta->grade;

                    $rSession   = "";
                    $timetable_id   = $Sessionmeta->timetable_id;
                    if(!empty($timetable_id)) {
                        $Timetable  = Timetable::find($timetable_id);
                        $tt_date  = $Timetable->date;
                        $tt_session  = $Timetable->session_number;
                        $rSession   = "(Session ". $tt_session .")<br />". $tt_date;
                    }

                    if($type === 'count')
                    {
                        $unit_count = $Sessionmeta->count;
                    }
                    elseif($type === 'grade')
                    {
                        $unit_count = $Sessionmeta->grade;
                    }
                    else
                    {
                        $unit_count = qualified_values($Sessionmeta->qualified);
                    }

                    echo <<<EOL
                        <tr>
                            <td>{$sl}</td>
                            <td class="text-left">{$pb_name}</td>
                            <td class="text-left">{$squad_num}</td>
                            <td>{$attendance}</td>
                            <td>{$unit_count}</td>
                            <td>{$rSession}</td>
                        </tr>
                    EOL;

                    $sl++;
                }
                echo <<<EOL
                </table>
                </div>
                EOL;
            }

            return;
        }

        // Get Probationers table data for extra session create form
        if ($requestName === "get_create_extraSession_form") {
            $result = [];
            $data   = [];
            $errors  = [];

            $batch_id       = $request->batch_id;
            $activity_id    = $request->activity_id;



            // $subactivity_id = $request->subactivity_id;
            // $session_date   = $request->session_date;

            // $date1  = date('Y-m-01', strtotime("{$session_date} -1 month"));
            // $date2  = date('Y-m-t', strtotime($session_date));
            // if(strtotime($date2) > time()) {
            //     $date2  = date('Y-m-d');
            // }

            // $session_time_arr = explode('-', $request->session_time);
            // if (count($session_time_arr) === 2) {

            //     $session_time_start = strtotime($session_date . ' ' . $session_time_arr[0]);
            //     $session_time_end = strtotime($session_date . ' ' . $session_time_arr[1]);

            //     if ( empty($session_time_start) || empty($session_time_end) ) {
            //         $errors[]   = "Invalid session time.";
            //     } else if ($session_time_start > $session_time_end) {
            //         $errors[]   = "Session start time is greater than end time.";
            //     }
            // }
            if(!empty($errors)) {
                $errorMsg   =  implode('<br />', $errors);
                // echo "<div class=\"msg msg-danger rl-margin-auto\">{$errorMsg}</div>";
                return json_encode([
                    "status"    => "error",
                    "message"   => $errorMsg,
                ]);
            }

            // if(empty($subactivity_id)) {
            //     $subactivity_id = null;
            // }

            // // Timetable
            // $extraSessionPbIds  = \App\Models\ExtraSessionmeta::where('probationers.batch_id', $batch_id)
            //     ->whereIn('extra_sessionmetas.attendance', ['P', 'MDO', 'NCM'])
            //     ->whereNotNull('extra_sessionmetas.timetable_id')
            //     ->join('probationers', 'probationers.id', '=', 'extra_sessionmetas.probationer_id')
            //     ->pluck('extra_sessionmetas.timetable_id')->toArray();

            // $probationer_ids  = \App\Models\probationer::where('probationers.batch_id', $batch_id)
            //     // ->whereDate('timetables.date', '>=', $date1)
            //     // ->whereDate('timetables.date', '<=', $date2)
            //     ->where('timetables.activity_id', $activity_id)
            //     // ->where('timetables.subactivity_id', $subactivity_id)
            //     ->whereNotIn('timetables.id', $extraSessionPbIds)
            //     ->whereNotIn('probationers_dailyactivity_data.attendance', ['P', 'MDO', 'NCM'])
            //     // ->select('timetables.id as timetableId')
            //     ->join('probationers_dailyactivity_data', 'probationers.id', '=', 'probationers_dailyactivity_data.probationer_id')
            //     ->join('timetables', 'probationers_dailyactivity_data.timetable_id', '=', 'timetables.id')
            //     ->pluck('probationers.id');

            // echo $probationer_ids;
            // return;


            $Batch = \App\Models\Batch::where('id', $batch_id)->select('BatchName')->first();

            $data   = view("timetable.create-extra-session-form", compact('request', 'Batch'))->render();
            return json_encode([
                "status"    => "success",
                "data"      => $data,
            ]);

            return;
        }

        // Get Probationers list
        if ($requestName === "get_extraSession_probationers") {
            $result = [];
            $data   = [];
            $errors  = [];

            $batch_id       = $request->batch_id;
            $activity_id    = $request->activity_id;
            $subactivity_id = $request->subactivity_id;

            if(!empty($errors)) {
                $errorMsg   =  implode('<br />', $errors);
                // echo "<div class=\"msg msg-danger rl-margin-auto\">{$errorMsg}</div>";
                return json_encode([
                    "status"    => "error",
                    "message"   => $errorMsg,
                ]);
            }

            if(empty($subactivity_id)) {
                $subactivity_id = null;
            }

            // Timetable
            $extraSessionPbIds  = \App\Models\ExtraSessionmeta::where('probationers.batch_id', $batch_id)
                ->whereIn('extra_sessionmetas.attendance', ['P', 'MDO', 'NCM'])
                ->whereNotNull('extra_sessionmetas.timetable_id')
                ->join('probationers', 'probationers.id', '=', 'extra_sessionmetas.probationer_id')
                ->pluck('extra_sessionmetas.timetable_id')->toArray();

            $probationerQ  = \App\Models\probationer::query()
                ->where('probationers.batch_id', $batch_id)
                ->where('timetables.activity_id', $activity_id)
                ->where('timetables.subactivity_id', $subactivity_id)
                //->whereNotIn('timetables.id', $extraSessionPbIds)
                ->whereNotIn('probationers_dailyactivity_data.attendance', ['P', 'MDO', 'NCM'])
                // ->select('timetables.id as timetableId')
                ->join('probationers_dailyactivity_data', 'probationers.id', '=', 'probationers_dailyactivity_data.probationer_id')
                ->join('timetables', 'probationers_dailyactivity_data.timetable_id', '=', 'timetables.id');

            // $probationer_ids    = $probationerQ->pluck('probationers.id');

            $Probationers    = $probationerQ->groupBy('probationers.id')
                ->selectRaw("probationers.*, count(probationers_dailyactivity_data.attendance) as session_count")
                ->get();
            // echo $probationer_ids;
            // return;


            if ($Probationers->count() > 0) {
                $Batch = \App\Models\Batch::where('id', $batch_id)->select('BatchName')->first();

                $data   = view("timetable.extra-session-probationers-select-form", compact('request', 'Probationers', 'Batch', 'extraSessionPbIds', 'probationerQ'))->render();
                return json_encode([
                    "status"    => "success",
                    "data"      => $data,
                    "Probationers"      => $Probationers,
                ]);

            } else {
                // echo "No probationers found.";
                return json_encode([
                    "status"    => "error",
                    "message"   => "No probationers found.",
                ]);
            }

            return;
        }

        // Create Extra Session Form Submit
        if ($requestName === "create_extrasession") {
            $result = [];
            $errors  = [];

            // print_r($request->all());
            // return;


    //    echo "";
    //    print_r(json_encode($request->all()));
    //    echo "<br />";

    //    exit;
            $batch_id       = $request->batch_id;
            $activity_id    = $request->activity_id;
            $subactivity_id = $request->subactivity_id;

            // $component_ids     = $request->component_id;
            $session_dates  = $request->session_date;
            $session_times     = $request->session_time;
            $di_ids      = $request->di_id;

            $probationer_ids    = $request->probationer_ids;
            $probationer_ids    = explode(',', $probationer_ids);
            $probationer_ids    = array_filter($probationer_ids);


            if (!is_array($probationer_ids) || empty($probationer_ids)) {
                return json_encode([
                    'status'    => 'error',
                    'message'   => 'Select probationers.',
                ]);
            }



            $sessionData    = [];
            foreach($session_dates as $key => $session_date) {
                if (isValidDate($session_date, 'Y-m-d') === true) {
                    $session_date   = date('Y-m-d', strtotime($session_date));

                    $sessionNum = $key+1;
                    if( !empty($session_times[$key]) ) {
                        $session_time_start = "";
                        $session_time_end   = "";

                        $session_time_arr = explode('-', $session_times[$key]);
                        if (count($session_time_arr) === 2) {
                            $session_time_start = strtotime($session_date . ' ' . $session_time_arr[0]);
                            $session_time_end = strtotime($session_date . ' ' . $session_time_arr[1]);

                            if ( empty($session_time_start) || empty($session_time_end) ) {
                                $errors[]   = "Invalid session time for Session {$sessionNum}.";
                            } else if ($session_time_start >= $session_time_end) {
                                $errors[]   = "Session end time should be greater than start time for Session {$sessionNum}.";
                            }
                        } else {
                            $errors[]   = "Invalid session time for Session {$sessionNum}.";
                        }
                    } else {
                        $errors[]   = "Session time is empty for Session {$sessionNum}.";
                    }

                    if( empty($di_ids[$key]) ) {
                        $errors[]   = "Invalid session time for Session {$sessionNum}.";
                    }

                    $sessionData[]    = [
                        "subactivity_id"  => empty($subactivity_id) ? null : $subactivity_id,
                        "component_id"  => null,
                        "di_id"         => $di_ids[$key],
                        "session_date" => $session_date,
                        "session_start" => $session_time_start,
                        "session_end"   => $session_time_end,
                    ];
                } else {
                    $errors[]   = "Date format should be 'YYYY-MM-DD'.";
                }


            }

            if( empty($errors) ) {
                // Create the session
                try {
                    foreach($sessionData as $session) {
                        $ExtraSession   = \App\Models\ExtraSession::create([
                            'batch_id'      => $batch_id,
                            'activity_id'   => $activity_id,
                            'subactivity_id'  => $session["subactivity_id"],
                            'component_id'  => $session["component_id"],
                            'drillinspector_id' => $session["di_id"],
                            'date'          => $session["session_date"],
                            'session_start' => $session["session_start"],
                            'session_end'   => $session["session_end"],
                        ]);

                        $session_id = $ExtraSession->id;

                        foreach ($probationer_ids as $probationer_id) {
                            $count = missed_sessions_count($activity_id, $session["subactivity_id"], $probationer_id);
                          //  return $count;
                            if($count > 0)
                            {
                                \App\Models\ExtraSessionmeta::create([
                                    'extra_session_id'  => $session_id,
                                    'probationer_id'    => $probationer_id,
                                ]);
                            }
                        }
                    }

                    return json_encode([
                        'status'    => 'success',
                        'message'    => "Session created successfully.",
                    ]);
                } catch (Exception $e) {

                    $msg    = "Unable to create session.<br />";
                    $msg    .= $e->getMessage();

                    return json_encode([
                        'status'    => 'error',
                        'message'    => $msg,
                    ]);
                }
            }


            if (!empty($errors)) {
                return json_encode([
                    'status'    => 'error',
                    'message'   => implode('<br />', $errors),
                ]);
            }
        }



        // Get extra_session Edit Form
        if ($requestName === "get_extra_session_edit") {
            $result = [];
            $errors  = [];

            // print_r($request->all());
            $sessionId       = $request->session_id;
            $ExtraSession   = \App\Models\ExtraSession::find($sessionId);
            $get_DI     = get_staffs();

            if (!empty($ExtraSession)) {
                return view('timetable.extra-session-edit-form', compact('ExtraSession', 'get_DI'));
            } else {
                echo 'Session data not found.';
            }

            return;
        }

        // Extra_session Edit Submit
        if ($requestName === "editExtraSession_submit") {
            $result = [];
            $errors  = [];

            // print_r($request->all());
            $sessionId  = $request->session_id;
            $date      = $request->session_date;
            $session_time      = $request->session_time;
            $di_id      = $request->new_di_id;

            $session_date   = "";
            $session_start = "";
            $session_end   = "";

            if (isValidDate($date, 'Y-m-d') === true) {
                $session_date   = date('Y-m-d', strtotime($date));

                if( !empty($session_time) ) {

                    $session_time_arr = explode('-', $session_time);
                    if (count($session_time_arr) === 2) {
                        $session_start = strtotime($session_date . ' ' . $session_time_arr[0]);
                        $session_end = strtotime($session_date . ' ' . $session_time_arr[1]);

                        if ( empty($session_start) || empty($session_end) ) {
                            $errors[]   = "Invalid session time.";
                        } else if ($session_start >= $session_end) {
                            $errors[]   = "Session end time should be greater than start time.";
                        }
                    } else {
                        $errors[]   = "Invalid session time.";
                    }
                } else {
                    $errors[]   = "Session time is empty.";
                }
            } else {
                $errors[]   = "Invalid date format (expected 'YYYY-MM-DD').";
            }

            if(empty($sessionId)) {
                $errors[]   = 'Session Id missing.';
            }
            if(empty($di_id)) {
                $errors[]   = 'Select a Drillinspector.';
            }

            if(empty($errors)) {
                $ExtraSession   = \App\Models\ExtraSession::find($sessionId);
                $ExtraSession->drillinspector_id    = $di_id;
                $ExtraSession->date             = $session_date;
                $ExtraSession->session_start    = $session_start;
                $ExtraSession->session_end      = $session_end;
                $ExtraSession->save();

                return json_encode([
                    'status'    => 'success',
                    'message'   => 'Details saved.',
                ]);
            }

            return json_encode([
                'status'    => 'error',
                'message'   => implode(',<br />', $errors),
            ]);
        }

        // Download Extra Session Probationer's Data
        if ($requestName === "download_extraSessionData") {
            $result = [];
            $errors  = [];

            $session_id       = intval($request->session_id);

            if (empty($session_id)) {
                $errors[]   = "Session id missing.";
            } else {
                $ExtraSession   = \App\Models\ExtraSession::find($session_id);
                if (!$ExtraSession) {
                    $errors[]   = "Session id not exist.";
                } else {
                    $SessionMetas   = $ExtraSession->metas;
                    if (count($SessionMetas) === 0) {
                        $errors[]   = "Probationer data not available for this session.";
                    }
                }
            }

            if (empty($errors)) {

                $data_request   = [
                    'session_id'  => $session_id,
                ];
                $data_request   = data_crypt( serialize($data_request) );

                $datasheet_url = url("/extrasessions/extrasession-datasheet/{$data_request}");
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

        // Get Extra Session Import Modal
        if ($requestName === "get_extraSessionImport_modal") {
            echo <<<EOL
                <div id="uploadFileTab">

                    <form name="importExtraSessionData_form" id="importExtraSessionData_form" action="#" method="post" class="text-center mt-3" enctype="multipart/form-data" accept-charset="utf-8">
                        <input type="file" name="sessionData_csv" accept=".csv" />
                    </form>

                    <div id="importExtraSessionData_status" class="mt-3"></div>

                    <div class="text-center mt-3">
                        <button type="button" class="btn btn-primary"  onclick="window.importExtraSessionData_submit();">Submit</button>
                    </div>
                </div>
            EOL;

            return;
        }

        // Import Extra Session Data
        if ($requestName === "import_extraSession_data") {
            // Import attendance data
            if ($request->hasFile('sessionData_csv') && $request->file('sessionData_csv')->isValid()) {
                $errorMsg       = [];
                $dataRowError   = [];

                $result     = [];

                $original_filename  = $request->sessionData_csv->getClientOriginalName();
                $ext = pathinfo($original_filename, PATHINFO_EXTENSION);

                if ($ext !== 'csv') {
                    $result['status']  = 'error';
                    $result['message']  = "Please upload a file with .csv extension.";

                    return json_encode($result);
                }

                $fileName   = time() . '-' . $original_filename;
                $request->sessionData_csv->storeAs('csv_files', $fileName);

                $filePath   = storage_path("app/public/csv_files/{$fileName}");
                $fileData   = csvToArray($filePath, ',');

                if ( is_array($fileData) && count($fileData) > 0) {
                    $valid_data_keys  = [
                        'key',
                        'component',
                        'attendance',
                        'grade',
                        'count',
                        'qualified',
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
                        $component  = trim($data["component"]);

                        $attendance     = strtoupper(trim($data["attendance"]));
                        $grade          = strtoupper(trim($data["grade"]));
                        $count          = trim($data["count"]);
                        $qualified      = trim($data["qualified"]);

                        $grade  = empty($grade)? null : $grade;
                        $count  = ($count === "")? null : $count;

                        if (!empty($composite_key) && !empty($attendance)) {
                            $session_id = 0;
                            $squad_id   = 0;
                            $probationer_id = null;
                            $component_id   = null;
                            $qualified_val   = null;

                            $keyData    = explode('-', $composite_key);
                            if(is_array($keyData) && count($keyData) === 3) {
                                $session_id     = $keyData[0];
                                $squad_id       = $keyData[1];
                                $probationer_id = $keyData[2];
                            }

                            if( empty($squad_id) || empty($session_id) || empty($probationer_id) ) {
                                $dataRowError[] = "Row #{$row_num}: Invalid key submitted.";
                            }
                            if( !in_array($attendance, valid_attendances()) ) {
                                $dataRowError[] = "Row #{$row_num}: Invalid attendance '{$attendance}'.";
                            }
                            if( !empty($grade) && !in_array($grade, range('A', 'E')) ) {
                                $dataRowError[] = "Row #{$row_num}: Invalid grade '{$grade}'.";
                            }
                            if( $attendance !== 'P' && !empty($grade) ) {
                                $dataRowError[] = "Row #{$row_num}: grade should be empty if attendance is not (P)resent.";
                            }
                            if( $attendance !== 'P' && !empty($count) ) {
                                $dataRowError[] = "Row #{$row_num}: count should be empty if attendance is not (P)resent.";
                            }

                            if(!empty($qualified)) {
                                $qualified1  = explode(' - ', $qualified);
                                if(is_array($qualified1) && count($qualified1) === 2) {
                                    $qualified_val  = $qualified1[0];
                                    if( ($qualified_val !== "") && !in_array(intval($qualified_val), [0, 1, 2], true) ) {
                                        $dataRowError[] = "Row #{$row_num}: Invalid qualified value '{$qualified}'.";
                                    }
                                } else {
                                    $dataRowError[] = "Row #{$row_num}: Invalid qualified value '{$qualified}'.";
                                }
                            }

                            // Verify squad_id for timetable_id and probationer_id
                            $extraSession  = \App\Models\ExtraSession::find($session_id);

                            if($extraSession) {
                                $subactivity_id = $extraSession->subactivity_id;

                                // Get component
                                if( !empty($subactivity_id) && !empty($component) ) {
                                    $component  = explode(' [', $component);
                                    if( isset($component[0]) ) {
                                        $component_id   = \App\Models\Activity::where('parent_id', $subactivity_id)->where('name', $component[0])->where('type', 'component')->value('id');
                                        $component_id   = empty($component_id) ? null : $component_id;
                                    }
                                }
                            } else {
                                $dataRowError[] = "Row #{$row_num}: Invalid data submitted, timetable data not matched.";
                            }

                            $attnData[]  = [
                                'session_id'    => $session_id,
                                'probationer_id'    => $probationer_id,
                                'component_id'    => $component_id,
                                'attendance'    => $attendance,
                                'grade'     => $grade,
                                'count'     => $count,
                                'qualified'     => $qualified_val,
                            ];
                        }

                        $i++;
                    }

                    if(empty($dataRowError)) {
                        $setAttnError = [];
                        $tt_count   = 0;

                        foreach($attnData as $data) {

                            $setAttendance  = set_extra_session_attendance($data);

                            if($setAttendance["status"] === "error") {
                                $setAttnError[] = "Error: ". $setAttendance["message"];
                            }

                            $tt_count++;
                        }

                        if(empty($setAttnError)) {
                            return json_encode([
                                'status'    => 'success',
                                'message'   => 'Session Data uploaded successfully.',
                                'tt_count'  => $tt_count,
                            ]);
                        } else {
                            return json_encode([
                                'status'    => 'error',
                                'message'   => implode('<br />', $setAttnError),
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
}
