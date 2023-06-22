<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Batch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Session;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = 'Activities';
        $page_description = 'List all activities';

        $b_id = current_batch();
        // Retrive activities
        $activities = Activity::where('batch_id',$b_id)->where('type', 'activity')->get();

        return view('activities.activities', compact('page_title', 'page_description', 'activities'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page_title = 'Create Activity';
        $page_description = 'Create new activity';

        return view('activities.create-activity', compact('page_title', 'page_description'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $result = [];
        $timestamp  = date('Y-m-d H:i:s');

        $batch_id       = $request->batch_id;
        $activity_name  = ucfirst($request->activity_name);

        if (empty($batch_id)) {
            $result['status']   = "error";
            $result['message']  = "Select batch";

            return json_encode($result);
        }

        if (empty($activity_name)) {
            $result['status']   = "error";
            $result['message']  = "Activity name is empty";

            return json_encode($result);
        }

        // Check If activity name already exist
        $verifyActivityName   = Activity::where([
            ['batch_id', $batch_id],
            ['name', $activity_name],
            ['type', 'activity']
        ])->first();

        if (!empty($verifyActivityName)) {
            $result['status']   = "error";
            $result['message']  = "Activity name already exist";
            return json_encode($result);
        }

        // Activity unit
        $activity_unit  = $request->activity_unit;
        $activity_unit  = empty($activity_unit) ? null : $activity_unit;

        // Activity grading
        $activity_has_grading  = $request->activity_has_grading;
        $activity_has_grading  = empty($activity_has_grading) ? 0 : 1;

        // Activity qualify
        $activity_has_qualify  = $request->activity_has_qualify;
        $activity_has_qualify  = empty($activity_has_qualify) ? 0 : 1;

        $Activity = Activity::create(
            [
                'batch_id' => $batch_id,
                'name' => $activity_name,
                'type' => 'activity',
                'unit' => $activity_unit,
                'has_grading' => $activity_has_grading,
                'has_qualify' => $activity_has_qualify
            ]
        );

        if (empty($Activity)) {
            $result['status']   = "error";
            $result['message']  = "Unable to insert Activity";

            return json_encode($result);
        }

        $activity_id  = $Activity->id;
        $result['activity_id']  = $activity_id;

        $sub_activities       = $request->sub_activity;
        $sub_activity_units   = $request->sub_activity_unit;
        $sub_activity_gradings   = $request->sub_activity_has_grading;
        $sub_activity_qualifys   = $request->sub_activity_has_qualify;

        if (!empty($sub_activities)) {

            $components       = $request->component;
            $component_units  = $request->component_unit;
            $component_has_grading  = $request->component_has_grading;
            $component_has_qualify  = $request->component_has_qualify;

            foreach ($sub_activities as $key => $sub_activity) {

                if (!empty($sub_activity)) {
                    $sub_activity_unit  = isset($sub_activity_units[$key]) ? $sub_activity_units[$key] : null;

                    $sub_activity_grading  = isset($sub_activity_gradings[$key]) ? $sub_activity_gradings[$key] : 0;
                    $sub_activity_grading  = empty($sub_activity_grading) ? 0 : 1;

                    $sub_activity_qualify  = isset($sub_activity_qualifys[$key]) ? $sub_activity_qualifys[$key] : 0;
                    $sub_activity_qualify  = empty($sub_activity_qualify) ? 0 : 1;

                    $subActivity = Activity::create(
                        [
                            'batch_id' => $batch_id,
                            'name' => $sub_activity,
                            'type' => 'subactivity',
                            'parent_id' => $activity_id,
                            'has_grading' => $sub_activity_grading,
                            'has_qualify' => $sub_activity_qualify
                        ]
                    );

                    $sub_activity_id = $subActivity->id;
                    if (isset($components[$key])) {

                        foreach ($components[$key] as $c_key => $component_name) {
                            $component_unit   = $component_units[$key][$c_key];

                            $component_grading  = isset($component_has_grading[$key][$c_key]) ? $component_has_grading[$key][$c_key] : 0;
                            $component_grading  = empty($component_grading) ? 0 : 1;

                            $component_qualify  = isset($component_has_qualify[$key][$c_key]) ? $component_has_qualify[$key][$c_key] : 0;
                            $component_qualify  = empty($component_qualify) ? 0 : 1;

                            Activity::create(
                                [
                                    'batch_id' => $batch_id,
                                    'name' => $component_name,
                                    'type' => 'component',
                                    'parent_id' => $sub_activity_id,
                                    'unit' => $component_unit,
                                    'has_grading' => $component_grading,
                                    'has_qualify' => $component_qualify
                                ]
                            );
                        }
                    } else {
                        if (!empty($sub_activity_unit)) {
                            $subActivity->unit = $sub_activity_unit;
                            $subActivity->save();
                        }
                    }
                }
            }
        }

        $result['status']   = "success";
        $result['message']  = "Activity created successfully";

        return json_encode($result);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function show(Activity $activity)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function edit(Activity $activity)
    {
        $page_title = 'Edit Activity';
        $page_description = 'Edit activity';

        $activity_id    = $activity->id;

        return view('activities.edit-activity', compact('activity', 'page_title', 'page_description'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Activity $activity)
    {
        // echo '<pre>';
        // print_r($request->all());
        // echo '</pre>';
        // return;

        $timestamp  = date('Y-m-d H:i:s');

        $activityId = $request->activity_id;

        $activity_name = $request->activity_name;
        $activity_unit = $request->activity_unit;
        $activity_gradings = $request->activity_has_grading;
        $activity_qualifys = $request->activity_has_qualify;

        $sub_activities       = $request->sub_activity;
        $sub_activity_units   = $request->sub_activity_unit;
        $sub_activity_gradings   = $request->sub_activity_has_grading;
        $sub_activity_qualifys   = $request->sub_activity_has_qualify;

        $components       = $request->component;
        $component_units  = $request->component_unit;
        $component_has_grading  = $request->component_has_grading;
        $component_has_qualify  = $request->component_has_qualify;

        foreach ($activity_name as $key => $name) {
            $unit   = $activity_unit[$key];
            if (empty($unit)) {
                $unit   = null;
            }

            $activity_grading   = isset($activity_gradings[$key]) ? $activity_gradings[$key] : 0;
            $activity_grading   = empty($activity_grading) ? 0 : 1;

            $activity_qualify   = isset($activity_qualifys[$key]) ? $activity_qualifys[$key] : 0;
            $activity_qualify   = empty($activity_qualify) ? 0 : 1;

            if (!empty($key) && !empty($name)) {
                Activity::find($key)->update([
                    'name' => $name,
                    'unit' => $unit,
                    'has_grading' => $activity_grading,
                    'has_qualify' => $activity_qualify,
                ]);
            }
        }

        $getActivity  = Activity::find($activityId);

        if (!empty($getActivity)) {
            $batch_id   = $getActivity->batch_id;

            if (!empty($sub_activities)) {
                foreach ($sub_activities as $key => $sub_activity) {

                    if (!empty($sub_activity)) {
                        $sub_activity_unit  = isset($sub_activity_units[$key]) ? $sub_activity_units[$key] : null;

                        $sub_activity_grading  = isset($sub_activity_gradings[$key]) ? $sub_activity_gradings[$key] : 0;
                        $sub_activity_grading  = empty($sub_activity_grading) ? 0 : 1;

                        $sub_activity_qualify  = isset($sub_activity_qualifys[$key]) ? $sub_activity_qualifys[$key] : 0;
                        $sub_activity_qualify  = empty($sub_activity_qualify) ? 0 : 1;

                        $subActivity = Activity::create(
                            [
                                'batch_id' => $batch_id,
                                'name' => $sub_activity,
                                'type' => 'subactivity',
                                'parent_id' => $activityId,
                                'has_grading' => $sub_activity_grading,
                                'has_qualify' => $sub_activity_qualify
                            ]
                        );

                        $sub_activity_id = $subActivity->id;
                        if (isset($components[$key])) {

                            foreach ($components[$key] as $c_key => $component_name) {
                                $component_unit   = $component_units[$key][$c_key];

                                $component_grading  = isset($component_has_grading[$key][$c_key]) ? $component_has_grading[$key][$c_key] : 0;
                                $component_grading  = empty($component_grading) ? 0 : 1;

                                $component_qualify  = isset($component_has_qualify[$key][$c_key]) ? $component_has_qualify[$key][$c_key] : 0;
                                $component_qualify  = empty($component_qualify) ? 0 : 1;

                                Activity::create(
                                    [
                                        'batch_id' => $batch_id,
                                        'name' => $component_name,
                                        'type' => 'component',
                                        'parent_id' => $sub_activity_id,
                                        'unit' => $component_unit,
                                        'has_grading' => $component_grading,
                                        'has_qualify' => $component_qualify
                                    ]
                                );
                            }
                        } else {

                            if (!empty($sub_activity_unit)) {
                                $subActivity->unit = $sub_activity_unit;
                                $subActivity->save();
                            }
                        }
                    }
                }
            }

            if (!empty($components)) {
                foreach ($components as $c_key => $component) {
                    $c_key_arr  = explode('-', $c_key);

                    if (count($c_key_arr) == 2) {
                        $subActivityId  = $c_key_arr[1];

                        for ($i = 0; $i < count($component); $i++) {
                            $component_name   = $components[$c_key][$i];
                            $component_unit   = $component_units[$c_key][$i];

                            $component_grading  = isset($component_has_grading[$c_key][$i]) ? $component_has_grading[$c_key][$i] : 0;
                            $component_grading  = empty($component_grading) ? 0 : 1;

                            $component_qualify  = isset($component_has_qualify[$c_key][$i]) ? $component_has_qualify[$c_key][$i] : 0;
                            $component_qualify  = empty($component_qualify) ? 0 : 1;

                            Activity::create(
                                [
                                    'batch_id' => $batch_id,
                                    'name' => $component_name,
                                    'type' => 'component',
                                    'parent_id' => $subActivityId,
                                    'unit' => $component_unit,
                                    'has_grading' => $component_grading,
                                    'has_qualify' => $component_qualify
                                ]
                            );
                        }
                    }
                }
            }
        }

        return json_encode([
            'status'    => "success",
            'message'    => "Activity details updated successfully"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function destroy(Activity $activity)
    {
        //
    }

    /**
     * Assign Activity.
     *
     * @return \Illuminate\Http\Response
     */
    public function assign()
    {
        $page_title = 'Assign Activity';
        $page_description = 'Assign activity';

        return view('activities.assign-activity', compact('page_title', 'page_description'));
    }

    /**
     * Process ajax requests.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function ajax(Request $request)
    {
        $requestName    = $request->requestName;

        // Activity Dropdown Options
        if ($requestName === "activityDropdownOptions") {
            $result = [];

            $batch_id   = $request->input('batch_id');

            if (!empty($batch_id)) {
                Session::put('current_batch', $batch_id);
                $activities = Activity::where('batch_id', $batch_id)
                    ->where('type', 'activity')->get();

                if (count($activities) > 0) {
                    echo "<option value=\"\">Select activity...</option>";

                    foreach ($activities as $activity) {
                        $activity_id   = $activity->id;
                        $activity_name   = $activity->name;

                        echo "<option value=\"{$activity_id}\">{$activity_name}</option>";
                    }
                } else {
                    echo "<option value=\"\">-- No Activity --</option>";
                }
            } else {
                echo "<option value=\"\">-- Batch Id Missing --</option>";
            }
        }

        // Activity Dropdown Options
        if ($requestName === "getListOfActivitiesTable") {
            $result = [];

            $batch_id   = $request->input('batch_id');
            $search     = $request->input('search');
            set_current_batch($batch_id);

            $activityQ = Activity::query();
            if (!empty($search)) {
                $batchQ = "batch_id = {$batch_id}";
                if (empty($batch_id)) {
                    $batchQ = "batch_id > {$batch_id}";
                }

                $subParents1     = Activity::whereRaw($batchQ)->where('type', 'subactivity')->where('name', 'like', "%{$search}%")->pluck('parent_id')->toArray();
                $compParents    = Activity::whereRaw($batchQ)->where('type', 'component')->where('name', 'like', "%{$search}%")->pluck('parent_id')->toArray();
                $subParents2    = Activity::whereIn('id', $compParents)->pluck('parent_id')->toArray();

                $subParents = array_unique(array_merge($subParents1, $subParents2), SORT_NUMERIC);

                $activityQ->whereIn('id', $subParents);

                if (!empty($batch_id)) {
                    $GLOBALS['batch_id']    = $batch_id;

                    $activityQ->where(function($query){
                        $query->where('batch_id', $GLOBALS['batch_id'])->where('type', 'activity');
                    });
                }
            } else {
                $activityQ->where('batch_id', $batch_id)->where('type', 'activity');
            }

            $activities = $activityQ->get();

            if (count($activities) > 0) {
                $sl = 1;
                foreach ($activities as $activity) {
                    $activity_id   = $activity->id;
                    $actBatch_id   = $activity->batch_id;
                    $activity_name   = $activity->name;
                    $activity_unit   = $activity->unit;

                    $batch_name  = DB::table('batches')->where('id', $actBatch_id)->value('BatchName');

                    $editActivity   = route('activities.edit', $activity->id);
                    $edit_icon      = asset('images/edit.png');
                    echo <<<EOL
                    <tr>
                        <td>{$sl}</td>
                        <td class="text-left">{$batch_name}</td>
                        <td class="text-left">{$activity_name}</td>
                        <td>{$activity_unit}</td>
                        <td><a href="{$editActivity}" data-toggle="tooltip" title="Edit"><img src="{$edit_icon}" /></a></td>
                    </tr>
                    EOL;

                    $sl++;
                }
            } else {
                echo "<tr style=\"background:none;\"><td colspan='5'><div class=\"msg msg-warning msg-full\">No Activities</div></td></tr>";
            }
        }

        // Get Assign Activity Form
        if ($requestName === "get_assignActivity_form") {
            return view('activities.assign-activity-form', ['request' => $request]);
        }

        // Submit Assign Activity Form
        if ($requestName === "submit_assignActivity") {
            $result = [];
            $timestamp  = date('Y-m-d H:i:s');

            $squad_id    = $request->input('squad_id');
            $activity_id = $request->input('activity_id');
            $trainer_id  = $request->input('trainer_id');

            if (empty($trainer_id)) {
                // $result['status']   = "error";
                // $result['message']  = "Please select a trainer.";

                // return json_encode($result);
                $trainer_id = null;
            }

            $squadTrainer   = DB::table('squad_activity_trainer')
                ->select('id')
                ->where('squad_id', $squad_id)
                ->where('activity_id', $activity_id)->get()->first();

            if ($squadTrainer) {
                DB::table('squad_activity_trainer')
                    ->where('id', $squadTrainer->id)
                    ->update([
                        'staff_id'  => $trainer_id,
                        'updated_at' => $timestamp
                    ]);
            } else {
                DB::table('squad_activity_trainer')->insert([
                    'squad_id'      => $squad_id,
                    'activity_id'   => $activity_id,
                    'staff_id'   => $trainer_id,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp
                ]);
            }

            $result['status']   = "success";
            $result['message']  = "Trainer updated for the Squad";

            return json_encode($result);
        }

        // Delete Activity
        if ($requestName === "delete_activity") {
            $result = [];

            $activity_id    = $request->input('activity_id');
            if (!empty($activity_id)) {
                Activity::find($activity_id)->delete();
                // Activity::where('parent_id', $activity_id)->delete();

                $result['status']   = "success";
                $result['message']  = "Records deleted successfully";
            } else {
                $result['status']   = "error";
                $result['message']  = "Activity Id is missing";
            }

            return json_encode($result);
        }

        // Undelete Activity
        if ($requestName === "undelete_activity") {
            $result = [];

            $activity_id    = $request->input('activity_id');
            if (!empty($activity_id)) {
                Activity::withTrashed()->find($activity_id)->restore();
                // Activity::withTrashed()->where('parent_id', $activity_id)->restore();

                $result['status']   = "success";
                $result['message']  = "Records deleted successfully";
            } else {
                $result['status']   = "error";
                $result['message']  = "Activity Id is missing";
            }

            return json_encode($result);
        }

        if($requestName === "get_activityImport_modal")
        {
            return view('activities.import-activity-modal', ['request' => $request]);
        }

        if($requestName === "download_activityDatasheet")
        {

           // print_r($request->all());exit;
            $batch_id = intval($request->data_batch_id);

            $errors = [];
            if (empty($batch_id)) {
                $errors[] = "Select Batch.";
            }


            $count = Activity::where('batch_id', $batch_id)->count();

            if ($count === 0) {
                return json_encode([
                    'status' => "error",
                    'message' => "Empty Activites",
                ]);
            }

            if (empty($errors)) {

                $data_request = [
                    'batch_id' => $batch_id,
                ];
                $data_request = data_crypt(serialize($data_request));

                $datasheet_url = url("/activities/download-activity-datasheet/{$data_request}");
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
        if($requestName === "import_Activity_DataSheet")
        {

        if ($request->hasFile('activity_csv') && $request->file('activity_csv')->isValid()) {
            $errorMsg = [];
            $dataRowError = [];
            $result = [];

            $original_filename = $request->activity_csv->getClientOriginalName();
            $ext = pathinfo($original_filename, PATHINFO_EXTENSION);

            if ($ext !== 'csv') {
                $result['status'] = 'error';
                $result['message'] = "Please upload a file with a .csv extension.";
                return json_encode($result);
            }

            $fileName = time() . '-' . $original_filename;
            $request->activity_csv->storeAs('csv_files', $fileName);

            $rowsToSkip = 0;
            $filePath = storage_path("app/public/csv_files/{$fileName}");
            $fileData = csvToArray($filePath, ',', $rowsToSkip);

           // return $fileData;

            if (count($fileData) > 0) {
                $valid_data_keys = ['batch', 'activity', 'subactivity', 'component', 'unit', 'grade', 'qualify'];

                $probationerData = [];
                $i = 1;
                foreach ($fileData as $key => $data) {

                    $batch_name = trim($data['batch']);
                    $batch = Batch::where('BatchName', $batch_name)->count();



                    if ($batch === 0) {
                        $result['status'] = 'error';
                        $result['message'] = "Invalid Batch.";
                        return json_encode($result);
                    }

                    $activities = Activity::where('batch_id', get_batch_id($batch_name))->count();

                    if ($activities !== 0) {
                        $result['status'] = 'error';
                        $result['message'] = "The Batch contains activities.";
                        return json_encode($result);
                    }


                    $row_num = $i++ + ($rowsToSkip + 1);


                    if ($i === 2) {
                        $data_keys = array_keys($data);
                        $diff_keys = array_diff($data_keys, $valid_data_keys);
                        if (count($diff_keys) > '1') {
                            $errorMsg = "This file contains invalid data.";
                            $result['message'] = $errorMsg;
                            $result['status'] = 'error';
                            return json_encode($result);
                        }
                    }


                    $batch_name = trim($data["batch"]);
                    $activity = trim($data["activity"]);
                    $subactivity = trim($data["subactivity"]);
                    $component = trim($data["component"]);
                    $unit = trim($data["unit"]);
                    $grade = trim($data["grade"]);
                    $qualify = trim($data["qualify"]);


        //return json_encode($count);

                    if (empty($batch_name)) {
                        $dataRowError[] = "Row #{$row_num}: Empty Batch.";
                    } else {
                        $check_batch = Batch::where('BatchName', $batch_name)->count();
                        if ($check_batch !== 1) {
                            $dataRowError[] = "Row #{$row_num}: Invalid Batch.";
                        }
                    }
                    if (empty($activity)) {
                        $dataRowError[] = "Row #{$row_num}: Empty Activity";
                    }


                    if($component != '')
                    {
                        $name = $component;
                        $type = 'component';
                        $parentytype = $subactivity;
                    }
                    elseif(!empty($subactivity) && empty($component))
                    {
                        $name = $subactivity;
                        $type = 'subactivity';
                        $parentytype = $activity;
                    }
                    elseif(empty($sub_activity) && empty($component))
                    {
                        $name = $activity;
                        $type = 'activity';
                        $parentytype = '';
                    }
                    else
                    {
                        $dataRowError[] = "Row #{$row_num}: please enter valid activites";
                    }

                    $activityDatas[] = [
                        'batch_id' => get_batch_id($batch_name),
                        'name' => $name,
                        'type' => $type,
                        'unit' => $unit,
                        'has_grading' => $grade,
                        'has_qualify' => $qualify,
                        'parent_type' => $parentytype,

                    ];
                }
                if (empty($dataRowError)) {
                    try
                    {
                        DB::beginTransaction();
                        foreach ($activityDatas as $activityData) {

                            if($activityData['parent_type'] == '')
                            {
                                $parent_id = null;
                                //return $activityData['parent_type'];
                            }
                            else
                            {
                               $parent_id = Activity::where('name', '=', $activityData['parent_type'])->where('batch_id', $activityData['batch_id'])->value('id');

                            }

                            $activity_ids = Activity::create([
                                'batch_id' => $activityData['batch_id'],
                                'name' => $activityData['name'],
                                'type' => $activityData['type'],
                                'parent_id' => $parent_id,
                                'unit' => $activityData['unit'],
                                'has_grading' => $activityData['has_grading'],
                                'has_qualify' => $activityData['has_qualify'],
                            ]);
                        }
                        $result['status'] = 'success';
                        $result['message'] = 'Data Uploaded Succesfully';
                        DB::commit();
                    } catch (\Illuminate\Database\QueryException $e) {
                        DB::rollback();
                        $errorCode = $e->errorInfo[1];
                        if ($errorCode == '1062') {
                            $duplicate = "Roll Number or Email or Mobile Number duplicates fields present in Sheet";
                            $result['error'] = 'error';
                            $result['message'] = $duplicate;
                        }
                    }
                    return json_encode($result);
                } else {
                    $dataErrorMsg1 = "<ul class=\"list-style\">";
                    for ($eii = 0; $eii < count($dataRowError); $eii++) {
                        $dataErrorMsg1 .= "<li>" . $dataRowError[$eii] . "</li>";
                    }
                    $dataErrorMsg1 .= "</ul>";

                    $errorMsg[] = $dataErrorMsg1;

                    $result['status'] = 'error';
                    $message = implode('<br />', $errorMsg);
                    $result['message'] = $message;
                    return json_encode($result);
                }
            }

        }
        }
    }

    public function activity_datasheet($data_request)
    {
        $data = unserialize(data_crypt($data_request, 'd'));
        $batch_id = isset($data["batch_id"]) ? $data["batch_id"] : 0;
        $spreadsheet = new Spreadsheet();
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Activites');

        $activities = Activity::where('batch_id', $batch_id)->where('type', 'activity')->get();

         $i = '2';
        $cell = 'A';

        $header[] = ['batch', 'activity', 'subactivity', 'component', 'unit', 'grade', 'qualify'];
        $sheet1->fromArray($header, '', 'A1');

        foreach ($activities as $activity) {
            $cell = 'A';
            $unit = ($activity->unit == null) ? '' : $activity->unit;
            $grade = ($activity->has_grading === 0) ? '' : $activity->has_grading;
            $qualify = ($activity->has_qualify ===  0) ? '' : $activity->has_qualify;

            $sheet1->setCellValue($cell++ . $i, batch_name($activity->batch_id));
            $sheet1->setCellValue($cell++ . $i, $activity->name);
            $sheet1->setCellValue($cell++ . $i, '');
            $sheet1->setCellValue($cell++ . $i, '');
            $sheet1->setCellValue($cell++ . $i, $unit);
            $sheet1->setCellValue($cell++ . $i, $grade);
            $sheet1->setCellValue($cell++ . $i, $qualify);

            $subactivities = Activity::where('parent_id', $activity->id)->where('type', 'subactivity')->get();
            foreach($subactivities as $subactivity)
            {
                $cell = 'A';
                $i++;
                $unit = ($subactivity->unit == null) ? '' : $subactivity->unit;
                $grade = ($subactivity->has_grading === 0) ? '' : $subactivity->has_grading;
                $qualify = ($subactivity->has_qualify ===  0) ? '' : $subactivity->has_qualify;


                $sheet1->setCellValue($cell++ . $i, batch_name($activity->batch_id));
                $sheet1->setCellValue($cell++ . $i, $activity->name);
                $sheet1->setCellValue($cell++ . $i, $subactivity->name);
                $sheet1->setCellValue($cell++ . $i, '');
                $sheet1->setCellValue($cell++ . $i, $unit);
                $sheet1->setCellValue($cell++ . $i, $grade);
                $sheet1->setCellValue($cell++ . $i, $qualify);

                $components = Activity::where('parent_id', $subactivity->id)->where('type', 'component')->get();

                foreach($components as $component)
                {
                    $cell = 'A';
                    $i++;
                    $unit = ($component->unit == null) ? '' : $component->unit;
                    $grade = ($component->has_grading === 0) ? '' : $component->has_grading;
                    $qualify = ($component->has_qualify ===  0) ? '' : $component->has_qualify;


                    $sheet1->setCellValue($cell++ . $i, batch_name($activity->batch_id));
                    $sheet1->setCellValue($cell++ . $i, $activity->name);
                    $sheet1->setCellValue($cell++ . $i, $subactivity->name);
                    $sheet1->setCellValue($cell++ . $i, $component->name);
                    $sheet1->setCellValue($cell++ . $i, $unit);
                    $sheet1->setCellValue($cell++ . $i, $grade);
                    $sheet1->setCellValue($cell++ . $i, $qualify);
                }
            }

            $i++;

        }
        $fileName = "Probationer_Datasheet_{$batch_id}" . date('Ymd-hia') . ".xlsx";
        spreadsheet_header($fileName);
        ob_end_clean();
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

        $writer->save('php://output');
        die();
    }
}
