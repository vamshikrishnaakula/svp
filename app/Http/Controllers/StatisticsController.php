<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\ExtraSessionmeta;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\Squad;
use Illuminate\Support\Facades\DB;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

use Illuminate\Support\Facades\Auth;
use Exception;

class StatisticsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $role   = Auth::user()->role;
        // if(!in_array($role, ['admin', 'faculty'])) {
        //     return false;
        // }

        $batches = Batch::all();
        // $dt = '';
        // $data = '';
        return view('statistics.statisticsReport',compact('batches', 'role'));
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

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

    public function report_single_activity_view(Request $request)
    {
        //$role   = Auth::user()->role;
        $bt = $request->batch_id;
        $request->sub_activity_id ? $sub_activity_id   = $request->sub_activity_id : $sub_activity_id = '';
        $batches = Batch::all();

         $daterange = $request->date;

        if (empty($daterange)) {
            $errors[] = "Select Date Range.";
        } else {
            $daterangeArray = explode(" - ", $daterange);
            if(count($daterangeArray) !== 2) {
                $errors[] = "Invalid Date Range (Expected: DD/MM/YYYY - DD/MM/YYYY).";
            } else if(!isValidDate($daterangeArray[0], 'd/m/Y') || !isValidDate($daterangeArray[1], 'd/m/Y')) {
                $errors[] = "Invalid Date Format (Expected: DD/MM/YYYY - DD/MM/YYYY).";
            } else {
                $from = convert_date($daterangeArray[0], 'd/m/Y');
                $to = convert_date($daterangeArray[1], 'd/m/Y');
            }
        }

        if($request->component != '')
        {
            $query = "and component_id = ".$request->component;
        }
        else
        {
            $query = '';
        }


        if($sub_activity_id == '')
        {
            $activities =  DB::select("select CASE when component_id IS NOT NULL then component_id when subactivity_id IS NOT NULL THEN subactivity_id else activity_id end AS ACT_ID, date, timetable_id, activity_id, subactivity_id, component_id from probationers_dailyactivity_data where Batch_id= $request->batch_id and squad_id= $request->squad_id and activity_id = $request->activity_id and date between '$from' and '$to' group by ACT_ID order by date ASC");
        }
        else
        {
            $activities =  DB::select("select CASE when component_id IS NOT NULL then component_id when subactivity_id IS NOT NULL THEN subactivity_id else activity_id end AS ACT_ID, date, timetable_id, activity_id, subactivity_id, component_id from probationers_dailyactivity_data where Batch_id= $request->batch_id and squad_id= $request->squad_id and activity_id = $request->activity_id and subactivity_id = $sub_activity_id $query and date between '$from' and '$to' group by ACT_ID order by date ASC");
        }

      //  return json_encode($activities);



        $probationer_lists = probationer_list($request->squad_id);

            if(count($activities) == '0')
            {
                return true;
            }

            foreach($activities as $squad)
            {

                $usersexport = DB::select("select * from `probationers_dailyactivity_data` where `squad_id` = $request->squad_id and (`probationers_dailyactivity_data`.`activity_id` = $squad->ACT_ID or `probationers_dailyactivity_data`.`subactivity_id` = $squad->ACT_ID or `probationers_dailyactivity_data`.`component_id` = $squad->ACT_ID) and date between '$from' and '$to' group by `timetable_id` order by `date` asc");
                foreach($usersexport as $value)
                {
                    $dt[] = date('d-m', strtotime($value->date));

                }
                $activities_dates[] = [
                    'act_name' => activity_name($squad->ACT_ID),
                    'unit' => activity_unit($squad->ACT_ID),
                    'count' => count($dt),
                    'dates' => $dt,

                ];
                unset($dt);
            }


        $dt = array();
        $activity_unit ='';
        foreach ($probationer_lists as $probationer_list)
        {
            foreach($activities as $squad)
            {
                $usersexports = DB::select("select * from `probationers_dailyactivity_data` where `squad_id` = $request->squad_id and (`probationers_dailyactivity_data`.`activity_id` = $squad->ACT_ID or `probationers_dailyactivity_data`.`subactivity_id` = $squad->ACT_ID or `probationers_dailyactivity_data`.`component_id` = $squad->ACT_ID) and date between '$from' and '$to' and probationer_id = $probationer_list->id group by `timetable_id` order by `date`, `id` asc");
                if(count($usersexports) !== 0)
                {
                    foreach($usersexports as $values)
                    {

                        if($values->attendance == 'P' || $values->attendance == 'MDO' || $values->attendance == 'NCM')
                        {
                          //  $pVal[] = (check_activity_unit_type($squad->ACT_ID) === 'count') ? $values->count : $values->grade;
                            $pVal[] =  (check_activity_unit_type($squad->ACT_ID) === 'count') ? ( $values->count ) : ( check_activity_unit_type($squad->ACT_ID) === 'grade' ? ($values->grade ) : ( qualified_values($values->qualified)));
                        }
                        else
                        {
                            $missed_classes = ExtraSessionmeta::where('timetable_id', $values->timetable_id)->where('probationer_id', $probationer_list->id)->first();
                            if(!empty($missed_classes))
                            {
                                $pVal[] = (check_activity_unit_type($squad->ACT_ID) === 'count') ? $missed_classes->count : $missed_classes->grade;
                            }
                            else
                            {
                                $pVal[] = $values->attendance;
                            }

                        }
                    }
                }
                else
                {
                    $pVal[] ='';
                }

            }
            $pCount[] = [
                'name' => probationer_name($probationer_list->id),
                'data' => $pVal,

            ];
            unset($pVal);
         }

         $tt = [
             'dt' => $activities_dates,
             'val' => $pCount
         ];

        // return json_encode($tt);

                     echo <<<EOL
                        <thead>
                        <tr>
                        <th></th>
                    EOL;
                        if(!empty($activities_dates))
                        {
                            foreach($activities_dates as $activities_date)
                            {
                               $colspan =  $activities_date['count'];
                               $activity_name = $activities_date['act_name'];
                               $activity_unit = ($activities_date['unit'] != '') ? $activities_date['unit'] : "Grade";

                              echo <<<EOL
                                    <th colspan="$colspan">$activity_name - ($activity_unit)</th>
                                 EOL;
                            }
                            echo    <<<EOL
                        </tr>
                        <tr>
                    EOL;
                            foreach($activities_dates as $key=>$activities_date)
                            {
                                if($key == '0')
                                {
                                    echo <<<EOL
                                    <th>Probationer Name</th>
                                 EOL;
                                }

                                foreach($activities_date['dates'] as $date)
                                {
                                    echo <<<EOL
                                        <th>$date</th>
                                    EOL;
                                }
                           }
                        }
                echo    <<<EOL
                        </tr>
                        <thead>
                        <tbody>
                    EOL;

                    if (!empty($pCount))
                    {
                        foreach ($pCount as $pCounts)
                        {
                            $p_name = $pCounts['name'];
                            echo  <<<EOL
                               <tr>
                            <td>$p_name</td>
                            EOL;
                            foreach ($pCounts['data'] as $counts)
                            {
                                $count = ($counts != '') ? $counts : '-';

                                echo  <<<EOL
                                    <td>$count</td>
                                EOL;
                            }
                    echo  <<<EOL
                        </tr>
                    EOL;
                        }
                     }
                    echo  <<<EOL
                         </tbody>
                    EOL;
        return;
     //return view('statistics.statisticsReport',compact('batches', 'dt', 'data', 'activity_unit', 'role'));
    }



    public function export($data_request)
    {

        $data   = unserialize( data_crypt($data_request, 'd') );
        $batch_id   = isset($data["batch_id"]) ? $data["batch_id"] : 0;
        $squad_id   = isset($data["squad_id"]) ? $data["squad_id"] : 0;
        $activity_id   = isset($data["activity_id"]) ? $data["activity_id"] : 0;
        $sub_activity_id   = isset($data["sub_activity_id"]) ? $data["sub_activity_id"] : 0;
        $component   = isset($data["component"]) ? $data["component"] : 0;
        $from   = isset($data["from"]) ? $data["from"] : "";
        $to   = isset($data["to"]) ? $data["to"] : "";


        if($component != '')
        {
            $query = "and component_id = ".$component;
        }
        else
        {
            $query = '';
        }

        if($sub_activity_id == '')
        {
            $activities =  DB::select("select CASE when component_id IS NOT NULL then component_id when subactivity_id IS NOT NULL THEN subactivity_id else activity_id end AS ACT_ID, date, timetable_id, activity_id, subactivity_id, component_id from probationers_dailyactivity_data where Batch_id= $batch_id and squad_id= $squad_id and activity_id = $activity_id $query and date between '$from' and '$to' group by ACT_ID order by date ASC");
        }
        else
        {
            $activities =  DB::select("select CASE when component_id IS NOT NULL then component_id when subactivity_id IS NOT NULL THEN subactivity_id else activity_id end AS ACT_ID, date, timetable_id, activity_id, subactivity_id, component_id from probationers_dailyactivity_data where Batch_id= $batch_id and squad_id= $squad_id and activity_id = $activity_id and subactivity_id = $sub_activity_id $query and date between '$from' and '$to' group by ACT_ID order by date ASC");
        }


        foreach($activities as $squad)
            {
                $usersexport = DB::select("select * from `probationers_dailyactivity_data` where `squad_id` = $squad_id and (`probationers_dailyactivity_data`.`activity_id` = $squad->ACT_ID or `probationers_dailyactivity_data`.`subactivity_id` = $squad->ACT_ID or `probationers_dailyactivity_data`.`component_id` = $squad->ACT_ID) and date between '$from' and '$to' group by `timetable_id` order by `date` asc");
                foreach($usersexport as $value)
                {
                    $dt[] = date('d-m', strtotime($value->date));

                }
                $activities_dates[] = [
                    'act_name' => activity_name($squad->ACT_ID),
                    'unit' => activity_unit($squad->ACT_ID),
                    'count' => count($dt),
                    'dates' => $dt,

                ];
                unset($dt);
            }


        $dt = array();
        $activity_unit ='';
        $probationer_lists = probationer_list($squad_id);

        foreach ($probationer_lists as $probationer_list)
        {
            foreach($activities as $squad)
            {
                $usersexports = DB::select("select * from `probationers_dailyactivity_data` where `squad_id` = $squad_id and (`probationers_dailyactivity_data`.`activity_id` = $squad->ACT_ID or `probationers_dailyactivity_data`.`subactivity_id` = $squad->ACT_ID or `probationers_dailyactivity_data`.`component_id` = $squad->ACT_ID) and date between '$from' and '$to' and probationer_id = $probationer_list->id group by `timetable_id` order by `date` asc");

                if(count($usersexports) !== 0)
                {
                    foreach($usersexports as $values)
                    {
                        if($values->attendance == 'P' || $values->attendance == 'MDO' || $values->attendance == 'NCM')
                        {
                          //  $pVal[] = (check_activity_unit_type($squad->ACT_ID) === 'count') ? $values->count : $values->grade;
                            $pVal[] =  (check_activity_unit_type($squad->ACT_ID) === 'count') ? ( $values->count ) : ( check_activity_unit_type($squad->ACT_ID) === 'grade' ? ($values->grade ) : ( qualified_values($values->qualified)));
                        }
                        else
                        {
                            $missed_classes = ExtraSessionmeta::where('timetable_id', $values->timetable_id)->where('probationer_id', $probationer_list->id)->first();
                            if(!empty($missed_classes))
                            {
                                $pVal[] = (check_activity_unit_type($squad->ACT_ID) === 'count') ? $missed_classes->count : $missed_classes->grade;
                            }
                            else
                            {
                                $pVal[] = $values->attendance;
                            }

                        }
                    }
                }
                else
                {
                    $pVal[] ='';
                }
            }

            $pCount[] = [
                'act_name' => activity_name($squad->ACT_ID),
                'name' => probationer_name($probationer_list->id),
                'data' => $pVal,

            ];
            unset($pVal);
         }


             $spreadsheet = new Spreadsheet();
           //  $spreadsheet->setActiveSheetIndex('1');
             $i = 3;
             $j = 3;
             $activitycell = 'B';
             $datecell = 'A';
            // $mergecell = 'B';

                        if(!empty($activities_dates))
                        {
                            $styleArray = array(
                                'font'  => array(
                                    'bold' => true,
                                    'color' => array('rgb' => '000000'),
                                    'size'  => 12,
                                    'name' => 'Verdana'
                                ));

                            foreach($activities_dates as $key=>$activities_date)
                            {
                                if($key == '0')
                                {
                                    $spreadsheet->getActiveSheet()->setCellValue("A2", 'Probationer Name');
                                }
                                $activity_name = $activities_date['act_name'];
                                $activity_unit = ($activities_date['unit'] != '') ? $activities_date['unit'] : "No Units";
                                $spreadsheet->getActiveSheet()->setCellValue($activitycell.'1', $activity_name .'-' .$activity_unit);
                                $spreadsheet->getActiveSheet()->getStyle($activitycell.'1')->applyFromArray($styleArray);

                                $mergecell = $activitycell;
                                foreach($activities_date['dates'] as $date)
                                {
                                    $datecell++;
                                    $spreadsheet->getActiveSheet()->setCellValue($datecell.'2', $date);
                                    $activitycell++;
                                }
                                $spreadsheet->getActiveSheet()->mergeCells("{$mergecell}1:{$datecell}1");
                           }
                        }

                    if (!empty($pCount))
                    {
                        foreach ($pCount as $pCounts)
                        {
                            $p_name = $pCounts['name'];
                           // <td>$p_name</td>
                           $spreadsheet->getActiveSheet()->setCellValue('A'.$i++, $p_name);
                           $valuecell = 'B';

                            foreach ($pCounts['data'] as $counts)
                            {
                                $count = $counts;
                               // <td>$count</td>
                               $spreadsheet->getActiveSheet()->setCellValue($valuecell++.$j, $count);
                            }
                            $j++;
                        }
                    }

                 //   $file_name = 'test.xlsx';
                    $file_name = batch_name($batch_id) . "_". squad_number($squad_id) . "_" . date("Y-m-d-Hi") . ".xlsx";
                    $fileName   = str_replace(' ', '-', $file_name);
                    spreadsheet_header($fileName);
                    ob_end_clean();
                    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

                    $writer->save('php://output');
                }



    public function sub_activity_count(Request $request)
    {
        $activity_id = $request->activity_id;
        $count = Activity::where('parent_id', $activity_id)->count();
        return $count;
    }

    public function component_count(Request $request)
    {
        $sub_activity_id = $request->sub_activity_id;
        $count = Activity::where('parent_id', $sub_activity_id)->count();
        return $count;
    }

    /**
     * Display compare probationers screen
     * Route: compare-probationers
     */
    public function compare_probationers(Request $request)
    {
        $batches = Batch::all();
        return view('statistics.compare',compact('batches'));
    }

    /**
     * Download timetable datasheet
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function download_statistics_datasheet($data_request)
    {
        $data   = unserialize( data_crypt($data_request, 'd') );

        // print_r($data);

        $batch_id   = isset($data["batch_id"]) ? $data["batch_id"] : 0;
        $squad_id   = isset($data["squad_id"]) ? $data["squad_id"] : 0;
        $date_from   = isset($data["date_from"]) ? $data["date_from"] : "";
        $date_to   = isset($data["date_to"]) ? $data["date_to"] : "";

        if (empty($batch_id)) {
            $errors[]   = "Select Batch.";
        }
        if (empty($squad_id)) {
            $errors[]   = "Select Squad.";
        }
        if (empty($date_from) || !isValidDate($date_from, 'Y-m-d')) {
            $errors[]   = "Select a valid date (from).";
        }
        if (empty($date_to) || !isValidDate($date_to, 'Y-m-d')) {
            $errors[]   = "Select a valid date (tp).";
        } else {
            if(strtotime($date_from) > strtotime($date_to) ) {
                $errors[]   = "Invalid date range selected";
            }
        }

        if (empty($errors)) {
            $Timetables   = \App\Models\Timetable::where('timetables.squad_id', $squad_id)
                ->where('timetables.activity_id', '>', 0)
                ->whereDate('timetables.date', '>=', $date_from)
                ->whereDate('timetables.date', '<=', $date_to)
                ->where('timetables.session_type', 'regular')
                ->where('timetables.session_start', '>', 0)
                ->leftJoin('probationers', 'timetables.squad_id', '=', 'probationers.squad_id')
                ->leftJoin('probationers_dailyactivity_data', function($join)
                {
                    $join->on('timetables.id', '=', 'probationers_dailyactivity_data.timetable_id');
                    $join->on('probationers.id', '=', 'probationers_dailyactivity_data.probationer_id');
                })
                ->select('timetables.*', 'probationers.id as probationer_id', 'probationers_dailyactivity_data.component_id', 'probationers_dailyactivity_data.grade', 'probationers_dailyactivity_data.count', 'probationers_dailyactivity_data.attendance')
                ->orderBy('timetables.date', 'asc')
                ->orderBy('timetables.session_number', 'asc')
                ->orderBy('probationers.position_number', 'asc')
                ->get();

            if(count($Timetables) == 0) {
                return json_encode([
                    'status'    => 'error',
                    'message'   => 'No data.',
                ]);
            }

            // ---------------------------------------------------------
            // Initialize Spreadsheet with 1st sheet as Timetables
            // ---------------------------------------------------------
            $spreadsheet = new Spreadsheet();
            $sheet1 = $spreadsheet->getActiveSheet();
            $sheet1->setTitle('AttendanceData');

            // Header row
            $sheet1->setCellValue('A1', 'key');
            $sheet1->setCellValue('B1', 'batch');
            $sheet1->setCellValue('C1', 'squad');
            $sheet1->setCellValue('D1', 'probationer');
            $sheet1->setCellValue('E1', 'date');
            $sheet1->setCellValue('F1', 'session_number');
            $sheet1->setCellValue('G1', 'activity');
            $sheet1->setCellValue('H1', 'sub_activity');
            $sheet1->setCellValue('I1', 'component');
            $sheet1->setCellValue('J1', 'attendance');
            $sheet1->setCellValue('K1', 'grade');
            $sheet1->setCellValue('L1', 'count');

            $batch      = batch_name($batch_id);
            $squad      = squad_number($squad_id);

            $subActivityIds = [];
            $components = [];
            $components_range = [];
            $rangeStart = 0;
            $rangeEnd = 0;

            $ttRow    = 2;

            // Get Components for data validation
            foreach ($Timetables as $data) {
                $subactivity_id = $data->subactivity_id;

                if(!empty($subactivity_id)) {

                    if(!in_array($subactivity_id, $subActivityIds)) {
                        // Get components
                        $getComponents  = \App\Models\Activity::where('parent_id', $subactivity_id)->where('type', 'component')->get();
                        if(count($getComponents) > 0) {
                            foreach($getComponents as $getComponent) {
                                $components[]   = "{$getComponent->name} [{$subactivity_id}]";
                                $rangeEnd++;
                            }
                            $rangeStart++;

                            $components_range[$subactivity_id]  = "{$rangeStart}|{$rangeEnd}";
                        } else {
                            $components_range[$subactivity_id]  = "";
                        }

                        $subActivityIds[]   = $subactivity_id;
                    }
                } else {
                    $components_range[$subactivity_id]  = "";
                }

            }

            // Create 2nd sheet for the Components list for data validation
            $cRow   = 2;
            if(!empty($components)) {
                $spreadsheet->createSheet();
                $sheet2 = $spreadsheet->getSheet(1);
                $sheet2->setTitle('DataLists');

                $sheet2->setCellValue("A1", "Components");

                foreach($components as $component) {
                    $sheet2->setCellValue("A{$cRow}", $component);
                    $cRow++;
                }
                // Hide DataLists Sheet
                $sheet2->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_HIDDEN);
            }

            $validAttendances    = valid_attendances();
            $validAttendances    = implode(', ', $validAttendances);

            $grades = "A, B, C, D, E";

            $sheet1 = $spreadsheet->getSheet(0);
            // Add Data to the Timetables sheet
            foreach ($Timetables as $data) {
                $timetable_id   = $data->id;

                $activity_id    = $data->activity_id;
                $activity       = activity_name($activity_id);

                $sub_activity   = "0";
                $subactivity_id = $data->subactivity_id;
                if(!empty($subactivity_id)) {
                    $sub_activity   = activity_name($subactivity_id);
                    $sub_activity   = "{$sub_activity} [{$subactivity_id}]";
                }

                // component
                $component  = "";
                $component_id = $data->component_id;
                if(!empty($component_id)) {
                    $component   = activity_name($component_id);
                    $component   = "{$component} [{$subactivity_id}]";
                }

                $probationer_id = $data->probationer_id;
                $probationer    = probationer_name($probationer_id);

                $date = $data->date;

                $attendance = empty($data->attendance) ? '' : $data->attendance;
                $grade      = empty($data->grade) ? '' : $data->grade;
                $count      = empty($data->count) ? '' : $data->count;

                $composite_key  = "{$squad_id}-{$timetable_id}-{$probationer_id}";

                // SET data cell values
                $sheet1->setCellValue("A{$ttRow}", $composite_key);
                $sheet1->setCellValue("B{$ttRow}", $batch);
                $sheet1->setCellValue("C{$ttRow}", $squad);
                $sheet1->setCellValue("D{$ttRow}", $probationer);
                $sheet1->setCellValue("E{$ttRow}", $date);
                $sheet1->setCellValue("F{$ttRow}", $data->session_number);
                $sheet1->setCellValue("G{$ttRow}", $activity);
                $sheet1->setCellValue("H{$ttRow}", $sub_activity);
                $sheet1->setCellValue("I{$ttRow}", $component);
                $sheet1->setCellValue("J{$ttRow}", $attendance);
                $sheet1->setCellValue("K{$ttRow}", $grade);
                $sheet1->setCellValue("L{$ttRow}", $count);

                // SET cell Data validation for dropdowns
                // Components
                $dataListRange  = $components_range[$subactivity_id];
                if(!empty($dataListRange)) {
                    $dataListRange  = explode('|', $dataListRange);
                    if(!empty($dataListRange) && (count($dataListRange) === 2)) {
                        $formula1   = '=DataLists!$A$'. ($dataListRange[0] + 1) .':$A$'. ($dataListRange[1] + 1);

                        $validation1 = $sheet1->getCell("I{$ttRow}")->getDataValidation(); // GET the cell for Data validation
                        $validation1->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST) // set the validation type to 'List'
                            ->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION) // set the validation type to 'List'
                            ->setShowDropDown(true)
                            ->setAllowBlank(false) // Do not allow empty value for activity
                            ->setShowInputMessage(true)
                            ->setPromptTitle('Pick from list')
                            ->setPrompt('Please pick a value from the drop-down list.')
                            ->setFormula1($formula1); // Set drop down options
                    }
                }

                // Cell validation dropdown list for Attendance
                $validatio2 = $sheet1->getCell("J{$ttRow}")->getDataValidation(); // GET the cell for Data validation
                $validatio2->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST) // set the validation type to 'List'
                    ->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION) // set the validation type to 'List'
                    ->setShowDropDown(true)
                    ->setAllowBlank(false) // Do not allow empty value for activity
                    ->setShowInputMessage(true)
                    ->setPromptTitle('Pick from list')
                    ->setPrompt('Please pick a value from the drop-down list.')
                    ->setFormula1('"'. $validAttendances .'"'); // Set attendance drop down options

                // Cell validation dropdown list for Grades
                $validatio2 = $sheet1->getCell("K{$ttRow}")->getDataValidation(); // GET the cell for Data validation
                $validatio2->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST) // set the validation type to 'List'
                    ->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION) // set the validation type to 'List'
                    ->setShowDropDown(true)
                    ->setAllowBlank(false) // Do not allow empty value for activity
                    ->setShowInputMessage(true)
                    ->setPromptTitle('Pick from list')
                    ->setPrompt('Please pick a value from the drop-down list.')
                    ->setFormula1('"'. $grades .'"'); // Set grades drop down options

                $ttRow++;
            }

            $row    = $ttRow - 1;
            foreach (range("A{$row}", "L{$row}") as $columnID) {
                $sheet1->getColumnDimension($columnID)->setAutoSize(true);
            }

            $timeNow    = date('Y-m-d-His');
            $fileName   = "Probationers-Attendance-Data-{$batch}_{$squad}_{$timeNow}.xlsx";
            $fileName   = str_replace(' ', '-', $fileName);

            // Spreadsheet Document Header
            spreadsheet_header($fileName);

            // Save Spreadsheet
            // $writer = new Xlsx($spreadsheet);

            ob_end_clean();
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

            $writer->save('php://output');

        } else {
            return json_encode([
                'status'    => 'error',
                'message'   => implode('<br />', $errors),
            ]);
        }

        return;

    }

    /**
     * Process ajax requests.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function ajax(Request $request)
    {
        $requestName    = $request->requestName;

        // Get Import Data Modal
        if ($requestName === "get_import_data_form") {
            return view('statistics.import-data-form', ['request' => $request]);
        }

        // Validate Download Statistics Data
        if ($requestName === "download_statistics_datasheet") {
            $result = [];
            $errors  = [];

            // print_r($request->all());
            $batch_id   = $request->data_batch_id;
            $squad_id   = $request->data_squad_id;
            $date_from  = $request->data_from_date;
            $date_to    = $request->data_to_date;

            if (empty($batch_id)) {
                $errors[]   = "Select Batch.";
            }
            if (empty($squad_id)) {
                $errors[]   = "Select Squad.";
            }
            if (empty($date_from) || !isValidDate($date_from, 'Y-m-d')) {
                $errors[]   = "Select a valid date (from).";
            }
            if (empty($date_to) || !isValidDate($date_to, 'Y-m-d')) {
                $errors[]   = "Select a valid date (to).";
            } else {
                if(strtotime($date_from) > strtotime($date_to) ) {
                    $errors[]   = "Invalid date range selected";
                }
            }

            if (empty($errors)) {

                $Timetables   = \App\Models\Timetable::where('timetables.squad_id', $squad_id)
                    ->where('timetables.activity_id', '>', 0)
                    ->whereDate('timetables.date', '>=', $date_from)
                    ->whereDate('timetables.date', '<=', $date_to)
                    ->where('timetables.session_type', 'regular')
                    ->where('timetables.session_start', '>', 0)
                    ->leftJoin('probationers', 'timetables.squad_id', '=', 'probationers.squad_id')
                    ->leftJoin('probationers_dailyactivity_data', function($join)
                    {
                        $join->on('timetables.id', '=', 'probationers_dailyactivity_data.timetable_id');
                        $join->on('probationers.id', '=', 'probationers_dailyactivity_data.probationer_id');
                    })
                    ->select('timetables.*', 'probationers.id as probationer_id', 'probationers_dailyactivity_data.grade', 'probationers_dailyactivity_data.count', 'probationers_dailyactivity_data.attendance')
                    ->orderBy('timetables.date', 'asc')
                    ->orderBy('timetables.session_number', 'asc')
                    ->get();

                if(count($Timetables) == 0) {
                    return json_encode([
                        'status'    => 'error',
                        'message'   => 'Timetable data not exist for the selected criteria.',
                    ]);
                }

                $data_request   = [
                    'batch_id'  => $batch_id,
                    'squad_id' => $squad_id,
                    'date_from' => $date_from,
                    'date_to'   => $date_to,
                ];
                $data_request   = data_crypt( serialize($data_request) );

                $datasheet_url = url("/statistics/download-attendance-data/{$data_request}");
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

        // Import Data Sheet
        if ($requestName === "import_DataSheet") {

            // Import attendance data
            if ($request->hasFile('data_csv') && $request->file('data_csv')->isValid()) {
                $errorMsg       = [];
                $dataRowError   = [];

                $result     = [];

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
                        'component',
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
                        // return json_encode($data["component"]);
                        $component  = trim($data["component"]);

                        $attendance     = strtoupper(trim($data["attendance"]));
                        $grade          = strtoupper(trim($data["grade"]));
                        $count          = trim($data["count"]);

                        if (!empty($composite_key) && !empty($attendance)) {
                            $batch_id       = 0;
                            $squad_id       = 0;
                            $timetable_id   = 0;
                            $probationer_id = 0;
                            $activity_id    = 0;
                            $subactivity_id = 0;
                            $component_id   = 0;
                            $session_date   = '';

                            $keyData    = explode('-', $composite_key);
                            if(is_array($keyData) && count($keyData) === 3) {
                                $squad_id       = $keyData[0];
                                $timetable_id   = $keyData[1];
                                $probationer_id = $keyData[2];
                            }

                            if( empty($squad_id) || empty($timetable_id) || empty($probationer_id) ) {
                                $dataRowError[] = "Row #{$row_num}: Invalid key submitted.";
                            }

                            // $attendance = ($attendance === "NAP") ? "A" : $attendance;
                            if( !in_array($attendance, valid_attendances()) ) {
                                $dataRowError[] = "Row #{$row_num}: Invalid attendance '{$attendance}'.";
                            }
                            if( !empty($grade) && !in_array($grade, valid_grades()) ) {
                                $dataRowError[] = "Row #{$row_num}: Invalid grade '{$grade}'.";
                            }
                            if( $attendance !== 'P' && !empty($grade) ) {
                                $dataRowError[] = "Row #{$row_num}: grade should be empty if attendance is not (P)resent.";
                            }
                            if( $attendance !== 'P' && !empty($count) ) {
                                $dataRowError[] = "Row #{$row_num}: count should be empty if attendance is not (P)resent.";
                            }

                            $timetable  = \App\Models\Timetable::find($timetable_id);

                            if($timetable) {
                                $batch_id       = $timetable->batch_id;
                                $squad_id       = $timetable->squad_id;
                                $activity_id    = $timetable->activity_id;
                                $subactivity_id = $timetable->subactivity_id;
                                $session_date   = $timetable->date;

                                // Check if currect data submitted
                                $pb_squad_id    = \App\Models\probationer::where('id', $probationer_id)->value('squad_id');
                                if( $squad_id !== $pb_squad_id ) {
                                    $dataRowError[] = "Row #{$row_num}: Invalid timetable_id and/or probationer_id.";
                                }

                                // Get component
                                if( !empty($subactivity_id) && !empty($component) ) {
                                    $component  = explode(' [', $component);
                                    if( isset($component[0]) ) {
                                        $component_id   = Activity::where('parent_id', $subactivity_id)->where('name', $component[0])->where('type', 'component')->value('id');
                                        $component_id   = empty($component_id) ? 0 : $component_id;
                                    }
                                }
                            } else {
                                $dataRowError[] = "Row #{$row_num}: Invalid data submitted, timetable data not matched.";
                            }

                            if(!empty($data["component"])){
                                $vComponent  = explode(' [', $data["component"]);
                                $vComponent_id   = Activity::where('parent_id', $subactivity_id)->where('name', $component[0])->where('type', 'component')->value('id');
                                $type = check_activity_unit_type($vComponent_id);
                                if($type == 'count')
                                {
                                    if( !empty($grade) && in_array($grade, valid_grades()) ) {
                                        $dataRowError[] = "Row #{$row_num}: For this component we are allowing only count.";
                                    }
                                }
                                elseif($type == 'grade')
                                {
                                    if( !empty($count) ) {
                                        $dataRowError[] = "Row #{$row_num}: For this component we are allowing only Grade.";
                                    }
                                }
                            }
                            elseif(!empty($subactivity_id)){
                               $type = check_activity_unit_type($subactivity_id);
                                if($type == 'count')
                                {
                                    if( !empty($grade) && in_array($grade, valid_grades()) ) {
                                        $dataRowError[] = "Row #{$row_num}: For this sub-activity we are allowing only count.";
                                    }
                                }
                                elseif($type == 'grade')
                                {
                                    if( !empty($count) ) {
                                        $dataRowError[] = "Row #{$row_num}: For this sub-activity we are allowing only Grade.";
                                    }
                                }
                            }
                            else
                            {
                                $type = check_activity_unit_type($activity_id);
                                if($type == 'count')
                                {
                                    if( !empty($grade) && in_array($grade, valid_grades()) ) {
                                        $dataRowError[] = "Row #{$row_num}: For this activity we are allowing only count.";
                                    }
                                }
                                elseif($type == 'grade')
                                {
                                    if( !empty($count) ) {
                                        $dataRowError[] = "Row #{$row_num}: For this activity we are allowing only Grade.";
                                    }
                                }
                            }

                            $attnData[]  = [
                                'batch_id'      => $batch_id,
                                'squad_id'      => $squad_id,
                                'activity_id'   => sanitize_activity_id($activity_id),
                                'subactivity_id'    => sanitize_activity_id($subactivity_id),
                                'component_id'    => sanitize_activity_id($component_id),
                                'probationer_id'    => $probationer_id,
                                'timetable_id'      => $timetable_id,
                                'attendance'    => $attendance,
                                'grade'     => $grade,
                                'count'     => $count,
                                'date'      => $session_date,
                            ];
                        }

                        $i++;
                    }

                    if(empty($dataRowError)) {
                        $createTxnError = [];
                        $tt_count   = 0;

                        $user_id    = Auth::id();
                        $timestamp  = date('Y-m-d H:i:s');

                        foreach($attnData as $data) {

                            try{
                                $pb_attndata_id   = DB::table('probationers_dailyactivity_data')
                                    ->where('probationer_id', $data['probationer_id'])
                                    ->where('timetable_id', $data['timetable_id'])
                                    ->value('id');

                                if(!empty($pb_attndata_id)) {
                                    DB::table('probationers_dailyactivity_data')
                                        ->where('id', $pb_attndata_id)
                                        ->update([
                                            'component_id'  => $data['component_id'],
                                            'grade'         => $data['grade'],
                                            'count'         => $data['count'],
                                            'attendance'    => $data['attendance'],
                                        ]);
                                } else {
                                    DB::table('probationers_dailyactivity_data')->insert([
                                        'Batch_id'    => $data['batch_id'],
                                        'squad_id'    => $data['squad_id'],
                                        'staff_id'    => $user_id,
                                        'activity_id'       => $data['activity_id'],
                                        'subactivity_id'    => $data['subactivity_id'],
                                        'component_id'      => $data['component_id'],
                                        'probationer_id'    => $data['probationer_id'],
                                        'timetable_id'  => $data['timetable_id'],
                                        'grade'         => $data['grade'],
                                        'count'         => $data['count'],
                                        'attendance'    => $data['attendance'],
                                        'date'          => $data['date'],
                                        'created_at'    => $timestamp,
                                        'updated_at'    => $timestamp,
                                    ]);
                                }

                            } catch (Exception $e) {
                                $createTxnError[] = "Error: ". $e->getMessage();
                            }

                            $tt_count++;
                        }

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

        // Get Compare screen filter buttons
        if ($requestName === "get_compare_filter_buttons") {
            $batch_id    = $request->batch_id;

            if(empty($batch_id)) {
                return json_encode([
                    'status'    => 'error',
                    'message'   => 'Please select a Batch',
                ]);
                ;
            }

            // Get Activities
            $activities = \App\Models\Activity::where('batch_id', $batch_id)->where('type', 'activity')->get();
            $data   = '<div class="mt-3">';
            foreach($activities as $activity) {
                $data   .= <<<EOL
                    <button type="button" data-activity-id="{$activity->id}" data-toggle="collapse" class="filter-btn filter-btn-active col-sm-2 mb-2">{$activity->name}
                        <span class="close_icon" aria-hidden="true"><i class="fas fa-times"></i></span>
                    </button>
                EOL;
            }
            $data   .= '</div>';

            return json_encode([
                'status'    => 'success',
                'data'    => $data,
            ]);
        }

        // Get Probationer Compare data
        if ($requestName === "get_probationer_compare_data") {
            $probationer_ids    = $request->probationer_ids;
            $activity_ids    = $request->activity_ids;

            if(!is_array($probationer_ids) || count(array_filter($probationer_ids)) < 2) {
                return json_encode([
                    'status'    => 'error',
                    'message'   => 'Please select at least 2 probationers to compare.',
                ]);
            }

            if(is_array($activity_ids) && count(array_filter($activity_ids)) > 0) {
                return json_encode([
                    'status'    => 'success',
                    'data'      => view('statistics.compare-data', compact('request'))->render()
                ]);
            }
        }

        if ($requestName === "request_data_download") {


            $daterange = $request->date;


            if (empty($daterange)) {
                $errors[] = "Select Date Range.";
            } else {
                $daterangeArray = explode(" - ", $daterange);
                if(count($daterangeArray) !== 2) {
                    $errors[] = "Invalid Date Range (Expected: DD/MM/YYYY - DD/MM/YYYY).";
                } else if(!isValidDate($daterangeArray[0], 'd/m/Y') || !isValidDate($daterangeArray[1], 'd/m/Y')) {
                    $errors[] = "Invalid Date Format (Expected: DD/MM/YYYY - DD/MM/YYYY).";
                } else {
                    $from = convert_date($daterangeArray[0], 'd/m/Y');
                    $to = convert_date($daterangeArray[1], 'd/m/Y');
                }
            }

            $batch_id   = $request->id;
            $squad_id   = $request->sid;
            $activity_id = $request->activity_id;
            $component = $request->component;
            $sub_activity_id = $request->sub_activity_id;
            $from  = $from;
            $to    = $to;
            if($request->component != '')
            {
                $query = "and component_id = ".$request->component;
            }
            else
            {
                $query = '';
            }

            if($request->sub_activity_id == '')
                {
                    $users =  DB::select("select CASE when component_id IS NOT NULL then component_id when subactivity_id IS NOT NULL THEN subactivity_id else activity_id end AS ACT_ID, date, timetable_id, activity_id, subactivity_id, component_id from probationers_dailyactivity_data where Batch_id= $request->id and squad_id= $request->sid and activity_id = $request->activity_id $query and date between '$from' and '$to' group by ACT_ID order by date ASC");
                }
            else
            {
                $users =  DB::select("select CASE when component_id IS NOT NULL then component_id when subactivity_id IS NOT NULL THEN subactivity_id else activity_id end AS ACT_ID, date, timetable_id, activity_id, subactivity_id, component_id from probationers_dailyactivity_data where Batch_id= $request->id and squad_id= $request->sid and activity_id = $request->activity_id and subactivity_id = $request->sub_activity_id $query and date between '$from' and '$to' group by ACT_ID order by date ASC");
            }

        if(count($users) === 0)
        {
            return json_encode([
                'status'    => 'error',
                'message'   => 'No data',
            ]);
        }

        $data_request   = [
            'batch_id'  => $batch_id,
            'squad_id' => $squad_id,
            'activity_id' => $activity_id,
            'sub_activity_id' => $sub_activity_id,
            'component' => $component,
            'from' => $from,
            'to'   => $to,
        ];
        $data_request   = data_crypt( serialize($data_request) );

        $datasheet_url = url("/export/{$data_request}");
        return json_encode([
            'status'            => "success",
            'datasheet_url'    => $datasheet_url
        ]);

        }



    }
}
