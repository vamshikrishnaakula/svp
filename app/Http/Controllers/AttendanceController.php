<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Squad;
use App\Models\Timetable;
use App\Models\ProbationersDailyactivityData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

use DateTime;
use DateInterval;
use DatePeriod;
use Exception;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Display Manual Attendance.
     *
     * @return \Illuminate\Http\Response
     */
    public function manual_attendance()
    {
        $page_title = 'Manual Attendance';

        // Get probationers
        // $probationers = DB::table('probationers')->where('type', 'activity')->get();
        $probationers   = "";

        return view('attendance.manual-attendance', compact('page_title', 'probationers'));
    }

    /**
     * Display Monthly Report.
     *
     * @return \Illuminate\Http\Response
     */
    public function monthly_report()
    {
        $page_title = 'Monthly Report';

        // Get probationers
        // $probationers = DB::table('probationers')->where('type', 'activity')->get();
        $probationers   = "";

        return view('attendance.attendance-report', compact('page_title', 'probationers'));
    }

    /**
     * Display Unattended Sessions.
     *
     * @return \Illuminate\Http\Response
     */
    public function monthly_sessions()
    {
        $page_title = 'Monthly Sessions';
        $probationers   = "";

        return view('attendance.monthly-sessions', compact('page_title', 'probationers'));
    }


    /**
     * Display Unattended Sessions.
     *
     * @return \Illuminate\Http\Response
     */
    public function missed_sessions()
    {
        $page_title = 'Missed Sessions';

        return view('attendance.missed-sessions', compact('page_title'));
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
     * @param  \App\Models\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function show(Attendance $attendance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function edit(Attendance $attendance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Attendance $attendance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function destroy(Attendance $attendance)
    {
        //
    }

    /**
     * Download Monthly Report.
     *
     * @return \Illuminate\Http\Response
     */
    public function monthly_report_download(Request $request)
    {
        $data   = json_decode($request->form_data);
      //  return json_encode($data);

        $batch_id   = $data->batch_id;
        $squad_ids   = $data->squad_id;

        // $year       = $data->year;
        // $month      = $data->month;

        // $days   = cal_days_in_month(0, $month, $year);
        // $date_start = date("Y-m-1", strtotime("{$year}-{$month}-01"));
        // $date_end   = date("Y-m-t", strtotime("{$year}-{$month}-01"));

        $dater_range  = $data->report_datetimerange;

        $date1  = "";
        $date2  = "";

        if (empty($dater_range)) {
            $errors[] = "Select Date Range.";
        } else {
            $daterangeArray = explode(" - ", $dater_range);
            if(count($daterangeArray) !== 2) {
                $errors[] = "Invalid Date Range (Expected: DD/MM/YYYY - DD/MM/YYYY).";
            } else if(!isValidDate($daterangeArray[0], 'd/m/Y') || !isValidDate($daterangeArray[1], 'd/m/Y')) {
                $errors[] = "Invalid Date Format (Expected: DD/MM/YYYY - DD/MM/YYYY).";
            } else {
                $date1 = convert_date($daterangeArray[0], 'd/m/Y');
                $date2 = convert_date($daterangeArray[1], 'd/m/Y');
            }
        }

        if (!empty($errors)) {
            echo "Error: " . implode('<br />', $errors);
            return;
        }

        $dateBegin  = new DateTime($date1);
        $dateEnd    = new DateTime(date("Y-m-d", strtotime($date2 ."+1 days")));

        $dateInterval   = DateInterval::createFromDateString('1 day');
        $datePeriod     = new DatePeriod($dateBegin, $dateInterval, $dateEnd);

        // Get probationers

          if(empty($squad_ids))
            {
                $probationers = \App\Models\probationer::select('id', 'Name')
                ->where('batch_id', $batch_id)
                ->orderBy('position_number', 'asc')
                ->get();

                $squad_id = Squad::where('Batch_Id', $batch_id)->pluck('id')->toArray();
            }
            else
            {
                $probationers = \App\Models\probationer::select('id', 'Name')
                ->where('batch_id', $batch_id)
                ->where('squad_id', $squad_ids)
                ->orderBy('position_number', 'asc')
                ->get();
                $squad_id = [$squad_ids];
            }

        // Get max session numbers
        // $maxSessionNums = Timetable::where('squad_id', $squad_id)
        //     ->whereDate('date', '>=', $date1)
        //     ->whereDate('date', '<=', $date2)
        //     ->where('session_type', 'regular')
        //     ->selectRaw('max(session_number) as max_session, date')
        //     ->groupBy('date')
        //     ->get();

        // $maxSessionNums = Timetable::where('squad_id', $squad_id)
        //     ->whereBetween('date', [$date1, $date2])
        //     ->where('session_type', 'regular')
        //     ->selectRaw('date, max(session_number) as max_session')
        //     ->groupBy('date')
        //     ->get();

        // $maxSession = [];
        // foreach($maxSessionNums as $maxSessionNum) {
        //     $maxSession[$maxSessionNum->date]   = $maxSessionNum->max_session;
        // }

        $maxSessionNum1 = Timetable::whereIn('squad_id', $squad_id)
            ->whereBetween('date', [$date1, $date2])
            ->where('session_type', 'regular')
            ->selectRaw('date, max(session_number) as max_session')
            ->groupBy('date')
            ->get();

        $sessionCount1 = [];
        foreach($maxSessionNum1 as $mx) {
            $sessionCount1[$mx->date]   = max(6, $mx->max_session);
        }

        $maxSession = [];
        foreach ($datePeriod as $dt) {
            $dt1   = $dt->format('Y-m-d');
            $maxSession[$dt1]   = isset($sessionCount1[$dt1])? max(6, $sessionCount1[$dt1]) : 6;
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header row
        $sheet->mergeCells('A1:A2');
        $sheet->setCellValue('A1', 'Name of the Probationer');
        $sheet->getStyle("A1")
            ->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        foreach (range('A', 'A') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $r1 = $r2 = 0;
        $col2   = "A";
        foreach ($datePeriod as $dt) {
            $date   = $dt->format('Y-m-d');
            $dt2   = $dt->format('d/m/Y');

            // if(isset($maxSession[$date])) {
            //     $sessionNum = max(6, $maxSession[$date]);
            // } else {
            //     $sessionNum = 6;
            // }
            $sessionNum = $maxSession[$date];

            $r1  = $r2 + 1;
            $r2  = $r1 + ($sessionNum - 1);
            $i  = 1;
            for($r=$r1; $r<=$r2; $r++) {
                $col    = toAlpha($r);
                $sheet->setCellValue("{$col}2", "S{$i}");

                $i++;
            }
            $col1    = toAlpha($r1);
            $col2    = toAlpha($r2);
            $sheet->mergeCells("{$col1}1:{$col2}1");
            $sheet->setCellValue("{$col1}1", "{$dt2}");
        }

        $sheet->getStyle("A:{$col2}")
            ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $row    = 3;
        foreach ($probationers as $probationer) {
            $pb_id      = $probationer->id;
            $pb_name    = $probationer->Name;


            $c      = 1;
            $sheet->setCellValue("A{$row}", $pb_name);

            foreach ($datePeriod as $dt) {
                $date   = $dt->format('Y-m-d');
                $dt2   = $dt->format('d/m/Y');

                // $timetableQ = Timetable::where('squad_id', $squad_id)
                //     ->whereDate('date', $date)
                //     ->where('session_type', 'regular')
                //     ->orderBy('session_number', 'asc');
                // $timetables = $timetableQ->get()->toArray();

                $squad = squad_id((int) $pb_id);
                $timetables = Timetable::where('squad_id', $squad)
                    ->whereDate('date', $date)
                    ->where('session_type', 'regular')
                    ->orderBy('session_number', 'asc')->get()->toArray();

                // Max session_number for the day
                // $maxSessionNum = $timetableQ->max('session_number');
                // $sessionCount   = max(6, $maxSessionNum);
                $sessionCount = $maxSession[$date];

                $timeTableData   = range(1, $sessionCount);

                if( !empty($timetables) ) {
                    foreach ($timetables as $data) {
                        for ($si=0; $si<$sessionCount; $si++) {
                            $sn = $si + 1;
                            if($sn === intval($data["session_number"])) {
                                $timeTableData[$si]  = $data;
                            }
                        }
                    }
                }

                $ai = 0;
                if(count($timeTableData) > 0) {
                    foreach($timeTableData as $timetable) {
                        $col    = toAlpha($c);

                        if( is_array($timetable) ) {
                            $getAttn = DB::table('probationers_dailyactivity_data')
                                ->where('probationer_id', $pb_id)
                                ->where('timetable_id', $timetable["id"])
                                ->select('attendance')->first();


                            if($getAttn) {
                                // echo "<td>{$getAttn->attendance}</td>";
                                $sheet->setCellValue($col . $row, $getAttn->attendance);
                            } else {
                                // echo "<td>-</td>";
                                $sheet->setCellValue($col . $row, "-");
                            }

                            $ai++;
                        } else {
                            $sheet->setCellValue($col . $row, "-");
                        }

                        $c++;
                    }
                }
            }

            $row++;
        }

        // $writer = new Xlsx($spreadsheet);
        // $writer->save('php://output');

        $month_name = date('Y-M', strtotime($date1));
        $fileName   = "Attendance_Report_{$month_name}-" . date('Ymd-hia') . ".xlsx";
        // Spreadsheet Document Header
        spreadsheet_header($fileName);

        ob_end_clean();
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

        $writer->save('php://output');
    }

    /**
     * Download Monthly Sessions.
     *
     * @return \Illuminate\Http\Response
     */
    public function monthly_sessions_download(Request $request)
    {
        $data   = json_decode($request->form_data);

        $batch_id   = $data->batch_id;
        $squad_id   = $data->squad_id;
        $from_date       = $data->from_date;
        $to_date      = $data->to_date;

        // $days   = cal_days_in_month(0, $month, $year);
        $date1 = date("Y-m-d", strtotime($from_date));
        $date2   = date("Y-m-d", strtotime($to_date));

        // Get probationers
        // $probationers = \App\Models\probationer::select('id','Name')
        //     ->where('squad_id', $squad_id)
        //     ->get();


            if(empty($squad_id))
            {
                $probationers = \App\Models\probationer::select('id', 'Name')
                ->where('batch_id', $batch_id)
                ->get();

                $squad_id = Squad::where('Batch_Id', $batch_id)->pluck('id')->toArray();
            }
            else
            {
                $probationers = \App\Models\probationer::select('id', 'Name')
                ->where('batch_id', $batch_id)
                ->where('squad_id', $squad_id)
                ->orderBy('position_number', 'asc')
                ->get();
                $squad_id = [$squad_id];
            }

            $timetables = \App\Models\Timetable::whereIn('squad_id', $squad_id)
            ->where("activity_id", "!=", 0)
            ->whereNotNull("activity_id")
            ->whereBetween("date", [$date1, $date2])
            ->orderBy("activity_id")
            ->orderBy("subactivity_id")
            ->get();

        $timetables_count   = count($timetables);

        $activities     = [];
        $subactivities  = [];
        $tt_ids         = [];

        if($timetables_count > 0) {
            foreach($timetables as $timetable) {

                $tt_ids[]     = $timetable->id;
                $tt_activity_id     = $timetable->activity_id;
                $tt_subactivity_id  = $timetable->subactivity_id;

                if( isset($activities[$tt_activity_id]) ) {
                    if(!in_array($tt_subactivity_id, $activities[$tt_activity_id])) {
                        $activities[$tt_activity_id][]    = $tt_subactivity_id;
                        $subactivities[]    = $tt_subactivity_id;
                    }
                } else {
                    $activities[$tt_activity_id][]    = $tt_subactivity_id;
                    $subactivities[]    = $tt_subactivity_id;
                }
            }
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header row
        $sheet->mergeCells('A1:A3');
        $sheet->setCellValue('A1', 'Name of the Probationer');
        $sheet->getStyle("A1")
            ->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        foreach (range('A', 'A') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $r1 = $r2 = 0;
        foreach($activities as $activity_id => $data) {
            foreach($data as $subactivity_id) {

                $activityName = activity_name($activity_id);
                if( empty($activityName) ) {
                    $activityName = "--";
                }
                // echo "<th colspan=\"3\">{$activityName}</th>";

                $subactivityName = activity_name((int)$subactivity_id);
                if( empty($subactivityName) ) {
                    $subactivityName = "--";
                }
                // echo "<th colspan=\"3\">{$subactivityName}</th>";

                $r1  = $r2 + 1;
                $r2  = $r1 + 2;

                $col1    = toAlpha($r1);
                $col2    = toAlpha($r2);

                // Activity name
                $sheet->setCellValue("{$col1}1", $activityName);
                $sheet->mergeCells("{$col1}1:{$col2}1");

                // Sub Activity name
                $sheet->setCellValue("{$col1}2", $subactivityName);
                $sheet->mergeCells("{$col1}2:{$col2}2");

                // headings
                $colH1    = toAlpha($r1);
                $colH2    = toAlpha($r1+1);
                $colH3    = toAlpha($r1+2);
                $sheet->setCellValue("{$colH1}3", "Total");
                $sheet->setCellValue("{$colH2}3", "Attended");
                $sheet->setCellValue("{$colH3}3", "Missed");
            }
        }

        $sheet->getStyle("A:{$col2}")
            ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $row    = 4;
        foreach ($probationers as $probationer) {
            $pb_id      = $probationer->id;
            $pb_name    = $probationer->Name;


            $c      = 1;
            $sheet->setCellValue("A{$row}", $pb_name);

            $r1 = $r2 = 0;
            foreach($activities as $activity_id => $data) {
                foreach($data as $subactivity_id) {
                    $r1  = $r2 + 1;
                    $r2  = $r1 + 2;

                    $col1    = toAlpha($r1);
                    $col2    = toAlpha($r1+1);
                    $col3    = toAlpha($r1+2);

                    $squad = squad_id((int) $pb_id);

                    $total = Timetable::where('squad_id', $squad)
                        ->whereBetween("date", [$date1, $date2])
                        ->where("activity_id", $activity_id)
                        ->where("subactivity_id", $subactivity_id)
                        ->where("session_start", '>', 0)
                        ->count();



                        $getAttnsQ = Timetable::query()
                        ->where("probationers_dailyactivity_data.probationer_id", $pb_id)
                        ->whereBetween("probationers_dailyactivity_data.date", [$date1, $date2])
                        ->where("probationers_dailyactivity_data.activity_id", $activity_id);
                    if(!empty($subactivity_id)) {
                        $getAttnsQ->where("probationers_dailyactivity_data.subactivity_id", $subactivity_id);
                    }
                    $getAttns  = $getAttnsQ->leftJoin('probationers_dailyactivity_data', 'probationers_dailyactivity_data.timetable_id', '=', 'timetables.id')
                        ->select('probationers_dailyactivity_data.attendance', 'probationers_dailyactivity_data.timetable_id')
                        ->groupBy('probationers_dailyactivity_data.timetable_id')
                        ->get();

                    $attended_count = count($getAttns);

                    $attended   = 0;
                    $missed     = 0;

                    if( $total > 0 ) {
                        foreach($getAttns as $getAttn) {
                            $timetable_id   = $getAttn->timetable_id;
                            if( in_array($getAttn->attendance, ['P', 'MDO', 'NCM']) ) {
                                $attended++;
                            } else {
                                $Extrasession = \App\Models\ExtraSessionmeta::where('probationer_id', $pb_id)
                                    ->whereIn('attendance', ['P', 'MDO', 'NCM'])
                                    ->where('timetable_id', $timetable_id)
                                    ->count();

                                if($Extrasession > 0) {
                                    $attended++;
                                }
                            }
                        }

                        $missed = $attended_count - $attended;

                    } else {
                        $total      = "-";
                        $attended   = "-";
                        $missed     = "-";
                    }

                    $sheet->setCellValue($col1 . $row, $total);
                    $sheet->setCellValue($col2 . $row, $attended);
                    $sheet->setCellValue($col3 . $row, $missed);
                }
            }

            $row++;
        }

        // $writer = new Xlsx($spreadsheet);
        // $writer->save('php://output');

       // $month_name = date('Y-M', strtotime($date1));
        $fileName   = "Monthly_sessions_{$date1}-" . date('Ymd-hia') . ".xlsx";
        // Spreadsheet Document Header
        spreadsheet_header($fileName);

        ob_end_clean();
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

        $writer->save('php://output');
    }

    /**
     * Download Missed Sessions.
     *
     * @return \Illuminate\Http\Response
     */
    public function missed_sessions_download(Request $request)
    {
        $data   = json_decode($request->form_data);

        $batch_id   = $data->batch_id;
        $squad_id   = $data->squad_id;
        // $year       = $data->year;
        // $month      = $data->month;
        $from_date       = $data->from_date;
        $to_date      = $data->to_date;


        // $date1 = date("Y-m-d", strtotime("{$year}-{$month}-01"));
        // $date2   = date("Y-m-t", strtotime("{$year}-{$month}-01"));

        $date1 = date("Y-m-d", strtotime($from_date));
        $date2   = date("Y-m-d", strtotime($to_date));

        // Get probationers
        $probationers = \App\Models\probationer::select('id','Name')
            ->where('squad_id', $squad_id)
            ->orderBy('position_number', 'asc')
            ->get();

        $timetables = \App\Models\Timetable::where('squad_id', $squad_id)
            ->where("activity_id", "!=", 0)
            ->whereNotNull("activity_id")
            ->whereBetween("date", [$date1, $date2])
            ->orderBy("activity_id")
            ->orderBy("subactivity_id")
            ->get();

        $timetables_count   = count($timetables);

        $activities     = [];
        $subactivities  = [];
        $tt_ids         = [];
        if($timetables_count > 0) {
            foreach($timetables as $timetable) {

                $tt_ids[]     = $timetable->id;
                $tt_activity_id     = $timetable->activity_id;
                $tt_subactivity_id  = $timetable->subactivity_id;

                if( isset($activities[$tt_activity_id]) ) {
                    if(!in_array($tt_subactivity_id, $activities[$tt_activity_id])) {
                        $activities[$tt_activity_id][]    = $tt_subactivity_id;
                        $subactivities[]    = $tt_subactivity_id;
                    }
                } else {
                    $activities[$tt_activity_id][]    = $tt_subactivity_id;
                    $subactivities[]    = $tt_subactivity_id;
                }
            }
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header row
        $sheet->mergeCells('A1:A2');
        $sheet->setCellValue('A1', 'Name of the Probationer');
        $sheet->getStyle("A1")
            ->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $r = 1;
        foreach($activities as $activity_id => $data) {
            foreach($data as $subactivity_id) {

                $activityName = activity_name($activity_id);
                if( empty($activityName) ) {
                    $activityName = "--";
                }

                $subactivityName = activity_name((int)$subactivity_id);
                if( empty($subactivityName) ) {
                    $subactivityName = "--";
                }

                $col1    = toAlpha($r);

                // Activity name
                $sheet->setCellValue("{$col1}1", $activityName);

                // Sub Activity name
                $sheet->setCellValue("{$col1}2", $subactivityName);

                $r++;
            }
        }

        $sheet->getStyle("A:{$col1}")
            ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $row    = 3;
        foreach ($probationers as $probationer) {
            $pb_id      = $probationer->id;
            $pb_name    = $probationer->Name;

            $sheet->setCellValue("A{$row}", $pb_name);

            $r = 1;
            foreach($activities as $activity_id => $data) {
                foreach($data as $subactivity_id) {

                    $col1    = toAlpha($r);

                    $total = Timetable::where('squad_id', $squad_id)
                        ->whereBetween("date", [$date1, $date2])
                        ->where("activity_id", $activity_id)
                        ->where("subactivity_id", $subactivity_id)
                        ->where("session_start", '>', 0)
                        ->count();

                    // $getAttns = Timetable::whereRaw("probationers_dailyactivity_data.probationer_id = ? AND probationers_dailyactivity_data.date BETWEEN ? AND ? AND timetables.activity_id = ? AND timetables.subactivity_id = ?", [$pb_id, $date1, $date2, $activity_id, $subactivity_id])
                    //     ->leftJoin('probationers_dailyactivity_data', 'probationers_dailyactivity_data.timetable_id', '=', 'timetables.id')
                    //     ->select('probationers_dailyactivity_data.attendance', 'probationers_dailyactivity_data.timetable_id')
                    //     ->groupBy('probationers_dailyactivity_data.timetable_id')
                    //     ->get();

                    $getAttnsQ = Timetable::query()
                        // ->whereRaw("probationers_dailyactivity_data.probationer_id = ? AND probationers_dailyactivity_data.date BETWEEN ? AND ? AND timetables.activity_id = ? AND timetables.subactivity_id = ?", [$pb_id, $dateStart, $dateEnd, $activity_id, $subactivity_id])
                        ->where("probationers_dailyactivity_data.probationer_id", $pb_id)
                        ->whereBetween("probationers_dailyactivity_data.date", [$date1, $date2])
                        ->where("probationers_dailyactivity_data.activity_id", $activity_id);
                    if(!empty($subactivity_id)) {
                        $getAttnsQ->where("probationers_dailyactivity_data.subactivity_id", $subactivity_id);
                    }
                    $getAttns  = $getAttnsQ->leftJoin('probationers_dailyactivity_data', 'probationers_dailyactivity_data.timetable_id', '=', 'timetables.id')
                        ->select('probationers_dailyactivity_data.attendance', 'probationers_dailyactivity_data.timetable_id')
                        ->groupBy('probationers_dailyactivity_data.timetable_id')
                        ->get();

                    $attended   = 0;
                    $missed     = 0;

                    if( $total > 0 ) {
                        foreach($getAttns as $getAttn) {
                            $timetable_id   = $getAttn->timetable_id;
                            if( in_array($getAttn->attendance, ['P', 'MDO', 'NCM']) ) {
                                $attended++;
                            } else {
                                $Extrasession = \App\Models\ExtraSessionmeta::where('probationer_id', $pb_id)
                                    ->whereIn('attendance', ['P', 'MDO', 'NCM'])
                                    ->where('timetable_id', $timetable_id)
                                    ->count();

                                if($Extrasession > 0) {
                                    $attended++;
                                }
                            }
                        }

                        $missed = $total - $attended;

                    } else {
                        $missed     = "-";
                    }

                    $sheet->setCellValue($col1 . $row, $missed);

                    $r++;
                }
            }

            $row++;
        }

        foreach (range('A', $col1) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // $writer = new Xlsx($spreadsheet);
        // $writer->save('php://output');

        $month_name = date('Y-M', strtotime($date1));
        $fileName   = "Missed_sessions_{$month_name}-" . date('Ymd-hia') . ".xlsx";
        // Spreadsheet Document Header
        spreadsheet_header($fileName);

        ob_end_clean();
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

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

        // Get Timetable Create Form
        // if ($requestName === "get_timetableView") {
        //     return view('timetable.get-timetable-view', ['request' => $request]);
        // }

        // Get Timetable Update Form
        if ($requestName === "get_manualAttendanceForm") {
            return view('attendance.form-manual-attendance', ['request' => $request]);
        }

        // Submit Timetable Update Form
        if ($requestName === "submit_manualAttendanceForm") {
            // echo '<pre>';
            // print_r($request->all());
            // echo '</pre>';

            $result = [];
            $timestamp  = date('Y-m-d H:i:s');

            $date        = $request->input('date');
            if( empty($date) ) {
                $result['status']   = "error";
                $result['message']  = "Date is required.";

                return json_encode($result);
            }

            $batch_id  = intval($request->batch_id);
            $squad_id  = intval($request->squad_id);
            $attData  = $request->input('attendance');


            if( !empty($attData) ) {

                try {
                    foreach($attData as $pb_id => $val) {

                        if( is_array($val) && count($val) > 0) {
                            foreach($val as $timetable_id => $attendance) {



                                if(!empty($attendance)) {
                                    // Squad activity trainer
                                    $activity_id    = Timetable::where('id', $timetable_id)->value('activity_id');
                                    $subactivity_id    = Timetable::where('id', $timetable_id)->value('subactivity_id');
                                    $staff_id   = DB::table('squad_activity_trainer')->where('squad_id', $squad_id)->where('activity_id', $activity_id)->value('staff_id');

                                    $getAttnDataQ   = ProbationersDailyactivityData::where('probationer_id', $pb_id)
                                        ->whereDate('date', $date)
                                        ->where('timetable_id', $timetable_id);

                                    $getAttnData = $getAttnDataQ->first();
                                    if($getAttnData) {
                                        $grade  = in_array($attendance, ["P", "MDO"]) ? $getAttnData->grade : "";
                                        $count  = in_array($attendance, ["P", "MDO"]) ? $getAttnData->count : "";

                                        $getAttnData->update([
                                            'activity_id' => $activity_id,
                                            'subactivity_id' => $subactivity_id,
                                            'attendance'    => $attendance,
                                            'grade'    => $grade,
                                            'count'    => $count,
                                        ]);
                                    } else {
                                        ProbationersDailyactivityData::create(
                                            [
                                                'Batch_id' => $batch_id,
                                                'squad_id' => $squad_id,
                                                'activity_id' => $activity_id,
                                                'subactivity_id' => $subactivity_id,
                                                'staff_id' => sanitize_activity_id($staff_id),
                                                'probationer_id' => $pb_id,
                                                'date'          => $date,
                                                'timetable_id'  => $timetable_id,
                                                'attendance' => $attendance,
                                                'grade' => '',
                                                'count' => '',
                                            ]
                                        );
                                    }
                                }
                            }
                        }
                    }

                    $result['status']   = "success";
                    $result['message']  = "Attendance updated successfully.";
                } catch (Exception $e) {
                    $result['status']   = "error";
                    $result['message']  = "ERROR: ". $e->getMessage();
                }
            }

            return json_encode($result);
        }

        // Get Monthly Report
        if ($requestName === "get_monthly_report_table") {
            return view('attendance.attendance-report-table', ['request' => $request]);
        }

        // Get Monthly Sessions
        if ($requestName === "get_monthlySessions_table") {
            return view('attendance.monthly-sessions-table', ['request' => $request]);
        }

        // Get Missed Sessions
        if ($requestName === "get_missedSessions_table") {
            return view('attendance.missed-sessions-table', ['request' => $request]);
        }

        // Get Monthly Report
        if ($requestName === "validate_monthly_report_download") {
            $data   = [];
            $errors = [];

            $batch_id = $request->batch_id;
            $squad_id = $request->squad_id;

            if( empty($batch_id) ) {
                $errors[]   = "Batch Id is missing.";
            }


            // $year   = $request->year;
            // $month  = $request->month;

            // if( empty($year) || empty($month) ) {
            //     $errors[]   = "Year and/or Month is missing.";
            // }

            $dater_range  = $request->report_datetimerange;

            if (empty($dater_range)) {
                $errors[] = "Select Date Range.";
            } else {
                $daterangeArray = explode(" - ", $dater_range);
                if(count($daterangeArray) !== 2) {
                    $errors[] = "Invalid Date Range (Expected: DD/MM/YYYY - DD/MM/YYYY).";
                } else if(!isValidDate($daterangeArray[0], 'd/m/Y') || !isValidDate($daterangeArray[1], 'd/m/Y')) {
                    $errors[] = "Invalid Date Format (Expected: DD/MM/YYYY - DD/MM/YYYY).";
                }
            }



            // $days   = cal_days_in_month(0, $month, $year);

            if(empty($squad_id))
            {
                $probationers = \App\Models\probationer::select('id')
                ->where('batch_id', $batch_id)
                ->count();

                $get_squads = Squad::where('Batch_Id', $batch_id)->pluck('id')->toArray();
                if(empty($get_squads)) {
                    $errors[]   = "No squads available for the selected Batch.";
                }
            }
            else
            {
                $probationers = \App\Models\probationer::select('id')
                ->where('batch_id', $batch_id)
                ->where('squad_id', $squad_id)
                ->count();
            }

            if(!empty($errors)) {
                return json_encode([
                    'status'    => 'error',
                    'message'   => 'Error: '. implode('<br />', $errors),
                ]);
            }


            if($probationers == 0) {
                return json_encode([
                    'status'    => 'error',
                    'message'   => 'No probationer found.',
                ]);
            }

            return json_encode([
                'status'    => 'success',
                'data'      => $request->all(),
            ]);
        }

        // Validate Monthly Session Download
        if ($requestName === "validate_monthly_sessions_download") {
            $errors = [];

            $batch_id = $request->batch_id;
            $squad_id = $request->squad_id;

            if( empty($batch_id) ) {
                $errors[]   = "Batch Id and/or Squad Id is missing.";
            }


            $from_date  = $request->from_date;
            $to_date   = $request->to_date;

            if(!empty($errors)) {
                return json_encode([
                    'status'    => 'error',
                    'message'   => 'Error: '. implode('<br />', $errors),
                ]);
            }


            if(empty($squad_id))
            {
                $probationers = \App\Models\probationer::select('id')
                ->where('batch_id', $batch_id)
                ->count();

                $squad_id = Squad::where('Batch_Id', $batch_id)->pluck('id')->toArray();
                if(empty($squad_id)) {
                    $errors[]   = "No squads available for the selected Batch.";
                }
            }
            else
            {
                $probationers = \App\Models\probationer::select('id')
                ->where('batch_id', $batch_id)
                ->where('squad_id', $squad_id)
                ->count();

                $squad_id = [$squad_id];
            }

            if($probationers == 0) {
                return json_encode([
                    'status'    => 'error',
                    'message'   => 'No probationer found.',
                ]);
            }

            // $date1 = date("Y-m-d", strtotime("{$year}-{$month}-01"));
            // $date2   = date("Y-m-t", strtotime("{$year}-{$month}-01"));

            $date1 = date('Y-m-d', strtotime($from_date));
            $date2 = date('Y-m-d', strtotime($to_date));

        // Get probationers
             $probationers = \App\Models\probationer::select('id','Name')
            ->where('squad_id', $squad_id)
            ->orderBy('position_number', 'asc')
            ->get();

            $timetables = \App\Models\Timetable::whereIn('squad_id', $squad_id)
            ->where("activity_id", "!=", 0)
            ->whereNotNull("activity_id")
            ->whereBetween("date", [$date1, $date2])
            ->orderBy("activity_id")
            ->orderBy("subactivity_id")
            ->get();


            if(count($timetables) == '0')
            {
                return json_encode([
                    'status'    => 'error',
                    'message'   => 'No Timetable found.',
                ]);
            }

            return json_encode([
                'status'    => 'success',
                'data'      => $request->all(),
            ]);
        }

        // Validate Monthly Session Download
        if ($requestName === "validate_missed_sessions_download") {
            $errors = [];

            $batch_id = $request->batch_id;
            $squad_id = $request->squad_id;

            if( empty($batch_id) || empty($squad_id) ) {
                $errors[]   = "Batch Id and/or Squad Id is missing.";
            }

            // $year   = $request->year;
            // $month  = $request->month;

            // if( empty($year) || empty($month) ) {
            //     $errors[]   = "Year and/or Month is missing.";
            // }

            if(!empty($errors)) {
                return json_encode([
                    'status'    => 'error',
                    'message'   => 'Error: '. implode('<br />', $errors),
                ]);
            }

            $probationers = \App\Models\probationer::select('id')
                ->where('batch_id', $batch_id)
                ->where('squad_id', $squad_id)
                ->count();

            if($probationers == 0) {
                return json_encode([
                    'status'    => 'error',
                    'message'   => 'No probationer found.',
                ]);
            }

            return json_encode([
                'status'    => 'success',
                'data'      => $request->all(),
            ]);
        }
    }
}
