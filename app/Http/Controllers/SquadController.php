<?php

namespace App\Http\Controllers;

use App\Staff;
use App\Models\User;
use App\Models\Squad;
use App\Models\probationer;
use App\Models\Batch;
use App\Models\Activity;
use App\Models\Assignprobationerstosquad;
use Illuminate\Http\Request;
use Session;

class SquadController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function totalsquads()
    {

        return view('home',compact('tsquads'));
    }

    public function index()
    {
        $batch_id = current_batch();
        $probationers = Probationer::whereNull('squad_id')->where('batch_id', $batch_id)->leftJoin('batches', 'batches.id', '=', 'probationers.batch_id')->select('probationers.id','probationers.RollNumber', 'probationers.Name', 'probationers.Cadre', 'batches.id as bid', 'batches.BatchName')->get();
        $batch = Batch::all();
        $role = ['Drillinspector', 'si', 'adi'];
        $staffs = Staff::whereIn('role', $role)->get();

        //print_r(json_encode($probationers));exit;
        return view('squad.squads',compact('probationers', 'staffs', 'batch', 'batch_id'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

         //Squad::where('Batch_Id',$request->id)->get();

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        //print_r($request->all());exit;

     //   $batch = Batch::where('BatchName', $request->Batch_Id)->first();
        $lastsquadinserted = Squad::create([
            'Batch_Id' => $request->Batch_Id,
            'SquadNumber' => $request->SquadNumber,
            'DrillInspector_Id' => $request->DrillInspector_Id,
        ]);
        $insertedId = $lastsquadinserted->id;
        if(!empty($request->pid))
            {
        for ($i = 0; $i < count($request->pid); $i++) {
            Probationer::where('id', $request->pid[$i])->update([
                'squad_id' => $insertedId
            ]);
        }
    }
         $batch = Batch::all();
        $batch_id = current_batch();
        $squadnumber = Squad::where('Batch_Id', $batch_id)
        ->leftJoin('users', 'squads.DrillInspector_Id', '=', 'users.id')
        ->select('users.name','squads.SquadNumber', 'squads.id')->get();
        //return view('squad.squadslist',compact('batch', 'squadnumber', 'batch_id'));
        return redirect()->action([SquadController::class, 'show']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Squad  $squad
     * @return \Illuminate\Http\Response
     */
    public function show(Squad $squad)
    {
        $batch = Batch::all();
        $batch_id = current_batch();
        $squadnumber = Squad::where('Batch_Id', $batch_id)
        ->leftJoin('users', 'squads.DrillInspector_Id', '=', 'users.id')
        ->select('users.name','squads.SquadNumber', 'squads.id')->get();

        return view('squad.squadslist',compact('batch', 'squadnumber', 'batch_id'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Squad  $squad
     * @return \Illuminate\Http\Response
     */
    public function edit(Squad $squad)
    {
        // print_r($squad->Batch_Id);exit;

        $batch = Batch::where('id', $squad->Batch_Id)->first();

        $role = ['Drillinspector', 'si', 'adi'];
        $staffs = Staff::whereIn('role', $role)->get();

        $probationers = Probationer::where('squad_id', null)
            ->where('batch_id', $squad->Batch_Id)
            ->leftJoin('batches', 'batches.id', '=', 'probationers.batch_id')
            ->select('probationers.id','probationers.RollNumber', 'probationers.Name', 'probationers.Cadre', 'batches.id as bid', 'batches.BatchName')
            ->orderBy('Name', 'asc')
            ->get();



        $assignedProbationers = Probationer::where('squad_id', $squad->id)
            ->leftJoin('batches', 'batches.id', '=', 'probationers.batch_id')
            ->select('probationers.id','probationers.RollNumber', 'probationers.Name', 'probationers.Cadre', 'batches.id as bid', 'batches.BatchName', 'probationers.position_number')
            ->orderBy('position_number', 'asc')
            ->orderBy('Name', 'asc')
            ->get();

        // $batch = Squad::where('id', $squad->id)->leftjoin('batches', 'batches.id', '=', 'squads.Batch_Id')->select('batches.id', 'batches.BatchName')->first();



        return view('squad.editsquadlist',compact('squad','probationers', 'staffs', 'batch', 'assignedProbationers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Squad  $squad
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Squad $squad)
    {
       // print_r(json_encode($request->input()));exit;

      //  $batch = Batch::where('BatchName', $request->Batch_Id)->first();
        $lastsquadinserted = Squad::where('id', $squad->id)->update([
            'Batch_Id' => $request->Batch_Id,
            'SquadNumber' => $request->SquadNumber,
            'DrillInspector_Id' => $request->DrillInspector_Id,
        ]);

        Probationer::where('squad_id',$squad->id)->update([
            'squad_id' => null
        ]);

        $insertedId = $squad->id;

        if(!empty($request->pid))
        {
            for ($i = 0; $i < count($request->pid); $i++) {
                $pb_id      = $request->pid[$i];
                $position   = $request->position_number[$pb_id];
                Probationer::where('id', $request->pid[$i])->update([
                    'squad_id' => $insertedId,
                    'position_number' => $position,
                ]);
            }
        }

        $batch = Batch::all();
        $batch_id = current_batch();
        $squadnumber = Squad::where('Batch_Id', $batch_id)
        ->leftJoin('users', 'squads.DrillInspector_Id', '=', 'users.id')
        ->select('users.name','squads.SquadNumber', 'squads.id')->get();

        return view('squad.squadslist',compact('batch', 'squadnumber', 'batch_id'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Squad  $squad
     * @return \Illuminate\Http\Response
     */
    public function destroy(Squad $squad)
    {


       // return redirect()->route('squads.index')->with('delete','Squad deleted successfully');

       // return redirect('/squadlist')->with('delete','Staff deleted successfully');
    }

    public function squaddata(Request $request)
    {
        Session::put('current_batch', $request->id);
        $squadnumber = Squad::where('Batch_Id', $request->id)
        ->leftJoin('users', 'squads.DrillInspector_Id', '=', 'users.id')
        ->select('users.name','squads.SquadNumber', 'squads.id')->get();
        return $squadnumber;
    }

    public function view_probationer(Request $request)
    {
        $prob_details = Probationer::where('squad_id', $request->id)
            ->select('probationers.Name', 'probationers.RollNumber', 'probationers.id')
            ->orderBy('position_number', 'asc')
            ->orderBy('Name', 'asc')
            ->get();

        return $prob_details;
    }

    public function delete_probationer(Request $request)
    {
        Probationer::where('squad_id', $request->id)->update('squad_id', null);
        return "Probationer Deleted Successfully in squad";
    }

    public function delete_squad(Request $request, Squad $squad)
    {
        Probationer::where('squad_id', $request->id)->update([
            'squad_id' => null,
        ]);
        Squad::where('id', $request->id)->delete();
       return $request->id;
    }


    public function squadDropdownOptions(Request $request)
    {
        if( !empty($request->batch_id) ) {
           // $query = "CAST(SquadNumber AS DECIMAL) ASC";
            Session::put('current_batch', $request->batch_id);
            $squads = Squad::where('Batch_Id', $request->batch_id)
                ->get();

            if( count($squads) > 0 ) {
                echo "<option value=\"\">Select squad...</option>";

                foreach($squads as $squad) {
                    $squad_id   = $squad->id;
                    $squad_no   = $squad->SquadNumber;

                    echo "<option value=\"{$squad_id}\">{$squad_no}</option>";
                }
            } else {
                echo "<option value=\"\">-- No Squad --</option>";
            }
        } else {
            echo "<option value=\"\">-- Batch Id Missing --</option>";
        }
    }

    public function activitiesDropdownOptions(Request $request)
    {
        if( !empty($request->batch_id) ) {
            $activities = Activity::where('batch_id', $request->batch_id)->where('type', 'activity')->orderBy('name', 'ASC')->get();

            if( count($activities) > 0 ) {
                echo "<option value=\"\">Select Activity</option>";

                foreach($activities as $squad) {
                    $activity_id   = $squad->id;
                    $activity_name   = $squad->name;

                    echo "<option value=\"{$activity_id}\">{$activity_name}</option>";
                }
            } else {
                echo "<option value=\"\">-- No Activity --</option>";
            }
        } else {
            echo "<option value=\"\">-- Batch Id Missing --</option>";
        }
    }

    public function subactivitiesDropdownOptions(Request $request)
    {
        if( !empty($request->Activity_Id) ) {
            $subactivities = Activity::where('parent_id', $request->Activity_Id)->orderBy('name', 'ASC')->get();

            if( count($subactivities) > 0 ) {
                echo "<option value=\"\">Select Sub Activity</option>";

                foreach($subactivities as $subactivity) {
                    $activity_id   = $subactivity->id;
                    $activity_name   = $subactivity->name;

                    echo "<option value=\"{$activity_id}\">{$activity_name}</option>";
                }
            } else {
                echo "<option value=\"\">-- No SubActivity --</option>";
            }
        } else {
            echo "<option value=\"\">-- Activity Id Missing --</option>";
        }
    }

    public function componentsDropdownOptions(Request $request)
    {
        if( !empty($request->SubActivityId) ) {
            $components = Activity::where('parent_id', $request->SubActivityId)->orderBy('name', 'ASC')->get();

            if( count($components) > 0 ) {
                echo "<option value=\"\">Select Component</option>";

                foreach($components as $component) {
                    $component_id   = $component->id;
                    $component_name   = $component->name;

                    echo "<option value=\"{$component_id}\">{$component_name}</option>";
                }
            } else {
                echo "<option value=\"\">-- No Component --</option>";
            }
        } else {
            echo "<option value=\"\">-- Sub Activity Id Missing --</option>";
        }
    }

    public function probationerDropdownOptions(Request $request)
    {
        if( !empty($request->SquadId) ) {
            $probationers = $probationers = Probationer::where('squad_id', $request->SquadId)->orderBy('position_number')->get();

            if( count($probationers) > 0 ) {
                echo "<option value=\"\">Select Probationers</option>";

                foreach($probationers as $probationer) {
                    $probationer_id   = $probationer->id;
                    $probationer_name   = $probationer->Name;

                    echo "<option value=\"{$probationer_id}\">{$probationer_name}</option>";
                }
            } else {
                echo "<option value=\"\">-- No Probationers --</option>";
            }
        } else {
            echo "<option value=\"\">-- Squad Id Missing --</option>";
        }
    }

}
