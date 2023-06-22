<?php

namespace App\Http\Controllers;

use App\Exports\ExcelExport;
use App\Models\Activity;
use App\Models\Batch;
use App\Models\Squad;
use App\Models\Timetable;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function classes_conduct_report()
    {
        $role = Auth::user()->role;
        $batches = Batch::all();
        return view('statistics.classes-conduct-report', compact('batches', 'role'));
    }

    /**
     * Missed Classes Report
     *
     * @return \Illuminate\Http\Response
     */
    public function missed_classes_report()
    {
        $role = Auth::user()->role;
        $batches = Batch::all();
        return view('statistics.missed-classes-report', compact('batches', 'role'));
    }

    /**
     * Extra Classes Report
     *
     * @return \Illuminate\Http\Response
     */
    public function extra_classes_report()
    {
        $role = Auth::user()->role;
        $batches = Batch::all();
        return view('statistics.extra-classes-report', compact('batches', 'role'));
    }

    /**
     * Pass Fail Report
     *
     * @return \Illuminate\Http\Response
     */
    public function pass_fail_report()
    {
        $role = Auth::user()->role;
        $batches = Batch::all();
        return view('statistics.pass-fail-report', compact('batches', 'role'));
    }

    public function export()
    {
        return Excel::download(new ExcelExport('2', '8'), 'excel.xlsx');

    }

    /**
     * Download classes conduct report in excel
     */
    public function download_classes_conduct_report($data_request)
    {
        $data = unserialize(data_crypt($data_request, 'd'));
        $batch_id = isset($data["batch_id"]) ? $data["batch_id"] : 0;
        $squad_id = isset($data["squad_id"]) ? $data["squad_id"] : 0;
        $from = isset($data["from"]) ? $data["from"] : 0;
        $to = isset($data["to"]) ? $data["to"] : 0;

        $spreadsheet = new Spreadsheet();
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Sessions Count');
        $sheet1->setCellValue('A1', 'Activity');
        $sheet1->setCellValue('B1', 'Sub Activity');

        $cell = 'C';

        if ($squad_id == '0') {
            $squads = Squad::where('batch_id', $batch_id)->get();
        } else {
            $squads = Squad::where('id', $squad_id)->get();
        }

        foreach ($squads as $index => $squad) {
        $j = '2';
        $i = '2';
        $k = '2';
        $merge = '1';
        $activities = Activity::where('batch_id', $batch_id)
            ->where('type', 'activity')->select('id', 'name', 'type')
            ->get()->toArray();

        if (count($activities) > 0) {

            foreach ($activities as $activity) {
                $subactivities = Activity::where('parent_id', $activity['id'])->get()->toArray();
                    if(count($subactivities) > 0)
                    {
                        foreach ($subactivities as $subactivity) {
                            $sub_activies_count = Timetable::where('subactivity_id', $subactivity['id'])->where('squad_id', $squad->id)->whereBetween("date", [$from, $to])->count();
                            if($index === 0)
                            {
                                $sheet1->setCellValue('B'.$i, activity_name($subactivity['id']));
                            }
                            $sheet1->setCellValue($cell.$i++, $sub_activies_count);
                        }
                        if(count($subactivities) !== 0)
                        {
                            $merge = $k + count($subactivities);
                            $merge--;
                            $sheet1->mergeCells("A{$k}:A{$merge}");
                            $sheet1->setCellValue('A'.$j, activity_name($activity['id']));
                            $merge++;
                            $k = $merge;
                            $j = $merge;
                        }
                        else
                        {
                            $sheet1->setCellValue('A'.$j, activity_name($activity['id']));
                        }
                    }
                    else
                    {
                            $activies_count = Timetable::where('activity_id', $activity['id'])->where('squad_id', $squad->id)->whereBetween("date", [$from, $to])->count();
                            if($index === 0)
                            {
                                $sheet1->setCellValue('A'.$i, activity_name($activity['id']));
                            }
                            $sheet1->setCellValue($cell.$i++, $activies_count);
                            $merge++;
                            $merge++;
                            $k = $merge;
                            $j = $merge;
                    }
                }
            }

            $squad_num  = "Squad ".squad_number($squad->id);
            $squad_num  = str_replace("Squad Squad", "Squad", $squad_num);

            $sheet1->setCellValue($cell++ . '1', $squad_num);
        }

        // Auto size columns
        foreach (range("A", "Z") as $columnID) {
            $sheet1->getColumnDimension($columnID)->setAutoSize(true);
        }

        $fileName = "Classes_Conduct_Report_" . batch_name($batch_id) . "_". date("Y-m-d-Hi") . ".xlsx";
        spreadsheet_header($fileName);
        ob_end_clean();
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        die();
    }

    /**
     * Download missed classes report in csv
     */
    public function download_missed_classes_report($data_request)
    {
        $data = unserialize(data_crypt($data_request, 'd'));
        $batch_id = isset($data["batch_id"]) ? $data["batch_id"] : 0;
        $activity_id = isset($data["activity_id"]) ? $data["activity_id"] : 0;
        $subactivity_id = isset($data["subactivity_id"]) ? $data["subactivity_id"] : 0;
        $date_from = isset($data["date_from"]) ? $data["date_from"] : 0;
        $date_to = isset($data["date_to"]) ? $data["date_to"] : 0;

        $ExtraClassQ   = \App\Models\ExtraSession::query()
            ->where('batch_id', $batch_id)
            ->whereNotNull('activity_id')
            ->whereDate('date', '>=', $date_from)
            ->whereDate('date', '<=', $date_to)
            ->where('session_start', '>', 0);

        if(!empty($activity_id)) {
            $ExtraClassQ->where('activity_id', $activity_id);
        }
        if(!empty($subactivity_id)) {
            $ExtraClassQ->where('subactivity_id', $subactivity_id);
        }

        $ExtraClasses   = $ExtraClassQ->orderBy('session_start', 'desc')->get();

        // Initialize the spreadsheet

        $spreadsheet = new Spreadsheet();
        $sheet1 = $spreadsheet->getActiveSheet();

        $sheet1->setTitle('Missed Classes Report');

        $sheet1->setCellValue('A1', 'Session#');
        $sheet1->setCellValue('B1', 'Batch');
        $sheet1->setCellValue('C1', 'Activity');
        $sheet1->setCellValue('D1', 'Sub Activity');
        $sheet1->setCellValue('E1', 'Staff / DI');
        $sheet1->setCellValue('F1', 'Date');
        $sheet1->setCellValue('G1', 'Time');
        $sheet1->setCellValue('H1', 'Total Probationer');

        $row = 2;

        foreach ($ExtraClasses as $ExtraClass) {
            $sessionId   = $ExtraClass->id;
            $batch_id   = $ExtraClass->batch_id;
            $batch      = batch_name($batch_id);

            $activity_id     = $ExtraClass->activity_id;
            $activity        = activity_name($activity_id);

            $subActivity_id  = $ExtraClass->subactivity_id;
            $subActivity     = "-";
            if (!empty($subActivity_id)) {
                $subActivity     = activity_name($subActivity_id);
            }

            $staff_id      = $ExtraClass->drillinspector_id;
            $staff_name    = user_name($staff_id);

            $date     = $ExtraClass->date;
            $session_start  = $ExtraClass->session_start;
            $session_start  = date('h:i A', $session_start);

            $session_end    = $ExtraClass->session_end;
            $session_end    = date('h:i A', $session_end);

            // Probationer count
            $probationer_count  = \App\Models\ExtraSessionmeta::where('extra_session_id', $sessionId)->count();

            $sheet1->setCellValue("A".$row, $sessionId);
            $sheet1->setCellValue("B".$row, $batch);
            $sheet1->setCellValue("C".$row, $activity);
            $sheet1->setCellValue("D".$row, $subActivity);
            $sheet1->setCellValue("E".$row, $staff_name);
            $sheet1->setCellValue("F".$row, $date);
            $sheet1->setCellValue("G".$row, $session_start .' - '. $session_end);
            $sheet1->setCellValue("H".$row, $probationer_count);

            $row++;
        }

        // Auto size columns
        foreach (range("A", "H") as $columnID) {
            $sheet1->getColumnDimension($columnID)->setAutoSize(true);
        }

        $fileName = "Missed_Classes_Report_" . batch_name($batch_id) . "_". date("Y-m-d-Hi") .".xlsx";
        spreadsheet_header($fileName);
        ob_end_clean();
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        die();
    }

    /**
     * Download missed classes ATTENDANCE report in csv
     */
    public function download_missed_classes_attendance_report($data_request)
    {
        $data = unserialize(data_crypt($data_request, 'd'));
        $batch_id = isset($data["batch_id"]) ? $data["batch_id"] : 0;
        $activity_id = isset($data["activity_id"]) ? $data["activity_id"] : 0;
        $subactivity_id = isset($data["subactivity_id"]) ? $data["subactivity_id"] : 0;
        $date_from = isset($data["date_from"]) ? $data["date_from"] : 0;
        $date_to = isset($data["date_to"]) ? $data["date_to"] : 0;

        $ExtraClassQ   = \App\Models\ExtraSession::query()
            ->where('batch_id', $batch_id)
            ->whereNotNull('activity_id')
            ->whereDate('date', '>=', $date_from)
            ->whereDate('date', '<=', $date_to)
            ->where('session_start', '>', 0);

        if(!empty($activity_id)) {
            $ExtraClassQ->where('activity_id', $activity_id);
        }
        if(!empty($subactivity_id)) {
            $ExtraClassQ->where('subactivity_id', $subactivity_id);
        }

        $ExtraClasses   = $ExtraClassQ->orderBy('session_start', 'desc')->get();

        // Initialize the spreadsheet

        $spreadsheet = new Spreadsheet();
        $sheet1 = $spreadsheet->getActiveSheet();

        $sheet1->setTitle('Missed Classes Report');

        $sheet1->setCellValue('A1', 'Session#');
        $sheet1->setCellValue('B1', 'Batch');
        $sheet1->setCellValue('C1', 'Activity');
        $sheet1->setCellValue('D1', 'Sub Activity');
        $sheet1->setCellValue('E1', 'Staff / DI');
        $sheet1->setCellValue('F1', 'Date');
        $sheet1->setCellValue('G1', 'Time');
        $sheet1->setCellValue('H1', 'Probationer');
        $sheet1->setCellValue('I1', 'Squad');
        $sheet1->setCellValue('J1', 'Attendance');
        $sheet1->setCellValue('K1', 'Count');
        $sheet1->setCellValue('L1', 'Grade');
        $sheet1->setCellValue('M1', 'Regular Session');

        $row = 2;

        foreach ($ExtraClasses as $ExtraClass) {
            $sessionId   = $ExtraClass->id;
            $batch_id   = $ExtraClass->batch_id;
            $batch      = batch_name($batch_id);

            $activity_id     = $ExtraClass->activity_id;
            $activity        = activity_name($activity_id);

            $subActivity_id  = $ExtraClass->subactivity_id;
            $subActivity     = "-";
            if (!empty($subActivity_id)) {
                $subActivity     = activity_name($subActivity_id);
            }

            $staff_id      = $ExtraClass->drillinspector_id;
            $staff_name    = user_name($staff_id);

            $date     = $ExtraClass->date;
            $session_start  = $ExtraClass->session_start;
            $session_start  = date('h:i A', $session_start);

            $session_end    = $ExtraClass->session_end;
            $session_end    = date('h:i A', $session_end);

            // Probationer count
            $sessionMeta  = \App\Models\ExtraSessionmeta::where('extra_session_id', $sessionId)->get();
            foreach($sessionMeta as $meta) {
                $pb_id      = $meta->probationer_id;

                $probationer   = \App\Models\probationer::find($pb_id);
                $pb_name    = $probationer->Name;
                $squad_id   = $probationer->squad_id;
                $squad_num  = squad_number($squad_id);

                $attendance      = $meta->attendance;
                $attendance      = empty($attendance)? "-" : $attendance;

                $unit_count      = $meta->count;
                $unit_count      = empty($unit_count)? "-" : $unit_count;

                $grade      = $meta->grade;
                $grade      = empty($grade)? "-" : $grade;

                $rSession   = "-";
                $timetable_id   = $meta->timetable_id;
                if(!empty($timetable_id)) {
                    $Timetable  = Timetable::find($timetable_id);
                    $tt_date  = $Timetable->date;
                    $tt_session  = $Timetable->session_number;
                    $rSession   = $tt_date ." (Session ". $tt_session .")";
                }

                $sheet1->setCellValue("A".$row, $sessionId);
                $sheet1->setCellValue("B".$row, $batch);
                $sheet1->setCellValue("C".$row, $activity);
                $sheet1->setCellValue("D".$row, $subActivity);
                $sheet1->setCellValue("E".$row, $staff_name);
                $sheet1->setCellValue("F".$row, $date);
                $sheet1->setCellValue("G".$row, $session_start .' - '. $session_end);
                $sheet1->setCellValue("H".$row, $pb_name);
                $sheet1->setCellValue("I".$row, $squad_num);
                $sheet1->setCellValue("J".$row, $attendance);
                $sheet1->setCellValue("K".$row, $unit_count);
                $sheet1->setCellValue("L".$row, $grade);
                $sheet1->setCellValue("M".$row, $rSession);

                $row++;
            }
        }

        // Auto size columns
        foreach (range("A", "M") as $columnID) {
            $sheet1->getColumnDimension($columnID)->setAutoSize(true);
        }

        $fileName = "Missed_Classes_Report_" . batch_name($batch_id) . "_". date("Y-m-d-Hi") .".xlsx";
        spreadsheet_header($fileName);
        ob_end_clean();
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        die();
    }

    /**
     * Download extra classes report in csv
     */
    public function download_extra_classes_report($data_request)
    {
        $data = unserialize(data_crypt($data_request, 'd'));
        $batch_id = isset($data["batch_id"]) ? $data["batch_id"] : 0;
        $activity_id = isset($data["activity_id"]) ? $data["activity_id"] : 0;
        $subactivity_id = isset($data["subactivity_id"]) ? $data["subactivity_id"] : 0;
        $date_from = isset($data["date_from"]) ? $data["date_from"] : 0;
        $date_to = isset($data["date_to"]) ? $data["date_to"] : 0;

        $ExtraClassQ   = \App\Models\ExtraClass::query()
            ->where('batch_id', $batch_id)
            ->whereNotNull('activity_id')
            ->whereDate('date', '>=', $date_from)
            ->whereDate('date', '<=', $date_to)
            ->where('session_start', '>', 0);

        if(!empty($activity_id)) {
            $ExtraClassQ->where('activity_id', $activity_id);
        }
        if(!empty($subactivity_id)) {
            $ExtraClassQ->where('subactivity_id', $subactivity_id);
        }

        $ExtraClasses   = $ExtraClassQ->orderBy('session_start', 'desc')->get();

        // Initialize the spreadsheet

        $spreadsheet = new Spreadsheet();
        $sheet1 = $spreadsheet->getActiveSheet();

        $sheet1->setTitle('Missed Classes Report');

        $sheet1->setCellValue('A1', 'Session#');
        $sheet1->setCellValue('B1', 'Batch');
        $sheet1->setCellValue('C1', 'Activity');
        $sheet1->setCellValue('D1', 'Sub Activity');
        $sheet1->setCellValue('E1', 'Staff / DI');
        $sheet1->setCellValue('F1', 'Date');
        $sheet1->setCellValue('G1', 'Time');
        $sheet1->setCellValue('H1', 'Total Probationer');

        $row = 2;

        foreach ($ExtraClasses as $ExtraClass) {
            $sessionId   = $ExtraClass->id;
            $batch_id   = $ExtraClass->batch_id;
            $batch      = batch_name($batch_id);

            $activity_id     = $ExtraClass->activity_id;
            $activity        = activity_name($activity_id);

            $subActivity_id  = $ExtraClass->subactivity_id;
            $subActivity     = "-";
            if (!empty($subActivity_id)) {
                $subActivity     = activity_name($subActivity_id);
            }

            $staff_id      = $ExtraClass->drillinspector_id;
            $staff_name    = user_name($staff_id);

            $date     = $ExtraClass->date;
            $session_start  = $ExtraClass->session_start;
            $session_start  = date('h:i A', $session_start);

            $session_end    = $ExtraClass->session_end;
            $session_end    = date('h:i A', $session_end);

            // Probationer count
            $probationer_count  = \App\Models\ExtraClassmeta::where('extra_class_id', $sessionId)->count();

            $sheet1->setCellValue("A".$row, $sessionId);
            $sheet1->setCellValue("B".$row, $batch);
            $sheet1->setCellValue("C".$row, $activity);
            $sheet1->setCellValue("D".$row, $subActivity);
            $sheet1->setCellValue("E".$row, $staff_name);
            $sheet1->setCellValue("F".$row, $date);
            $sheet1->setCellValue("G".$row, $session_start .' - '. $session_end);
            $sheet1->setCellValue("H".$row, $probationer_count);

            $row++;
        }

        // Auto size columns
        foreach (range("A", "H") as $columnID) {
            $sheet1->getColumnDimension($columnID)->setAutoSize(true);
        }

        $fileName = "Extra_Classes_Report_" . batch_name($batch_id) . "_". date("Y-m-d-Hi") .".xlsx";
        spreadsheet_header($fileName);
        ob_end_clean();
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        die();
    }

    /**
     * Download extra classes ATTENDANCE report in csv
     */
    public function download_extra_classes_attendance_report($data_request)
    {
        $data = unserialize(data_crypt($data_request, 'd'));
        $batch_id = isset($data["batch_id"]) ? $data["batch_id"] : 0;
        $activity_id = isset($data["activity_id"]) ? $data["activity_id"] : 0;
        $subactivity_id = isset($data["subactivity_id"]) ? $data["subactivity_id"] : 0;
        $date_from = isset($data["date_from"]) ? $data["date_from"] : 0;
        $date_to = isset($data["date_to"]) ? $data["date_to"] : 0;

        $ExtraClassQ   = \App\Models\ExtraClass::query()
            ->where('batch_id', $batch_id)
            ->whereNotNull('activity_id')
            ->whereDate('date', '>=', $date_from)
            ->whereDate('date', '<=', $date_to)
            ->where('session_start', '>', 0);

        if(!empty($activity_id)) {
            $ExtraClassQ->where('activity_id', $activity_id);
        }
        if(!empty($subactivity_id)) {
            $ExtraClassQ->where('subactivity_id', $subactivity_id);
        }

        $ExtraClasses   = $ExtraClassQ->orderBy('session_start', 'desc')->get();

        // Initialize the spreadsheet

        $spreadsheet = new Spreadsheet();
        $sheet1 = $spreadsheet->getActiveSheet();

        $sheet1->setTitle('Missed Classes Report');

        $sheet1->setCellValue('A1', 'Session#');
        $sheet1->setCellValue('B1', 'Batch');
        $sheet1->setCellValue('C1', 'Activity');
        $sheet1->setCellValue('D1', 'Sub Activity');
        $sheet1->setCellValue('E1', 'Staff / DI');
        $sheet1->setCellValue('F1', 'Date');
        $sheet1->setCellValue('G1', 'Time');
        $sheet1->setCellValue('H1', 'Probationer');
        $sheet1->setCellValue('I1', 'Squad');
        $sheet1->setCellValue('J1', 'Attendance');
        $sheet1->setCellValue('K1', 'Count');
        $sheet1->setCellValue('L1', 'Grade');
        $sheet1->setCellValue('M1', 'Regular Session');

        $row = 2;

        foreach ($ExtraClasses as $ExtraClass) {
            $sessionId   = $ExtraClass->id;
            $batch_id   = $ExtraClass->batch_id;
            $batch      = batch_name($batch_id);

            $activity_id     = $ExtraClass->activity_id;
            $activity        = activity_name($activity_id);

            $subActivity_id  = $ExtraClass->subactivity_id;
            $subActivity     = "-";
            if (!empty($subActivity_id)) {
                $subActivity     = activity_name($subActivity_id);
            }

            $staff_id      = $ExtraClass->drillinspector_id;
            $staff_name    = user_name($staff_id);

            $date     = $ExtraClass->date;
            $session_start  = $ExtraClass->session_start;
            $session_start  = date('h:i A', $session_start);

            $session_end    = $ExtraClass->session_end;
            $session_end    = date('h:i A', $session_end);

            // Probationer count
            $sessionMeta  = \App\Models\ExtraClassmeta::where('extra_class_id', $sessionId)->get();
            foreach($sessionMeta as $meta) {
                $pb_id      = $meta->probationer_id;

                $probationer   = \App\Models\probationer::find($pb_id);
                $pb_name    = $probationer->Name;
                $squad_id   = $probationer->squad_id;
                $squad_num  = squad_number($squad_id);

                $attendance      = $meta->attendance;
                $attendance      = empty($attendance)? "-" : $attendance;

                $unit_count      = $meta->count;
                $unit_count      = empty($unit_count)? "-" : $unit_count;

                $grade      = $meta->grade;
                $grade      = empty($grade)? "-" : $grade;

                $rSession   = "-";
                $timetable_id   = $meta->timetable_id;
                if(!empty($timetable_id)) {
                    $Timetable  = Timetable::find($timetable_id);
                    $tt_date  = $Timetable->date;
                    $tt_session  = $Timetable->session_number;
                    $rSession   = $tt_date ." (Session ". $tt_session .")";
                }

                $sheet1->setCellValue("A".$row, $sessionId);
                $sheet1->setCellValue("B".$row, $batch);
                $sheet1->setCellValue("C".$row, $activity);
                $sheet1->setCellValue("D".$row, $subActivity);
                $sheet1->setCellValue("E".$row, $staff_name);
                $sheet1->setCellValue("F".$row, $date);
                $sheet1->setCellValue("G".$row, $session_start .' - '. $session_end);
                $sheet1->setCellValue("H".$row, $pb_name);
                $sheet1->setCellValue("I".$row, $squad_num);
                $sheet1->setCellValue("J".$row, $attendance);
                $sheet1->setCellValue("K".$row, $unit_count);
                $sheet1->setCellValue("L".$row, $grade);
                $sheet1->setCellValue("M".$row, $rSession);

                $row++;
            }
        }

        // Auto size columns
        foreach (range("A", "M") as $columnID) {
            $sheet1->getColumnDimension($columnID)->setAutoSize(true);
        }

        $fileName = "Extra_Classes_Report_" . batch_name($batch_id) . "_". date("Y-m-d-Hi") .".xlsx";
        spreadsheet_header($fileName);
        ob_end_clean();
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        die();
    }

    /**
     * Download Pass Fail report in csv
     */
    public function download_pass_fail_report($data_request)
    {
        $data = unserialize(data_crypt($data_request, 'd'));

        $batch_id = isset($data["batch_id"]) ? $data["batch_id"] : 0;
        $squad_id = isset($data["squad_id"]) ? $data["squad_id"] : 0;
        $date_from = isset($data["date_from"]) ? $data["date_from"] : 0;
        $date_to = isset($data["date_to"]) ? $data["date_to"] : 0;

        // Initialize the spreadsheet

        $spreadsheet = new Spreadsheet();
        $sheet1 = $spreadsheet->getActiveSheet();

        $sheet1->setTitle('Result Sheet');

        $sheet1->setCellValue('A1', 'Activity');
        $sheet1->setCellValue('B1', 'Sub Activity');
        $sheet1->setCellValue('C1', 'Probationer');
        $sheet1->setCellValue('D1', 'Squad');
        $sheet1->setCellValue('E1', 'Session Date');
        $sheet1->setCellValue('F1', 'Time');
        $sheet1->setCellValue('G1', 'Attendance');
        $sheet1->setCellValue('H1', 'Result');

        // Get activities
        $timetableQ   = \App\Models\Activity::query()
            ->where('activities.batch_id', $batch_id)
            ->where('activities.has_qualify', 1);
        if(!empty($squad_id)) {
            $timetableQ->where('timetables.squad_id', $squad_id);
        }
        $timetables = $timetableQ->whereDate('timetables.date', '>=', $date_from)
            ->whereDate('timetables.date', '<=', $date_to)
            ->where('timetables.session_start', '>', 0)
            ->join('timetables', function ($join) {
                $join->on('activities.id', '=', 'timetables.activity_id')
                    ->orOn('activities.id', '=', 'timetables.subactivity_id');
            })
            ->select('activities.name as activity_name', 'timetables.id as tt_id', 'timetables.*')
            ->orderBy('activities.name')
            ->orderBy('timetables.session_start', 'desc')
            ->get();

        // Probationers
        $probationerQ   = \App\Models\probationer::query()
            ->where('batch_id', $batch_id);
        if(!empty($squad_id)) {
            $probationerQ->where('squad_id', $squad_id);
        }
        $probationers   = $probationerQ->orderBy('squad_id', 'asc')
            ->orderBy('position_number', 'asc')
            ->get();

        $row = 2;
        foreach($timetables as $timetable) {
            $activity_id    = $timetable->activity_id;
            $activity_name  = activity_name((int)$activity_id);

            $subactivity_id    = $timetable->subactivity_id;
            $subactivity_name  = activity_name((int)$subactivity_id);


            $tt_id      = $timetable->tt_id;
            $tt_date    = $timetable->date;

            $tt_start  = $timetable->session_start;
            $tt_start  = date('h:i A', $tt_start);

            $tt_end    = $timetable->session_end;
            $tt_end    = date('h:i A', $tt_end);

            foreach($probationers as $probationer) {
                $pb_id  = $probationer->id;
                $pb_name  = $probationer->Name;

                $squad_id  = $probationer->squad_id;
                $squad_num  = squad_number($squad_id);

                $attn   = \App\Models\ProbationersDailyactivityData::where('probationer_id', $pb_id)
                    ->where('timetable_id', $tt_id)
                    ->first();

                $attendance = "-";
                $result = "-";
                if($attn) {
                    $attendance = $attn->attendance;
                    $attendance = empty($attendance)? "-" : $attendance;

                    $result = $attn->qualified;
                    $result = ($result === null) ? "-" : qualified_values($result);
                }

                $sheet1->setCellValue("A".$row, $activity_name);
                $sheet1->setCellValue("B".$row, $subactivity_name);
                $sheet1->setCellValue("C".$row, $pb_name);
                $sheet1->setCellValue("D".$row, $squad_num);
                $sheet1->setCellValue("E".$row, $tt_date);
                $sheet1->setCellValue("F".$row, $tt_start .' - '. $tt_end);
                $sheet1->setCellValue("G".$row, $attendance);
                $sheet1->setCellValue("H".$row, $result);

                $row++;
            }
        }

        // Auto size columns
        foreach (range("A", "H") as $columnID) {
            $sheet1->getColumnDimension($columnID)->setAutoSize(true);
        }

        $fileName = "Pass_Fail_Report_" . batch_name($batch_id) . "_". date("Y-m-d-Hi") .".xlsx";
        spreadsheet_header($fileName);
        ob_end_clean();
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        die();
    }

    public function ajax(Request $request)
    {
        $requestName = $request->requestName;

        // validate classes conduct report download
        if ($requestName === "download_session_count") {
            $errors = [];

            $batch_id = intval($request->batch_id);
            $squad_id = intval(isset($request->squad_id) ? $request->squad_id : '0');
            $daterange = $request->date;

            $date_from   = "";
            $date_to   = "";

            if (empty($batch_id)) {
                $errors[] = "Select Batch.";
            }

            if (empty($daterange)) {
                $errors[] = "Select Date Range.";
            } else {
                $daterangeArray = explode(" - ", $daterange);
                if(count($daterangeArray) !== 2) {
                    $errors[] = "Invalid Date Range (Expected: DD/MM/YYYY - DD/MM/YYYY).";
                } else if(!isValidDate($daterangeArray[0], 'd/m/Y') || !isValidDate($daterangeArray[1], 'd/m/Y')) {
                    $errors[] = "Invalid Date Format (Expected: DD/MM/YYYY - DD/MM/YYYY).";
                } else {
                    $date_from = convert_date($daterangeArray[0], 'd/m/Y');
                    $date_to = convert_date($daterangeArray[1], 'd/m/Y');
                }
            }

            if (!empty($errors)) {
                return json_encode([
                    'status' => "error",
                    'message' => "Error: " . implode('<br />', $errors),
                ]);
            }

            if ($squad_id != '') {
                $count = Timetable::where('batch_id', $batch_id)->where('squad_id', $squad_id)->whereBetween('date', [$date_from, $date_to])->count();
            } else {
                $count = Timetable::where('batch_id', $batch_id)->whereBetween('date', [$date_from, $date_to])->count();
            }

            if ($count === 0) {
                return json_encode([
                    'status' => "error",
                    'message' => "Empty sessions",
                ]);
            }

            if (empty($errors)) {

                $data_request = [
                    'batch_id' => $batch_id,
                    'squad_id' => $squad_id,
                    'from' => $date_from,
                    'to' => $date_to,
                ];
                $data_request = data_crypt(serialize($data_request));

                $datasheet_url = url("/reports/download-classes-conduct-report/{$data_request}");
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

        if ($requestName === "get_classes_conduct_report") {
            $errors = [];

            $batch_id = intval($request->batch_id);
            $squad_id = intval(isset($request->squad_id) ? $request->squad_id : '0');
            $daterange = $request->date;

            $date_from   = "";
            $date_to   = "";

            if (empty($batch_id)) {
                $errors[] = "Select Batch.";
            }
            if (empty($date)) {
                $errors[] = "Select Date.";
            }

            if (empty($daterange)) {
                $errors[] = "Select Date Range.";
            } else {
                $daterangeArray = explode(" - ", $daterange);
                if(count($daterangeArray) !== 2) {
                    $errors[] = "Invalid Date Range (Expected: DD/MM/YYYY - DD/MM/YYYY).";
                } else if(!isValidDate($daterangeArray[0], 'd/m/Y') || !isValidDate($daterangeArray[1], 'd/m/Y')) {
                    $errors[] = "Invalid Date Format (Expected: DD/MM/YYYY - DD/MM/YYYY).";
                } else {
                    $date_from = convert_date($daterangeArray[0], 'd/m/Y');
                    $date_to = convert_date($daterangeArray[1], 'd/m/Y');
                }
            }

            if (!empty($errors)) {
                return json_encode([
                    'status' => "error",
                    'message' => "Error: " . implode('<br />', $errors),
                ]);
            }

            if ($squad_id == '0') {
                $squads = Squad::where('batch_id', $batch_id)->get();
            } else {
                $squads = Squad::where('id', $squad_id)->get();
            }

            foreach ($squads as $index => $squad) {
                 $activities = Activity::where('batch_id', $batch_id)
                    ->where('type', 'activity')
                    ->get()->toArray();

                if (count($activities) > 0) {

                    foreach ($activities as $activity) {
                        $subactivities = Activity::where('parent_id', $activity['id'])->get()->toArray();
                            if(count($subactivities) > 0)
                            {
                                foreach ($subactivities as $subactivity) {
                                    $sub_activies_count = Timetable::where('subactivity_id', $subactivity['id'])->where('squad_id', $squad->id)->count();
                                    if($index === 0)
                                    {

                                    }

                                        if(count($subactivities) !== 0)
                                        {

                                        }
                                    else
                                    {

                                    }
                                }
                             }
                            else
                            {
                                $activies_count = Timetable::where('activity_id', $activity['id'])->where('squad_id', $squad->id)->count();

                            }
                        }
                    }
                }
        }

        // validate missed class report download
        if ($requestName === "download_missedClass_report") {
            $errors = [];

            $report_type    = $request->report_type;
            $batch_id       = intval($request->batch_id);
            $activity_id    = intval($request->activity_id);
            $subactivity_id = intval($request->subactivity_id);
            $daterange      = $request->report_daterange;

            $date_from   = "";
            $date_to   = "";

            if (empty($batch_id)) {
                $errors[] = "Select Batch.";
            }
            if (empty($daterange)) {
                $errors[] = "Select Date Range.";
            } else {
                $daterangeArray = explode(" - ", $daterange);
                if(count($daterangeArray) !== 2) {
                    $errors[] = "Invalid Date Range (Expected: DD/MM/YYYY - DD/MM/YYYY).";
                } else if(!isValidDate($daterangeArray[0], 'd/m/Y') || !isValidDate($daterangeArray[1], 'd/m/Y')) {
                    $errors[] = "Invalid Date Format (Expected: DD/MM/YYYY - DD/MM/YYYY).";
                } else {
                    $date_from = convert_date($daterangeArray[0], 'd/m/Y');
                    $date_to = convert_date($daterangeArray[1], 'd/m/Y');
                }
            }

            if (!empty($errors)) {
                return json_encode([
                    'status' => "error",
                    'message' => "Error: " . implode('<br />', $errors),
                ]);
            }

            $ExtraClassQ   = \App\Models\ExtraSession::query()
                ->where('batch_id', $batch_id)
                ->whereNotNull('activity_id')
                ->whereDate('date', '>=', $date_from)
                ->whereDate('date', '<=', $date_to)
                ->where('session_start', '>', 0);

            if(!empty($activity_id)) {
                $ExtraClassQ->where('activity_id', $activity_id);
            }
            if(!empty($subactivity_id)) {
                $ExtraClassQ->where('subactivity_id', $subactivity_id);
            }

            $ExtraClasses   = $ExtraClassQ->orderBy('session_start', 'desc')->get();

            if ($ExtraClasses->count() > 0) {
                $data_request = [
                    'batch_id'      => $batch_id,
                    'activity_id'   => $activity_id,
                    'subactivity_id' => $subactivity_id,
                    'date_from'     => $date_from,
                    'date_to'     => $date_to,
                ];
                $data_request = data_crypt(serialize($data_request));

                if($report_type === 'attendance_report') {
                    $report_url = url("/reports/download-missed-classes-attendance-report/{$data_request}");
                } else {
                    $report_url = url("/reports/download-missed-classes-report/{$data_request}");
                }

                return json_encode([
                    'status' => "success",
                    'report_url' => $report_url,
                ]);
            } else {
                return json_encode([
                    'status' => "error",
                    'message' => "No session found.",
                ]);
            }
        }

        // validate exra class report download
        if ($requestName === "download_extraClass_report") {
            $errors = [];

            $report_type    = $request->report_type;
            $batch_id       = intval($request->batch_id);
            $activity_id    = intval($request->activity_id);
            $subactivity_id = intval($request->subactivity_id);
            $daterange      = $request->report_daterange;

            $date_from   = "";
            $date_to   = "";

            if (empty($batch_id)) {
                $errors[] = "Select Batch.";
            }
            if (empty($daterange)) {
                $errors[] = "Select Date Range.";
            } else {
                $daterangeArray = explode(" - ", $daterange);
                if(count($daterangeArray) !== 2) {
                    $errors[] = "Invalid Date Range (Expected: DD/MM/YYYY - DD/MM/YYYY).";
                } else if(!isValidDate($daterangeArray[0], 'd/m/Y') || !isValidDate($daterangeArray[1], 'd/m/Y')) {
                    $errors[] = "Invalid Date Format (Expected: DD/MM/YYYY - DD/MM/YYYY).";
                } else {
                    $date_from = convert_date($daterangeArray[0], 'd/m/Y');
                    $date_to = convert_date($daterangeArray[1], 'd/m/Y');
                }
            }

            if (!empty($errors)) {
                return json_encode([
                    'status' => "error",
                    'message' => "Error: " . implode('<br />', $errors),
                ]);
            }

            $ExtraClassQ   = \App\Models\ExtraClass::query()
                ->where('batch_id', $batch_id)
                ->whereNotNull('activity_id')
                ->whereDate('date', '>=', $date_from)
                ->whereDate('date', '<=', $date_to)
                ->where('session_start', '>', 0);

            if(!empty($activity_id)) {
                $ExtraClassQ->where('activity_id', $activity_id);
            }
            if(!empty($subactivity_id)) {
                $ExtraClassQ->where('subactivity_id', $subactivity_id);
            }

            $ExtraClasses   = $ExtraClassQ->orderBy('session_start', 'desc')->get();

            if ($ExtraClasses->count() > 0) {
                $data_request = [
                    'batch_id'      => $batch_id,
                    'activity_id'   => $activity_id,
                    'subactivity_id' => $subactivity_id,
                    'date_from'     => $date_from,
                    'date_to'     => $date_to,
                ];
                $data_request = data_crypt(serialize($data_request));

                if($report_type === 'attendance_report') {
                    $report_url = url("/reports/download-extra-classes-attendance-report/{$data_request}");
                } else {
                    $report_url = url("/reports/download-extra-classes-report/{$data_request}");
                }

                return json_encode([
                    'status' => "success",
                    'report_url' => $report_url,
                ]);
            } else {
                return json_encode([
                    'status' => "error",
                    'message' => "No session found.",
                ]);
            }
        }

        // validate Pass Fail report download
        if ($requestName === "download_pass_fail_report") {
            $errors = [];

            $batch_id       = intval($request->batch_id);
            $squad_id       = intval($request->squad_id);
            $daterange      = $request->report_daterange;

            $date_from   = "";
            $date_to   = "";

            if (empty($batch_id)) {
                $errors[] = "Select Batch.";
            }
            if (empty($daterange)) {
                $errors[] = "Select Date Range.";
            } else {
                $daterangeArray = explode(" - ", $daterange);
                if(count($daterangeArray) !== 2) {
                    $errors[] = "Invalid Date Range (Expected: DD/MM/YYYY - DD/MM/YYYY).";
                } else if(!isValidDate($daterangeArray[0], 'd/m/Y') || !isValidDate($daterangeArray[1], 'd/m/Y')) {
                    $errors[] = "Invalid Date Format (Expected: DD/MM/YYYY - DD/MM/YYYY).";
                } else {
                    $date_from = convert_date($daterangeArray[0], 'd/m/Y');
                    $date_to = convert_date($daterangeArray[1], 'd/m/Y');
                }
            }

            if (!empty($errors)) {
                return json_encode([
                    'status' => "error",
                    'message' => "Error: " . implode('<br />', $errors),
                ]);
            }

            // Probationer Count
            $probationerQ   = \App\Models\probationer::query()
                ->where('batch_id', $batch_id);
            if(!empty($squad_id)) {
                $probationerQ->where('squad_id', $squad_id);
            }
            $pb_count   = $probationerQ->count();

            if ($pb_count === 0) {
                return json_encode([
                    'status' => "error",
                    'message' => "No probationer found.",
                ]);
            }

            // Activity Count
            $activities_count   = \App\Models\Activity::query()
                ->where('batch_id', $batch_id)
                ->where('has_qualify', 1)
                ->count();

            if ($activities_count === 0) {
                return json_encode([
                    'status' => "error",
                    'message' => "No activity found with pass/fail.",
                ]);
            }

            // Probationer Count
            $timetableQ   = \App\Models\Timetable::query()
                ->where('batch_id', $batch_id);
            if(!empty($squad_id)) {
                $timetableQ->where('squad_id', $squad_id);
            }
            $timetable_count   = $timetableQ->whereDate('date', '>=', $date_from)
                ->whereDate('date', '<=', $date_to)
                ->where('session_start', '>', 0)
                ->count();

            if ($timetable_count === 0) {
                return json_encode([
                    'status' => "error",
                    'message' => "No timetable/sessions found.",
                ]);
            }

            if ($pb_count > 0 && $timetable_count > 0) {
                $data_request = [
                    'batch_id'  => $batch_id,
                    'squad_id'  => $squad_id,
                    'date_from' => $date_from,
                    'date_to'   => $date_to,
                ];
                $data_request = data_crypt(serialize($data_request));

                $report_url = url("/reports/download-pass-fail-report/{$data_request}");

                return json_encode([
                    'status' => "success",
                    'report_url' => $report_url,
                ]);
            } else {
                return json_encode([
                    'status' => "error",
                    'message' => "No session found.",
                ]);
            }
        }
    }
}
