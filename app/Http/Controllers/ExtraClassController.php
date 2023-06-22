<?php

namespace App\Http\Controllers;

use App\Models\ExtraClass;
use App\Models\ExtraSession;
use App\Models\ExtraClassmeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

use Exception;

class ExtraClassController extends Controller
{

    /**
     * View Extra Classes
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function extra_classes(Request $request)
    {
        return view('timetable.extra-classes');
    }


    /**
     * Create Extra Class View
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function create_extra_class(Request $request)
    {
        $batches    = \App\Models\Batch::all();
        $get_DI     = get_staffs();
        return view('timetable.create-extra-class', compact('batches', 'get_DI'));
    }

    /**
     * Download extra class datasheet
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function extraclass_datasheet($data_request)
    {
        $data   = unserialize( data_crypt($data_request, 'd') );

        // print_r($data);

        $session_id   = isset($data["session_id"]) ? $data["session_id"] : 0;

        if( empty($session_id) ) {
            return "Invalid data request.";
        }

        $ExtraClass   = \App\Models\ExtraClass::find($session_id);
        if(!$ExtraClass) {
            return "Session not exist, or invalid data request.";
        }

        // Get Session Meta
        $SessionMetas   = $ExtraClass->metas;
        if (count($SessionMetas) === 0) {
            return "Probationer data not available for this session.";
        }

        $batch_id   = $ExtraClass->batch_id;
        $batch_name = batch_name($batch_id);

        $activity_id     = $ExtraClass->activity_id;
        $activity        = activity_name($activity_id);

        $subActivity_id = $ExtraClass->subactivity_id;
        $subactivity    = "";
        if (!empty($subActivity_id)) {
            $subactivity   = activity_name($subActivity_id);
        }

        $component_id  = $ExtraClass->component_id;
        $component     = "";
        if(!empty($component_id)) {
            $component     = activity_name($component_id);
        }

        $di_id      = $ExtraClass->drillinspector_id;
        $di_name    = user_name($di_id);

        $date     = $ExtraClass->date;
        $session_start  = $ExtraClass->session_start;
        $session_start  = date('h:i A', $session_start);

        $session_end    = $ExtraClass->session_end;
        $session_end    = date('h:i A', $session_end);

        // ---------------------------------------------------------
        // Initialize Spreadsheet with 1st sheet as ExtraClassData
        // ---------------------------------------------------------
        $spreadsheet = new Spreadsheet();
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('ExtraClassData');

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

        $grades = "A, B, C, D, E";

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
            $sheet1->setCellValue("J{$row}", $component);
            $sheet1->setCellValue("K{$row}", $attendance);
            $sheet1->setCellValue("L{$row}", $grade);
            $sheet1->setCellValue("M{$row}", $count);

            // Cell validation dropdown list
            $validation = $sheet1->getCell("L{$row}")->getDataValidation(); // GET the cell for Data validation
            $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST) // set the validation type to 'List'
                ->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION) // set the validation type to 'List'
                ->setShowDropDown(true)
                ->setAllowBlank(false) // Do not allow empty value for activity
                ->setShowInputMessage(true)
                ->setPromptTitle('Pick from list')
                ->setPrompt('Please pick a value from the drop-down list.')
                ->setFormula1('"'. $grades .'"'); // Set drop down options

            $row++;
        }

        $row    = $row - 1;
        foreach (range("A{$row}", "M{$row}") as $columnID) {
            $sheet1->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Save Spreadsheet
        // $writer = new Xlsx($spreadsheet);

        $fileName   = "Extra_Class_Datasheet_{$batch_id}_{$date}_{$session_id}_" . date('Ymd-hia') . ".xlsx";

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
        if ($requestName === "get_extraclasses") {
            $result = [];
            $errors  = [];

            // print_r($request->all());
            $batch_id   = $request->batch_id;
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
                $dataQuery   = \App\Models\ExtraClass::query()
                    ->where('batch_id', $batch_id)
                    ->whereNotNull('activity_id');

                if ( !empty($date_to) ) {
                    $dataQuery->whereDate('date', '>=', $date_from)->whereDate('date', '<=', $date_to);
                } else {
                    $dataQuery->whereDate('date', $date_to);
                }

                $ExtraClasses   = $dataQuery->where('session_start', '>', 0)
                    ->orderBy('session_start', 'desc')->get();

                if (count($ExtraClasses) > 0) {

                    $data   = "";
                    foreach ($ExtraClasses as $ExtraClass) {

                        $sessionId   = $ExtraClass->id;
                        $batch_id   = $ExtraClass->batch_id;
                        $batch      = batch_name($batch_id);

                        $activity_id     = $ExtraClass->activity_id;
                        $activity        = activity_name($activity_id);

                        $subActivity_id  = $ExtraClass->subactivity_id;
                        $subActivity     = "--";
                        if (!empty($subActivity_id)) {
                            $subActivity     = activity_name($subActivity_id);
                        }

                        $di_id      = $ExtraClass->drillinspector_id;
                        $di_name    = user_name($di_id);

                        $date     = $ExtraClass->date;
                        $session_start  = $ExtraClass->session_start;
                        $session_start  = date('h:i A', $session_start);

                        $session_end    = $ExtraClass->session_end;
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
                                    <a href="#" onclick="window.get_editExtraClass({$sessionId}); return false;" data-toggle="tooltip" title="Edit sessions details">
                                        <img src="{$editIcon}">
                                    </a>
                                    <a href="#" onclick="window.download_extraClassData({$sessionId}); return false;" data-toggle="tooltip" title="Download attendance data">
                                        <img src="{$downloadIcon}" style="width:33px;">
                                    </a>
                                    <a href="#" onclick="window.get_extraClassMetas({$sessionId}); return false;" data-toggle="tooltip" title="View probationers">
                                        <img src="{$viewIcon}">
                                    </a>
                                </td>
                            </tr>
                        EOL;
                    }
                } else {
                    $data   = "<tr><td colspan=\"6\">No Session Found</td></tr>";
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
        if ($requestName === "get_extra_class_metas") {
            $result = [];
            $errors  = [];

            // print_r($request->all());
            $sessionId       = $request->sessionId;
            $ExtraClass   = \App\Models\ExtraClass::find($sessionId);
            $Sessionmetas   = $ExtraClass->metas;

            $sessionId   = $ExtraClass->id;
            $batch_id   = $ExtraClass->batch_id;
            $batch      = batch_name($batch_id);

            $activity_id     = $ExtraClass->activity_id;
            $activity        = activity_name($activity_id);

            $subActivity_id  = $ExtraClass->subactivity_id;
            $subActivity     = "";
            if (!empty($subActivity_id)) {
                $subActivity     = activity_name($subActivity_id);
            }

            $di_id      = $ExtraClass->drillinspector_id;
            $di_name    = user_name($di_id);

            $date     = $ExtraClass->date;
            $session_start  = $ExtraClass->session_start;
            $session_start  = date('h:i A', $session_start);

            $session_end    = $ExtraClass->session_end;
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
                        <td>Activity</td>
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
                            <th>Count</th>
                            <th>Grade</th>
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
                    $unit_count      = $Sessionmeta->count;
                    $grade      = $Sessionmeta->grade;

                    echo <<<EOL
                        <tr>
                            <td>{$sl}</td>
                            <td class="text-left">{$pb_name}</td>
                            <td class="text-left">{$squad_num}</td>
                            <td>{$attendance}</td>
                            <td>{$unit_count}</td>
                            <td>{$grade}</td>
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

        // Get create extra class form Probationers data
        if ($requestName === "get_create_extraClass_probationers") {
            $result = [];
            $errors  = [];

            $batch_id       = $request->batch_id;
            $activity_id    = $request->activity_id;
            $subactivity_id = $request->subactivity_id;
            $session_date   = $request->session_date;

            $date1  = date('Y-m-01', strtotime("{$session_date} -1 month"));
            $date2  = date('Y-m-t', strtotime($session_date));
            if(strtotime($date2) > time()) {
                $date2  = date('Y-m-d');
            }

            $session_time_arr = explode('-', $request->session_time);
            if (count($session_time_arr) === 2) {

                $session_time_start = strtotime($session_date . ' ' . $session_time_arr[0]);
                $session_time_end = strtotime($session_date . ' ' . $session_time_arr[1]);

                if ( empty($session_time_start) || empty($session_time_end) ) {
                    $errors[]   = "Invalid session time.";
                } else if ($session_time_start > $session_time_end) {
                    $errors[]   = "Session start time is greater than end time.";
                }
            }
            if(!empty($errors)) {
                $errorMsg   =  implode('<br />', $errors);
                // echo "<div class=\"msg msg-danger rl-margin-auto\">{$errorMsg}</div>";
                return json_encode([
                    "status"    => "error",
                    "message"   => $errorMsg,
                ]);
            }

            // Timetable
            $Probationers  = \App\Models\probationer::where('probationers.batch_id', $batch_id)
                ->orderBy('Name', 'asc')->get();


            if (count($Probationers)>0) {
                $Batch = \App\Models\Batch::where('id', $batch_id)->select('BatchName')->first();

                $data   = view("timetable.create-extra-class-form", compact('request', 'Probationers', 'Batch'))->render();
                return json_encode([
                    "status"    => "success",
                    "data"      => $data,
                ]);
            } else {
                // echo "No probationer found.";
                return json_encode([
                    "status"    => "error",
                    "message"   => "No probationers found.",
                ]);
            }

            return;
        }

        // Create Extra Class Form Submit
        if ($requestName === "create_extraclass") {
            $result = [];
            $errors  = [];

            // print_r($request->all());
            $batch_id       = $request->batch_id;
            $activity_id    = $request->activity_id;
            $subactivity_id = $request->subactivity_id;

            // $component_ids     = $request->component_id;
            $session_dates     = $request->session_date;
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
                        "subactivity_id"  => sanitize_activity_id($subactivity_id),
                        "component_id"  => null,
                        "di_id"         => $di_ids[$key],
                        "session_date"  => $session_date,
                        "session_start" => $session_time_start,
                        "session_end"   => $session_time_end,
                    ];
                } else {
                    $errors[]   = "Date format should be 'YYYY-MM-DD'.";
                }
            }

            // $session_time_arr = explode('-', $session_time);
            // if (count($session_time_arr) === 2) {

            //     $session_time_start = strtotime($session_date . ' ' . $session_time_arr[0]);
            //     $session_time_end = strtotime($session_date . ' ' . $session_time_arr[1]);

            //     if ( empty($session_time_start) || empty($session_time_end) ) {
            //         $errors[]   = "Invalid session time.";
            //     } else if ($session_time_start > $session_time_end) {
            //         $errors[]   = "Session start time is greater than end time.";
            //     }

            //     if( empty($errors) ) {
            //         // Create the session
            //         try {
            //             $ExtraClass   = \App\Models\ExtraClass::create([
            //                 'batch_id'      => $batch_id,
            //                 'activity_id'   => $activity_id,
            //                 'subactivity_id'    => $subactivity_id,
            //                 'drillinspector_id' => $di_id,
            //                 'date'          => $session_date,
            //                 'session_start' => $session_time_start,
            //                 'session_end'   => $session_time_end,
            //             ]);
            //         } catch (Exception $e) {
            //             $msg    = "Unable to create session.<br />";
            //             $msg    .= $e->getMessage();

            //             return json_encode([
            //                 'status'    => 'error',
            //                 'message'    => $msg,
            //             ]);
            //         }

            //         if ($ExtraClass) {
            //             $class_id = $ExtraClass->id;

            //             foreach ($probationer_ids as $probationer_id) {
            //                 \App\Models\ExtraClassmeta::create([
            //                     'extra_class_id'    => $class_id,
            //                     'probationer_id'    => $probationer_id,
            //                 ]);
            //             }

            //             return json_encode([
            //                 'status'    => 'success',
            //                 'message'    => "Session created successfully.",
            //             ]);
            //         }
            //     }
            // } else {
            //     $errors[]   = "Invalid session time.";
            // }

            if( empty($errors) ) {
                // Create the session
                try {
                    foreach($sessionData as $session) {
                        $ExtraClass   = \App\Models\ExtraClass::create([
                            'batch_id'      => $batch_id,
                            'activity_id'   => sanitize_activity_id($activity_id),
                            'subactivity_id'    => sanitize_activity_id($session["subactivity_id"]),
                            'component_id'  => sanitize_activity_id($session["component_id"]),
                            'drillinspector_id' => $session["di_id"],
                            'date'          => $session["session_date"],
                            'session_start' => $session["session_start"],
                            'session_end'   => $session["session_end"],
                        ]);

                        $class_id = $ExtraClass->id;

                        foreach ($probationer_ids as $probationer_id) {
                            \App\Models\ExtraClassmeta::create([
                                'extra_class_id'  => $class_id,
                                'probationer_id'    => $probationer_id,
                            ]);
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
        if ($requestName === "get_extra_class_edit") {
            $result = [];
            $errors  = [];

            // print_r($request->all());
            $classId       = $request->class_id;
            $ExtraClass   = \App\Models\ExtraClass::find($classId);
            $get_DI     = get_staffs();

            if (!empty($ExtraClass)) {
                return view('timetable.extra-class-edit-form', compact('ExtraClass', 'get_DI'));
            } else {
                echo 'Session data not found.';
            }

            return;
        }

        // Extra_session Edit Submit
        if ($requestName === "editExtraClass_submit") {
            $result = [];
            $errors  = [];

            // print_r($request->all());
            $classId  = $request->class_id;
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
                $errors[]   = "Invalid session date.";
            }

            if(empty($classId)) {
                $errors[]   = 'Class Id missing.';
            }
            if(empty($di_id)) {
                $errors[]   = 'Select a Drillinspector.';
            }

            if(empty($errors)) {
                $ExtraClass   = \App\Models\ExtraClass::find($classId);
                $ExtraClass->drillinspector_id    = $di_id;
                $ExtraClass->date           = $session_date;
                $ExtraClass->session_start  = $session_start;
                $ExtraClass->session_end    = $session_end;
                $ExtraClass->save();

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
        if ($requestName === "download_extraClassData") {
            $result = [];
            $errors  = [];

            $session_id       = intval($request->session_id);

            if (empty($session_id)) {
                $errors[]   = "Session id missing.";
            } else {
                $ExtraClass   = \App\Models\ExtraClass::find($session_id);
                if (!$ExtraClass) {
                    $errors[]   = "Session id not exist.";
                } else {
                    $SessionMetas   = $ExtraClass->metas;
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

                $datasheet_url = url("/extraclasses/extraclass-datasheet/{$data_request}");
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
        if ($requestName === "get_extraClassImport_modal") {
            echo <<<EOL
                <div id="uploadFileTab">

                    <form name="importExtraClassData_form" id="importExtraClassData_form" action="#" method="post" class="text-center mt-3" enctype="multipart/form-data" accept-charset="utf-8">
                        <input type="file" name="sessionData_csv" accept=".csv" />
                    </form>

                    <div id="importExtraClassData_status" class="mt-3"></div>

                    <div class="text-center mt-3">
                        <button type="button" class="btn btn-primary"  onclick="window.importExtraClassData_submit();">Submit</button>
                    </div>
                </div>
            EOL;

            return;
        }

        // Import Extra Session Data
        if ($requestName === "import_extraClass_data") {
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
                        'attendance',
                        'grade',
                        'count',
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

                        $attendance     = strtoupper(trim($data["attendance"]));
                        $grade          = strtoupper(trim($data["grade"]));
                        $count          = trim($data["count"]);

                        if (!empty($composite_key) && !empty($attendance)) {
                            $session_id = 0;
                            $squad_id   = 0;
                            $probationer_id = 0;

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

                            $attnData[]  = [
                                'session_id'    => $session_id,
                                'probationer_id'    => $probationer_id,
                                'attendance'    => $attendance,
                                'grade'     => $grade,
                                'count'     => $count,
                            ];
                        }

                        $i++;
                    }

                    if(empty($dataRowError)) {
                        $setAttnError = [];
                        $tt_count   = 0;

                        foreach($attnData as $data) {

                            try {
                                ExtraClassmeta::updateOrCreate(
                                    [
                                        'extra_class_id'   => $data['session_id'],
                                        'probationer_id'    => $data['probationer_id'],
                                    ],
                                    [
                                        'attendance'    => $data['attendance'],
                                        'grade'    => $data['grade'],
                                        'count'    => $data['count'],
                                    ]
                                );
                            } catch (Exception $e) {
                                $setAttnError[] = "Error: ". $e->getMessage();
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

    public function delete(Request $request,$sessionId)
    {
        ExtraClass::where('id',$request->sessionId)->delete();
        DB::table('extra_classmetas')->where('extra_class_id',$request->sessionId)->delete();
           return redirect('/timetables/extra-classes')->with('delete', 'ExtraClass deleted successfully');

    }
    public function missedclass_delete(Request $request,$sessionId)
    {
        ExtraSession::where('id',$request->sessionId)->delete();
        DB::table('extra_sessionmetas')->where('extra_session_id',$request->sessionId)->delete();
        return redirect('/timetables/missed-classes')->with('delete', 'MissedClass deleted successfully');
    }
}
