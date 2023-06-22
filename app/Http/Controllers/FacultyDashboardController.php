<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FacultyDashboardController extends Controller
{
    /**
     * Display Squad List
     *
     * @return \Illuminate\Http\Response
     */
    public function squads()
    {
        return view('faculty-dashboard.squads');
    }

    /**
     * Display Staff List
     *
     * @return \Illuminate\Http\Response
     */
    public function staffs()
    {
        return view('faculty-dashboard.staffs');
    }

    /**
     * Display Probationers List
     *
     * @return \Illuminate\Http\Response
     */
    public function probationers()
    {
        return view('faculty-dashboard.probationers');
    }

    /**
     * Display Activities List
     *
     * @return \Illuminate\Http\Response
     */
    public function activities()
    {
        return view('faculty-dashboard.activities');
    }
    /**
     * Display Activities List
     *
     * @return \Illuminate\Http\Response
     */
    public function activity_show($activity_id)
    {
        return view('faculty-dashboard.activity-show', compact('activity_id'));
    }

    /**
     * Display Attendance Report
     *
     * @return \Illuminate\Http\Response
     */
    public function monthly_attendance_report()
    {
        return view('faculty-dashboard.attendance.attendance-report');
    }

    /**
     * Display Monthly Sessions
     *
     * @return \Illuminate\Http\Response
     */
    public function monthly_sessions()
    {
        return view('faculty-dashboard.attendance.monthly-sessions');
    }

    /**
     * Display Missed Sessions Report
     *
     * @return \Illuminate\Http\Response
     */
    public function missed_sessions()
    {
        return view('faculty-dashboard.attendance.missed-sessions');
    }

    /**
     * Display Timetables View
     *
     * @return \Illuminate\Http\Response
     */
    public function timetables_view()
    {
        return view('faculty-dashboard.timetable.timetable-view');
    }

    public function dischargesummarys()
    {
        return view('faculty-dashboard.discharge-summary');
    }

    public function dischargesummaryss()
    {
        {
            return view('hospitalization.discharge-summary');
        }
    }

    public function dischargesummarysss()
    {
        {
            return view('receptionist.discharge-summary');
        }
    }
    

    /**
     * Display Timetables View
     *
     * @return \Illuminate\Http\Response
     */
    public function timetables_extra_sessions()
    {
        return view('faculty-dashboard.timetable.extra-sessions');
    }

    /**
     * Display Patient List
     *
     * @return \Illuminate\Http\Response
     */
    public function patient_list()
    {
        $patientslist = DB::table('in_patients')
            ->leftJoin('probationers', 'in_patients.probationer_id', '=', 'probationers.id')
            ->leftJoin('appoinments', 'appoinments.id', '=', 'in_patients.appointment_id')
            ->select(
                'probationers.id',
                'probationers.batch_id',
                'probationers.Name',
                'probationers.RollNumber',
                'probationers.gender',
                'probationers.EmergencyPhone',
                'in_patients.admitted_date',
                'in_patients.id',
                'in_patients.status',
                'appoinments.id as appoinmentid',
                'appoinments.Doctor_Id'
            )->get();
        return view('faculty-dashboard.hospitalization.patient-list', compact('patientslist'));
    }

    /**
     * Display Health Profile
     *
     * @return \Illuminate\Http\Response
     */
    public function health_profile()
    {
        return view('faculty-dashboard.hospitalization.health-profile');
    }

    /**
     * Display Medical Records
     *
     * @return \Illuminate\Http\Response
     */
    public function medical_records()
    {
        return view('faculty-dashboard.hospitalization.medical-records');
    }

    /**
     * Display Health Profile
     *
     * @return \Illuminate\Http\Response
     */
    public function medical_examination()
    {
        return view('faculty-dashboard.hospitalization.medical-examination');
    }

    /**
     * Display Health Profile
     *
     * @return \Illuminate\Http\Response
     */
    public function fitness_evaluation()
    {
        return view('faculty-dashboard.fitnessevaluation.fitness-evaluation');
    }

    /**
     * Display Health Profile
     *
     * @return \Illuminate\Http\Response
     */

    public function fitness_evaluation_data(Request $request)
    {
        $get_probationer = \App\Models\probationer::where('id', $request->id)->first();
        $fitness   = DB::table('fitness_evaluvation')->where('Probationer_Id', $get_probationer->id)->orderBy('id', 'DESC')->first();
        return view('faculty-dashboard.fitnessevaluation.fitness-evaluation-data',compact('get_probationer', 'fitness'));
    }

    /**
     * General Assesment.
     *
     * @return \Illuminate\Http\Response
     */
    public function general_assesment()
    {
        $batches = \App\Models\Batch::all();
        return view('faculty-dashboard.fitnessevaluation.general-assesment',compact('batches'));
    }

    public function general_assesment_data(Request $request)
    {
        $Probationer = \App\Models\probationer::where('id', $request->id)->first();
        $fitness   = DB::table('fitness_evaluvation')->where('Probationer_Id', $Probationer->id)->orderBy('id', 'DESC')->first();
        return view('faculty-dashboard.fitnessevaluation.general-assesment-data',compact('Probationer', 'fitness'));
    }

    /**
     * Process ajax requests.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function ajax(Request $request)
    {
        $requestName    = $request->requestName;

        // Get Squads
        if ($requestName === "get_squads") {
            return view('faculty-dashboard.squads-table', ['request' => $request]);
        }

        // Get Probationers
        if ($requestName === "get_probationers") {
            return view('faculty-dashboard.probationers-table', ['request' => $request]);
        }

        // Get Squad Probationers
        if ($requestName === "get_squad_probationers") {
            $squad_id   = $request->squad_id;
            if(empty($squad_id)) {
                echo "Squad Id missing";
                return;
            }

            $squad_num  = squad_number($squad_id);

            $Probationers   = \App\Models\probationer::where('squad_id', $squad_id)->orderBy('Name', 'asc')->get();
            if(count($Probationers)>0) {
                echo <<<EOL
                <div class="listdetails">
                    <table id="extra_sessions_table" class="table">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Probationer</th>
                                <th>Roll Number</th>
                                <th>Squad</th>
                            </tr>
                        </thead>
                        <tbody>
                EOL;

                $sl = 1;
                foreach($Probationers as $Probationer) {
                    $pb_id      = $Probationer->probationer_id;
                    $pb_name    = $Probationer->Name;
                    $roll    = $Probationer->RollNumber;

                    echo <<<EOL
                        <tr>
                            <td>{$sl}</td>
                            <td class="text-left">{$pb_name}</td>
                            <td class="text-left">{$roll}</td>
                            <td class="text-left">{$squad_num}</td>
                        </tr>
                    EOL;

                    $sl++;
                }

                echo <<<EOL
                        </tbody>
                    </table>
                </div>
                EOL;
            } else {
                echo "No probationers found";
            }
            return;
        }

        // Get Squad Probationers
        if ($requestName === "get_activities_by_batch") {
            $batch_id   = $request->batch_id;

            $ac_Query = \App\Models\Activity::query();
            if (!empty($batch_id)) {
                $ac_Query->where('batch_id', $batch_id);
            }
            $activities = $ac_Query->where('type', 'activity')->get();

            if (count($activities) > 0) {

                $sl = 1;

                foreach ($activities as $activity) {
                    $activity_id   = $activity->id;
                    $activity_name   = $activity->name;
                    $activity_unit   = $activity->unit;

                    $batchId    = $activity->batch_id;

                    $batch_name = "";
                    $batch  = \App\Models\Batch::where('id', $batchId)->first();
                    if ($batch) {
                        $batch_name = $batch->BatchName;
                    }

                    $view   = url('activity-view/'.$activity->id);
                    $icon   = asset('images/view.png');
                    echo <<<EOL
                    <tr>
                        <td>{$sl}</td>
                        <td class="text-left">{$batch_name}</td>
                        <td class="text-left">{$activity_name}</td>
                        <td>{$activity_unit}</td>
                        <td><a href="{$view}" data-toggle="tooltip" title="View"><img src="{$icon}" /></a></td>
                    </tr>
                    EOL;

                    $sl++;
                }
            } else {
                echo "<div class=\"msg msg-warning msg-full\">No Activities</div>";
            }

        }

        // Get Squad Probationers
        if ($requestName === "get_medical_records") {
            $roll_no    = $request->roll_no;

            $probationer_id = \App\Models\probationer::where('RollNumber', $roll_no)->value('id');
            if(!$probationer_id) {
                return 'Roll Number not exist.';
            }
            return view('faculty-dashboard.hospitalization.medical-records-data', compact('probationer_id'));
        }
    }
}
