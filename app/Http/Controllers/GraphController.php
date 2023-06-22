<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\probationer;
use App\Models\Fitness;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;

use Session;

use Illuminate\Support\Facades\Auth;

class GraphController extends Controller
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
        return view('graphs.graph',compact('batches', 'role'));
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
    public function monthlycharts()
    {
        $batches = Batch::all();
        return view('graphs.monthlygraph',compact('batches'));
    }

    public function probationerchart(Request $request)
    {

        $batch_id   = $request->bid;
        $squad_id   = $request->sid;
        $activity_id   = $request->activity_id;
        ($request->sub_activity_id != '') ? $sub_activity_id = $request->sub_activity_id :$sub_activity_id=null;
        ($request->component != '') ? $component_id = $request->component :$component_id=null;
        $date   = $request->date;
        if($request->date != '')
        {
            $m_and_y = explode("/", $request->date);

        }
        $probationer_list = Probationer::where('squad_id', $squad_id)->select('Name')->get();


        $day_wise = DB::table('probationers_dailyactivity_data')->select(DB::raw("CASE when component_id IS NOT NULL then component_id when subactivity_id IS NOT NULL THEN subactivity_id else activity_id end AS ACT_ID"), 'date', 'timetable_id', 'activity_id', 'subactivity_id', 'component_id', 'id')
        ->where('squad_id', $squad_id)
        ->where('activity_id', $activity_id)
        ->orderBy('date', 'asc')
        ->groupBy('timetable_id');


        if(!empty($sub_activity_id))
        {
            $day_wise->where('probationers_dailyactivity_data.subactivity_id', $sub_activity_id);
        }

        if(!empty($component_id))
        {
            $day_wise->where('probationers_dailyactivity_data.component_id', $component_id);
        }

        $results = $day_wise->get();

        // return "hii";

        if(count($results) == '0')
        {
            return "1";
        }
        foreach($results as $day)
        {
            $activity_unit = DB::table('activities')->where('activities.id', $day->ACT_ID)->first();

            $usersexport = DB::select("select  GROUP_CONCAT(count) as count, `date` from `probationers_dailyactivity_data` where (`probationers_dailyactivity_data`.`activity_id` = $day->ACT_ID or `probationers_dailyactivity_data`.`subactivity_id` = $day->ACT_ID or `probationers_dailyactivity_data`.`component_id` = $day->ACT_ID) and `probationers_dailyactivity_data`.`timetable_id` = '$day->timetable_id'");
            foreach($usersexport as $row)
            {
                $output[] = array(
                    'date'  => date('d-m', strtotime($row->date)),
                    'count' => $row->count,
                    'probationer' => $probationer_list
                );
            }
        }
        return json_encode($output);
    }

    public function probationersinglechart(Request $request)
    {
        // $batch_id   = $request->bid;
        // $squad_id   = isset($request->sid);
        //
        // $activity_id   = $request->activity_id;
        // ($request->sub_activity_id != '') ? $sub_activity_id = $request->sub_activity_id :$sub_activity_id='';
        // ($request->component != '') ? $component_id = $request->component :$component_id='';
        // $date   = $request->date;
        // if($request->date != '')
        // {
        //     $m_and_y = explode("/", $request->date);

        // }
        // $day_wise = DB::select("select CASE when component_id IS NOT NULL then component_id when subactivity_id IS NOT NULL THEN subactivity_id else activity_id end AS ACT_ID, date, timetable_id, activity_id, subactivity_id, component_id, id from probationers_dailyactivity_data where probationer_id = $probationer_id and activity_id = $activity_id and subactivity_id = $sub_activity_id and component_id = $component_id GROUP BY timetable_id order by date ASC");


        $batch_id = $request->bid;
        $request->sub_activity_id ? $sub_activity_id   = $request->sub_activity_id : $sub_activity_id = '';

        $probationer_id   = $request->pid;
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
            $day_wise =  DB::select("select CASE when component_id IS NOT NULL then component_id when subactivity_id IS NOT NULL THEN subactivity_id else activity_id end AS ACT_ID, date, timetable_id, activity_id, subactivity_id, component_id from probationers_dailyactivity_data where probationer_id= $probationer_id  and activity_id = $request->activity_id group by timetable_id order by date ASC");
        }
        else
        {
            $day_wise =  DB::select("select CASE when component_id IS NOT NULL then component_id when subactivity_id IS NOT NULL THEN subactivity_id else activity_id end AS ACT_ID, date, timetable_id, activity_id, subactivity_id, component_id from probationers_dailyactivity_data where probationer_id= $probationer_id  and activity_id = $request->activity_id and subactivity_id = $sub_activity_id $query  group by timetable_id order by date ASC");
        }

        if(count($day_wise) == '0')
        {
            return "1";
        }
        foreach($day_wise as $day)
        {
            $activity_unit = DB::table('activities')->where('activities.id', $day->ACT_ID)->first();

            $usersexport = DB::select("select `grade`, `count`, `date` from `probationers_dailyactivity_data` where probationer_id = $probationer_id and  (`probationers_dailyactivity_data`.`activity_id` = $day->ACT_ID or `probationers_dailyactivity_data`.`subactivity_id` = $day->ACT_ID or `probationers_dailyactivity_data`.`component_id` = $day->ACT_ID) and `probationers_dailyactivity_data`.`timetable_id` = '$day->timetable_id' and `probationers_dailyactivity_data`.`attendance` IN ('P', 'MDO', 'NCM')");

            foreach($usersexport as $row)
            {
                if(!empty($row))
                {
                    $output[] = array(
                        'date'  => date('d-m', strtotime($row->date)),
                        'count' => ($row->grade == '') ? $row->count : grade_to_num($row->grade),
                        'unit'  => (isset($activity_unit->unit) ? $activity_unit->unit : ''),
                    );
                }
            }
        }
        return json_encode($output);
    }


    public function probationermonthlyavgchart(Request $request)
    {
        $batch_id   = $request->bid;
        $squad_id   = $request->sid;
        $activity_id   = $request->activity_id;
        $sub_activity_id   = $request->sub_activity_id;
        ($request->component != '') ? $component_id = $request->component :$component_id='0';
        $date   = $request->date;
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


        $data = array();
        $post_label_array = array();
		$count_array = array();

        $day_wise = DB::select("select CASE when component_id IS NOT NULL then component_id when subactivity_id IS NOT NULL THEN subactivity_id else activity_id end AS ACT_ID, date, timetable_id, activity_id, subactivity_id, component_id from probationers_dailyactivity_data where activity_id = $activity_id and subactivity_id = $sub_activity_id and component_id = $component_id and date between '$from' and '$to' group by ACT_ID order by date ASC");

        if(count($day_wise) == '0')
        {
            return '1';
        }
        $act_id = $day_wise[0]->ACT_ID;

        $probationer_list = Probationer::where('squad_id', $squad_id)->get();
        foreach($probationer_list as $list)
        {
            $usersexport = DB::select("select Name, ROUND(AVG(CASE grade WHEN 'A' THEN 5 WHEN 'B' THEN 4 WHEN 'C' THEN 3 WHEN 'D' THEN 2 WHEN 'E' THEN 1 ELSE 0 END)) AS avg_gpa, ROUND(AVG(count)) as count from `probationers_dailyactivity_data` left join probationers on probationers.id = probationers_dailyactivity_data.probationer_id where (`probationers_dailyactivity_data`.`activity_id` = $act_id or `probationers_dailyactivity_data`.`subactivity_id` = $act_id or `probationers_dailyactivity_data`.`component_id` = $act_id) and probationer_id = $list->id  order by date asc");

                foreach($usersexport as $user)
                    {
                        if($user->avg_gpa != '')
                        {
                            $output[] = array(
                                'name'  => $user->Name,
                                'count' => $user->avg_gpa
                            );
                        }
                        else
                        {
                            $output[] = array(
                                'name'  => $user->Name,
                                'count' => $user->count
                            );
                        }
                    }
        }
        return json_encode($output);
    }

    public function compare_fitnessanalysis(Request $request)
    {
        $batches = Batch::all();
        return view('statistics.fitness-analysis',compact('batches'));
    }

    public function fitnesschart(Request $request)
    {

        $batch_id   = $request->bid;
        $squad_id   = $request->sid;
        $probationer_id   = $request->pid;
        $fitness_id   = $request->fitness_id;


        isset($request->date) ? $daterange = $request->date : $daterange = '';


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
        if($probationer_id == '')
        {
        $probationer_list = Probationer::where('squad_id', $squad_id)->select('id')->get();
       
        foreach($probationer_list as $probationer_lists)
        {
            // $usersexport = DB::select("SELECT Probationer_Id, $fitness_id as 'value', probationers.name as 'pid'  FROM `fitness_evaluvation` LEFT JOIN probationers on fitness_evaluvation.Probationer_Id = probationers.id WHERE Probationer_Id = '$probationer_lists->id' and date between '$from' and '$to' ORDER BY fitness_evaluvation.id ASC");


            $usersexport = Fitness::select('probationer_id', 'fitness_meta.fitness_value', 'probationers.name as pid')
            ->leftJoin('probationers', 'probationers.id', '=', 'fitness_meta.probationer_id')
            ->whereBetween('date', [$from, $to])
            ->where('probationer_id', $probationer_lists->id)->where('fitness_name', $fitness_id)
            ->orderBy('date', 'asc')
            ->get();


            if(!empty($usersexport))
            {
                foreach($usersexport as $row)
                {
                    $output[] = array(
                        'date'  => $row->pid,
                        'count' => $row->fitness_value,
                    );
                }
            }
            else
            {
                $output = '1';
            }

        }
        $outputs = isset($output) ? $output : '1';
        return json_encode($outputs);
     }
     else
     {
        // $usersexport = DB::select("SELECT Probationer_Id, $fitness_id as 'value', probationers.name as 'pid', `month`, `year`, `date`  FROM `fitness_evaluvation` LEFT JOIN probationers on fitness_evaluvation.Probationer_Id = probationers.id WHERE Probationer_Id = '$probationer_id' and date between '$from' and '$to' ORDER BY fitness_evaluvation.id ASC");

        $usersexport = Fitness::select('probationer_id', 'fitness_meta.fitness_value', 'probationers.name as pid', 'fitness_meta.date')
        ->leftJoin('probationers', 'probationers.id', '=', 'fitness_meta.probationer_id')
        ->whereBetween('date', [$from, $to])
        ->where('probationer_id', $probationer_id)->where('fitness_name', $fitness_id)
        ->orderBy('date', 'asc')
        ->get();

        // print_r(json_encode($usersexport));exit;


        if(count($usersexport) != '0')
        {
            foreach($usersexport as $row)
            {
                $output[] = array(
                    'date'  => date('d-m-Y', strtotime($row->date)),
                    'count' => $row->fitness_value,
                );
            }
        }
        else
        {
            $output = '1';
        }
        return json_encode($output);
     }

    }



    public function fitnessdownload(Request $request)
    {
        $batch_id   = $request->bid;
        $squad_id   = $request->sid;
        $probationer_id   = $request->pid;
        $fitness_id   = $request->fitness_id;
        $date   = $request->date;


        $spreadsheet = new Spreadsheet();
        isset($request->date) ? $daterange = $request->date : $daterange = '';

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
        if($probationer_id != '' && $fitness_id != '')
        {

            $n='2';
            $p = '2';
            $usercell = 'C';
            $userdatacell = 'C';

                $m='2';
                $k='2';
                $p = '1';

             $dates =  Fitness::select('date')
                                ->whereBetween('date', [$from, $to])->where('probationers.id', $probationer_id)->where('fitness_name', $fitness_id)
                                ->leftJoin('probationers', 'probationers.id', '=', 'fitness_meta.probationer_id')
                                ->orderBy('date', 'asc')->groupBy('date')->get();
                                

                                if(count($dates) != '0')
                                {
                                    foreach($dates as $row)
                                    {
                                        $output[] = array(
                                            'date'  => date('d-m-Y', strtotime($row->date)),
                                            //'count' => $row->fitness_value,
                                        );
                                    }
                                }
                                else
                                {
                                    $output = 1;
                                }
                                return json_encode($output);

            $names = Fitness::select('name', 'squad_id')->where('probationers.id', '=', $probationer_id)->whereBetween('date', [$from, $to])
            ->leftJoin('probationers', 'probationers.id', '=', 'fitness_meta.probationer_id')->groupBy('probationers.id')->first();

            if(!empty($names))
            {
                $spreadsheet->getActiveSheet()->setCellValue('B'.$m++, $names->name);
                $spreadsheet->getActiveSheet()->setCellValue('B1', 'Probationer Name');
                $spreadsheet->getActiveSheet()->setCellValue('A1', 'Squad Number');
                $spreadsheet->getActiveSheet()->setCellValue('A'.$k++, squad_number($names->squad_id));
            }

            
                foreach($dates as $date)
                {
                    $n='2';
                    $spreadsheet->getActiveSheet()->setCellValue($usercell++.$p,  ' ('.$fitness_id . ') ' . "\r\n" . date('d-m-Y', strtotime($date->date)));
                    $probationerlist = probationer::select('id')->where('probationers.id', $probationer_id)->first();
    
                    // foreach($probationerlist as $prob)
                    // {
                        $usersexport = Fitness::select('fitness_meta.fitness_value')
                                            ->where('date', $date->date)->where('probationer_id', $probationerlist->id)->where('fitness_name', $fitness_id)
                                            ->first();
                            $count = ($usersexport['fitness_value'] != '') ? $usersexport['fitness_value'] : '-';
                            $spreadsheet->getActiveSheet()->setCellValue($userdatacell.$n++, $count);
                    //}
                    $userdatacell++;
                }
            
         
            $i='1';
            $m++;
            $k++;

        }
        elseif ($probationer_id != '' && $fitness_id == '') {

            $fitness = [
                'weight',
                'bmi',
                'bodyfat',
                'fitnessscore',
                'endurancegrade',
                'strengthgrade',
                'flexibilitygrade',
            ];

            $n='2';
            $p = '2';
            $usercell = 'C';
            $userdatacell = 'C';

            foreach($fitness as $fitnessname)
            {
                $m='2';
                $k='2';
                $p = '1';

             $dates =  Fitness::select('date')
                                ->whereBetween('date', [$from, $to])->where('probationers.id', $probationer_id)->where('fitness_name', $fitnessname)
                                ->leftJoin('probationers', 'probationers.id', '=', 'fitness_meta.probationer_id')
                                ->orderBy('date', 'asc')->groupBy('date')->get();

                                if(count($dates) != '0')
                                {
                                    foreach($dates as $row)
                                    {
                                        $output[] = array(
                                            'date'  => date('d-m-Y', strtotime($row->date)),
                                            //'count' => $row->fitness_value,
                                        );
                                    }
                                }
                                else
                                {
                                    $output = '1';
                                }
                                return json_encode($output);




            $names = Fitness::select('name', 'squad_id')->where('probationers.id', '=', $probationer_id)->whereBetween('date', [$from, $to])
            ->leftJoin('probationers', 'probationers.id', '=', 'fitness_meta.probationer_id')->groupBy('probationers.id')->first();

            if(!empty($names))
            {
                $spreadsheet->getActiveSheet()->setCellValue('B'.$m++, $names->name);
                $spreadsheet->getActiveSheet()->setCellValue('B1', 'Probationer Name');
                $spreadsheet->getActiveSheet()->setCellValue('A1', 'Squad Number');
                $spreadsheet->getActiveSheet()->setCellValue('A'.$k++, squad_number($names->squad_id));
            }


            foreach($dates as $date)
            {
                $n='2';
                $spreadsheet->getActiveSheet()->setCellValue($usercell++.$p,  ' ('.$fitnessname . ') ' . "\r\n" . date('d-m-Y', strtotime($date->date)));
                $probationerlist = probationer::select('id')->where('probationers.id', $probationer_id)->first();

                // foreach($probationerlist as $prob)
                // {
                    $usersexport = Fitness::select('fitness_meta.fitness_value')
                                        ->where('date', $date->date)->where('probationer_id', $probationerlist->id)->where('fitness_name', $fitnessname)
                                        ->first();
                        $count = ($usersexport['fitness_value'] != '') ? $usersexport['fitness_value'] : '-';
                        $spreadsheet->getActiveSheet()->setCellValue($userdatacell.$n++, $count);
                //}
                $userdatacell++;
            }
            $i='1';
            $m++;
            $k++;
            }

        }
        elseif ($probationer_id == '' && $fitness_id != '') {
            $n='2';
            $p = '2';
            $usercell = 'C';
            $userdatacell = 'C';
            $m='2';
            $k='2';
            $p = '1';

         $dates =  Fitness::select('date')
                            ->whereBetween('date', [$from, $to])->where('probationers.squad_id', $squad_id)->where('fitness_name', $fitness_id)
                            ->leftJoin('probationers', 'probationers.id', '=', 'fitness_meta.probationer_id')
                            ->orderBy('date', 'asc')->groupBy('date')->get();

                            if(count($dates) != '0')
                            {
                                foreach($dates as $row)
                                {
                                    $output[] = array(
                                        'date'  => date('d-m-Y', strtotime($row->date)),
                                        //'count' => $row->fitness_value,
                                    );
                                }
                            }
                            else
                            {
                                $output = '1';
                            }
                            return json_encode($output);



        $names = Fitness::select('name', 'squad_id')->where('probationers.squad_id', '=', $squad_id)->whereBetween('date', [$from, $to])
        ->leftJoin('probationers', 'probationers.id', '=', 'fitness_meta.probationer_id')->groupBy('probationers.id')->orderBy('probationers.position_number', 'asc')->get();
        foreach($names as $name)
        {
            $spreadsheet->getActiveSheet()->setCellValue('B'.$m++, $name->name);
            $spreadsheet->getActiveSheet()->setCellValue('B1', 'Probationer Name');
            $spreadsheet->getActiveSheet()->setCellValue('A1', 'Squad Number');
            $spreadsheet->getActiveSheet()->setCellValue('A'.$k++, squad_number($name->squad_id));
        }

        foreach($dates as $date)
        {
            $n='2';
            $spreadsheet->getActiveSheet()->setCellValue($usercell++.$p,  ' ('.$fitness_id . ') ' . date('d-m-Y', strtotime($date->date)));
            $probationerlist = probationer::select('id')->where('probationers.squad_id', $squad_id)->orderBy('probationers.position_number', 'asc')->get();

            foreach($probationerlist as $prob)
            {
                $usersexport = Fitness::select('fitness_meta.fitness_value')
                                    ->where('date', $date->date)->where('probationer_id', $prob->id)->where('fitness_name', $fitness_id)
                                    ->first();
                    $count = (isset($usersexport['fitness_value']) != '') ? $usersexport['fitness_value'] : '-';
                    $spreadsheet->getActiveSheet()->setCellValue($userdatacell.$n++, $count);
            }
            $userdatacell++;
        }
        $i='1';
        $m++;
        $k++;
        }
        else
        {
            $fitness = [
                'weight',
                'bmi',
                'bodyfat',
                'fitnessscore',
                'endurancegrade',
                'strengthgrade',
                'flexibilitygrade',
            ];

            $n='2';
            $p = '2';
            $usercell = 'C';
            $userdatacell = 'C';

            foreach($fitness as $fitnessname)
            {
                $m='2';
                $k='2';
                $p = '1';

             $dates =  Fitness::select('date')
                                ->whereBetween('date', [$from, $to])->where('probationers.squad_id', $squad_id)->where('fitness_name', $fitnessname)
                                ->leftJoin('probationers', 'probationers.id', '=', 'fitness_meta.probationer_id')
                                ->orderBy('date', 'asc')->groupBy('date')->get();

                                if(count($dates) != '0')
                                {
                                    foreach($dates as $row)
                                    {
                                        $output[] = array(
                                            'date'  => date('d-m-Y', strtotime($row->date)),
                                            //'count' => $row->fitness_value,
                                        );
                                    }
                                }
                                else
                                {
                                    $output = '1';
                                }
                                return json_encode($output);




            $names = Fitness::select('name', 'squad_id')->where('probationers.squad_id', '=', $squad_id)->whereBetween('date', [$from, $to])
            ->leftJoin('probationers', 'probationers.id', '=', 'fitness_meta.probationer_id')->groupBy('probationers.id')->orderBy('probationers.position_number', 'asc')->get();
            foreach($names as $name)
            {
                $spreadsheet->getActiveSheet()->setCellValue('B'.$m++, $name->name);
                $spreadsheet->getActiveSheet()->setCellValue('B1', 'Probationer Name');
                $spreadsheet->getActiveSheet()->setCellValue('A1', 'Squad Number');
                $spreadsheet->getActiveSheet()->setCellValue('A'.$k++, squad_number($name->squad_id));
            }

            foreach($dates as $date)
            {
                $n='2';
                $spreadsheet->getActiveSheet()->setCellValue($usercell++.$p,  ' ('.$fitnessname . ') ' . "\r\n" . date('d-m-Y', strtotime($date->date)));
                $probationerlist = probationer::select('id')->where('probationers.squad_id', $squad_id)->orderBy('probationers.position_number', 'asc')->get();

                foreach($probationerlist as $prob)
                {
                    $usersexport = Fitness::select('fitness_meta.fitness_value')
                                        ->where('date', $date->date)->where('probationer_id', $prob->id)->where('fitness_name', $fitnessname)
                                        ->first();
                        $count = (isset($usersexport['fitness_value']) != '') ? $usersexport['fitness_value'] : '-';
                        $spreadsheet->getActiveSheet()->setCellValue($userdatacell.$n++, $count);
                }
                $userdatacell++;
            }
            $i='1';
            $m++;
            $k++;
            }
        }

            //$file_name = '.xlsx';
            $file_name = "uploads/fitness_" . batch_name($batch_id) . "_". date("Y-m-d-Hi") . ".xlsx";

            $baseurl = url('/'.$file_name);

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="' . $file_name . '"');

            header('Cache-Control: max-age=0');
            $writer = new Xlsx($spreadsheet);
            $writer->save($file_name);

    //  }
    //  else
    //  {
    //     $usersexport = DB::select("SELECT Probationer_Id, $fitness_id as 'value', probationers.name as 'pid', `month`, `year`  FROM `fitness_evaluvation` LEFT JOIN probationers on fitness_evaluvation.Probationer_Id = probationers.id WHERE Probationer_Id = '$probationer_id' $query");

    //     if(!empty($usersexport))
    //     {
    //         foreach($usersexport as $row)
    //         {
    //             $output[] = array(
    //                 'date'  => $row->month . '/' . $row->year,
    //                 'count' => $row->value,
    //             );
    //         }
    //     }
    //     else
    //     {
    //         $output = '1';
    //     }
    //     return json_encode($output);
    //  }

     return $baseurl;
    }
}
