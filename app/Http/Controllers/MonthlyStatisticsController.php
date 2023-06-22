<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Batch;
use App\Models\Squad;
use App\Models\Activity;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use Illuminate\Support\Facades\Auth;

class MonthlyStatisticsController extends Controller
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
        return view('statistics.statisticsReport2',compact('batches', 'role'));
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
    public function report_monthly_activity_view(Request $request)
    {
        $role   = Auth::user()->role;

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

        if($request->sub_activity_id != '' and $request->activity_id == '')
        {
            $query = "and subactivity_id = ".$request->sub_activity_id;
        }
        elseif($request->sub_activity_id != '' and $request->activity_id != '')
        {
            $query = "and subactivity_id = " . $request->sub_activity_id . " and activity_id = " . $request->activity_id;
        }
        elseif($request->sub_activity_id == '' and $request->activity_id != '')
        {
            $query = " and activity_id = " . $request->activity_id;
        }
        else
        {
            $query = '';
        }
        $batches = Batch::all();

        $users =  DB::select("select CASE when component_id IS NOT NULL then component_id when subactivity_id IS NOT NULL THEN subactivity_id else activity_id end AS ACT_ID, date, timetable_id, activity_id, subactivity_id, component_id from probationers_dailyactivity_data where Batch_id= $request->batch_id and squad_id= $request->squad_id $query and date between '$from' and '$to' group by ACT_ID order by date ASC");
        if(count($users) == '0')
        {
            return redirect('/monthlyreports')->with('delete', 'No data avaliable');
        }
        $dt = array();
        $data = [];
        foreach($users as $squad)
        {
            $usersexport = DB::select("SELECT * FROM `activities` WHERE id = $squad->ACT_ID");
            foreach($usersexport as $value)
            {
                $dt[] = $value->name;
            }
        }
        $probationers =  DB::select("select CASE when component_id IS NOT NULL then component_id when subactivity_id IS NOT NULL THEN subactivity_id else activity_id end AS ACT_ID, probationer_id from probationers_dailyactivity_data where Batch_id= $request->batch_id and squad_id= $request->squad_id $query and date between '$from' and '$to'  group by probationer_id order by probationer_id ASC");
        $probationer_count = (count($users));
        foreach($probationers as $probationer)
        {
            $activity =  DB::select("select CASE when component_id IS NOT NULL then component_id when subactivity_id IS NOT NULL THEN subactivity_id else activity_id end AS ACT_ID, date, timetable_id, activity_id, subactivity_id, component_id from probationers_dailyactivity_data where Batch_id= $request->batch_id and squad_id= $request->squad_id $query and date between '$from' and '$to' group by ACT_ID order by date ASC");
            foreach($activity as $activites)
            {
                $probationer_data = DB::select("select Name, ROUND(AVG(
                    CASE grade
                        WHEN 'A' THEN 5
                        WHEN 'B' THEN 4
                        WHEN 'C' THEN 3
                        WHEN 'D' THEN 2
                        WHEN 'E' THEN 1
                        ELSE 0
                    END
                    )) AS avg_gpa, ROUND(AVG(count)) from `probationers_dailyactivity_data` left join probationers on probationers.id = probationers_dailyactivity_data.probationer_id where (`probationers_dailyactivity_data`.`activity_id` = $activites->ACT_ID or `probationers_dailyactivity_data`.`subactivity_id` = $activites->ACT_ID or `probationers_dailyactivity_data`.`component_id` = $activites->ACT_ID) and probationer_id = $probationer->probationer_id and date between '$from' and '$to' order by date asc");
                    foreach($probationer_data as $probs)
                    {
                        $data[] = $probs;
                    }
            }

        }
        $data1 =  array_chunk($data,$probationer_count);

       return view('statistics.statisticsReport2',compact('batches', 'dt', 'data1', 'role'));
    }

    public function monthlyexport(Request $request)
    {
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

        if($request->sub_activity_id != '' and $request->activity_id == '')
        {
            $query = "and subactivity_id = ".$request->sub_activity_id;
        }
        elseif($request->sub_activity_id != '' and $request->activity_id != '')
        {
            $query = "and subactivity_id = " . $request->sub_activity_id . " and activity_id = " . $request->activity_id;
        }
        elseif($request->sub_activity_id == '' and $request->activity_id != '')
        {
            $query = " and activity_id = " . $request->activity_id;
        }
        else
        {
            $query = '';
        }
        function replacewithgraade($template)
        {
            switch ($template) {
                case "5":
                  return "A";
                  break;
                case "4":
                    return "B";
                  break;
                case "3":
                    return "C";
                  break;
                case "2":
                    return "D";
                  break;
                case "1":
                    return "E";
                break;
                default:
                return "-";
              }
        }
        $users =  DB::select("select CASE when component_id IS NOT NULL then component_id when subactivity_id IS NOT NULL THEN subactivity_id else activity_id end AS ACT_ID, date, timetable_id, activity_id, subactivity_id, component_id from probationers_dailyactivity_data where Batch_id= $request->batch_id and squad_id= $request->squad_id $query and date between '$from' and '$to' group by ACT_ID order by date ASC");

        if(count($users) == '0')
        {
            return true;
        }
        $dt = array();
        $data = [];
        $p='0';
        $q = '1';
        $usercell = 'A';
        $spreadsheet = new Spreadsheet();
        $row ='B';
        $k = '4';
        $spreadsheet->getActiveSheet($p)->setCellValue("A4", "Probationer Name");
        $activity_name = Activity::where('id', $request->activity_id)->first();
        // $spreadsheet->getActiveSheet($p)->setTitle($activity_name->name);
        foreach($users as $squad)
        {
            $usersexport = DB::select("SELECT * FROM `activities` WHERE id = $squad->ACT_ID");
            foreach($usersexport as $value)
            {
                $spreadsheet->getActiveSheet($p)->setCellValue($row++.$k, $value->name);

            }
        }
        $squad1 = DB::table('squads')->where('squads.id', $request->squad_id)->first();
        $batch = DB::table('batches')->where('batches.id', $request->batch_id)->first();
        $spreadsheet->getActiveSheet($p)->setCellValue("A1", "Monthly Grade report");
        $spreadsheet->getActiveSheet($p)->setCellValue("A2", "Batch NO");
        $spreadsheet->getActiveSheet($p)->setCellValue("C2", "Squad No");
        $spreadsheet->getActiveSheet($p)->setCellValue("B2", $batch->BatchName);
        $spreadsheet->getActiveSheet($p)->setCellValue("D2", $squad1->SquadNumber);
        $probationers =  DB::select("select CASE when component_id IS NOT NULL then component_id when subactivity_id IS NOT NULL THEN subactivity_id else activity_id end AS ACT_ID, probationer_id from probationers_dailyactivity_data where Batch_id= $request->batch_id and squad_id= $request->squad_id $query and date between '$from' and '$to'  group by probationer_id order by probationer_id ASC");
        $probationer_count = (count($users));

        $n = '5';
        foreach($probationers as $probationer)
        {
            $activity =  DB::select("select CASE when component_id IS NOT NULL then component_id when subactivity_id IS NOT NULL THEN subactivity_id else activity_id end AS ACT_ID, date, timetable_id, activity_id, subactivity_id, component_id from probationers_dailyactivity_data where Batch_id= $request->batch_id and squad_id= $request->squad_id $query and date between '$from' and '$to' group by ACT_ID order by date ASC");
            $i='1';

            foreach($activity as $activites)
            {
                $probationer_data = DB::select("select Name, ROUND(AVG(
                    CASE grade
                        WHEN 'A' THEN 5
                        WHEN 'B' THEN 4
                        WHEN 'C' THEN 3
                        WHEN 'D' THEN 2
                        WHEN 'E' THEN 1
                        ELSE 0
                    END
                    )) AS avg_gpa, ROUND(AVG(count)) as count from `probationers_dailyactivity_data` left join probationers on probationers.id = probationers_dailyactivity_data.probationer_id where (`probationers_dailyactivity_data`.`activity_id` = $activites->ACT_ID or `probationers_dailyactivity_data`.`subactivity_id` = $activites->ACT_ID or `probationers_dailyactivity_data`.`component_id` = $activites->ACT_ID) and probationer_id = $probationer->probationer_id and date between '$from' and '$to' order by date asc");
                    foreach($probationer_data as $probs)
                    {
                        if($i == '1')
                        {
                            $spreadsheet->getActiveSheet($p)->setCellValue($usercell++.$n, $probs->Name);
                            if($probs->avg_gpa != null)
                            {
                                $spreadsheet->getActiveSheet($p)->setCellValue($usercell++.$n, replacewithgraade($probs->avg_gpa));
                            }
                            else
                            {
                                $spreadsheet->getActiveSheet($p)->setCellValue($usercell++.$n, replacewithgraade($probs->count));
                            }

                          //  $n++;
                            $i++;
                       }
                       else
                       {
                       // $highestColumn = $spreadsheet->getActiveSheet($p)->getHighestColumn($n);
                       if($probs->avg_gpa != null)
                       {
                           $spreadsheet->getActiveSheet($p)->setCellValue($usercell++.$n, replacewithgraade($probs->avg_gpa));
                       }
                       else
                       {
                           $spreadsheet->getActiveSheet($p)->setCellValue($usercell++.$n, replacewithgraade($probs->count));
                       }
                      //  $n++;
                       }
                    }
            }
            $n++;
            $usercell = 'A';
        }

        if($request->activity_id != '')
        {
            $activity_name = Activity::where('id', $request->activity_id)->first();
            $selected_activity_name = preg_replace('/\s+/', '', $activity_name->name);
        }
        else
        {
            $selected_activity_name =preg_replace('/\s+/', '', $request->squad_id);
        }


        if($request->sub_activity_id != '')
        {
            $sub_activity_name = Activity::where('id', $request->sub_activity_id)->first();
            $selected_sub_activity_name = preg_replace('/\s+/', '', $sub_activity_name->name);
        }
        else
        {
            $selected_sub_activity_name ='';
        }
        $batch_name = Batch::where('id', $request->batch_id)->first();
        $squad_name = Squad::where('id', $request->squad_id)->first();
        $batch = preg_replace('/\s+/', '', $batch_name->BatchName);
        $squad = preg_replace('/\s+/', '', $squad_name->SquadNumber);

        if (!file_exists('uploads')) {
            mkdir('uploads', 0777, true);
        }

        $file_name= 'uploads/MonthlyReports' .'_' . $batch .'_' . $squad . '_' . $selected_activity_name . '_' .$selected_activity_name . '_' . $selected_sub_activity_name .'_'.$m_and_y[0].' '. $m_and_y[1] .'.xlsx';

        $baseurl = url('/'.$file_name);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $file_name . '"');

        header('Cache-Control: max-age=0');
        $writer = new Xlsx($spreadsheet);
        $writer->save($file_name);

        return $baseurl;
    }

}
